<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('horarios')
            ->where('activo', true)
            ->where('hora_inicio', '09:00:00')
            ->update([
                'hora_inicio' => '08:00:00',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Los horarios se pueden configurar desde el panel de administración.
    }
};
