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

                    <form id="formulario-cita" method="POST" action="{{ route('citas.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha</label>
                                <input
                                    type="date"
                                    name="fecha"
                                    value="{{ old('fecha') }}"
                                    min="{{ now()->toDateString() }}"
                                    class="form-control @error('fecha') is-invalid @enderror"
                                    id="fecha-cita"
                                >
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hora</label>
                                <input
                                    type="time"
                                    name="hora"
                                    value="{{ old('hora') }}"
                                    min="09:00"
                                    max="18:00"
                                    class="form-control @error('hora') is-invalid @enderror"
                                    id="hora-cita"
                                    data-fecha-actual="{{ now()->toDateString() }}"
                                    data-hora-actual="{{ now()->format('H:i') }}"
                                >
                                @error('hora')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Horario disponible: 9:00 a. m. a 6:00 p. m.</div>
                                <div id="hora-cita-aviso" class="text-danger small mt-1" role="alert" aria-live="polite" hidden></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h5 class="fw-bold mb-3">Selecciona los servicios</h5>
                            @error('servicios')
                                <div class="alert alert-warning" role="alert">{{ $message }}</div>
                            @enderror
                            <div id="servicios-aviso" class="alert alert-warning" role="alert" aria-live="assertive" hidden>
                                Debes seleccionar al menos un servicio.
                            </div>

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

@push('scripts')
    <script>
        const fechaCita = document.getElementById('fecha-cita');
        const horaCita = document.getElementById('hora-cita');
        const avisoHoraCita = document.getElementById('hora-cita-aviso');
        const formularioCita = document.getElementById('formulario-cita');
        const avisoServicios = document.getElementById('servicios-aviso');
        const serviciosCita = document.querySelectorAll('input[name="servicios[]"]');
        const horaMinimaJornada = '09:00';
        const horaMaximaJornada = '18:00';

        function actualizarHoraMinima() {
            horaCita.min = fechaCita.value === horaCita.dataset.fechaActual
                && horaCita.dataset.horaActual > horaMinimaJornada
                ? horaCita.dataset.horaActual
                : horaMinimaJornada;
            horaCita.max = horaMaximaJornada;

            validarHoraCita();
        }

        function validarHoraCita() {
            const fueraDelHorario = horaCita.value
                && (horaCita.value < horaMinimaJornada || horaCita.value > horaMaximaJornada);
            const anteriorAHoraActual = horaCita.min > horaMinimaJornada
                && horaCita.value
                && horaCita.value < horaCita.min;
            const horaInvalida = fueraDelHorario || anteriorAHoraActual;

            horaCita.classList.toggle('is-invalid', Boolean(horaInvalida));
            avisoHoraCita.hidden = !horaInvalida;
            avisoHoraCita.textContent = fueraDelHorario
                ? 'Las citas solo se pueden reservar entre las 9:00 a. m. y las 6:00 p. m.'
                : anteriorAHoraActual
                    ? `La hora debe ser igual o posterior a las ${horaCita.min} (hora de Bogotá).`
                : '';
        }

        formularioCita.addEventListener('submit', function (event) {
            const hayServicioSeleccionado = Array.from(serviciosCita).some(servicio => servicio.checked);

            if (!hayServicioSeleccionado) {
                event.preventDefault();
                avisoServicios.hidden = false;
                avisoServicios.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        serviciosCita.forEach(servicio => {
            servicio.addEventListener('change', function () {
                if (Array.from(serviciosCita).some(servicioSeleccionado => servicioSeleccionado.checked)) {
                    avisoServicios.hidden = true;
                }
            });
        });

        fechaCita.addEventListener('change', actualizarHoraMinima);
        horaCita.addEventListener('input', validarHoraCita);
        horaCita.addEventListener('change', validarHoraCita);
        actualizarHoraMinima();
    </script>
@endpush