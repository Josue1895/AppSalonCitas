
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->unsignedTinyInteger('dia_semana')
                ->nullable()
                ->after('id');
        });

        // Actualizamos el registro existente como lunes.
        DB::table('horarios')
            ->where('id', 1)
            ->update([
                'dia_semana' => 1,
                'hora_inicio' => '09:00:00',
                'hora_fin' => '18:00:00',
                'activo' => 1,
            ]);

        // Martes a domingo.
        $horarios = [
            ['dia_semana' => 2, 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00', 'activo' => 1],
            ['dia_semana' => 3, 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00', 'activo' => 1],
            ['dia_semana' => 4, 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00', 'activo' => 1],
            ['dia_semana' => 5, 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00', 'activo' => 1],
            ['dia_semana' => 6, 'hora_inicio' => '09:00:00', 'hora_fin' => '14:00:00', 'activo' => 1],
            ['dia_semana' => 7, 'hora_inicio' => '00:00:00', 'hora_fin' => '00:00:00', 'activo' => 0],
        ];

        foreach ($horarios as $horario) {
            DB::table('horarios')->insert($horario + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Todos los días deben tener una identificación obligatoria.
        Schema::table('horarios', function (Blueprint $table) {
            $table->unsignedTinyInteger('dia_semana')
                ->nullable(false)
                ->change();

            $table->unique('dia_semana', 'horarios_dia_semana_unique');
        });
    }

    public function down(): void
    {
        // Se conserva la estructura y los datos para proteger los horarios.
    }
};
