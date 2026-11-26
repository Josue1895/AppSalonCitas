<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Service;
use App\Models\Appointment;
use App\Http\Controllers\ApiAuthController;

// =========================
// LOGIN API
// =========================

Route::post('/login', [ApiAuthController::class, 'login']);


// =========================
// RUTAS PROTEGIDAS SANCTUM
// =========================

Route::middleware('auth:sanctum')->group(function () {

    // =========================
    // API SERVICIOS
    // =========================

    Route::get('/servicios', function () {

        return response()->json([
            'success' => true,
            'data' => Service::where('activo', 1)->get()
        ]);

    });


    // =========================
    // API CITAS POR USUARIO
    // =========================

    Route::get('/citas/usuario/{id}', function ($id) {

        $citas = Appointment::where('usuarioId', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $citas
        ]);

    });


    // =========================
    // CREAR CITA API
    // =========================

    Route::post('/citas', function (Request $request) {

        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required',
            'usuarioId' => 'required',
        ]);

        $cita = Appointment::create([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'usuarioId' => $request->usuarioId,
            'total' => 0,
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita creada correctamente',
            'data' => $cita
        ]);

    });


    // =========================
    // ACTUALIZAR CITA API
    // =========================

    Route::put('/citas/{id}', function (Request $request, $id) {

        $cita = Appointment::findOrFail($id);

        $cita->update([
            'fecha' => $request->fecha,
            'hora' => $request->hora
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita actualizada correctamente',
            'data' => $cita
        ]);

    });

});