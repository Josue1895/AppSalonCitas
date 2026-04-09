<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsuarios = User::count();
        $totalServicios = Service::count();
        $totalCitas = Appointment::count();

        return view('admin.index', compact('totalUsuarios', 'totalServicios', 'totalCitas'));
    }

    public function citas()
    {
        $citas = Appointment::with('usuario')->orderBy('fecha', 'asc')->orderBy('hora', 'asc')->get();

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

        return redirect()->route('admin.citas')->with('success', 'Estado de la cita actualizado correctamente');
    }
}