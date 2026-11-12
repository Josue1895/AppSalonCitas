<?php

namespace Tests\Feature\Requerimientos\Cliente;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-01 Registro: pantalla Registro con nombre, apellido, correo, teléfono y
 * contraseña. Si el correo ya existe, se ve el aviso de error.
 */
#[Group('RF-01')]
class RF01RegistroTest extends RequerimientoTestCase
{
    private function datosRegistro(array $datos = []): array
    {
        return array_merge([
            'nombre' => 'Laura',
            'apellido' => 'Martínez',
            'email' => 'laura@test.com',
            'telefono' => '3109876543',
            'password' => 'clave123',
            'password_confirmation' => 'clave123',
        ], $datos);
    }

    public function test_la_pantalla_de_registro_tiene_todos_los_campos(): void
    {
        $respuesta = $this->get(route('registro'));

        $respuesta->assertOk();

        foreach (['nombre', 'apellido', 'email', 'telefono', 'password'] as $campo) {
            $respuesta->assertSee('name="' . $campo . '"', false);
        }
    }

    public function test_un_registro_valido_crea_un_cliente_y_lo_envia_al_login(): void
    {
        $respuesta = $this->post(route('registro.store'), $this->datosRegistro());

        $respuesta->assertRedirect(route('login'));
        $respuesta->assertSessionHas('success');

        $usuario = User::where('email', 'laura@test.com')->first();

        $this->assertNotNull($usuario, 'El usuario no se guardó en la base de datos.');
        $this->assertSame('Laura', $usuario->nombre);
        $this->assertSame('3109876543', $usuario->telefono);
        $this->assertFalse($usuario->admin, 'Un usuario registrado no debe ser administrador.');
        $this->assertTrue(Hash::check('clave123', $usuario->password));
    }

    public function test_un_correo_ya_registrado_muestra_error(): void
    {
        $this->crearCliente(['email' => 'laura@test.com']);

        $respuesta = $this->from(route('registro'))
            ->post(route('registro.store'), $this->datosRegistro());

        $respuesta->assertRedirect(route('registro'));
        $respuesta->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'laura@test.com')->count());
    }

    public function test_el_aviso_de_correo_duplicado_esta_en_espanol(): void
    {
        $this->crearCliente(['email' => 'laura@test.com']);

        $this->from(route('registro'))
            ->post(route('registro.store'), $this->datosRegistro());

        $mensaje = session('errors')->first('email');

        $this->assertStringContainsStringIgnoringCase(
            'correo',
            $mensaje,
            "El aviso de correo duplicado no está en español. Se mostró: \"$mensaje\""
        );
    }

    public function test_los_campos_obligatorios_vacios_muestran_error(): void
    {
        $respuesta = $this->from(route('registro'))
            ->post(route('registro.store'), []);

        $respuesta->assertSessionHasErrors(['nombre', 'apellido', 'email', 'password']);
        $this->assertSame(0, User::count());
    }

    public function test_las_contrasenas_que_no_coinciden_muestran_error(): void
    {
        $respuesta = $this->from(route('registro'))
            ->post(route('registro.store'), $this->datosRegistro([
                'password_confirmation' => 'otraClave',
            ]));

        $respuesta->assertSessionHasErrors('password');
        $this->assertSame(0, User::count());
    }
}
