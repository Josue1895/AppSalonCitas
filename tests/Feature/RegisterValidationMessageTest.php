<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegisterValidationMessageTest extends TestCase
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

    public function test_duplicate_email_shows_spanish_validation_message(): void
    {
        User::create([
            'nombre' => 'Cliente',
            'apellido' => 'Existente',
            'email' => 'duplicado@example.com',
            'password' => 'secret123',
            'telefono' => '3001234567',
            'admin' => 0,
            'confirmado' => 1,
            'token' => null,
        ]);

        $response = $this->from(route('registro'))->post(route('registro.store'), [
            'nombre' => 'Nuevo',
            'apellido' => 'Usuario',
            'email' => 'duplicado@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'telefono' => '3007654321',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHas('errors', function ($errors) {
            return $errors->first('email') === 'Este correo ya está registrado.';
        });
    }
}
