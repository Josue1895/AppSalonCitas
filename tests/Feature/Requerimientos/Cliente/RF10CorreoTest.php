<?php

namespace Tests\Feature\Requerimientos\Cliente;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-10 Correo: los avisos de reserva, modificación y confirmación dicen que
 * se envió un correo con la fecha, la hora y el lugar.
 *
 * La modificación no se prueba aquí porque la funcionalidad no existe (ver RF-08).
 */
#[Group('RF-10')]
class RF10CorreoTest extends RequerimientoTestCase
{
    public function test_al_reservar_se_envia_un_correo_al_cliente(): void
    {
        $cliente = $this->crearCliente(['email' => 'ana@test.com']);
        $servicio = $this->crearServicio();

        $this->actingAs($cliente)
            ->post(route('citas.store'), $this->datosReserva($servicio))
            ->assertSessionHasNoErrors();

        $this->assertCount(
            1,
            $this->correosEnviados(),
            'Al reservar una cita no se envió ningún correo al cliente.'
        );
    }

    public function test_el_aviso_de_reserva_indica_que_se_envio_un_correo(): void
    {
        $cliente = $this->crearCliente();
        $servicio = $this->crearServicio();

        $this->actingAs($cliente)->post(route('citas.store'), $this->datosReserva($servicio));

        $mensaje = (string) session('success');

        $this->assertStringContainsStringIgnoringCase(
            'correo',
            $mensaje,
            "El aviso de reserva no menciona el correo. Se mostró: \"$mensaje\""
        );
    }

    public function test_al_confirmar_se_envia_un_correo_con_fecha_y_hora(): void
    {
        $cliente = $this->crearCliente(['email' => 'ana@test.com']);
        $cita = $this->crearCita($cliente, ['fecha' => self::MIERCOLES, 'hora' => '10:00:00']);

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'confirmada']);

        $correos = $this->correosEnviados();

        $this->assertCount(1, $correos, 'Al confirmar la cita no se envió el correo.');

        $correo = $correos->first();

        $this->assertSame('ana@test.com', $correo->getTo()[0]->getAddress());
        $this->assertStringContainsString('21/10/2026', $correo->getHtmlBody());
        $this->assertStringContainsString('10:00', $correo->getHtmlBody());
    }

    public function test_el_correo_de_confirmacion_incluye_el_lugar(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'confirmada']);

        $cuerpo = (string) $this->correosEnviados()->first()?->getHtmlBody();

        $this->assertMatchesRegularExpression(
            '/direcci[oó]n|lugar|ubicaci[oó]n|Cra\.|Cali/iu',
            $cuerpo,
            'El correo de confirmación no indica el lugar de la cita.'
        );
    }
}
