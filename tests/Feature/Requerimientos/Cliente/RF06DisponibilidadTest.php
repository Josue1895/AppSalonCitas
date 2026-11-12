<?php

namespace Tests\Feature\Requerimientos\Cliente;

use App\Models\Appointment;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-06 Disponibilidad: error si la hora está tomada o fuera del horario de
 * atención. La comprobación real la hace el servidor antes de guardar.
 *
 * Situación base: el miércoles ya existe una cita de 10:00 a 11:00.
 */
#[Group('RF-06')]
class RF06DisponibilidadTest extends RequerimientoTestCase
{
    private function reservar(array $datos, int $duracion = 60)
    {
        $servicio = $this->crearServicio(['duracion' => $duracion]);

        return $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), $this->datosReserva($servicio, $datos));
    }

    private function crearCitaExistente(string $estado = 'pendiente'): void
    {
        $this->crearCita($this->crearCliente(), [
            'hora' => '10:00:00',
            'hora_fin' => '11:00:00',
            'estado' => $estado,
        ]);
    }

    public function test_una_hora_ya_tomada_muestra_el_error_de_horario_ocupado(): void
    {
        $this->crearCitaExistente();

        $this->reservar(['hora' => '10:30'])
            ->assertSessionHasErrors([
                'hora' => 'Ese horario ya está ocupado. Selecciona otra hora para tu cita.',
            ]);

        $this->assertSame(1, Appointment::count());
    }

    public function test_una_cita_que_se_cruza_por_el_inicio_tambien_es_rechazada(): void
    {
        $this->crearCitaExistente();

        // 09:30 + 60 min = 10:30, se cruza con la cita de 10:00.
        $this->reservar(['hora' => '09:30'])->assertSessionHasErrors('hora');

        $this->assertSame(1, Appointment::count());
    }

    public function test_una_cita_justo_despues_de_otra_si_se_permite(): void
    {
        $this->crearCitaExistente();

        $this->reservar(['hora' => '11:00'])->assertSessionHasNoErrors();

        $this->assertSame(2, Appointment::count());
    }

    public function test_el_horario_de_una_cita_cancelada_queda_disponible(): void
    {
        $this->crearCitaExistente('cancelada');

        $this->reservar(['hora' => '10:00'])->assertSessionHasNoErrors();

        $this->assertSame(2, Appointment::count());
    }

    public function test_una_hora_antes_de_la_apertura_es_rechazada(): void
    {
        $this->reservar(['hora' => '07:30'])->assertSessionHasErrors([
            'hora' => 'La hora seleccionada está fuera del horario de atención.',
        ]);

        $this->assertSame(0, Appointment::count());
    }

    public function test_una_hora_despues_del_cierre_es_rechazada(): void
    {
        $this->reservar(['hora' => '18:30'])->assertSessionHasErrors('hora');

        $this->assertSame(0, Appointment::count());
    }

    public function test_un_servicio_que_termina_despues_del_cierre_es_rechazado(): void
    {
        // 17:30 + 60 min = 18:30, después del cierre de las 18:00.
        $this->reservar(['hora' => '17:30'])->assertSessionHasErrors('hora');

        $this->assertSame(0, Appointment::count());
    }

    public function test_un_dia_cerrado_es_rechazado(): void
    {
        $this->reservar(['fecha' => self::DOMINGO])->assertSessionHasErrors([
            'fecha' => 'No hay atención disponible para el día seleccionado.',
        ]);

        $this->assertSame(0, Appointment::count());
    }

    public function test_una_fecha_pasada_es_rechazada(): void
    {
        $this->reservar(['fecha' => '2026-10-19'])->assertSessionHasErrors('fecha');

        $this->assertSame(0, Appointment::count());
    }
}
