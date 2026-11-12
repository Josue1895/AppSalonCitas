<?php

namespace Tests\Unit;

use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Pruebas unitarias de las reglas del formulario de registro.
 *
 * Cada prueba revisa una sola regla. La regla "correo único" no se prueba
 * aquí porque necesita la base de datos; esa parte la cubre RF01RegistroTest.
 */
class RegisterRequestTest extends TestCase
{
    private function reglas(): array
    {
        return (new RegisterRequest())->rules();
    }

    /**
     * Valida los datos usando solo la regla del campo indicado.
     */
    private function esValido(string $campo, array $datos): bool
    {
        $validador = Validator::make($datos, [$campo => $this->reglas()[$campo]]);

        return !$validador->fails();
    }

    public function test_el_nombre_es_obligatorio_y_tiene_maximo_60_caracteres(): void
    {
        $this->assertTrue($this->esValido('nombre', ['nombre' => 'Laura']));
        $this->assertFalse($this->esValido('nombre', ['nombre' => '']));
        $this->assertFalse($this->esValido('nombre', ['nombre' => str_repeat('a', 61)]));
    }

    public function test_el_apellido_es_obligatorio(): void
    {
        $this->assertTrue($this->esValido('apellido', ['apellido' => 'Martínez']));
        $this->assertFalse($this->esValido('apellido', ['apellido' => '']));
    }

    public function test_el_correo_debe_ser_valido_y_unico(): void
    {
        $regla = $this->reglas()['email'];

        $this->assertStringContainsString('required', $regla);
        $this->assertStringContainsString('email', $regla);
        $this->assertStringContainsString('unique:usuarios,email', $regla);
    }

    public function test_la_contrasena_debe_tener_minimo_6_caracteres(): void
    {
        $this->assertFalse($this->esValido('password', [
            'password' => '12345',
            'password_confirmation' => '12345',
        ]));

        $this->assertTrue($this->esValido('password', [
            'password' => '123456',
            'password_confirmation' => '123456',
        ]));
    }

    public function test_la_contrasena_debe_coincidir_con_su_confirmacion(): void
    {
        $this->assertFalse($this->esValido('password', [
            'password' => 'clave123',
            'password_confirmation' => 'otraClave',
        ]));
    }

    public function test_el_telefono_es_opcional_y_tiene_maximo_10_caracteres(): void
    {
        $this->assertTrue($this->esValido('telefono', ['telefono' => null]));
        $this->assertTrue($this->esValido('telefono', ['telefono' => '3001234567']));
        $this->assertFalse($this->esValido('telefono', ['telefono' => '30012345678']));
    }
}
