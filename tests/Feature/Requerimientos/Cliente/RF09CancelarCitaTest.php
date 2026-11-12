<?php

namespace Tests\Feature\Requerimientos\Cliente;

use App\Models\Appointment;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-09 Cancelar: botón Cancelar en Mis Citas. El aviso confirma que el
 * horario quedó disponible y la cita cambia a "cancelada".
 */
#[Group('RF-09')]
class RF09CancelarCitaTest extends RequerimientoTestCase
{
    public function test_mis_citas_muestra_el_boton_cancelar_en_citas_pendientes(): void
    {
        $cliente = $this->crearCliente();
        $cita = $this->crearCita($cliente);

        $this->actingAs($cliente)
            ->get(route('citas.mis'))
            ->assertSee(route('citas.cancelar', $cita->id), false)
            ->assertSee('Cancelar');
    }

    public function test_el_cliente_cancela_su_cita_y_cambia_a_cancelada(): void
    {
        $cliente = $this->crearCliente();
        $cita = $this->crearCita($cliente);

        $this->actingAs($cliente)
            ->post(route('citas.cancelar', $cita->id))
            ->assertRedirect(route('citas.mis'))
            ->assertSessionHas('success');

        $this->assertSame('cancelada', $cita->fresh()->estado);
    }

    public function test_el_aviso_confirma_que_el_horario_quedo_disponible(): void
    {
        $cliente = $this->crearCliente();
        $cita = $this->crearCita($cliente);

        $this->actingAs($cliente)->post(route('citas.cancelar', $cita->id));

        $mensaje = session('success');

        $this->assertStringContainsStringIgnoringCase(
            'disponible',
            (string) $mensaje,
            "El aviso de cancelación no indica que el horario quedó disponible. Se mostró: \"$mensaje\""
        );
    }

    public function test_despues_de_cancelar_otro_cliente_puede_reservar_ese_horario(): void
    {
        $servicio = $this->crearServicio();
        $cliente = $this->crearCliente();
        $cita = $this->crearCita($cliente, ['hora' => '10:00:00', 'hora_fin' => '11:00:00']);

        $this->actingAs($cliente)->post(route('citas.cancelar', $cita->id));

        $this->actingAs($this->crearCliente())
            ->post(route('citas.store'), $this->datosReserva($servicio, ['hora' => '10:00']))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Appointment::where('estado', 'pendiente')->count());
    }

    public function test_un_cliente_no_puede_cancelar_la_cita_de_otro(): void
    {
        $duena = $this->crearCliente();
        $cita = $this->crearCita($duena);

        $this->actingAs($this->crearCliente())
            ->post(route('citas.cancelar', $cita->id))
            ->assertSessionHas('error');

        $this->assertSame('pendiente', $cita->fresh()->estado);
    }

    public function test_un_invitado_no_puede_cancelar_citas(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->post(route('citas.cancelar', $cita->id))
            ->assertRedirect(route('login'));

        $this->assertSame('pendiente', $cita->fresh()->estado);
    }
}
