<?php

namespace Tests\Feature\Requerimientos\Cliente;

use App\Models\Appointment;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-08 Modificar: botón Modificar en Mis Citas. Abre Modificar Cita con los
 * datos precargados, y al guardar sale el aviso de cita modificada.
 */
#[Group('RF-08')]
class RF08ModificarCitaTest extends RequerimientoTestCase
{
    public function test_mis_citas_tiene_un_boton_para_modificar_la_cita(): void
    {
        $cliente = $this->crearCliente();
        $cita = $this->crearCita($cliente);

        $this->actingAs($cliente)
            ->get(route('citas.mis'))
            ->assertSee(route('citas.edit', $cita->id), false);
    }

    public function test_el_boton_se_llama_modificar_como_en_el_diseno(): void
    {
        $cliente = $this->crearCliente();
        $this->crearCita($cliente);

        $html = $this->actingAs($cliente)->get(route('citas.mis'))->getContent();

        $this->assertMatchesRegularExpression(
            '/>\s*Modificar\s*</u',
            $html,
            'El botón dice "Editar"; el requerimiento y el diseño lo llaman "Modificar".'
        );
    }

    public function test_la_pantalla_de_modificar_muestra_los_datos_precargados(): void
    {
        $cliente = $this->crearCliente();
        $servicio = $this->crearServicio(['nombre' => 'Pedicure']);
        $cita = $this->crearCita($cliente, [
            'fecha' => '2026-10-22',
            'hora' => '15:00:00',
            'hora_fin' => '16:00:00',
        ], [$servicio]);

        $this->actingAs($cliente)
            ->get(route('citas.edit', $cita->id))
            ->assertOk()
            ->assertSee('value="2026-10-22"', false)
            ->assertSee('value="15:00"', false)
            ->assertSee('Pedicure');
    }

    public function test_al_guardar_se_actualizan_la_fecha_la_hora_y_el_total(): void
    {
        $cliente = $this->crearCliente();
        $corte = $this->crearServicio(['precio' => 25000]);
        $tinte = $this->crearServicio(['nombre' => 'Tinte', 'precio' => 60000, 'duracion' => 30]);
        $cita = $this->crearCita($cliente, ['total' => 25000], [$corte]);

        $this->actingAs($cliente)
            ->put(route('citas.update', $cita->id), [
                'fecha' => '2026-10-22',
                'hora' => '14:00',
                'servicios' => [$corte->id, $tinte->id],
            ])
            ->assertRedirect(route('citas.mis'))
            ->assertSessionHasNoErrors();

        $cita->refresh();

        $this->assertSame('2026-10-22', $cita->fecha->format('Y-m-d'));
        $this->assertSame('14:00:00', $cita->hora);
        $this->assertSame('15:30:00', $cita->hora_fin);
        $this->assertEquals(85000, (float) $cita->total);
        $this->assertCount(2, $cita->citasServicios);
        $this->assertSame(1, Appointment::count(), 'Se creó una cita nueva en vez de modificar la existente.');
    }

    public function test_al_guardar_sale_el_aviso_de_cita_modificada(): void
    {
        $cliente = $this->crearCliente();
        $servicio = $this->crearServicio();
        $cita = $this->crearCita($cliente, [], [$servicio]);

        $this->actingAs($cliente)
            ->put(route('citas.update', $cita->id), $this->datosReserva($servicio, ['hora' => '12:00']))
            ->assertSessionHas('success', fn ($mensaje) => preg_match('/modificad|actualizad/i', $mensaje) === 1);
    }

    public function test_mover_la_cita_unos_minutos_no_choca_consigo_misma(): void
    {
        $cliente = $this->crearCliente();
        $servicio = $this->crearServicio();
        $cita = $this->crearCita($cliente, ['hora' => '10:00:00', 'hora_fin' => '11:00:00'], [$servicio]);

        $this->actingAs($cliente)
            ->put(route('citas.update', $cita->id), $this->datosReserva($servicio, ['hora' => '10:30']))
            ->assertSessionHasNoErrors();

        $this->assertSame('10:30:00', $cita->fresh()->hora);
    }

    public function test_no_se_puede_mover_a_una_hora_ocupada_por_otro_cliente(): void
    {
        $servicio = $this->crearServicio();
        $this->crearCita($this->crearCliente(), ['hora' => '15:00:00', 'hora_fin' => '16:00:00']);

        $cliente = $this->crearCliente();
        $cita = $this->crearCita($cliente, [], [$servicio]);

        $this->actingAs($cliente)
            ->from(route('citas.edit', $cita->id))
            ->put(route('citas.update', $cita->id), $this->datosReserva($servicio, ['hora' => '15:00']))
            ->assertSessionHasErrors('hora');

        $this->assertSame('10:00:00', $cita->fresh()->hora);
    }

    public function test_un_cliente_no_puede_modificar_la_cita_de_otro(): void
    {
        $servicio = $this->crearServicio();
        $cita = $this->crearCita($this->crearCliente(), [], [$servicio]);
        $intruso = $this->crearCliente();

        $this->actingAs($intruso)->get(route('citas.edit', $cita->id))->assertNotFound();

        $this->actingAs($intruso)
            ->put(route('citas.update', $cita->id), $this->datosReserva($servicio, ['hora' => '12:00']))
            ->assertSessionHas('error');

        $this->assertSame('10:00:00', $cita->fresh()->hora);
    }

    public function test_una_cita_cancelada_no_se_puede_modificar(): void
    {
        $cliente = $this->crearCliente();
        $servicio = $this->crearServicio();
        $cita = $this->crearCita($cliente, ['estado' => 'cancelada'], [$servicio]);

        $this->actingAs($cliente)
            ->put(route('citas.update', $cita->id), $this->datosReserva($servicio, ['hora' => '12:00']))
            ->assertSessionHas('error');

        $this->assertSame('10:00:00', $cita->fresh()->hora);
    }
}
