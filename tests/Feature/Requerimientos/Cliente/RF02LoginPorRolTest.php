<?php

namespace Tests\Feature\Requerimientos\Cliente;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-02 Login por rol: datos incorrectos muestran aviso de error. Si son
 * correctos, el cliente entra a su inicio y el administrador a su panel,
 * cada uno con su propio menú.
 */
#[Group('RF-02')]
class RF02LoginPorRolTest extends RequerimientoTestCase
{
    public function test_datos_incorrectos_muestran_aviso_de_error(): void
    {
        $this->crearCliente(['email' => 'ana@test.com']);

        $respuesta = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'ana@test.com',
            'password' => 'incorrecta',
        ]);

        $respuesta->assertRedirect(route('login'));
        $respuesta->assertSessionHasErrors(['email' => 'Credenciales incorrectas']);
        $this->assertGuest();
    }

    public function test_un_correo_que_no_existe_muestra_aviso_de_error(): void
    {
        $respuesta = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'noexiste@test.com',
            'password' => self::PASSWORD,
        ]);

        $respuesta->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_el_cliente_entra_a_su_inicio_con_menu_de_cliente(): void
    {
        $cliente = $this->crearCliente(['email' => 'ana@test.com']);

        $respuesta = $this->post(route('login.store'), [
            'email' => 'ana@test.com',
            'password' => self::PASSWORD,
        ]);

        $respuesta->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($cliente);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Mis Citas')
            ->assertDontSee('Panel Admin');
    }

    public function test_el_administrador_entra_a_su_panel_con_menu_de_administrador(): void
    {
        $admin = $this->crearAdmin();

        $respuesta = $this->post(route('login.store'), [
            'email' => 'admin@test.com',
            'password' => self::PASSWORD,
        ]);

        $respuesta->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs($admin);

        $this->get(route('admin.index'))
            ->assertOk()
            ->assertSee('Administración')
            ->assertSee(route('admin.citas'), false)
            ->assertSee(route('admin.servicios.index'), false)
            ->assertSee(route('admin.horarios'), false)
            ->assertSee(route('admin.reportes'), false);
    }
}
