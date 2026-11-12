<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    public function test_non_admin_cannot_access_user_management(): void
    {
        $usuario = new User(['admin' => false]);
        $usuario->id = 2;

        $response = $this->actingAs($usuario)
            ->get(route('admin.usuarios'));

        $response->assertRedirect(route('home'));
    }

    public function test_non_admin_cannot_change_a_user_role(): void
    {
        $usuario = new User(['admin' => false]);
        $usuario->id = 2;

        $response = $this->actingAs($usuario)
            ->put(route('admin.usuarios.rol', 1), ['admin' => 1]);

        $response->assertRedirect(route('home'));
    }
}
