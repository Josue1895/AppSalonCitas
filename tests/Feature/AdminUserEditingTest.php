<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminUserEditingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('usuarios');
        Schema::create('usuarios', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 60);
            $table->string('apellido', 60);
            $table->string('email', 30)->unique();
            $table->string('password');
            $table->string('telefono', 10)->nullable();
            $table->boolean('admin')->default(false);
            $table->boolean('confirmado')->default(false);
            $table->string('token', 15)->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_can_open_an_edit_form_with_the_users_details_and_role(): void
    {
        $usuario = $this->crearUsuario([
            'nombre' => 'Camila',
            'apellido' => 'Rojas',
            'email' => 'camila@example.com',
        ]);

        $response = $this->actingAs(new User(['admin' => true]))
            ->get(route('admin.usuarios.edit', $usuario->id));

        $response->assertOk()
            ->assertSee('Editar usuario')
            ->assertSee('value="Camila"', false)
            ->assertSee('value="Rojas"', false)
            ->assertSee('value="camila@example.com"', false)
            ->assertSee('name="admin"', false)
            ->assertSee('Cliente');
    }

    public function test_admin_can_update_user_details_and_role_without_changing_blank_password(): void
    {
        $usuario = $this->crearUsuario([
            'nombre' => 'Camila',
            'apellido' => 'Rojas',
            'email' => 'camila@example.com',
        ]);
        $contrasenaOriginal = $usuario->password;

        $response = $this->actingAs(new User(['admin' => true]))
            ->put(route('admin.usuarios.update', $usuario->id), [
                'nombre' => 'Camila Maria',
                'apellido' => 'Rojas',
                'email' => 'camila@example.com',
                'telefono' => '3001234567',
                'password' => '',
                'password_confirmation' => '',
                'admin' => '1',
            ]);

        $response->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('success', 'Usuario actualizado correctamente.');

        $usuario->refresh();
        $this->assertSame('Camila Maria', $usuario->nombre);
        $this->assertSame('3001234567', $usuario->telefono);
        $this->assertTrue($usuario->admin);
        $this->assertSame($contrasenaOriginal, $usuario->password);
        $this->assertTrue(Hash::check('secret123', $usuario->password));
    }

    public function test_admin_cannot_remove_their_own_admin_role_from_edit_form(): void
    {
        $admin = $this->crearUsuario([
            'nombre' => 'Admin',
            'apellido' => 'Principal',
            'email' => 'admin@example.com',
            'admin' => true,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.usuarios.update', $admin->id), [
                'nombre' => 'Admin',
                'apellido' => 'Principal',
                'email' => 'admin@example.com',
                'telefono' => '',
                'password' => '',
                'password_confirmation' => '',
                'admin' => '0',
            ]);

        $response->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('error', 'No puedes quitarte a ti mismo el rol de administrador.');
        $this->assertTrue($admin->fresh()->admin);
    }

    private function crearUsuario(array $datos): User
    {
        return User::create(array_merge([
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'email' => 'cliente@example.com',
            'password' => 'secret123',
            'telefono' => null,
            'admin' => false,
            'confirmado' => true,
            'token' => null,
        ], $datos));
    }
}
