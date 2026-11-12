<?php

namespace Tests\Feature\Requerimientos\Administrador;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-14 Confirmar: botón Confirmar en las citas pendientes. Al confirmar, el
 * aviso indica que se notificó al cliente por correo.
 */
#[Group('RF-14')]
class RF14ConfirmarCitaTest extends RequerimientoTestCase
{
    public function test_las_citas_pendientes_tienen_un_boton_confirmar(): void
    {
        $this->crearCita($this->crearCliente());

        $html = $this->actingAs($this->crearAdmin())
            ->get(route('admin.citas'))
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<button[^>]*>\s*Confirmar\s*<\/button>/u',
            $html,
            'La lista de citas no tiene un botón "Confirmar"; solo hay un selector de estado con el botón "Guardar".'
        );
    }

    public function test_al_confirmar_la_cita_cambia_a_confirmada(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'confirmada'])
            ->assertRedirect(route('admin.citas'));

        $this->assertSame('confirmada', $cita->fresh()->estado);
    }

    public function test_el_aviso_indica_que_se_notifico_al_cliente_por_correo(): void
    {
        $cita = $this->crearCita($this->crearCliente(['email' => 'ana@test.com']));

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'confirmada'])
            ->assertSessionHas('success', 'Estado actualizado y correo de notificación enviado al cliente.');

        $this->assertCount(1, $this->correosEnviados());
        $this->assertSame('ana@test.com', $this->correosEnviados()->first()->getTo()[0]->getAddress());
    }

    public function test_guardar_el_mismo_estado_no_reenvia_el_correo(): void
    {
        $cita = $this->crearCita($this->crearCliente(), ['estado' => 'confirmada']);

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'confirmada']);

        $this->assertCount(0, $this->correosEnviados());
    }
}
