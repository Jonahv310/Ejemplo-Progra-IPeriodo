<?php

namespace App\Services;

use App\Models\Especialidad;
use Illuminate\Pagination\LengthAwarePaginator;

// Separación de responsabilidades decide si el usuario tiene acceso a lo que está solicitando. EL modelo se encarga de la lógica de negocio, el controlador se encarga de recibir la petición y devolver la respuesta, y el servicio se encarga de la conexión entre el controlador y el modelo, es decir, se encarga de la lógica de aplicación. El servicio se encarga de llamar al modelo para obtener los datos necesarios y luego devolverlos al controlador para que este los devuelva al usuario.
// En conclusión, mover la logica de negocio a un servicio aumentala escalabilidad, separa las responsabilidades, facilita el mantenimiento, y genera un diseño profesiona de arquitectura
class EspecialidadesService
{
    // esta función retorna una lista paginada de especialidades,
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Especialidad::query();
        // Filtro de busqueda por nombre
        if (isset($filters['q'])) {
            $query->where('nombre', 'like', '%'.$filters['q'].'%');
        }

        return $query->orderBy('nombre')->paginate($this->resolvePerPage($filters));
    }

    public function delete(Especialidad $especialidad): bool
    {
        return (bool) $especialidad->delete();

    }

    public function resolvePerPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        if ($perPage <= 0) {
            return 15;
        }

        return min($perPage, 100);

    }
}
