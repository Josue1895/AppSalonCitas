<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthLoginRedirectTest extends TestCase
{
    public function test_customer_login_redirects_to_home(): void
    {
        Auth::shouldReceive('attempt')->once()->andReturnTrue();
        Auth::shouldReceive('user')->once()->andReturn((object) ['admin' => false]);

        $response = $this->post(route('login.store'), [
            'email' => 'cliente@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        Auth::shouldReceive('attempt')->once()->andReturnTrue();
        Auth::shouldReceive('user')->once()->andReturn((object) ['admin' => true]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.index'));
    }
}
