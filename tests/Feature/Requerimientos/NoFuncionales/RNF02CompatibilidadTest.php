<?php

namespace Tests\Feature\Requerimientos\NoFuncionales;

use App\Models\Appointment;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RNF-02 Compatibilidad: las validaciones están representadas con los avisos
 * de horario ocupado y de campos vacíos. La versión móvil es la siguiente
 * iteración del diseño.
 *
 * Se comprueba que los avisos salen del servidor (funcionan aunque el
 * navegador no ejecute JavaScript) y que las pantallas están preparadas para
 * adaptarse a pantallas pequeñas (meta viewport + Bootstrap).
 */
#[Group('RNF-02')]
class RNF02CompatibilidadTest extends RequerimientoTestCase
{
    public function test_el_aviso_de_campos_vacios_se_ve_en_la_pantalla_sin_javascript(): void
    {
        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->followingRedirects()
            ->post(route('citas.store'), [])
            ->assertOk()
            ->assertSee('La fecha de la cita es obligatoria.')
            ->assertSee('La hora de la cita es obligatoria.')
            ->assertSee('Debes seleccionar al menos un servicio.');

        $this->assertSame(0, Appointment::count());
    }

    public function test_el_aviso_de_horario_ocupado_se_ve_en_la_pantalla_sin_javascript(): void
    {
        $this->crearCita($this->crearCliente());
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearCliente())
            ->from(route('citas.index'))
            ->followingRedirects()
            ->post(route('citas.store'), $this->datosReserva($servicio, ['hora' => '10:00']))
            ->assertOk()
            ->assertSee('Ese horario ya está ocupado');

        $this->assertSame(1, Appointment::count());
    }

    public function test_la_validacion_tambien_aplica_al_modificar_una_cita(): void
    {
        $servicio = $this->crearServicio();
        $cliente = $this->crearCliente();
        $cita = $this->crearCita($cliente, [], [$servicio]);

        $this->actingAs($cliente)
            ->from(route('citas.edit', $cita->id))
            ->put(route('citas.update', $cita->id), [])
            ->assertSessionHasErrors(['fecha', 'hora', 'servicios']);
    }

    public static function pantallasPublicas(): array
    {
        return [
            'inicio' => ['home'],
            'login' => ['login'],
            'registro' => ['registro'],
            'servicios' => ['servicios.index'],
        ];
    }

    #[DataProvider('pantallasPublicas')]
    public function test_las_pantallas_publicas_estan_preparadas_para_movil(string $ruta): void
    {
        $this->get(route($ruta))
            ->assertOk()
            ->assertSee('name="viewport"', false)
            ->assertSee('width=device-width', false)
            ->assertSee('bootstrap', false);
    }

    public function test_las_pantallas_del_cliente_estan_preparadas_para_movil(): void
    {
        $cliente = $this->crearCliente();

        foreach (['citas.index', 'citas.mis'] as $ruta) {
            $this->actingAs($cliente)
                ->get(route($ruta))
                ->assertOk()
                ->assertSee('width=device-width', false);
        }
    }

    public function test_las_pantallas_del_administrador_estan_preparadas_para_movil(): void
    {
        $admin = $this->crearAdmin();

        foreach (['admin.index', 'admin.citas', 'admin.servicios.index', 'admin.horarios', 'admin.reportes', 'admin.usuarios'] as $ruta) {
            $this->actingAs($admin)
                ->get(route($ruta))
                ->assertOk()
                ->assertSee('width=device-width', false);
        }
    }
}
