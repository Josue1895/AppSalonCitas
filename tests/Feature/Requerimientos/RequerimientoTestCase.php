<?php

namespace Tests\Feature\Requerimientos;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Horario;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Base para las pruebas de requerimientos (RF / RNF).
 *
 * Las migraciones del proyecto usan SQL exclusivo de MySQL
 * (UPDATE ... INNER JOIN, ALTER TABLE ... MODIFY), por lo que no se pueden
 * ejecutar sobre SQLite en memoria. Aquí se replica la estructura FINAL de las
 * tablas tras todas las migraciones, incluidas las llaves foráneas.
 *
 * "Ahora" queda fijo en: martes 20/10/2026, 14:00 hora de Bogotá.
 */
abstract class RequerimientoTestCase extends TestCase
{
    /** Martes (día actual simulado). */
    protected const HOY = '2026-10-20';

    /** Miércoles: día abierto de 08:00 a 18:00. */
    protected const MIERCOLES = '2026-10-21';

    /** Domingo: día cerrado. */
    protected const DOMINGO = '2026-10-25';

    protected const PASSWORD = 'secreto123';

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-20 19:00:00', 'UTC'));

        $this->crearEsquema();
        $this->crearHorarios();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function crearEsquema(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->string('apellido', 60);
            $table->string('email', 30)->unique();
            $table->string('password', 255);
            $table->string('telefono', 10)->nullable();
            $table->boolean('admin')->default(false);
            $table->boolean('confirmado')->default(false);
            $table->string('token', 15)->nullable();
            $table->timestamps();
        });

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

        // MySQL convierte '10:00' en '10:00:00' en columnas TIME; SQLite no.
        // El panel de horarios guarda las horas como 'H:i', así que se imita
        // ese comportamiento para no generar errores que en MySQL no ocurren.
        DB::unprepared("
            CREATE TRIGGER horarios_normalizar_horas AFTER UPDATE ON horarios
            FOR EACH ROW WHEN length(NEW.hora_inicio) = 5 OR length(NEW.hora_fin) = 5
            BEGIN
                UPDATE horarios
                SET hora_inicio = time(NEW.hora_inicio), hora_fin = time(NEW.hora_fin)
                WHERE id = NEW.id;
            END;
        ");

        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->time('hora_fin')->nullable();
            $table->unsignedBigInteger('usuarioId')->nullable();
            $table->unsignedBigInteger('horarioId');
            $table->decimal('total', 8, 2)->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('usuarioId')->references('id')->on('usuarios')->nullOnDelete();
            $table->foreign('horarioId')->references('id')->on('horarios')->restrictOnDelete();
        });

        Schema::create('citas_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('citaId')->nullable();
            $table->unsignedBigInteger('servicioId')->nullable();
            $table->decimal('precio_unitario', 8, 2);

            $table->foreign('citaId')->references('id')->on('citas')->cascadeOnDelete();
            $table->foreign('servicioId')->references('id')->on('servicios')->restrictOnDelete();
        });
    }

    /**
     * Lunes a sábado de 08:00 a 18:00 y domingo cerrado.
     */
    private function crearHorarios(): void
    {
        foreach (Horario::DIAS as $dia => $nombre) {
            DB::table('horarios')->insert([
                'id' => $dia,
                'dia_semana' => $dia,
                'hora_inicio' => $dia === 7 ? '00:00:00' : '08:00:00',
                'hora_fin' => $dia === 7 ? '00:00:00' : '18:00:00',
                'activo' => $dia !== 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ------------------------------------------------------------------
    // Datos de prueba
    // ------------------------------------------------------------------

    protected function crearCliente(array $datos = []): User
    {
        static $consecutivo = 0;
        $consecutivo++;

        return User::create(array_merge([
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'email' => "cliente{$consecutivo}@test.com",
            'password' => self::PASSWORD,
            'telefono' => '3001234567',
            'admin' => false,
            'confirmado' => true,
        ], $datos));
    }

    protected function crearAdmin(array $datos = []): User
    {
        return $this->crearCliente(array_merge([
            'nombre' => 'Admin',
            'email' => 'admin@test.com',
            'admin' => true,
        ], $datos));
    }

    protected function crearServicio(array $datos = []): Service
    {
        return Service::create(array_merge([
            'nombre' => 'Corte de cabello',
            'precio' => 25000,
            'descripcion' => 'Corte y peinado',
            'duracion' => 60,
            'activo' => true,
        ], $datos));
    }

    /**
     * Crea una cita directamente en la base de datos.
     *
     * @param  Service[]  $servicios
     */
    protected function crearCita(User $cliente, array $datos = [], array $servicios = []): Appointment
    {
        $fecha = $datos['fecha'] ?? self::MIERCOLES;

        $cita = Appointment::create(array_merge([
            'fecha' => $fecha,
            'hora' => '10:00:00',
            'hora_fin' => '11:00:00',
            'usuarioId' => $cliente->id,
            'horarioId' => Carbon::parse($fecha)->dayOfWeekIso,
            'total' => 25000,
            'estado' => 'pendiente',
        ], $datos));

        foreach ($servicios as $servicio) {
            AppointmentService::create([
                'citaId' => $cita->id,
                'servicioId' => $servicio->id,
                'precio_unitario' => $servicio->precio,
            ]);
        }

        return $cita;
    }

    /**
     * Datos válidos para el formulario de reserva.
     */
    protected function datosReserva(Service $servicio, array $datos = []): array
    {
        return array_merge([
            'fecha' => self::MIERCOLES,
            'hora' => '10:00',
            'servicios' => [$servicio->id],
        ], $datos);
    }

    /**
     * Correos capturados por el mailer "array" (configurado en phpunit.xml).
     *
     * @return Collection<int, \Symfony\Component\Mime\Email>
     */
    protected function correosEnviados(): Collection
    {
        return Mail::mailer()->getSymfonyTransport()->messages()
            ->map(fn ($enviado) => $enviado->getOriginalMessage());
    }
}
