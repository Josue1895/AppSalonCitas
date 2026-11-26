```php id="np4f6q"
@extends('layouts.app')

@section('title', 'Editar Cita - AppSalon')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="row justify-content-center">

    <div class="col-lg-6">

        {{-- Mensajes --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Errores --}}
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
                    Editar Cita
                </h2>

                <form method="POST"
                      action="{{ route('citas.actualizar', $cita->id) }}">

                    @csrf

                    {{-- Fecha --}}
                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Fecha
                        </label>

                        <input
                            type="text"
                            id="fecha"
                            name="fecha"
                            value="{{ old('fecha', $cita->fecha) }}"
                            class="form-control rounded-3"
                            required
                        >

                    </div>

                    {{-- Hora --}}
                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Hora
                        </label>

                        <input
                            type="text"
                            id="hora"
                            name="hora"
                            value="{{ old('hora', $cita->hora) }}"
                            class="form-control rounded-3"
                            required
                        >

                    </div>

                    <div class="d-grid gap-2">

                        <button type="submit"
                                class="btn btn-primary rounded-3 py-2 fw-semibold">

                            Guardar cambios

                        </button>

                        <a href="{{ route('citas.mis') }}"
                           class="btn btn-outline-secondary rounded-3">

                            Volver

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

{{-- Flatpickr --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

    // Fecha
    flatpickr("#fecha", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: true
    });

    // Hora
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
```
