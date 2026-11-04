
@extends('layouts.admin')

@section('title', 'Administrar Horarios - Relux')

@section('content')

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Horarios de atención</h2>
        <p class="text-muted mb-0">
            Configura los días y las horas de apertura y cierre del salón.
        </p>
    </div>

    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
        Volver al panel
    </a>
</div>

<form action="{{ route('admin.horarios.actualizar') }}" method="POST">
    @csrf

    <div class="card shadow-sm rounded-4">
        <div class="card-body p-3 p-md-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Horario semanal</h5>
                    <small class="text-muted">
                        Activa los días en los que el salón presta servicios.
                    </small>
                </div>
                <span class="badge rounded-pill text-bg-light">
                    7 días
                </span>
            </div>

            @foreach(\App\Models\Horario::DIAS as $dia => $nombre)
                @php
                    $horario = $horarios->get($dia);
                    $activo = old("horarios.$dia.activo", $horario->activo ?? false);
                    $horaInicio = old(
                        "horarios.$dia.hora_inicio",
                        isset($horario) ? substr($horario->hora_inicio, 0, 5) : ''
                    );
                    $horaFin = old(
                        "horarios.$dia.hora_fin",
                        isset($horario) ? substr($horario->hora_fin, 0, 5) : ''
                    );
                @endphp

                <div class="border rounded-4 p-3 mb-3 horario-row"
                     data-dia="{{ $dia }}">

                    <div class="row align-items-center g-3">

                        <div class="col-12 col-md-3">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <div class="fw-bold">{{ $nombre }}</div>
                                    <small class="text-muted estado-texto">
                                        {{ $activo ? 'Abierto' : 'Cerrado' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-semibold">
                                Apertura
                            </label>
                            <input
                                type="time"
                                name="horarios[{{ $dia }}][hora_inicio]"
                                class="form-control hora-input"
                                value="{{ $horaInicio }}"
                                {{ $activo ? '' : 'disabled' }}
                            >
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold">
                                Cierre
                            </label>
                            <input
                                type="time"
                                name="horarios[{{ $dia }}][hora_fin]"
                                class="form-control hora-input"
                                value="{{ $horaFin }}"
                                {{ $activo ? '' : 'disabled' }}
                            >
                        </div>

                        <div class="col-12 col-md-2">
                            <div class="form-check form-switch">
                                <input
                                    type="hidden"
                                    name="horarios[{{ $dia }}][activo]"
                                    value="0"
                                >

                                <input
                                    class="form-check-input estado-switch"
                                    type="checkbox"
                                    role="switch"
                                    id="dia_{{ $dia }}"
                                    name="horarios[{{ $dia }}][activo]"
                                    value="1"
                                    {{ $activo ? 'checked' : '' }}
                                >

                                <label class="form-check-label fw-semibold"
                                       for="dia_{{ $dia }}">
                                    Abierto
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    Guardar horarios
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.horario-row').forEach(function (fila) {
        const interruptor = fila.querySelector('.estado-switch');
        const camposHora = fila.querySelectorAll('.hora-input');
        const estadoTexto = fila.querySelector('.estado-texto');

        function actualizarEstado() {
            const abierto = interruptor.checked;

            camposHora.forEach(function (campo) {
                campo.disabled = !abierto;
            });

            estadoTexto.textContent = abierto ? 'Abierto' : 'Cerrado';
        }

        interruptor.addEventListener('change', actualizarEstado);
        actualizarEstado();
    });
</script>
@endpush
