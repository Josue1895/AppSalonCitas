<?php

namespace Tests\Feature\Requerimientos\NoFuncionales;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RNF-03 Usabilidad: toda acción importante muestra un mensaje claro, verde
 * si salió bien y rojo si hubo un error.
 *
 * RNF-02 (validaciones de horario ocupado y campos vacíos) queda cubierto por
 * RF-05 y RF-06. La versión móvil y RNF-04 (arquitectura MVC) no se pueden
 * verificar con pruebas automáticas de PHPUnit.
 */
#[Group('RNF-03')]
class RNF03UsabilidadTest extends RequerimientoTestCase
{
    public function test_una_reserva_exitosa_muestra_mensaje_verde(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->followingRedirects()
            ->post(route('citas.store'), $this->datosReserva($servicio))
            ->assertSee('alert alert-success', false)
            ->assertSee('Cita reservada correctamente');
    }

    public function test_una_accion_no_permitida_muestra_mensaje_rojo(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->actingAs($this->crearCliente())
            ->followingRedirects()
            ->post(route('citas.cancelar', $cita->id))
            ->assertSee('alert alert-danger', false)
            ->assertSee('No puedes cancelar esta cita');
    }

    public function test_un_error_de_validacion_en_la_reserva_se_muestra_en_rojo(): void
    {
        $this->crearCita($this->crearCliente());
        $servicio = $this->crearServicio();

        $html = $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->followingRedirects()
            ->post(route('citas.store'), $this->datosReserva($servicio, ['hora' => '10:00']))
            ->assertSee('Ese horario ya está ocupado')
            ->getContent();

        $this->assertStringContainsString(
            'alert alert-danger',
            $html,
            'El error "horario ocupado" se muestra en amarillo (alert-warning), no en rojo.'
        );
    }

    public function test_un_login_incorrecto_muestra_el_error(): void
    {
        $this->crearCliente(['email' => 'ana@test.com']);

        $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login.store'), ['email' => 'ana@test.com', 'password' => 'incorrecta'])
            ->assertSee('Credenciales incorrectas');
    }
}
