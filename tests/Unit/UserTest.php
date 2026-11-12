<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Pruebas unitarias del modelo User.
 *
 * Los usuarios se crean con "new User(...)" y nunca se guardan: se prueba
 * solo cómo se comporta el modelo, sin la base de datos.
 */
class UserTest extends TestCase
{
    public function test_la_contrasena_se_cifra_al_asignarla(): void
    {
        // Preparar y actuar
        $usuario = new User(['password' => 'clave123']);

        // Comprobar
        $this->assertNotSame('clave123', $usuario->password);
        $this->assertTrue(Hash::check('clave123', $usuario->password));
    }

    public function test_la_contrasena_no_se_muestra_al_convertir_el_usuario_en_datos(): void
    {
        $usuario = new User(['nombre' => 'Laura', 'password' => 'clave123']);

        $this->assertArrayNotHasKey('password', $usuario->toArray());
    }

    public function test_el_campo_admin_se_convierte_en_verdadero_o_falso(): void
    {
        $this->assertTrue((new User(['admin' => 1]))->admin);
        $this->assertFalse((new User(['admin' => 0]))->admin);
    }

    public function test_usa_la_tabla_usuarios(): void
    {
        $this->assertSame('usuarios', (new User())->getTable());
    }

    public function test_sus_citas_se_relacionan_por_el_campo_usuarioId(): void
    {
        $relacion = (new User())->citas();

        $this->assertSame('usuarioId', $relacion->getForeignKeyName());
    }
}
