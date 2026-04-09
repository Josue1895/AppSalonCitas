<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $servicios = Service::where('activo', 1)->get();
        return view('appointments.index', compact('servicios'));
    }

    public function store(AppointmentRequest $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para reservar');
        }

        $cita = Appointment::create([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'usuarioId' => Auth::user()->id,
            'total' => 0,
            'estado' => 'pendiente'
        ]);

        $total = 0;

        foreach ($request->servicios as $servicioId) {
            $servicio = Service::findOrFail($servicioId);

            AppointmentService::create([
                'citaId' => $cita->id,
                'servicioId' => $servicioId
            ]);

            $total += $servicio->precio;
        }

        $cita->total = $total;
        $cita->save();

        return redirect()->route('citas.mis')->with('success', 'Cita reservada correctamente');
    }

    public function misCitas()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
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
        return redirect()->route('login')->with('error', 'Debes iniciar sesión');
    }

    $cita = Appointment::findOrFail($id);

    // Validar que la cita sea del usuario
    if ($cita->usuarioId != Auth::user()->id) {
        return redirect()->route('citas.mis')->with('error', 'No puedes cancelar esta cita');
    }

    // Solo permitir cancelar si está pendiente
    if ($cita->estado != 'pendiente') {
        return redirect()->route('citas.mis')->with('error', 'Solo puedes cancelar citas pendientes');
    }

    $cita->estado = 'cancelada';
    $cita->save();

    return redirect()->route('citas.mis')->with('success', 'Cita cancelada correctamente');
}

}