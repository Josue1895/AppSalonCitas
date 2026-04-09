<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'admin',
        'confirmado',
        'token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'admin' => 'boolean',
            'confirmado' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function citas()
    {
        return $this->hasMany(Appointment::class, 'usuarioId');
    }
}