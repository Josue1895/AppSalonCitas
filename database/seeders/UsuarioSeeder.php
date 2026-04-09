<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'nombre' => 'Admin',
                'apellido' => 'Principal',
                'email' => 'admin@appsalon.com',
                'password' => Hash::make('123456'),
                'telefono' => '3001234567',
                'admin' => 1,
                'confirmado' => 1,
                'token' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Cliente',
                'apellido' => 'Prueba',
                'email' => 'cliente@appsalon.com',
                'password' => Hash::make('123456'),
                'telefono' => '3007654321',
                'admin' => 0,
                'confirmado' => 1,
                'token' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}