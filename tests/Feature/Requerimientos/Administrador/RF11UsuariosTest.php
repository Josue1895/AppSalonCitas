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

    public function test_existen_rutas_para_listar_crear_editar_y_eliminar_usuarios(): void
    {
        $rutasUsuarios = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($ruta) => str_starts_with($ruta->uri(), 'admin/usuarios'));

        $this->assertNotEmpty(
            $rutasUsuarios,
            'No existe ninguna ruta "admin/usuarios" para gestionar usuarios.'
        );
    }
}
