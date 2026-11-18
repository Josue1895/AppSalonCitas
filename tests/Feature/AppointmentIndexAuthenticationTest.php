<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppointmentIndexAuthenticationTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_opening_booking_page(): void
    {
        $response = $this->get(route('citas.index'));

        $response->assertRedirect(route('login'));
    }
}
