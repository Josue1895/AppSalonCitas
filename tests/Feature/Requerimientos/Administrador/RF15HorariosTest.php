<?php

namespace Tests\Feature\Requerimientos\Administrador;

use App\Models\Appointment;
use App\Models\Horario;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-15 Horario: menú Horarios. Cada día tiene un interruptor Abierto/Cerrado
 * y horas de apertura y cierre. Los clientes solo pueden reservar en los días
 * habilitados.
 */
#[Group('RF-15')]
class RF15HorariosTest extends RequerimientoTestCase
{
    /**
     * Semana completa abierta de 08:00 a 18:00 con cambios puntuales.
     */
    private function semana(array $cambios = []): array
    {
        $horarios = [];

        foreach (Horario::DIAS as $dia => $nombre) {
            $horarios[$dia] = $cambios[$dia] ?? [
                'activo' => 1,
                'hora_inicio' => '08:00',
                'hora_fin' => '18:00',
            ];
        }

        return ['horarios' => $horarios];
    }

    public function test_la_pantalla_muestra_los_siete_dias_con_interruptor_y_horas(): void
    {
        $respuesta = $this->actingAs($this->crearAdmin())
            ->get(route('admin.horarios'))
            ->assertOk();

        foreach (Horario::DIAS as $dia => $nombre) {
            $respuesta->assertSee($nombre);
            $respuesta->assertSee("horarios[$dia][activo]", false);
            $respuesta->assertSee("horarios[$dia][hora_inicio]", false);
            $respuesta->assertSee("horarios[$dia][hora_fin]", false);
        }

        $respuesta->assertSee('Abierto')->assertSee('Cerrado');
    }

    public function test_el_administrador_cierra_un_dia_y_el_cliente_ya_no_puede_reservar(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.horarios.actualizar'), $this->semana([
                3 => ['activo' => 0, 'hora_inicio' => null, 'hora_fin' => null],
            ]))
            ->assertRedirect(route('admin.horarios'))
            ->assertSessionHas('success');

        $this->assertFalse(Horario::where('dia_semana', 3)->first()->activo);

        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->post(route('citas.store'), $this->datosReserva($servicio))
            ->assertSessionHasErrors('fecha');

        $this->assertSame(0, Appointment::count());
    }

    public function test_el_administrador_abre_un_dia_cerrado_y_el_cliente_puede_reservar(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.horarios.actualizar'), $this->semana([
                7 => ['activo' => 1, 'hora_inicio' => '10:00', 'hora_fin' => '14:00'],
            ]));

        $this->actingAs($this->crearCliente())
            ->post(route('citas.store'), $this->datosReserva($servicio, [
                'fecha' => self::DOMINGO,
                'hora' => '11:00',
            ]))
            ->assertRedirect(route('citas.mis'))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Appointment::count());
    }

    public function test_las_nuevas_horas_de_apertura_y_cierre_se_respetan(): void
    {
        $servicio = $this->crearServicio(['duracion' => 30]);

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.horarios.actualizar'), $this->semana([
                3 => ['activo' => 1, 'hora_inicio' => '12:00', 'hora_fin' => '16:00'],
            ]));

        $cliente = $this->crearCliente();

        $this->actingAs($cliente)
            ->post(route('citas.store'), $this->datosReserva($servicio, ['hora' => '10:00']))
            ->assertSessionHasErrors('hora');

        $this->actingAs($cliente)
            ->post(route('citas.store'), $this->datosReserva($servicio, ['hora' => '12:00']))
            ->assertRedirect(route('citas.mis'))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Appointment::count());
    }

    public function test_un_dia_abierto_con_cierre_antes_de_la_apertura_es_rechazado(): void
    {
        $this->actingAs($this->crearAdmin())
            ->from(route('admin.horarios'))
            ->post(route('admin.horarios.actualizar'), $this->semana([
                1 => ['activo' => 1, 'hora_inicio' => '18:00', 'hora_fin' => '08:00'],
            ]))
            ->assertSessionHasErrors('horarios.1.hora_fin');

        $this->assertSame('08:00:00', Horario::where('dia_semana', 1)->first()->hora_inicio);
    }

    public function test_un_cliente_no_puede_cambiar_los_horarios(): void
    {
        $this->actingAs($this->crearCliente())
            ->post(route('admin.horarios.actualizar'), $this->semana([
                1 => ['activo' => 0, 'hora_inicio' => null, 'hora_fin' => null],
            ]))
            ->assertRedirect(route('home'));

        $this->assertTrue(Horario::where('dia_semana', 1)->first()->activo);
    }
}
