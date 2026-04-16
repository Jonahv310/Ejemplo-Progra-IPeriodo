<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';
    protected $fillable = ['nombre', 'apellido','genero', 'telefono', 'email', 'fecha_nacimiento', 'direccion', 'numero_identificacion'];

    public function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'paciente_id');  
    }
}
