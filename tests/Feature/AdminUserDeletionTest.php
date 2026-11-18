<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminUserDeletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('citas');
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

        Schema::create('citas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('usuarioId')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();
        });
    }

    public function test_admin_user_list_offers_a_delete_action(): void
    {
        $usuario = $this->crearUsuario();

        $response = $this->actingAs(new User(['admin' => true]))
            ->get(route('admin.usuarios'));

        $response->assertOk()
            ->assertSee('Eliminar')
            ->assertSee(route('admin.usuarios.destroy', $usuario->id));
    }

    public function test_admin_can_delete_a_user_and_preserve_unlinked_appointments(): void
    {
        $usuario = $this->crearUsuario();
        DB::table('citas')->insert(['usuarioId' => $usuario->id]);

        $response = $this->actingAs(new User(['admin' => true]))
            ->delete(route('admin.usuarios.destroy', $usuario->id));

        $response->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('success', 'Usuario eliminado correctamente.');

        $this->assertDatabaseMissing('usuarios', ['id' => $usuario->id]);
        $this->assertDatabaseHas('citas', [
            'usuarioId' => null,
        ]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = $this->crearUsuario([
            'nombre' => 'Admin',
            'apellido' => 'Principal',
            'email' => 'admin@example.com',
            'admin' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.usuarios.destroy', $admin->id));

        $response->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('error', 'No puedes eliminar tu propio usuario.');
        $this->assertDatabaseHas('usuarios', [
            'id' => $admin->id,
            'admin' => true,
        ]);
    }

    private function crearUsuario(array $datos = []): User
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
