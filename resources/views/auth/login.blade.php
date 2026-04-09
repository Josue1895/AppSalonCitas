@extends('layouts.app')

@section('title', 'Login - AppSalon')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="text-center mb-2 fw-bold">Bienvenido de nuevo</h2>
<p class="text-center text-muted mb-4">Ingresa a tu cuenta para gestionar tus citas</p>

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="correo@ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="********">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('registro') }}">¿No tienes cuenta? Regístrate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection