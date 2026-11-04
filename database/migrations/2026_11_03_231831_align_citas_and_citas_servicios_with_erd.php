
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    public function up(): void
    {
        /*
         * 1. Agregar las nuevas columnas como nullable
         * para poder completar primero los registros existentes.
         */

        Schema::table('citas', function (Blueprint $table) {
            $table->unsignedBigInteger('horarioId')->nullable();
        });

        Schema::table('citas_servicios', function (Blueprint $table) {
            $table->decimal('precio_unitario', 8, 2)->nullable();
        });

        /*
         * 2. Asignar a cada cita el horario correspondiente
         * al día de la semana de su fecha.
         *
         * WEEKDAY(): lunes = 0, domingo = 6.
         * Por eso sumamos 1 para coincidir con horarios.
         */

        DB::statement("
            UPDATE citas c
            INNER JOIN horarios h
                ON h.dia_semana = WEEKDAY(c.fecha) + 1
            SET c.horarioId = h.id
            WHERE c.horarioId IS NULL
        ");

        /*
         * 3. Recuperar el precio actual del servicio
         * como valor inicial para los registros históricos.
         */

        DB::statement("
            UPDATE citas_servicios cs
            INNER JOIN servicios s
                ON s.id = cs.servicioId
            SET cs.precio_unitario = s.precio
            WHERE cs.precio_unitario IS NULL
        ");

        /*
         * 4. Comprobar que todos los registros
         * hayan podido relacionarse correctamente.
         */

        $citasSinHorario = DB::table('citas')
            ->whereNull('horarioId')
            ->count();

        $serviciosSinPrecio = DB::table('citas_servicios')
            ->whereNull('precio_unitario')
            ->count();

        $serviciosSinRelacion = DB::table('citas_servicios as cs')
            ->leftJoin('servicios as s', 's.id', '=', 'cs.servicioId')
            ->whereNull('s.id')
            ->count();

        if (
            $citasSinHorario > 0 ||
            $serviciosSinPrecio > 0 ||
            $serviciosSinRelacion > 0
        ) {
            throw new RuntimeException(
                'No se pudo completar la migración: existen citas o servicios sin relación o precio. No se aplicaron las restricciones finales.'
            );
        }

        /*
         * 5. Convertir las nuevas columnas en obligatorias.
         */

        DB::statement("
            ALTER TABLE citas
            MODIFY horarioId BIGINT UNSIGNED NOT NULL
        ");

        DB::statement("
            ALTER TABLE citas_servicios
            MODIFY precio_unitario DECIMAL(8,2) NOT NULL
        ");

        /*
         * 6. Proteger la relación entre horarios y citas.
         * No se podrá eliminar un horario utilizado.
         */

        Schema::table('citas', function (Blueprint $table) {
            $table->foreign('horarioId', 'citas_horarioid_foreign')
                ->references('id')
                ->on('horarios')
                ->restrictOnDelete();
        });

        /*
         * 7. Cambiar la relación de servicios:
         * de CASCADE a RESTRICT.
         *
         * Se conserva CASCADE entre citas y sus detalles.
         */

        Schema::table('citas_servicios', function (Blueprint $table) {
            $table->dropForeign(['servicioId']);
        });

        Schema::table('citas_servicios', function (Blueprint $table) {
            $table->foreign('servicioId', 'citas_servicios_servicioid_foreign')
                ->references('id')
                ->on('servicios')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        /*
         * Restaurar la regla original de servicios.
         */

        Schema::table('citas_servicios', function (Blueprint $table) {
            $table->dropForeign('citas_servicios_servicioid_foreign');
        });

        Schema::table('citas_servicios', function (Blueprint $table) {
            $table->foreign('servicioId')
                ->references('id')
                ->on('servicios')
                ->cascadeOnDelete();
        });

        /*
         * Eliminar la nueva relación con horarios
         * y las columnas agregadas.
         */

        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign('citas_horarioid_foreign');
            $table->dropColumn('horarioId');
        });

        Schema::table('citas_servicios', function (Blueprint $table) {
            $table->dropColumn('precio_unitario');
        });
    }
};
