@extends('layouts.app')

@section('title', 'Horarios del Salón')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold">
            Configuración de Horarios
        </h2>

        <p class="text-muted">
            Administra el horario de atención del salón
        </p>

    </div>

    <a href="{{ route('admin.index') }}"
       class="btn btn-outline-secondary">

        Volver

    </a>

</div>

<div class="card border-0 shadow-sm rounded-4">

    <div class="card-body">

        <form
            action="{{ route('admin.horarios.update') }}"
            method="POST">

            @csrf

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Hora de apertura
                </label>

                <input
                    type="time"
                    name="hora_inicio"
                    value="{{ substr($horario->hora_inicio,0,5) }}"
                    class="form-control">

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Hora de cierre
                </label>

                <input
                    type="time"
                    name="hora_fin"
                    value="{{ substr($horario->hora_fin,0,5) }}"
                    class="form-control">

            </div>

            <div class="form-check form-switch mb-4">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="activo"
                    id="activo"

                    {{ $horario->activo ? 'checked' : '' }}>

                <label
                    class="form-check-label"
                    for="activo">

                    Salón disponible para reservas

                </label>

            </div>

            <button
                type="submit"
                class="btn btn-success">

                Guardar Cambios

            </button>

        </form>

    </div>

</div>

@endsection