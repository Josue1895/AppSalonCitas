@extends('layouts.admin')

@section('title', 'Crear usuario - RELUX')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Crear usuario</h2>
                <p class="text-muted mb-0">Registra una cuenta desde el panel de administración.</p>
            </div>

            <a href="{{ route('admin.usuarios') }}" class="btn btn-outline-secondary">
                Volver a usuarios
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.usuarios.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label fw-semibold">Nombre</label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                class="form-control @error('nombre') is-invalid @enderror"
                                maxlength="60"
                                required
                            >
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label fw-semibold">Apellido</label>
                            <input
                                type="text"
                                id="apellido"
                                name="apellido"
                                value="{{ old('apellido') }}"
                                class="form-control @error('apellido') is-invalid @enderror"
                                maxlength="60"
                                required
                            >
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                maxlength="30"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                            <input
                                type="tel"
                                id="telefono"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                class="form-control @error('telefono') is-invalid @enderror"
                                maxlength="10"
                            >
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Contraseña</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                minlength="6"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Confirmar contraseña
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                minlength="6"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="admin" class="form-label fw-semibold">Rol</label>
                            <select
                                id="admin"
                                name="admin"
                                class="form-select @error('admin') is-invalid @enderror"
                                required
                            >
                                <option value="0" @selected(old('admin', '0') == '0')>Cliente</option>
                                <option value="1" @selected(old('admin') == '1')>Administrador</option>
                            </select>
                            @error('admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('admin.usuarios') }}" class="btn btn-outline-secondary px-4">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4">Crear usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
