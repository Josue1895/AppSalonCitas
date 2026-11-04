<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminController extends Controller
{
    /**
     * Panel principal de administración.
     */
    public function index()
    {
        // Indicadores generales.
        $totalUsuarios = User::count();
        $totalServicios = Service::count();
        $totalCitas = Appointment::count();

        // Indicadores de actividad.
        $citasHoy = Appointment::whereDate('fecha', today())->count();

        $citasCompletadas = Appointment::where('estado', 'completada')->count();

        // Solo se consideran ingresos de citas completadas.
        $ingresosTotales = Appointment::where('estado', 'completada')
            ->sum('total');

        // Distribución de las citas por estado.
        $citasPorEstado = [
            'pendiente' => Appointment::where('estado', 'pendiente')->count(),
            'confirmada' => Appointment::where('estado', 'confirmada')->count(),
            'completada' => $citasCompletadas,
            'cancelada' => Appointment::where('estado', 'cancelada')->count(),
        ];

        // Próximas cinco citas que no estén canceladas.
        $proximasCitas = Appointment::with('usuario')
            ->whereDate('fecha', '>=', today())
            ->where('estado', '!=', 'cancelada')
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->take(5)
            ->get();

        // Últimas cinco citas registradas por fecha.
        $citasRecientes = Appointment::with('usuario')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'totalUsuarios',
            'totalServicios',
            'totalCitas',
            'citasHoy',
            'citasCompletadas',
            'ingresosTotales',
            'citasPorEstado',
            'proximasCitas',
            'citasRecientes'
        ));
    }

    /**
     * Mostrar todas las citas.
     */
    public function citas()
    {
        $citas = Appointment::with('usuario')
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        return view('admin.citas', compact('citas'));
    }

    /**
     * Actualizar el estado de una cita y notificar al cliente por correo.
     */
    public function cambiarEstado(Request $request, $id)
    {
        $datos = $request->validate([
            'estado' => 'required|in:pendiente,confirmada,completada,cancelada'
        ]);

        $cita = Appointment::with(['usuario', 'citasServicios.servicio'])
            ->findOrFail($id);

        $estadoAnterior = $cita->estado;
        $nuevoEstado = $datos['estado'];

        // Evita enviar correos si el administrador vuelve a guardar el mismo estado.
        if ($estadoAnterior === $nuevoEstado) {
            return redirect()->route('admin.citas')
                ->with('success', 'La cita conserva el mismo estado. No fue necesario enviar una notificación.');
        }

        $cita->estado = $nuevoEstado;
        $cita->save();

        // El correo no debe deshacer ni impedir el cambio de estado si SMTP falla.
        $usuario = $cita->usuario;

        if ($usuario && !empty($usuario->email)) {
            try {
                Mail::send('emails.cita-estado', [
                    'usuario' => $usuario,
                    'cita' => $cita,
                    'estadoAnterior' => $estadoAnterior,
                    'nuevoEstado' => $nuevoEstado,
                ], function ($message) use ($usuario, $cita, $nuevoEstado) {
                    $message->to($usuario->email)
                        ->subject('Actualización de tu cita - RELUX | Cita #' . $cita->id);
                });

                return redirect()->route('admin.citas')
                    ->with('success', 'Estado actualizado y correo de notificación enviado al cliente.');
            } catch (Throwable $exception) {
                report($exception);

                return redirect()->route('admin.citas')
                    ->with('success', 'El estado de la cita se actualizó correctamente.')
                    ->with('error', 'No se pudo enviar el correo al cliente. Verifica la configuración SMTP.');
            }
        }

        return redirect()->route('admin.citas')
            ->with('success', 'El estado se actualizó, pero la cita no tiene un cliente con correo registrado.');
    }

    /**
     * Mostrar los horarios de los siete días.
     */
    public function horarios()
    {
        $horarios = Horario::orderBy('dia_semana')
            ->get()
            ->keyBy('dia_semana');

        return view('admin.horarios', compact('horarios'));
    }

    /**
     * Guardar los cambios realizados por el administrador.
     */
    public function actualizarHorarios(Request $request)
    {
        $datos = $request->validate([
            'horarios' => ['required', 'array', 'size:7'],
            'horarios.*.activo' => ['required', 'boolean'],
            'horarios.*.hora_inicio' => ['nullable', 'date_format:H:i'],
            'horarios.*.hora_fin' => ['nullable', 'date_format:H:i'],
        ]);

        foreach (Horario::DIAS as $dia => $nombre) {
            $horario = $datos['horarios'][$dia] ?? null;

            if (!$horario) {
                throw ValidationException::withMessages([
                    'horarios' => "Falta la configuración del día $nombre."
                ]);
            }

            if ((int) $horario['activo'] === 1) {
                if (
                    empty($horario['hora_inicio']) ||
                    empty($horario['hora_fin']) ||
                    $horario['hora_inicio'] >= $horario['hora_fin']
                ) {
                    throw ValidationException::withMessages([
                        "horarios.$dia.hora_fin" =>
                            "El horario de $nombre debe tener una apertura y un cierre válidos."
                    ]);
                }
            }
        }

        DB::transaction(function () use ($datos) {
            foreach (Horario::DIAS as $dia => $nombre) {
                $horario = $datos['horarios'][$dia];
                $activo = (int) $horario['activo'] === 1;

                Horario::where('dia_semana', $dia)->update([
                    'activo' => $activo,
                    'hora_inicio' => $activo
                        ? $horario['hora_inicio']
                        : '00:00:00',
                    'hora_fin' => $activo
                        ? $horario['hora_fin']
                        : '00:00:00',
                ]);
            }
        });

        return redirect()->route('admin.horarios')
            ->with('success', 'Los horarios se actualizaron correctamente.');
    }
}
