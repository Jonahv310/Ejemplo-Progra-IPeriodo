<?php

namespace App\Services;

use App\Models\Doctor;
use Illuminate\Pagination\LenghtAwarePaginator;
use Illuminatre\Support\Facades\DB;

// Separación de responsabilidades decide si el usuario tiene acceso a lo que está solicitando. EL modelo se encarga de la lógica de negocio, el controlador se encarga de recibir la petición y devolver la respuesta, y el servicio se encarga de la conexión entre el controlador y el modelo, es decir, se encarga de la lógica de aplicación. El servicio se encarga de llamar al modelo para obtener los datos necesarios y luego devolverlos al controlador para que este los devuelva al usuario.
// En conclusión, mover la logica de negocio a un servicio aumentala escalabilidad, separa las responsabilidades, facilita el mantenimiento, y genera un diseño profesiona de arquitectura
class DoctorService
{
    // Service logic for Doctor model
    public function list(array $filters = []): LenghtAwarePaginator
    {
        $query = Doctor::query()->with('especialidades');
        // Filtro de busqueda por nombre
        if (! empty($filters['estado'].'%')) {
            $query->where('estado', $filters['estado']);
        }

        if (! empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($subquery) use ($term) {
                $subquery->where('nombre', 'like', $term)
                    ->orWhere('apellido', 'like', $term)
                    ->orWhere('numero_colegiado', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });

        }

        return $query->orderBy('nombre')->orderBy('apellido')->paginate($this->resolvePerPage($filters));
    }

    public function create(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            $doctor = Doctor::create($data);
        });
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data) {
            $doctor->update($data);

            return $doctor;
        });
    }
}
