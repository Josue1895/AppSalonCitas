<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Horario;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $servicios = Service::where('activo', 1)->get();

        $horarios = Horario::orderBy('dia_semana')
            ->get()
            ->keyBy('dia_semana');

        return view('appointments.index', compact('servicios', 'horarios'));
    }

    public function store(AppointmentRequest $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para reservar');
        }

        $serviciosIds = array_unique($request->input('servicios', []));

        $servicios = Service::whereIn('id', $serviciosIds)
            ->where('activo', 1)
            ->get();

        if ($servicios->count() !== count($serviciosIds)) {
            throw ValidationException::withMessages([
                'servicios' => 'Uno o varios servicios seleccionados no están disponibles.',
            ]);
        }

        $duracionTotal = (int) $servicios->sum('duracion');
        $total = $servicios->sum('precio');

        if ($duracionTotal <= 0) {
            throw ValidationException::withMessages([
                'servicios' => 'La duración de los servicios seleccionados no es válida.',
            ]);
        }

        $horaInicio = Carbon::createFromFormat('H:i', $request->hora);
        $horaFin = $horaInicio->copy()->addMinutes($duracionTotal);

        DB::transaction(function () use (
            $request,
            $servicios,
            $horaInicio,
            $horaFin,
            $total
        ) {
            // Obtener y bloquear el horario correspondiente al día.
            $diaSemana = (int) Carbon::createFromFormat(
                'Y-m-d',
                $request->fecha
            )->dayOfWeekIso;

            $horario = Horario::where('dia_semana', $diaSemana)
                ->lockForUpdate()
                ->first();

            if (!$horario || !$horario->activo) {
                throw ValidationException::withMessages([
                    'fecha' => 'No hay atención disponible para el día seleccionado.',
                ]);
            }

            $horaCierre = Carbon::createFromFormat(
                'H:i:s',
                $horario->hora_fin
            );

            if ($horaFin->greaterThan($horaCierre)) {
                throw ValidationException::withMessages([
                    'hora' => 'La duración de los servicios supera el horario de atención. Selecciona una hora más temprana.',
                ]);
            }

            // Consultar las citas del mismo día que no estén canceladas.
            $citasExistentes = Appointment::with('citasServicios.servicio')
                ->whereDate('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->lockForUpdate()
                ->get();

            $inicioNuevo = $horaInicio->format('H:i:s');
            $finNuevo = $horaFin->format('H:i:s');

            foreach ($citasExistentes as $citaExistente) {
                $inicioExistente = Carbon::createFromFormat(
                    'H:i:s',
                    $citaExistente->hora
                );

                // Las citas antiguas pueden tener hora_fin NULL.
                if ($citaExistente->hora_fin) {
                    $finExistente = Carbon::createFromFormat(
                        'H:i:s',
                        $citaExistente->hora_fin
                    );
                } else {
                    $duracionExistente = (int) $citaExistente
                        ->citasServicios
                        ->sum(function ($citaServicio) {
                            return $citaServicio->servicio->duracion ?? 0;
                        });

                    // Si no conocemos la duración, bloqueamos el resto del día.
                    if ($duracionExistente <= 0) {
                        $duracionExistente = 1440;
                    }

                    $finExistente = $inicioExistente->copy()
                        ->addMinutes($duracionExistente);
                }

                $inicioExistenteTexto = $inicioExistente->format('H:i:s');
                $finExistenteTexto = $finExistente->format('H:i:s');

                // Evitar cruces entre citas.
                if (
                    $inicioNuevo < $finExistenteTexto &&
                    $finNuevo > $inicioExistenteTexto
                ) {
                    throw ValidationException::withMessages([
                        'hora' => 'Ese horario ya está ocupado. Selecciona otra hora para tu cita.',
                    ]);
                }
            }

            // Crear la cita incluyendo su horario.
            $cita = Appointment::create([
                'fecha' => $request->fecha,
                'hora' => $horaInicio->format('H:i:s'),
                'hora_fin' => $horaFin->format('H:i:s'),
                'usuarioId' => Auth::user()->id,
                'horarioId' => $horario->id,
                'total' => $total,
                'estado' => 'pendiente',
            ]);

            // Guardar cada servicio con su precio al momento de reservar.
            foreach ($servicios as $servicio) {
                AppointmentService::create([
                    'citaId' => $cita->id,
                    'servicioId' => $servicio->id,
                    'precio_unitario' => $servicio->precio,
                ]);
            }
        });

        return redirect()->route('citas.mis')
            ->with('success', 'Cita reservada correctamente');
    }

    public function misCitas()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $citas = Appointment::where('usuarioId', Auth::user()->id)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        return view('appointments.mis-citas', compact('citas'));
    }

    public function cancelar($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $cita = Appointment::findOrFail($id);

        if ($cita->usuarioId != Auth::user()->id) {
            return redirect()->route('citas.mis')
                ->with('error', 'No puedes cancelar esta cita');
        }

        if ($cita->estado != 'pendiente') {
            return redirect()->route('citas.mis')
                ->with('error', 'Solo puedes cancelar citas pendientes');
        }

        $cita->estado = 'cancelada';
        $cita->save();

        return redirect()->route('citas.mis')
            ->with('success', 'Cita cancelada correctamente');
    }
}
