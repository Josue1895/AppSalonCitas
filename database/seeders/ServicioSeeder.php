<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('servicios')->insert([
            [
                'nombre' => 'Corte de Cabello',
                'precio' => 25000,
                'descripcion' => 'Servicio de corte de cabello personalizado',
                'duracion' => 60,
                'activo' => 1,
                'created_at' => now()
            ],
            [
                'nombre' => 'Manicure',
                'precio' => 30000,
                'descripcion' => 'Servicio de manicure profesional',
                'duracion' => 45,
                'activo' => 1,
                'created_at' => now()
            ],
            [
                'nombre' => 'Pedicure',
                'precio' => 35000,
                'descripcion' => 'Servicio de pedicure completo',
                'duracion' => 50,
                'activo' => 1,
                'created_at' => now()
            ],
            [
                'nombre' => 'Tintura',
                'precio' => 80000,
                'descripcion' => 'Aplicación de color al cabello',
                'duracion' => 120,
                'activo' => 1,
                'created_at' => now()
            ],
            [
                'nombre' => 'Peinado',
                'precio' => 40000,
                'descripcion' => 'Peinado para ocasión especial',
                'duracion' => 60,
                'activo' => 1,
                'created_at' => now()
            ]
        ]);
    }
}