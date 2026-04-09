@extends('layouts.app')

@section('title', 'Reservar Cita - AppSalon')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-4 text-center">Reservar Cita</h2>

                    @if(!Auth::check())
                        <div class="alert alert-info">
                            Debes iniciar sesión para reservar una cita.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('citas.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" value="{{ old('fecha') }}" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hora</label>
                                <input type="time" name="hora" value="{{ old('hora') }}" class="form-control">
                            </div>
                        </div>

                        <div class="mt-3">
                            <h5 class="fw-bold mb-3">Selecciona los servicios</h5>

                            <div class="row">
                                @foreach($servicios as $servicio)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border rounded-4">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="servicios[]" value="{{ $servicio->id }}" id="servicio{{ $servicio->id }}">
                                                    <label class="form-check-label w-100" for="servicio{{ $servicio->id }}">
                                                        <strong>{{ $servicio->nombre }}</strong><br>
                                                        <span class="text-muted">{{ $servicio->descripcion }}</span><br>
                                                        <span><strong>${{ $servicio->precio }}</strong> - {{ $servicio->duracion }} min</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Reservar cita</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection