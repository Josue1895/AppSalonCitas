<?php

namespace Tests\Feature;

use App\Models\Horario;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentDateValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::parse('2026-10-20 19:00:00', 'UTC')
        );
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Obtiene un servicio existente para las solicitudes de prueba.
     */
    private function obtenerServicioId(): int
    {
        $servicioId = Service::query()->value('id');

        if (!$servicioId) {
            $this->markTestSkipped(
                'No hay servicios registrados para realizar la prueba.'
            );
        }

        return (int) $servicioId;
    }

    /**
     * Obtiene un horario activo para un día concreto.
     */
    private function obtenerHorarioActivo(int $diaSemana): Horario
    {
        $horario = Horario::where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->first();

        if (!$horario) {
            $this->markTestSkipped(
                'No existe un horario activo para el día seleccionado.'
            );
        }

        return $horario;
    }

    /**
     * Comprueba que las fechas anteriores a hoy sean rechazadas.
     */
    public function test_it_rejects_a_date_before_today(): void
    {
        $this->assertSame('America/Bogota', now()->timezoneName);

        $response = $this->post(route('citas.store'), [
            'fecha' => '2026-10-19',
            'hora' => '15:00',
            'servicios' => [$this->obtenerServicioId()],
        ]);

        $response->assertSessionHasErrors('fecha');
    }

    /**
     * Comprueba que no se pueda reservar una hora anterior a la actual.
     */
    public function test_it_rejects_a_time_before_now_on_the_current_date(): void
    {
        $diaSemana = (int) now()->format('N');

        $horario = $this->obtenerHorarioActivo($diaSemana);

        $horaInicio = substr($horario->hora_inicio, 0, 5);
        $horaFin = substr($horario->hora_fin, 0, 5);

        // Se busca una hora anterior a las 19:00 y dentro del horario.
        $horaAnterior = Carbon::parse('2026-10-20 19:00:00')
            ->subMinute()
            ->format('H:i');

        if (
            $horaAnterior < $horaInicio ||
            $horaAnterior >= $horaFin
        ) {
            $this->markTestSkipped(
                'No existe una hora anterior a la actual dentro del horario configurado.'
            );
        }

        $response = $this->post(route('citas.store'), [
            'fecha' => '2026-10-20',
            'hora' => $horaAnterior,
            'servicios' => [$this->obtenerServicioId()],
        ]);

        $response->assertSessionHasErrors('hora');
    }

    /**
     * Comprueba que la hora actual y las fechas futuras
     * superen la validación de fecha y hora.
     */
    public function test_it_allows_the_current_time_and_future_dates_to_pass_date_validation(): void
    {
        $diaSemanaHoy = (int) now()->format('N');

        $horarioHoy = $this->obtenerHorarioActivo($diaSemanaHoy);

        $horaActual = now()->format('H:i');
        $horaInicioHoy = substr($horarioHoy->hora_inicio, 0, 5);
        $horaFinHoy = substr($horarioHoy->hora_fin, 0, 5);

        if (
            $horaActual < $horaInicioHoy ||
            $horaActual >= $horaFinHoy
        ) {
            $this->markTestSkipped(
                'La hora actual está fuera del horario configurado.'
            );
        }

        $servicioId = $this->obtenerServicioId();

        $todayResponse = $this->post(route('citas.store'), [
            'fecha' => '2026-10-20',
            'hora' => $horaActual,
            'servicios' => [$servicioId],
        ]);

        $todayResponse->assertRedirect(route('login'));
        $todayResponse->assertSessionHasNoErrors();

        $diaSemanaFuturo = (int) Carbon::parse('2026-10-21')->format('N');

        $horarioFuturo = $this->obtenerHorarioActivo($diaSemanaFuturo);

        $horaFutura = substr($horarioFuturo->hora_inicio, 0, 5);

        $futureDateResponse = $this->post(route('citas.store'), [
            'fecha' => '2026-10-21',
            'hora' => $horaFutura,
            'servicios' => [$servicioId],
        ]);

        $futureDateResponse->assertRedirect(route('login'));
        $futureDateResponse->assertSessionHasNoErrors();
    }

    /**
     * Comprueba que se rechacen las horas anteriores
     * al inicio y las iguales o posteriores al cierre.
     */
    public function test_it_rejects_times_outside_the_booking_hours(): void
    {
        $horario = $this->obtenerHorarioActivo(3);

        $servicioId = $this->obtenerServicioId();

        $horaAnterior = now()->subMinute()->format('H:i');

        $horaDeCierre = substr($horario->hora_fin, 0, 5);

        foreach ([$horaAntesDeAbrir, $horaDeCierre] as $hora) {
            $response = $this->post(route('citas.store'), [
                'fecha' => '2026-10-21',
                'hora' => $hora,
                'servicios' => [$servicioId],
            ]);

            $response->assertSessionHasErrors('hora');
        }
    }

    /**
     * Comprueba que se permitan la hora de apertura
     * y una hora posterior dentro del horario.
     */
    public function test_it_allows_booking_start_times_inside_business_hours(): void
    {
        $horario = $this->obtenerHorarioActivo(3);

        $servicioId = $this->obtenerServicioId();

        $horaApertura = substr($horario->hora_inicio, 0, 5);

        $horaInterior = Carbon::createFromFormat(
            'H:i:s',
            $horario->hora_inicio
        )->addMinute()->format('H:i');

        $horaCierre = substr($horario->hora_fin, 0, 5);

        if ($horaInterior >= $horaCierre) {
            $this->markTestSkipped(
                'El horario configurado no permite probar una hora interior.'
            );
        }

        foreach ([$horaApertura, $horaInterior] as $hora) {
            $response = $this->post(route('citas.store'), [
                'fecha' => '2026-10-21',
                'hora' => $hora,
                'servicios' => [$servicioId],
            ]);

            $response->assertRedirect(route('login'));
            $response->assertSessionHasNoErrors();
        }
    }

    /**
     * Comprueba que sea obligatorio seleccionar al menos un servicio.
     */
    public function test_it_requires_at_least_one_service(): void
    {
        $response = $this->post(route('citas.store'), [
            'fecha' => '2026-10-21',
            'hora' => '10:00',
        ]);

        $response->assertSessionHasErrors('servicios');
    }
}
