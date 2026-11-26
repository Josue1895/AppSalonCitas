
@extends('layouts.app')

@section('title', 'Reservar Cita - AppSalon')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4">

                <h2 class="fw-bold mb-4 text-center">
                    Reservar Cita
                </h2>

                @if(!Auth::check())
                    <div class="alert alert-info">
                        Debes iniciar sesión para reservar una cita.
                    </div>
                @endif

                <form method="POST" action="{{ route('citas.store') }}">
                    @csrf

                    <div class="row">

                        {{-- Fecha --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Fecha
                            </label>

                            <input
                                type="text"
                                id="fecha"
                                name="fecha"
                                value="{{ old('fecha') }}"
                                class="form-control rounded-3"
                                placeholder="Selecciona una fecha"
                                required
                            >
                        </div>

                        {{-- Hora --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Hora
                            </label>

                            <input
                                type="text"
                                id="hora"
                                name="hora"
                                value="{{ old('hora') }}"
                                class="form-control rounded-3"
                                placeholder="Selecciona una hora"
                                required
                            >
                        </div>
                    </div>

                    {{-- Servicios --}}
                    <div class="mt-4">

                        <h5 class="fw-bold mb-3">
                            Selecciona los servicios
                        </h5>

                        <div class="row">

                            @foreach($servicios as $servicio)

                                <div class="col-md-6 mb-3">

                                    <div class="card h-100 border-0 shadow-sm rounded-4">

                                        <div class="card-body">

                                            <div class="form-check">

                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="servicios[]"
                                                    value="{{ $servicio->id }}"
                                                    id="servicio{{ $servicio->id }}"
                                                >

                                                <label
                                                    class="form-check-label w-100"
                                                    for="servicio{{ $servicio->id }}"
                                                >

                                                    <strong>
                                                        {{ $servicio->nombre }}
                                                    </strong>

                                                    <br>

                                                    <span class="text-muted">
                                                        {{ $servicio->descripcion }}
                                                    </span>

                                                    <br>

                                                    <span class="fw-semibold text-primary">
                                                        ${{ $servicio->precio }}
                                                    </span>

                                                    <span class="text-muted">
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

                    <button type="submit" class="btn btn-primary w-100 mt-4 py-2 rounded-3 fw-semibold">
                        Reservar cita
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- Flatpickr --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

    // Calendario fecha
    flatpickr("#fecha", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: true
    });

    // Selector hora
    flatpickr("#hora", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 30,
        minTime: "08:00",
        maxTime: "18:00",
        disableMobile: true
    });

</script>

@endsection

