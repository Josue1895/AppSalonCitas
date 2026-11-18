<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AppointmentCustomerAuthorizationTest extends TestCase
{
    public function test_admin_is_rejected_before_booking_validation(): void
    {
        $this->actingAs(new User(['admin' => true]));

        $response = $this->post(route('citas.store'), []);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas(
            'error',
            'Solo los clientes pueden reservar citas.'
        );
        $response->assertSessionHasNoErrors();
    }

    public function test_customer_reaches_booking_validation(): void
    {
        $this->actingAs(new User(['admin' => false]));

        $response = $this->post(route('citas.store'), []);

        $response->assertSessionHasErrors(['fecha', 'hora', 'servicios']);
    }
}
