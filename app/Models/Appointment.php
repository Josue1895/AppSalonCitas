<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'citas';

    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'hora',
        'usuarioId',
        'total',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuarioId');
    }

    public function citasServicios()
    {
        return $this->hasMany(AppointmentService::class, 'citaId');
    }
}