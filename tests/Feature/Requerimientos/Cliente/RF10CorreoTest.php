<?php

namespace Tests\Feature\Requerimientos\Cliente;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-10 Correo: los avisos de reserva, modificación y confirmación dicen que
 * se envió un correo con la fecha, la hora y el lugar.
 */
#[Group('RF-10')]
class RF10CorreoTest extends RequerimientoTestCase
{
    private const PATRON_LUGAR = '/direcci[oó]n|lugar|ubicaci[oó]n|Cra\.|Cali/iu';

    // ------------------------------------------------------------------
    // Reserva
    // ------------------------------------------------------------------

    public function test_al_reservar_se_envia_un_correo_con_fecha_y_hora(): void
    {
        $cliente = $this->crearCliente(['email' => 'ana@test.com']);
        $servicio = $this->crearServicio();

        $this->actingAs($cliente)
            ->post(route('citas.store'), $this->datosReserva($servicio, ['hora' => '10:00']))
            ->assertSessionHasNoErrors();

        $correos = $this->correosEnviados();

        $this->assertCount(1, $correos, 'Al reservar una cita no se envió ningún correo al cliente.');
        $this->assertSame('ana@test.com', $correos->first()->getTo()[0]->getAddress());
        $this->assertStringContainsString('21/10/2026', $correos->first()->getHtmlBody());
        $this->assertStringContainsString('10:00', $correos->first()->getHtmlBody());
    }

    public function test_el_aviso_de_reserva_indica_que_se_envio_un_correo(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->post(route('citas.store'), $this->datosReserva($servicio))
            ->assertSessionHas('success', fn ($mensaje) => stripos($mensaje, 'correo') !== false);
    }

    public function test_el_correo_de_reserva_incluye_el_lugar(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->post(route('citas.store'), $this->datosReserva($servicio));

        $this->assertMatchesRegularExpression(
            self::PATRON_LUGAR,
            (string) $this->correosEnviados()->first()?->getHtmlBody(),
            'El correo de reserva no indica el lugar de la cita.'
        );
    }

    // ------------------------------------------------------------------
    // Modificación
    // ------------------------------------------------------------------

    public function test_al_modificar_se_envia_un_correo_al_cliente(): void
    {
        $cliente = $this->crearCliente(['email' => 'ana@test.com']);
        $servicio = $this->crearServicio();
        $cita = $this->crearCita($cliente, [], [$servicio]);

        $this->actingAs($cliente)
            ->put(route('citas.update', $cita->id), $this->datosReserva($servicio, ['hora' => '12:00']))
            ->assertSessionHasNoErrors();

        $this->assertCount(1, $this->correosEnviados(), 'Al modificar la cita no se envió ningún correo.');
    }

    public function test_el_aviso_de_modificacion_indica_que_se_envio_un_correo(): void
    {
        $cliente = $this->crearCliente();
        $servicio = $this->crearServicio();
        $cita = $this->crearCita($cliente, [], [$servicio]);

        $this->actingAs($cliente)
            ->put(route('citas.update', $cita->id), $this->datosReserva($servicio, ['hora' => '12:00']));

        $mensaje = (string) session('success');

        $this->assertStringContainsStringIgnoringCase(
            'correo',
            $mensaje,
            "El aviso de modificación no menciona el correo. Se mostró: \"$mensaje\""
        );
    }

    // ------------------------------------------------------------------
    // Confirmación
    // ------------------------------------------------------------------

    public function test_al_confirmar_se_envia_un_correo_con_fecha_y_hora(): void
    {
        $cliente = $this->crearCliente(['email' => 'ana@test.com']);
        $cita = $this->crearCita($cliente, ['fecha' => self::MIERCOLES, 'hora' => '10:00:00']);

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'confirmada']);

        $correos = $this->correosEnviados();

        $this->assertCount(1, $correos, 'Al confirmar la cita no se envió el correo.');
        $this->assertSame('ana@test.com', $correos->first()->getTo()[0]->getAddress());
        $this->assertStringContainsString('21/10/2026', $correos->first()->getHtmlBody());
        $this->assertStringContainsString('10:00', $correos->first()->getHtmlBody());
    }

    public function test_el_correo_de_confirmacion_incluye_el_lugar(): void
    {
        $cita = $this->crearCita($this->crearCliente());

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.citas.estado', $cita->id), ['estado' => 'confirmada']);

        $this->assertMatchesRegularExpression(
            self::PATRON_LUGAR,
            (string) $this->correosEnviados()->first()?->getHtmlBody(),
            'El correo de confirmación no indica el lugar de la cita.'
        );
    }
}
