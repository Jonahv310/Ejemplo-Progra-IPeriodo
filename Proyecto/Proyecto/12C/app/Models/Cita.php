<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    protected $fillable = ['doctor_id', 'paciente_id', 'fecha', 'hora', 'motivo', 'estado', 'observaciones'];

     public function casts(): array
    {
        return [
            'fecha' => 'date',
            'hora' => 'datetime:H:i:s',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
