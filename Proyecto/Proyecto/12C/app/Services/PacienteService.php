<?php
namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;

class PacienteService
{
    public function list (array $filters = []): LengthAwarePaginator
    {
        $query = \App\Models\Paciente::query();
        if (!empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function ($subquery) use ($term) {
                $subquery->where('nombre', 'like', $term)
                    ->orWhere('apellido', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('numero_identificacion', 'like', $term);
            });
        }
       
        
        return $query->orderBy('apellido')->orderBy('nombre')->paginate($this->resolvePerPage($filters));
    }
    
    public function create (array $data): Paciente
    {
        return Paciente::create($data);

    }
    public function update (Paciente $paciente, array $data): Paciente
    {
        $paciente->update($data);
        return $paciente->refresh();
    }
    public function delete (Paciente $paciente) : bool
    {
        return (bool) $paciente->delete();
    }
    public function resolvePerPage (array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        if ($perPage <= 0) return 15;
        return min($perPage, 100);
        
    }
}