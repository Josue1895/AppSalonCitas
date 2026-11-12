<?php

namespace Tests\Unit;

use App\Models\Appointment;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Pruebas unitarias del modelo Appointment (citas).
 *
 * Las citas se crean con "new Appointment(...)" y nunca se guardan.
 */
class AppointmentTest extends TestCase
{
    public function test_usa_la_tabla_citas(): void
    {
        $this->assertSame('citas', (new Appointment())->getTable());
    }

    public function test_la_fecha_se_convierte_en_un_objeto_fecha(): void
    {
        // Preparar y actuar
        $cita = new Appointment(['fecha' => '2026-10-21']);

        // Comprobar
        $this->assertInstanceOf(Carbon::class, $cita->fecha);
        $this->assertSame('21/10/2026', $cita->fecha->format('d/m/Y'));
    }

    public function test_el_total_se_guarda_con_dos_decimales(): void
    {
        $cita = new Appointment(['total' => 25000]);

        $this->assertSame('25000.00', $cita->total);
    }

    public function test_se_puede_asignar_el_estado(): void
    {
        $cita = new Appointment(['estado' => 'confirmada']);

        $this->assertSame('confirmada', $cita->estado);
    }

    public function test_pertenece_a_un_usuario_por_el_campo_usuarioId(): void
    {
        $relacion = (new Appointment())->usuario();

        $this->assertSame('usuarioId', $relacion->getForeignKeyName());
    }

    public function test_sus_servicios_se_relacionan_por_el_campo_citaId(): void
    {
        $relacion = (new Appointment())->citasServicios();

        $this->assertSame('citaId', $relacion->getForeignKeyName());
    }
}
