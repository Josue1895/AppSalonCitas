@extends('layouts.app')

@section('title', 'Registro - AppSalon')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="text-center mb-2 fw-bold">Crea tu cuenta</h2>
<p class="text-center text-muted mb-4">Regístrate para reservar y administrar tus citas</p>

                    <form method="POST" action="{{ route('registro.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" value="{{ old('apellido') }}" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Registrarse</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}">Ya tengo cuenta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection