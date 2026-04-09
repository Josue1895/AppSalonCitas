<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'precio',
        'descripcion',
        'duracion',
        'activo'
    ];

    public function citasServicios()
    {
        return $this->hasMany(AppointmentService::class, 'servicioId');
    }
}
