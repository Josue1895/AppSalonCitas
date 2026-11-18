<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserCreationTest extends TestCase
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

    public function test_admin_can_open_the_create_user_form(): void
    {
        $admin = new User(['admin' => true]);

        $response = $this->actingAs($admin)
            ->get(route('admin.usuarios.create'));

        $response->assertOk()
            ->assertSee('Crear usuario')
            ->assertSee(route('admin.usuarios.store'));
    }

    public function test_admin_can_create_a_user_with_a_hashed_password(): void
    {
        $admin = new User(['admin' => true]);

        $response = $this->actingAs($admin)
            ->post(route('admin.usuarios.store'), [
                'nombre' => 'Ana',
                'apellido' => 'Gomez',
                'email' => 'ana@example.com',
                'telefono' => '3001234567',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'admin' => '0',
            ]);

        $response->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('success', 'Usuario creado correctamente.');

        $this->assertDatabaseHas('usuarios', [
            'nombre' => 'Ana',
            'apellido' => 'Gomez',
            'email' => 'ana@example.com',
            'admin' => false,
            'confirmado' => true,
            'token' => null,
        ]);

        $usuario = User::where('email', 'ana@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $usuario->password));
    }
}
