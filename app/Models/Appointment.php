<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'citas';

    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'hora',
        'hora_fin',
        'usuarioId',
        'horarioId',
        'total',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuarioId');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horarioId');
    }

    public function citasServicios()
    {
        return $this->hasMany(AppointmentService::class, 'citaId');
    }
}
