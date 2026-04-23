<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LenghtAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class CitaService
{
    public function list(array $filters = []): LenghtAwarePaginator
    {
        $query = Cita::query()->with(['doctor', 'paciente']);

        if (! empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (! empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($subquery) use ($term) {
                $subquery->whereHas('doctor', function ($doctorQuery) use ($term) {
                    $doctorQuery->where('nombre', 'like', $term)
                        ->orWhere('apellido', 'like', $term);
                })->orWhereHas('paciente', function ($pacienteQuery) use ($term) {
                    $pacienteQuery->where('nombre', 'like', $term)
                        ->orWhere('apellido', 'like', $term);
                });
            });
        }

        return $query->orderBy('fecha_hora')->paginate($this->resolvePerPage($filters));
    }

    public function create(array $data): Cita
    {
        return DB::transaction(function () use ($data) {
            // Validar que el doctor y el paciente existan
            if (! Doctor::find($data['doctor_id'])) {
                throw ValidationException::withMessages(['doctor_id' => 'El doctor especificado no existe.']);
            }

            if (! Paciente::find($data['paciente_id'])) {
                throw ValidationException::withMessages(['paciente_id' => 'El paciente especificado no existe.']);
            }

            return Cita::create($data);
        });
    }

    public function update(Cita $cita, array $data): Cita
    {
        $mergedData = array_merge(
            $cita->only(['doctor_id', 'fecha', 'hora']),
            $data
        );

        $this->validateAvailability(
            $mergedData['doctor_id'],
            $mergedData['fecha'],
            $mergedData['hora'],
            $cita->id
        );
        $cita->update($data);
        return $cita->refresh()->load(['doctor', 'paciente']);
    }

    public function changeStatus(Cita $cita, string $estado): Cita
    {
        if (! in_array($estado, ['pendiente', 'confirmada', 'cancelada'])) {
            throw ValidationException::withMessages(['estado' => 'Estado no válido.']);
        }

        $cita->update(['estado' => $estado]);
        return $cita->refresh()->load(['doctor', 'paciente']);
    }

    public function delete(Cita $cita): bool
    {
        return (bool)$cita->delete();
    }

    public function validateAvailability(int $doctorId, string $fecha, string $hora, ?int $ignoreCitaId = null): void
    {
        $query = Cita::query()
            ->where('doctor_id', $doctorId)
            ->where('fecha', $fecha)
            ->where('hora', $hora)
            ->where('estado', '!=', 'cancelada');

        if ($ignoreCitaId !== null) {
            $query->where('id', '!=', $ignoreCitaId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages(['hora' => 'El doctor no está disponible en esa fecha y hora.']);
        }
    }

    private function resolvePerPage(array $filters): int
    {
        $perPage = $filters['per_page'] ?? 15;
        return is_numeric($perPage) && (int)$perPage > 0 ? (int)$perPage : 15;
    }
}