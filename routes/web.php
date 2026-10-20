<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReportController;

// nueva pagina de inicio
Route::view('/', 'landing')->name('home');

// Autenticación
Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro');
Route::post('/registro', [AuthController::class, 'register'])->name('registro.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Servicios públicos
Route::get('/servicios', [ServiceController::class, 'index'])->name('servicios.index');

// Citas
Route::get('/citas', [AppointmentController::class, 'index'])->name('citas.index');
Route::post('/citas', [AppointmentController::class, 'store'])->name('citas.store');
Route::get('/mis-citas', [AppointmentController::class, 'misCitas'])->name('citas.mis');
Route::post('/mis-citas/cancelar/{id}', [AppointmentController::class, 'cancelar'])->name('citas.cancelar');

// nueva funcionalidad para recuperar contraseña

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');

// Admin protegido
Route::middleware('admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/servicios', [ServiceController::class, 'adminIndex'])->name('admin.servicios.index');
    Route::get('/admin/servicios/crear', [ServiceController::class, 'create'])->name('admin.servicios.create');
    Route::post('/admin/servicios', [ServiceController::class, 'store'])->name('admin.servicios.store');
    Route::get('/admin/servicios/editar/{id}', [ServiceController::class, 'edit'])->name('admin.servicios.edit');
Route::post('/admin/servicios/actualizar/{id}', [ServiceController::class, 'update'])->name('admin.servicios.update');
Route::post('/admin/servicios/eliminar/{id}', [ServiceController::class, 'destroy'])->name('admin.servicios.destroy');
Route::get('/admin/citas', [AdminController::class, 'citas'])->name('admin.citas');
Route::post('/admin/citas/estado/{id}', [AdminController::class, 'cambiarEstado'])->name('admin.citas.estado');
    Route::get('/admin/reportes', [ReportController::class, 'index'])->name('admin.reportes');
    Route::get('/admin/reportes/pdf', [ReportController::class, 'pdf'])->name('admin.reportes.pdf');
    Route::get('/admin/reportes/excel', [ReportController::class, 'excel'])->name('admin.reportes.excel');
});