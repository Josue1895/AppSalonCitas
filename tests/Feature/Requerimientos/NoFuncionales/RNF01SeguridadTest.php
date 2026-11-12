<?php

namespace Tests\Feature\Requerimientos\NoFuncionales;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RNF-01 Seguridad: cada rol tiene su menú; el administrador no ve opciones
 * de cliente y el cliente no ve el panel. Contraseñas cifradas y rutas
 * protegidas.
 */
#[Group('RNF-01')]
class RNF01SeguridadTest extends RequerimientoTestCase
{
    public static function rutasAdmin(): array
    {
        return [
            'panel' => ['admin.index'],
            'citas' => ['admin.citas'],
            'servicios' => ['admin.servicios.index'],
            'crear servicio' => ['admin.servicios.create'],
            'horarios' => ['admin.horarios'],
            'reportes' => ['admin.reportes'],
        ];
    }

    #[DataProvider('rutasAdmin')]
    public function test_un_invitado_no_entra_al_panel_admin(string $ruta): void
    {
        $this->get(route($ruta))->assertRedirect(route('login'));
    }

    #[DataProvider('rutasAdmin')]
    public function test_un_cliente_no_entra_al_panel_admin(string $ruta): void
    {
        $this->actingAs($this->crearCliente())
            ->get(route($ruta))
            ->assertRedirect(route('home'));
    }

    public function test_el_cliente_no_ve_el_enlace_al_panel(): void
    {
        $this->actingAs($this->crearCliente())
            ->get(route('home'))
            ->assertDontSee('Panel Admin')
            ->assertDontSee(route('admin.index') . '"', false);
    }

    public function test_el_administrador_no_ve_las_opciones_de_cliente(): void
    {
        $this->actingAs($this->crearAdmin())
            ->get(route('home'))
            ->assertDontSee(route('citas.mis'), false)
            ->assertDontSee(route('citas.index'), false);
    }

    public function test_la_contrasena_se_guarda_cifrada(): void
    {
        $this->post(route('registro.store'), [
            'nombre' => 'Laura',
            'apellido' => 'Martínez',
            'email' => 'laura@test.com',
            'password' => 'clave123',
            'password_confirmation' => 'clave123',
        ]);

        $guardada = User::where('email', 'laura@test.com')->value('password');

        $this->assertNotSame('clave123', $guardada);
        $this->assertStringStartsWith('$2y$', $guardada, 'La contraseña no está cifrada con bcrypt.');
    }

    public function test_cerrar_sesion_desconecta_al_usuario(): void
    {
        $this->actingAs($this->crearCliente())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
