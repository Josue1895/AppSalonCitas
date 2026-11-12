<?php

namespace Tests\Feature\Requerimientos\Administrador;

use App\Models\Service;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-12 Servicios: menú Servicios. Se crean, editan o eliminan servicios y se
 * cambian los precios en Editar Servicio.
 */
#[Group('RF-12')]
class RF12ServiciosAdminTest extends RequerimientoTestCase
{
    public function test_el_administrador_ve_todos_los_servicios_incluso_inactivos(): void
    {
        $this->crearServicio(['nombre' => 'Servicio Activo']);
        $this->crearServicio(['nombre' => 'Servicio Inactivo', 'activo' => false]);

        $this->actingAs($this->crearAdmin())
            ->get(route('admin.servicios.index'))
            ->assertOk()
            ->assertSee('Servicio Activo')
            ->assertSee('Servicio Inactivo');
    }

    public function test_el_administrador_crea_un_servicio(): void
    {
        $this->actingAs($this->crearAdmin())
            ->post(route('admin.servicios.store'), [
                'nombre' => 'Depilación',
                'precio' => 30000,
                'descripcion' => 'Depilación con cera',
                'duracion' => 40,
            ])
            ->assertRedirect(route('admin.servicios.index'))
            ->assertSessionHas('success');

        $servicio = Service::where('nombre', 'Depilación')->first();

        $this->assertNotNull($servicio);
        $this->assertEquals(30000, (float) $servicio->precio);
        $this->assertSame(40, (int) $servicio->duracion);
        $this->assertTrue((bool) $servicio->activo);
    }

    public function test_crear_un_servicio_sin_datos_obligatorios_muestra_errores(): void
    {
        $this->actingAs($this->crearAdmin())
            ->from(route('admin.servicios.create'))
            ->post(route('admin.servicios.store'), [])
            ->assertSessionHasErrors(['nombre', 'precio', 'duracion']);

        $this->assertSame(0, Service::count());
    }

    public function test_editar_servicio_muestra_los_datos_precargados(): void
    {
        $servicio = $this->crearServicio(['nombre' => 'Masaje Relajante', 'precio' => 70000]);

        $this->actingAs($this->crearAdmin())
            ->get(route('admin.servicios.edit', $servicio->id))
            ->assertOk()
            ->assertSee('value="Masaje Relajante"', false)
            ->assertSee('70000');
    }

    public function test_el_administrador_cambia_el_precio_de_un_servicio(): void
    {
        $servicio = $this->crearServicio(['precio' => 25000]);

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.servicios.update', $servicio->id), [
                'nombre' => $servicio->nombre,
                'precio' => 32000,
                'descripcion' => $servicio->descripcion,
                'duracion' => $servicio->duracion,
            ])
            ->assertRedirect(route('admin.servicios.index'))
            ->assertSessionHas('success');

        $this->assertEquals(32000, (float) $servicio->fresh()->precio);
    }

    public function test_cambiar_el_precio_no_altera_el_total_de_citas_ya_reservadas(): void
    {
        $servicio = $this->crearServicio(['precio' => 25000]);
        $cita = $this->crearCita($this->crearCliente(), ['total' => 25000], [$servicio]);

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.servicios.update', $servicio->id), [
                'nombre' => $servicio->nombre,
                'precio' => 50000,
                'duracion' => $servicio->duracion,
            ]);

        $this->assertEquals(25000, (float) $cita->fresh()->total);
        $this->assertEquals(25000, (float) $cita->citasServicios()->first()->precio_unitario);
    }

    public function test_el_administrador_elimina_un_servicio(): void
    {
        $servicio = $this->crearServicio();

        $this->actingAs($this->crearAdmin())
            ->post(route('admin.servicios.destroy', $servicio->id))
            ->assertRedirect(route('admin.servicios.index'))
            ->assertSessionHas('success');

        $this->assertNull(Service::find($servicio->id));
    }

    public function test_eliminar_un_servicio_usado_en_citas_muestra_un_aviso_y_no_un_error_500(): void
    {
        $servicio = $this->crearServicio();
        $this->crearCita($this->crearCliente(), [], [$servicio]);

        $respuesta = $this->actingAs($this->crearAdmin())
            ->post(route('admin.servicios.destroy', $servicio->id));

        $this->assertNotSame(
            500,
            $respuesta->status(),
            'Eliminar un servicio que ya tiene citas provoca un error 500 en lugar de un aviso.'
        );
        $respuesta->assertRedirect(route('admin.servicios.index'));
    }

    public function test_un_cliente_no_puede_gestionar_servicios(): void
    {
        $servicio = $this->crearServicio(['precio' => 25000]);
        $cliente = $this->crearCliente();

        $this->actingAs($cliente)->get(route('admin.servicios.index'))->assertRedirect(route('home'));

        $this->actingAs($cliente)->post(route('admin.servicios.update', $servicio->id), [
            'nombre' => 'Hackeado',
            'precio' => 1,
            'duracion' => 1,
        ])->assertRedirect(route('home'));

        $this->assertEquals(25000, (float) $servicio->fresh()->precio);
    }
}
