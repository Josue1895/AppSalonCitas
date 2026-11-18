<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppointmentStoreAuthenticationTest extends TestCase
{
    public function test_guest_is_redirected_to_login_before_booking_validation(): void
    {
        $response = $this->post(route('citas.store'), []);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasNoErrors();
    }
}
