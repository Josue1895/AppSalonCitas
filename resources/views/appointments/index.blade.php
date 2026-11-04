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
                                    required
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
                                    class="form-control @error('hora') is-invalid @enderror"
                                    id="hora-cita"
                                    data-fecha-actual="{{ now()->toDateString() }}"
                                    data-hora-actual="{{ now()->format('H:i') }}"
                                    required
                                >
                                @error('hora')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text" id="horario-disponible">
                                    Selecciona una fecha para consultar el horario disponible.
                                </div>

                                <div
                                    id="hora-cita-aviso"
                                    class="text-danger small mt-1"
                                    role="alert"
                                    aria-live="polite"
                                    hidden
                                ></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h5 class="fw-bold mb-3">Selecciona los servicios</h5>

                            @error('servicios')
                                <div class="alert alert-warning" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div
                                id="servicios-aviso"
                                class="alert alert-warning"
                                role="alert"
                                aria-live="assertive"
                                hidden
                            >
                                Debes seleccionar al menos un servicio.
                            </div>

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
                                                        data-duracion="{{ $servicio->duracion }}"
                                                    >

                                                    <label
                                                        class="form-check-label w-100"
                                                        for="servicio{{ $servicio->id }}"
                                                    >
                                                        <strong>{{ $servicio->nombre }}</strong><br>
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

                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            Reservar cita
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    $horariosJson = $horarios->map(function ($horario) {
        return [
            'activo' => (bool) $horario->activo,
            'hora_inicio' => substr($horario->hora_inicio, 0, 5),
            'hora_fin' => substr($horario->hora_fin, 0, 5),
        ];
    });
@endphp

@push('scripts')
    <script>
        const fechaCita = document.getElementById('fecha-cita');
        const horaCita = document.getElementById('hora-cita');
        const avisoHoraCita = document.getElementById('hora-cita-aviso');
        const horarioDisponible = document.getElementById('horario-disponible');
        const formularioCita = document.getElementById('formulario-cita');
        const avisoServicios = document.getElementById('servicios-aviso');
        const serviciosCita = document.querySelectorAll('input[name="servicios[]"]');

        // Horarios obtenidos de la base de datos.
        // Lunes = 1, martes = 2, ..., domingo = 7.
        const horarios = @json($horariosJson);

        let horarioSeleccionado = null;

        function obtenerDuracionTotal() {
            let duracion = 0;

            serviciosCita.forEach(servicio => {
                if (servicio.checked) {
                    duracion += Number(servicio.dataset.duracion || 0);
                }
            });

            return duracion;
        }

        function convertirHoraAMinutos(hora) {
            const partes = hora.split(':');

            return Number(partes[0]) * 60 + Number(partes[1]);
        }

        function formatearHora(hora) {
            const [horas, minutos] = hora.split(':').map(Number);
            const periodo = horas >= 12 ? 'p. m.' : 'a. m.';
            const hora12 = horas % 12 || 12;

            return `${hora12}:${String(minutos).padStart(2, '0')} ${periodo}`;
        }

        function obtenerHorarioFecha() {
            if (!fechaCita.value) {
                return null;
            }

            const fecha = new Date(fechaCita.value + 'T12:00:00');
            const diaSemana = ((fecha.getDay() + 6) % 7) + 1;

            return horarios[diaSemana] || null;
        }

        function actualizarHorario() {
            horarioSeleccionado = obtenerHorarioFecha();

            horaCita.classList.remove('is-invalid');
            avisoHoraCita.hidden = true;
            avisoHoraCita.textContent = '';

            if (!horarioSeleccionado || !horarioSeleccionado.activo) {
                horaCita.value = '';
                horaCita.disabled = true;
                horaCita.removeAttribute('min');
                horaCita.removeAttribute('max');

                horarioDisponible.textContent =
                    'No hay atención disponible para esta fecha.';

                return;
            }

            horaCita.disabled = false;
            horaCita.min = horarioSeleccionado.hora_inicio;
            horaCita.max = horarioSeleccionado.hora_fin;

            horarioDisponible.textContent =
                `Horario disponible: ${formatearHora(horarioSeleccionado.hora_inicio)} a ${formatearHora(horarioSeleccionado.hora_fin)}.`;

            if (
                fechaCita.value === horaCita.dataset.fechaActual &&
                horaCita.dataset.horaActual > horaCita.min
            ) {
                horaCita.min = horaCita.dataset.horaActual;
            }

            validarHoraCita();
        }

        function validarHoraCita() {
            if (!fechaCita.value || !horaCita.value) {
                horaCita.classList.remove('is-invalid');
                avisoHoraCita.hidden = true;
                avisoHoraCita.textContent = '';

                return true;
            }

            if (!horarioSeleccionado || !horarioSeleccionado.activo) {
                return false;
            }

            const horaInicio = convertirHoraAMinutos(horaCita.value);
            const horaApertura = convertirHoraAMinutos(horarioSeleccionado.hora_inicio);
            const horaCierre = convertirHoraAMinutos(horarioSeleccionado.hora_fin);

            const duracionTotal = obtenerDuracionTotal();
            const horaFinalizacion = horaInicio + duracionTotal;

            let mensaje = '';

            if (horaInicio < horaApertura || horaInicio >= horaCierre) {
                mensaje = 'La hora seleccionada está fuera del horario de atención.';
            } else if (horaCita.min && horaCita.value < horaCita.min) {
                mensaje = `La hora debe ser igual o posterior a las ${horaCita.min}.`;
            } else if (duracionTotal > 0 && horaFinalizacion > horaCierre) {
                mensaje = 'La duración de los servicios supera el horario de atención. Selecciona una hora más temprana.';
            }

            const horaInvalida = mensaje !== '';

            horaCita.classList.toggle('is-invalid', horaInvalida);
            avisoHoraCita.hidden = !horaInvalida;
            avisoHoraCita.textContent = mensaje;

            return !horaInvalida;
        }

        formularioCita.addEventListener('submit', function (event) {
            const hayServicioSeleccionado = Array.from(serviciosCita)
                .some(servicio => servicio.checked);

            if (!hayServicioSeleccionado) {
                event.preventDefault();
                avisoServicios.hidden = false;

                avisoServicios.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                return;
            }

            avisoServicios.hidden = true;

            if (!fechaCita.value || !horarioSeleccionado || !horarioSeleccionado.activo) {
                event.preventDefault();

                horarioDisponible.textContent =
                    'Selecciona una fecha con atención disponible.';

                fechaCita.focus();

                return;
            }

            if (!horaCita.value || !validarHoraCita()) {
                event.preventDefault();
                horaCita.focus();
            }
        });

        serviciosCita.forEach(servicio => {
            servicio.addEventListener('change', function () {
                if (Array.from(serviciosCita).some(item => item.checked)) {
                    avisoServicios.hidden = true;
                }

                validarHoraCita();
            });
        });

        fechaCita.addEventListener('change', function () {
            horaCita.value = '';
            actualizarHorario();
        });

        horaCita.addEventListener('input', validarHoraCita);
        horaCita.addEventListener('change', validarHoraCita);

        // Inicializar el horario al cargar la vista.
        actualizarHorario();
    </script>
@endpush
