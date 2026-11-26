<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Horario;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstadoCitaMail;

class AdminController extends Controller
{
    public function index()
    {
        // Totales
        $totalUsuarios = User::count();
        $totalServicios = Service::count();
        $totalCitas = Appointment::count();

        // Citas hoy
        $citasHoy = Appointment::whereDate('fecha', now()->toDateString())->count();

        // Ingresos totales
        $ingresosTotales = Appointment::where('estado', '!=', 'cancelada')->sum('total');

        // Citas pendientes
        $citasPendientes = Appointment::where('estado', 'pendiente')->count();

        // Datos para gráfica
        $citasCompletadas = Appointment::where('estado', 'completada')->count();

        $citasCanceladas = Appointment::where('estado', 'cancelada')->count();

        return view('admin.index', compact(
            'totalUsuarios',
            'totalServicios',
            'totalCitas',
            'citasHoy',
            'ingresosTotales',
            'citasPendientes',
            'citasCompletadas',
            'citasCanceladas'
        ));
    }

    public function citas()
    {
        $citas = Appointment::with('usuario')
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        return view('admin.citas', compact('citas'));
    }



   public function cambiarEstado(Request $request, $id)
{
    $request->validate([
        'estado' => 'required|in:pendiente,confirmada,completada,cancelada'
    ]);

    $cita = Appointment::findOrFail($id);

    $cita->estado = $request->estado;
    $cita->save();

    $cita->load('usuario');

    Mail::to($cita->usuario->email)
        ->send(new EstadoCitaMail($cita));

    return redirect()
        ->route('admin.citas')
        ->with('success', 'Estado de la cita actualizado correctamente');
}





    public function usuarios()
{
$usuarios = User::orderBy('id', 'desc')->get();


return view('admin.usuarios', compact('usuarios'));


}

public function cambiarEstadoUsuario($id)
{
$usuario = User::findOrFail($id);


$usuario->confirmado = !$usuario->confirmado;

$usuario->save();

return redirect()
    ->route('admin.usuarios')
    ->with('success', 'Estado del usuario actualizado correctamente');


}

public function cambiarRolUsuario($id)
{
$usuario = User::findOrFail($id);


$usuario->admin = !$usuario->admin;

$usuario->save();

return redirect()
    ->route('admin.usuarios')
    ->with('success', 'Rol del usuario actualizado correctamente');


}




public function agenda()
{
    $fecha = request('fecha', now()->format('Y-m-d'));

    $citas = Appointment::with('usuario')
        ->whereDate('fecha', $fecha)
        ->orderBy('hora')
        ->get();

    return view('admin.agenda', compact(
        'citas',
        'fecha'
    ));
}



public function horarios()
{
    $horario = Horario::first();

    return view(
        'admin.horarios',
        compact('horario')
    );
}

public function actualizarHorarios(Request $request)
{
    $request->validate([
        'hora_inicio' => 'required',
        'hora_fin' => 'required'
    ]);

    $horario = Horario::first();

    $horario->update([
        'hora_inicio' => $request->hora_inicio,
        'hora_fin' => $request->hora_fin,
        'activo' => $request->has('activo')
    ]);

    return redirect()
        ->route('admin.horarios')
        ->with(
            'success',
            'Horario actualizado correctamente'
        );
}

}