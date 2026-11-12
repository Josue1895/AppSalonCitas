<?php

namespace Tests\Feature\Requerimientos\Cliente;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-07 Mis citas: fecha, hora, total y estado de cada cita. Solo aparecen
 * las del cliente que inició sesión.
 */
#[Group('RF-07')]
class RF07MisCitasTest extends RequerimientoTestCase
{
    public function test_un_invitado_es_enviado_al_login(): void
    {
        $this->get(route('citas.mis'))->assertRedirect(route('login'));
    }

    public function test_muestra_fecha_hora_total_y_estado_de_las_citas_del_cliente(): void
    {
        $cliente = $this->crearCliente();

        $this->crearCita($cliente, [
            'fecha' => '2026-10-22',
            'hora' => '15:00:00',
            'hora_fin' => '16:00:00',
            'total' => 45000,
            'estado' => 'confirmada',
        ]);

        $this->actingAs($cliente)
            ->get(route('citas.mis'))
            ->assertOk()
            ->assertSee('2026-10-22')
            ->assertSee('15:00')
            ->assertSee('45000')
            ->assertSee('Confirmada');
    }

    public function test_no_muestra_las_citas_de_otro_cliente(): void
    {
        $cliente = $this->crearCliente();
        $otroCliente = $this->crearCliente();

        $this->crearCita($cliente, ['total' => 11111]);
        $this->crearCita($otroCliente, ['fecha' => '2026-10-23', 'total' => 77777]);

        $this->actingAs($cliente)
            ->get(route('citas.mis'))
            ->assertSee('11111')
            ->assertDontSee('77777')
            ->assertDontSee('2026-10-23');
    }

    public function test_sin_citas_muestra_un_mensaje(): void
    {
        $this->actingAs($this->crearCliente())
            ->get(route('citas.mis'))
            ->assertSee('No tienes citas registradas.');
    }
}
