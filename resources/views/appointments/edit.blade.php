@extends('layouts.app')

@section('title', 'Editar Cita - AppSalon')

@section('content')
    @php
        $serviciosSeleccionados = old(
            'servicios',
            $cita->citasServicios->pluck('servicioId')->all()
        );
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-4 text-center">Editar cita #{{ $cita->id }}</h2>

                    <form method="POST" action="{{ route('citas.update', $cita->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha-cita" class="form-label">Fecha</label>
                                <input
                                    type="date"
                                    name="fecha"
                                    id="fecha-cita"
                                    value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}"
                                    min="{{ now()->toDateString() }}"
                                    class="form-control @error('fecha') is-invalid @enderror"
                                    required
                                >
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="hora-cita" class="form-label">Hora</label>
                                <input
                                    type="time"
                                    name="hora"
                                    id="hora-cita"
                                    value="{{ old('hora', substr($cita->hora, 0, 5)) }}"
                                    class="form-control @error('hora') is-invalid @enderror"
                                    required
                                >
                                @error('hora')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <h5 class="fw-bold mb-3">Servicios</h5>

                            @error('servicios')
                                <div class="alert alert-warning" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="row">
                                @foreach($servicios as $servicio)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border rounded-4">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="servicios[]"
                                                        value="{{ $servicio->id }}"
                                                        id="servicio{{ $servicio->id }}"
                                                        @checked(in_array($servicio->id, $serviciosSeleccionados))
                                                    >
                                                    <label
                                                        class="form-check-label w-100"
                                                        for="servicio{{ $servicio->id }}"
                                                    >
                                                        <strong>{{ $servicio->nombre }}</strong>
                                                        @if(!$servicio->activo)
                                                            <span class="badge bg-secondary">No disponible</span>
                                                        @endif
                                                        <br>
                                                        <span class="text-muted">{{ $servicio->descripcion }}</span><br>
                                                        <span>
                                                            <strong>${{ $servicio->precio }}</strong>
                                                            - {{ $servicio->duracion }} min
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('citas.mis') }}" class="btn btn-outline-secondary w-50">
                                Volver
                            </a>
                            <button type="submit" class="btn btn-primary w-50">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
