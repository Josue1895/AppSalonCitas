<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppointmentService extends Model
{
    use HasFactory;

    protected $table = 'citas_servicios';

    public $timestamps = false;

    protected $fillable = [
        'citaId',
        'servicioId',
        'precio_unitario',
    ];

    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
        ];
    }

    public function cita()
    {
        return $this->belongsTo(Appointment::class, 'citaId');
    }

    public function servicio()
    {
        return $this->belongsTo(Service::class, 'servicioId');
    }
}
