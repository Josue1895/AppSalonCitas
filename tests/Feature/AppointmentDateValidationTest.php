<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentDateValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-20 19:00:00', 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_rejects_a_date_before_today(): void
    {
        $this->assertSame('America/Bogota', now()->timezoneName);

        $response = $this->post(route('citas.store'), [
            'fecha' => '2026-10-19',
            'hora' => '15:00',
            'servicios' => [1],
        ]);

        $response->assertSessionHasErrors('fecha');
    }

    public function test_it_rejects_a_time_before_now_on_the_current_date(): void
    {
        $response = $this->post(route('citas.store'), [
            'fecha' => '2026-10-20',
            'hora' => '13:59',
            'servicios' => [1],
        ]);

        $response->assertSessionHasErrors('hora');
    }

    public function test_it_allows_the_current_time_and_future_dates_to_pass_date_validation(): void
    {
        $todayResponse = $this->post(route('citas.store'), [
            'fecha' => '2026-10-20',
            'hora' => '14:00',
            'servicios' => [1],
        ]);

        $todayResponse->assertRedirect(route('login'));
        $todayResponse->assertSessionHasNoErrors();

        $futureDateResponse = $this->post(route('citas.store'), [
            'fecha' => '2026-10-21',
            'hora' => '09:00',
            'servicios' => [1],
        ]);

        $futureDateResponse->assertRedirect(route('login'));
        $futureDateResponse->assertSessionHasNoErrors();
    }
}
