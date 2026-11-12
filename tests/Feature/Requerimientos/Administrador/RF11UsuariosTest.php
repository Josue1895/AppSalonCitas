<?php

namespace Tests\Feature\Requerimientos\Administrador;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-11 Usuarios: menú Usuarios con la lista y las opciones Crear, Editar y
 * Eliminar. Editar abre el formulario del usuario con su rol.
 */
#[Group('RF-11')]
class RF11UsuariosTest extends RequerimientoTestCase
{
    /**
     * Rutas del panel de usuarios cuyo método y dirección coinciden.
     */
    private function rutasUsuarios(string $metodo, string $patronUri): \Illuminate\Support\Collection
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($ruta) => str_starts_with($ruta->uri(), 'admin/usuarios')
                && in_array($metodo, $ruta->methods(), true)
                && preg_match($patronUri, $ruta->uri()));
    }

    // ------------------------------------------------------------------
    // Lo que existe
    // ------------------------------------------------------------------

    public function test_el_menu_del_administrador_tiene_la_opcion_usuarios(): void
    {
        $html = $this->actingAs($this->crearAdmin())
            ->get(route('admin.index'))
            ->assertOk()
            ->getContent();

        // Se busca un enlace del menú, no la tarjeta "Usuarios registrados".
        $this->assertMatchesRegularExpression(
            '/<a[^>]*class="nav-link[^"]*"[^>]*>\s*Usuarios\s*<\/a>/u',
            $html,
            'El menú del administrador no tiene la opción "Usuarios".'
        );
    }

    public function test_la_lista_muestra_los_usuarios_con_su_rol(): void
    {
        $this->crearCliente(['nombre' => 'Ana', 'apellido' => 'Gómez', 'email' => 'ana@test.com']);

        $this->actingAs($this->crearAdmin())
            ->get(route('admin.usuarios'))
            ->assertOk()
            ->assertSee('Ana')
            ->assertSee('ana@test.com')
            ->assertSee('Cliente')
            ->assertSee('Admin');
    }

    public function test_la_busqueda_filtra_la_lista(): void
    {
        $this->crearCliente(['nombre' => 'Ana', 'email' => 'ana@test.com']);
        $this->crearCliente(['nombre' => 'Luis', 'email' => 'luis@test.com']);

        $this->actingAs($this->crearAdmin())
            ->get(route('admin.usuarios', ['buscar' => 'ana']))
            ->assertSee('ana@test.com')
            ->assertDontSee('luis@test.com');
    }

    public function test_el_administrador_cambia_el_rol_de_un_usuario(): void
    {
        $cliente = $this->crearCliente();

        $this->actingAs($this->crearAdmin())
            ->put(route('admin.usuarios.rol', $cliente->id), ['admin' => 1])
            ->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('success');

        $this->assertTrue($cliente->fresh()->admin);
    }

    public function test_el_administrador_no_puede_quitarse_su_propio_rol(): void
    {
        $admin = $this->crearAdmin();

        $this->actingAs($admin)
            ->put(route('admin.usuarios.rol', $admin->id), ['admin' => 0])
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->admin);
    }

    public function test_un_cliente_no_puede_cambiar_roles(): void
    {
        $cliente = $this->crearCliente();
        $otro = $this->crearCliente();

        $this->actingAs($cliente)
            ->put(route('admin.usuarios.rol', $otro->id), ['admin' => 1])
            ->assertRedirect(route('home'));

        $this->assertFalse($otro->fresh()->admin);
    }

    // ------------------------------------------------------------------
    // Lo que pide el requerimiento y falta
    // ------------------------------------------------------------------

    public function test_existe_la_opcion_crear_usuario(): void
    {
        $this->assertNotEmpty(
            $this->rutasUsuarios('POST', '#^admin/usuarios$#'),
            'No hay forma de crear un usuario desde el panel (falta la opción "Crear").'
        );
    }

    public function test_existe_el_formulario_editar_usuario_con_su_rol(): void
    {
        $this->assertNotEmpty(
            $this->rutasUsuarios('GET', '#editar|edit#'),
            'No hay formulario para editar los datos del usuario; solo un botón para cambiar el rol.'
        );
    }

    public function test_existe_la_opcion_eliminar_usuario(): void
    {
        $this->assertNotEmpty(
            $this->rutasUsuarios('DELETE', '#\{#')->merge($this->rutasUsuarios('POST', '#eliminar#')),
            'No hay forma de eliminar un usuario desde el panel (falta la opción "Eliminar").'
        );
    }
}
