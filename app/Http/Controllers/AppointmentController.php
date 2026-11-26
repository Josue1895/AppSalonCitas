<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\Horario;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaReservadaMail;

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
            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión para reservar');
        }

        // Validar horario laboral
        $horarioSalon = Horario::first();

if (!$horarioSalon || !$horarioSalon->activo) {

    return redirect()
        ->back()
        ->withInput()
        ->with('error', 'El salón no está disponible para reservas');
}

$hora = strtotime($request->hora);

$horaApertura = strtotime($horarioSalon->hora_inicio);
$horaCierre = strtotime($horarioSalon->hora_fin);

if ($hora < $horaApertura || $hora > $horaCierre) {

    return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Horario fuera del horario laboral del salón');
}

        // Validar cita duplicada
        $citaExistente = Appointment::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($citaExistente) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya existe una cita reservada en ese horario');
        }

        

        // Crear cita
        $cita = Appointment::create([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'usuarioId' => Auth::user()->id,
            'total' => 0,
            'estado' => 'pendiente'
        ]);

        $total = 0;

        // Guardar servicios
        foreach ($request->servicios as $servicioId) {

            $servicio = Service::findOrFail($servicioId);

            AppointmentService::create([
                'citaId' => $cita->id,
                'servicioId' => $servicioId
            ]);

            $total += $servicio->precio;
        }

        // Actualizar total
        $cita->total = $total;
$cita->save();

// Cargar usuario para el correo
$cita->load('usuario');

// Enviar correo
Mail::to($cita->usuario->email)
    ->send(new CitaReservadaMail($cita));

return redirect()
    ->route('citas.mis')
    ->with('success', 'Cita reservada correctamente');
    }

    public function misCitas()
    {
        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $citas = Appointment::where('usuarioId', Auth::user()->id)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        return view('appointments.mis-citas', compact('citas'));
    }

    // =========================
    // EDITAR CITA
    // =========================

    public function editar($id)
    {
        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $cita = Appointment::findOrFail($id);

        // Validar propietario
        if ($cita->usuarioId != Auth::user()->id) {

            return redirect()
                ->route('citas.mis')
                ->with('error', 'No puedes editar esta cita');
        }

        // Solo editar pendientes
        if ($cita->estado != 'pendiente') {

            return redirect()
                ->route('citas.mis')
                ->with('error', 'Solo puedes editar citas pendientes');
        }

        return view('appointments.editar', compact('cita'));
    }

    // =========================
    // ACTUALIZAR CITA
    // =========================

    public function actualizar(AppointmentRequest $request, $id)
    {
        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $cita = Appointment::findOrFail($id);

        // Validar propietario
        if ($cita->usuarioId != Auth::user()->id) {

            return redirect()
                ->route('citas.mis')
                ->with('error', 'No puedes modificar esta cita');
        }

        // Validar horario laboral
       $horarioSalon = Horario::first();

if (!$horarioSalon || !$horarioSalon->activo) {

    return redirect()
        ->back()
        ->withInput()
        ->with('error', 'El salón no está disponible para reservas');
}

$hora = strtotime($request->hora);

$horaApertura = strtotime($horarioSalon->hora_inicio);
$horaCierre = strtotime($horarioSalon->hora_fin);

if ($hora < $horaApertura || $hora > $horaCierre) {

    return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Horario fuera del horario laboral');
}

        // Validar duplicados excluyendo la misma cita
        $citaExistente = Appointment::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->where('id', '!=', $cita->id)
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($citaExistente) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ese horario ya está reservado');
        }

        // Actualizar
        $cita->fecha = $request->fecha;
        $cita->hora = $request->hora;
        $cita->save();

        return redirect()
            ->route('citas.mis')
            ->with('success', 'Cita actualizada correctamente');
    }

    // =========================
    // CANCELAR CITA
    // =========================

    public function cancelar($id)
    {
        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión');
        }

        $cita = Appointment::findOrFail($id);

        // Validar propietario
        if ($cita->usuarioId != Auth::user()->id) {

            return redirect()
                ->route('citas.mis')
                ->with('error', 'No puedes cancelar esta cita');
        }

        // Solo cancelar pendientes
        if ($cita->estado != 'pendiente') {

            return redirect()
                ->route('citas.mis')
                ->with('error', 'Solo puedes cancelar citas pendientes');
        }

        // Cancelar
        $cita->estado = 'cancelada';
        $cita->save();

        return redirect()
            ->route('citas.mis')
            ->with('success', 'Cita cancelada correctamente');
    }
}