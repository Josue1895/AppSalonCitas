
@extends('layouts.admin')

@section('title', 'Crear Servicio - Relux')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Crear servicio</h2>
                <p class="text-muted mb-0">
                    Registra un nuevo servicio para ofrecer a los clientes.
                </p>
            </div>

            <a href="{{ route('admin.servicios.index') }}"
               class="btn btn-outline-secondary">
                Volver a servicios
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.servicios.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label fw-semibold">
                                Nombre del servicio
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                class="form-control @error('nombre') is-invalid @enderror"
                                placeholder="Ej. Corte de cabello"
                                required
                            >

                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label fw-semibold">
                                Precio
                            </label>

                            <input
                                type="number"
                                id="precio"
                                name="precio"
                                step="0.01"
                                min="0"
                                value="{{ old('precio') }}"
                                class="form-control @error('precio') is-invalid @enderror"
                                placeholder="Ej. 25000"
                                required
                            >

                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label fw-semibold">
                                Descripción
                            </label>

                            <textarea
                                id="descripcion"
                                name="descripcion"
                                class="form-control @error('descripcion') is-invalid @enderror"
                                rows="4"
                                placeholder="Describe brevemente el servicio"
                            >{{ old('descripcion') }}</textarea>

                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="duracion" class="form-label fw-semibold">
                                Duración (minutos)
                            </label>

                            <input
                                type="number"
                                id="duracion"
                                name="duracion"
                                min="1"
                                step="1"
                                value="{{ old('duracion') }}"
                                class="form-control @error('duracion') is-invalid @enderror"
                                placeholder="Ej. 60"
                                required
                            >

                            @error('duracion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                Indica cuánto tiempo requiere realizar el servicio.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                        <a
                            href="{{ route('admin.servicios.index') }}"
                            class="btn btn-outline-secondary px-4"
                        >
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary px-4">
                            Guardar servicio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
