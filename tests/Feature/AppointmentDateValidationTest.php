<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AppointmentDateValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-20 19:00:00', 'UTC'));

        $this->crearTablasNecesarias();
        $this->sembrarDatos();
    }

    /**
     * Las migraciones del proyecto usan SQL exclusivo de MySQL
     * (UPDATE ... INNER JOIN, ALTER TABLE ... MODIFY), así que no se pueden
     * ejecutar sobre SQLite en memoria. Creamos solo las tablas que consulta
     * AppointmentRequest, replicando su estructura final.
     */
    private function crearTablasNecesarias(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->decimal('precio', 8, 2);
            $table->text('descripcion')->nullable();
            $table->integer('duracion')->default(60);
            $table->boolean('activo')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('dia_semana')->unique();
            $table->time('hora_inicio')->default('08:00:00');
            $table->time('hora_fin')->default('18:00:00');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Servicio con id 1 y horario de atención de 08:00 a 18:00 de lunes a
     * sábado (domingo cerrado), que es lo que asumen estas pruebas.
     */
    private function sembrarDatos(): void
    {
        DB::table('servicios')->insert([
            'id' => 1,
            'nombre' => 'Corte de cabello',
            'precio' => 20000,
        ]);

        foreach (range(1, 7) as $dia) {
            DB::table('horarios')->insert([
                'dia_semana' => $dia,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '18:00:00',
                'activo' => $dia !== 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
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

    public function test_it_rejects_times_outside_the_booking_hours(): void
    {
        foreach (['07:59', '18:01'] as $hora) {
            $response = $this->post(route('citas.store'), [
                'fecha' => '2026-10-21',
                'hora' => $hora,
                'servicios' => [1],
            ]);

            $response->assertSessionHasErrors('hora');
        }
    }

    public function test_it_allows_the_opening_time_and_last_minute_before_closing(): void
    {
        foreach (['08:00', '17:59'] as $hora) {
            $response = $this->post(route('citas.store'), [
                'fecha' => '2026-10-21',
                'hora' => $hora,
                'servicios' => [1],
            ]);

            $response->assertRedirect(route('login'));
            $response->assertSessionHasNoErrors();
        }
    }

    public function test_it_requires_at_least_one_service(): void
    {
        $response = $this->post(route('citas.store'), [
            'fecha' => '2026-10-21',
            'hora' => '10:00',
        ]);

        $response->assertSessionHasErrors('servicios');
    }
}
