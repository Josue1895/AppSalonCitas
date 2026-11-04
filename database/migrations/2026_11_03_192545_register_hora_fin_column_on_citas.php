<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('citas', 'hora_fin')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->time('hora_fin')->nullable()->after('hora');
            });
        }
    }

    public function down(): void
    {
        // Se conserva la columna para proteger los datos existentes.
    }
};