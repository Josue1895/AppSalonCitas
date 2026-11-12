<?php

namespace Tests\Feature\Requerimientos\Cliente;

use App\Models\Appointment;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-04 Reservar cita: desde cualquier botón Reservar. Si no ha iniciado
 * sesión, primero lo manda al Login, porque solo un cliente autenticado
 * puede reservar.
 */
#[Group('RF-04')]
class RF04ReservarRequiereLoginTest extends RequerimientoTestCase
{
    public function test_un_invitado_que_entra_a_reservar_es_enviado_al_login(): void
    {
        $this->get(route('citas.index'))
            ->assertRedirect(route('login'));
    }

    public function test_un_invitado_que_envia_una_reserva_valida_es_enviado_al_login(): void
    {
        $servicio = $this->crearServicio();

        $this->post(route('citas.store'), $this->datosReserva($servicio))
            ->assertRedirect(route('login'));

        $this->assertSame(0, Appointment::count());
    }

    public function test_un_invitado_con_datos_incompletos_tambien_es_enviado_primero_al_login(): void
    {
        $this->from(route('citas.index'))
            ->post(route('citas.store'), [])
            ->assertRedirect(route('login'));
    }

    public function test_un_cliente_autenticado_puede_reservar(): void
    {
        $cliente = $this->crearCliente();
        $corte = $this->crearServicio(['precio' => 25000, 'duracion' => 30]);
        $tinte = $this->crearServicio(['nombre' => 'Tinte', 'precio' => 60000, 'duracion' => 60]);

        $respuesta = $this->actingAs($cliente)->post(route('citas.store'), [
            'fecha' => self::MIERCOLES,
            'hora' => '10:00',
            'servicios' => [$corte->id, $tinte->id],
        ]);

        $respuesta->assertRedirect(route('citas.mis'));
        $respuesta->assertSessionHas('success');

        $cita = Appointment::first();

        $this->assertNotNull($cita, 'La cita no se guardó.');
        $this->assertSame($cliente->id, (int) $cita->usuarioId);
        $this->assertSame('pendiente', $cita->estado);
        $this->assertEquals(85000, (float) $cita->total);
        $this->assertSame('10:00:00', $cita->hora);
        $this->assertSame('11:30:00', $cita->hora_fin);
        $this->assertCount(2, $cita->citasServicios);
    }

    public function test_un_administrador_no_puede_reservar_porque_no_es_cliente(): void
    {
        $admin = $this->crearAdmin();
        $servicio = $this->crearServicio();

        $this->actingAs($admin)->post(route('citas.store'), $this->datosReserva($servicio));

        $this->assertSame(
            0,
            Appointment::count(),
            'El administrador pudo reservar una cita, pero solo un cliente debería poder hacerlo.'
        );
    }
}
