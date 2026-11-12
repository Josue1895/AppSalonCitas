<?php

namespace Tests\Feature\Requerimientos\Cliente;

use App\Models\Appointment;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-05 Servicio, fecha y hora: en Reservar Cita se eligen los tres. Si falta
 * alguno, aparece el aviso de campos vacíos.
 */
#[Group('RF-05')]
class RF05CamposReservaTest extends RequerimientoTestCase
{
    public function test_el_formulario_permite_elegir_servicio_fecha_y_hora(): void
    {
        $this->crearServicio(['nombre' => 'Pedicure']);

        $this->actingAs($this->crearCliente())
            ->get(route('citas.index'))
            ->assertOk()
            ->assertSee('name="fecha"', false)
            ->assertSee('name="hora"', false)
            ->assertSee('name="servicios[]"', false)
            ->assertSee('Pedicure');
    }

    public function test_sin_servicio_aparece_el_aviso(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), $this->datosReserva($servicio, ['servicios' => []]))
            ->assertRedirect(route('citas.index'))
            ->assertSessionHasErrors(['servicios' => 'Debes seleccionar al menos un servicio.']);

        $this->assertSame(0, Appointment::count());
    }

    public function test_sin_fecha_aparece_el_aviso(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), $this->datosReserva($servicio, ['fecha' => '']))
            ->assertSessionHasErrors(['fecha' => 'La fecha de la cita es obligatoria.']);

        $this->assertSame(0, Appointment::count());
    }

    public function test_sin_hora_aparece_el_aviso(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), $this->datosReserva($servicio, ['hora' => '']))
            ->assertSessionHasErrors(['hora' => 'La hora de la cita es obligatoria.']);

        $this->assertSame(0, Appointment::count());
    }

    public function test_con_todo_vacio_aparecen_los_tres_avisos(): void
    {
        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), [])
            ->assertSessionHasErrors(['fecha', 'hora', 'servicios']);
    }

    public function test_un_servicio_que_no_existe_es_rechazado(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), $this->datosReserva($servicio, ['servicios' => [999]]))
            ->assertSessionHasErrors('servicios.0');

        $this->assertSame(0, Appointment::count());
    }

    public function test_un_servicio_inactivo_es_rechazado(): void
    {
        $servicio = $this->crearServicio(['activo' => false]);

        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), $this->datosReserva($servicio))
            ->assertSessionHasErrors('servicios');

        $this->assertSame(0, Appointment::count());
    }
}
