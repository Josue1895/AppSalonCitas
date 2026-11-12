<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Horario;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

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
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para reservar');
        }

        return $this->guardarCita($request);
    }

    public function edit($id)
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $cita = Appointment::with('citasServicios.servicio')
            ->where('usuarioId', Auth::id())
            ->findOrFail($id);

        if ($cita->estado !== 'pendiente') {
            return redirect()->route('citas.mis')
                ->with('error', 'Solo puedes editar citas pendientes.');
        }

        $serviciosActuales = $cita->citasServicios->pluck('servicioId')->all();
        $servicios = Service::where('activo', 1)
            ->orWhereIn('id', $serviciosActuales)
            ->orderBy('nombre')
            ->get();

        return view('appointments.edit', compact('cita', 'servicios'));
    }

    public function update(AppointmentRequest $request, $id)
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $cita = Appointment::findOrFail($id);

        if ($cita->usuarioId != Auth::id()) {
            return redirect()->route('citas.mis')
                ->with('error', 'No puedes editar esta cita.');
        }

        if ($cita->estado !== 'pendiente') {
            return redirect()->route('citas.mis')
                ->with('error', 'Solo puedes editar citas pendientes.');
        }

        return $this->guardarCita($request, $cita->id);
    }

    private function guardarCita(AppointmentRequest $request, ?int $citaId = null)
    {
        $serviciosIds = array_values(array_unique($request->input('servicios', [])));
        $serviciosActuales = $citaId
            ? AppointmentService::where('citaId', $citaId)->pluck('servicioId')->all()
            : [];

        $consultaServicios = Service::whereIn('id', $serviciosIds);

        if ($citaId) {
            $consultaServicios->where(function ($query) use ($serviciosActuales) {
                $query->where('activo', 1)
                    ->orWhereIn('id', $serviciosActuales);
            });
        } else {
            $consultaServicios->where('activo', 1);
        }

        $servicios = $consultaServicios->get();

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

        $resultado = DB::transaction(function () use (
            $request,
            $servicios,
            $horaInicio,
            $horaFin,
            $total,
            $citaId
        ) {
            $diaSemana = (int) Carbon::createFromFormat(
                'Y-m-d',
                $request->fecha
            )->dayOfWeekIso;

            $horario = Horario::where('dia_semana', $diaSemana)
                ->lockForUpdate()
                ->first();

            if (! $horario || ! $horario->activo) {
                throw ValidationException::withMessages([
                    'fecha' => 'No hay atención disponible para el día seleccionado.',
                ]);
            }

            $horaCierre = Carbon::createFromFormat('H:i:s', $horario->hora_fin);

            if ($horaFin->greaterThan($horaCierre)) {
                throw ValidationException::withMessages([
                    'hora' => 'La duración de los servicios supera el horario de atención. Selecciona una hora más temprana.',
                ]);
            }

            $cita = null;

            if ($citaId) {
                $cita = Appointment::whereKey($citaId)
                    ->lockForUpdate()
                    ->first();

                if (! $cita || $cita->usuarioId != Auth::id()) {
                    return 'no_autorizada';
                }

                if ($cita->estado !== 'pendiente') {
                    return 'no_pendiente';
                }
            }

            $citasExistentes = Appointment::with('citasServicios.servicio')
                ->whereDate('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->when($citaId, function ($query) use ($citaId) {
                    $query->where('id', '!=', $citaId);
                })
                ->lockForUpdate()
                ->get();

            $inicioNuevo = $horaInicio->format('H:i:s');
            $finNuevo = $horaFin->format('H:i:s');

            foreach ($citasExistentes as $citaExistente) {
                $inicioExistente = Carbon::createFromFormat('H:i:s', $citaExistente->hora);

                if ($citaExistente->hora_fin) {
                    $finExistente = Carbon::createFromFormat('H:i:s', $citaExistente->hora_fin);
                } else {
                    $duracionExistente = (int) $citaExistente->citasServicios
                        ->sum(function ($citaServicio) {
                            return $citaServicio->servicio->duracion ?? 0;
                        });

                    if ($duracionExistente <= 0) {
                        $duracionExistente = 1440;
                    }

                    $finExistente = $inicioExistente->copy()
                        ->addMinutes($duracionExistente);
                }

                if (
                    $inicioNuevo < $finExistente->format('H:i:s') &&
                    $finNuevo > $inicioExistente->format('H:i:s')
                ) {
                    throw ValidationException::withMessages([
                        'hora' => 'Ese horario ya está ocupado. Selecciona otra hora para tu cita.',
                    ]);
                }
            }

            $datosCita = [
                'fecha' => $request->fecha,
                'hora' => $horaInicio->format('H:i:s'),
                'hora_fin' => $horaFin->format('H:i:s'),
                'usuarioId' => Auth::user()->id,
                'horarioId' => $horario->id,
                'total' => $total,
            ];

            if ($cita) {
                $cita->fill($datosCita)->save();
                AppointmentService::where('citaId', $cita->id)->delete();
            } else {
                $datosCita['estado'] = 'pendiente';
                $cita = Appointment::create($datosCita);
            }

            foreach ($servicios as $servicio) {
                AppointmentService::create([
                    'citaId' => $cita->id,
                    'servicioId' => $servicio->id,
                    'precio_unitario' => $servicio->precio,
                ]);
            }

            return $cita->id;
        });

        if ($resultado === 'no_autorizada') {
            return redirect()->route('citas.mis')
                ->with('error', 'No puedes editar esta cita.');
        }

        if ($resultado === 'no_pendiente') {
            return redirect()->route('citas.mis')
                ->with('error', 'Solo puedes editar citas pendientes.');
        }

        if (! $citaId) {
            $cita = Appointment::with(['usuario', 'citasServicios.servicio'])
                ->findOrFail($resultado);
            $usuario = $cita->usuario;

            if (! $usuario || empty($usuario->email)) {
                return redirect()->route('citas.mis')
                    ->with('success', 'Cita reservada correctamente.')
                    ->with('error', 'No se pudo enviar la confirmación porque no hay un correo registrado.');
            }

            $mailer = config('mail.default');
            $transport = config("mail.mailers.{$mailer}.transport");

            if (in_array($transport, ['log', 'array'], true)) {
                return redirect()->route('citas.mis')
                    ->with('success', 'Cita reservada correctamente.')
                    ->with('error', 'El correo no se envió: la aplicación está usando un transporte de prueba. Configura SMTP para entregarlo.');
            }

            try {
                Mail::send('emails.cita-reservada', [
                    'usuario' => $usuario,
                    'cita' => $cita,
                ], function ($message) use ($usuario, $cita) {
                    $message->to($usuario->email)
                        ->subject('Confirmación de tu cita - RELUX | Cita #'.$cita->id);
                });
            } catch (Throwable $exception) {
                report($exception);

                return redirect()->route('citas.mis')
                    ->with('success', 'Cita reservada correctamente.')
                    ->with('error', 'No se pudo enviar el correo de confirmación. Verifica la configuración SMTP.');
            }
        }

        return redirect()->route('citas.mis')
            ->with('success', $citaId
                ? 'Cita actualizada correctamente.'
                : 'Cita reservada correctamente. Te enviamos la confirmación a tu correo.');
    }

    public function misCitas()
    {
        if (! Auth::check()) {
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
        if (! Auth::check()) {
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
