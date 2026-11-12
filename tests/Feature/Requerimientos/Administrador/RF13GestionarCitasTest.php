<?php

namespace Tests\Feature\Requerimientos\Administrador;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-13 Gestionar citas: menú Citas. El administrador ve las citas de todos
 * los clientes y puede cambiar su estado o cancelarlas.
 */
#[Group('RF-13')]
class RF13GestionarCitasTest extends RequerimientoTestCase
{
    public function test_el_administrador_ve_las_citas_de_todos_los_clientes(): void
    {
        $this->crearCita($this->crearCliente(['nombre' => 'Ana', 'apellido' => 'Gómez']));
        $this->crearCita(
            $this->crearCliente(['nombre' => 'Luis', 'apellido' => 'Pérez']),
            ['hora' => '12:00:00', 'hora_fin' => '13:00:00']
        );

        $this->actingAs($this->crearAdmin())
            ->get(route('admin.citas'))
            ->assertOk()
            ->assertSee('Ana')
            ->assertSee('Gómez')
            ->assertSee('Luis')
            ->assertSee('Pérez')
            ->assertSee('Total: 2');
    }

    public function test_el_administrador_cambia_el_estado_de_una_cita(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'completada'])
            ->assertRedirect(route('admin.citas'))
            ->assertSessionHas('success');

        $this->assertSame('completada', $cita->fresh()->estado);
    }

    public function test_el_administrador_cancela_una_cita(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'cancelada'])
            ->assertSessionHas('success');

        $this->assertSame('cancelada', $cita->fresh()->estado);
    }

    public function test_un_estado_invalido_es_rechazado(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->actingAs($this->crearAdmin())
            ->from(route('admin.citas'))
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'inventado'])
            ->assertSessionHasErrors('estado');

        $this->assertSame('pendiente', $cita->fresh()->estado);
    }

    public function test_un_cliente_no_puede_ver_ni_cambiar_citas_de_otros(): void
    {
        $cita = $this->crearCita($this->crearCliente());
        $intruso = $this->crearCliente();

        $this->actingAs($intruso)->get(route('admin.citas'))->assertRedirect(route('home'));

        $this->actingAs($intruso)
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'cancelada'])
            ->assertRedirect(route('home'));

        $this->assertSame('pendiente', $cita->fresh()->estado);
    }
}
