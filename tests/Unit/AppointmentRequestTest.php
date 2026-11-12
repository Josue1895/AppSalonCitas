<?php

namespace Tests\Unit;

use App\Http\Requests\AppointmentRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Pruebas unitarias de las reglas del formulario de reserva.
 *
 * Se prueba la clase AppointmentRequest por sí sola: sus reglas, sus mensajes
 * y cómo responde cada regla ante datos buenos y malos. No se usa la base de
 * datos ni se abre ninguna página.
 */
class AppointmentRequestTest extends TestCase
{
    private function reglas(): array
    {
        return (new AppointmentRequest())->rules();
    }

    /**
     * Valida un solo campo usando únicamente su regla.
     */
    private function esValido(string $campo, mixed $valor): bool
    {
        $validador = Validator::make(
            [$campo => $valor],
            [$campo => $this->reglas()[$campo]]
        );

        return !$validador->fails();
    }

    public function test_cualquier_persona_puede_usar_el_formulario(): void
    {
        $this->assertTrue((new AppointmentRequest())->authorize());
    }

    public function test_la_fecha_de_la_cita_es_obligatoria_y_no_puede_ser_pasada(): void
    {
        // Preparar y actuar
        $reglas = $this->reglas();

        // Comprobar
        $this->assertContains('required', $reglas['fecha']);
        $this->assertContains('after_or_equal:today', $reglas['fecha']);
    }

    public function test_la_fecha_debe_tener_formato_anio_mes_dia(): void
    {
        $this->assertTrue($this->esValido('fecha', '2030-10-21'));
        $this->assertFalse($this->esValido('fecha', '21-10-2030'));
        $this->assertFalse($this->esValido('fecha', '21/10/2030'));
    }

    public function test_la_fecha_no_puede_quedar_vacia(): void
    {
        $this->assertFalse($this->esValido('fecha', ''));
    }

    public function test_una_fecha_pasada_es_rechazada(): void
    {
        $this->assertFalse($this->esValido('fecha', '2020-01-01'));
    }

    public function test_la_hora_debe_tener_formato_de_24_horas(): void
    {
        $this->assertTrue($this->esValido('hora', '09:00'));
        $this->assertTrue($this->esValido('hora', '17:30'));
        $this->assertFalse($this->esValido('hora', '9am'));
        $this->assertFalse($this->esValido('hora', '25:00'));
    }

    public function test_la_hora_no_puede_quedar_vacia(): void
    {
        $this->assertFalse($this->esValido('hora', ''));
    }

    public function test_se_debe_elegir_al_menos_un_servicio(): void
    {
        $this->assertContains('min:1', $this->reglas()['servicios']);
        $this->assertFalse($this->esValido('servicios', []));
    }

    public function test_los_mensajes_de_error_estan_en_espanol(): void
    {
        $mensajes = (new AppointmentRequest())->messages();

        $this->assertSame('La fecha de la cita es obligatoria.', $mensajes['fecha.required']);
        $this->assertSame('La hora de la cita es obligatoria.', $mensajes['hora.required']);
        $this->assertSame('Debes seleccionar al menos un servicio.', $mensajes['servicios.required']);
        $this->assertSame('La fecha de la cita no puede ser anterior a hoy.', $mensajes['fecha.after_or_equal']);
    }
}
