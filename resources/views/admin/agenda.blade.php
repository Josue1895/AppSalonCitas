@extends('layouts.app')

@section('title', 'Agenda del Día')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold">
            Agenda de Citas
        </h2>

        <p class="text-muted">
            Visualiza las citas programadas por fecha
        </p>
    </div>

    <a href="{{ route('admin.index') }}"
       class="btn btn-outline-secondary">
        Volver
    </a>

</div>

<form method="GET" class="card shadow-sm border-0 rounded-4 mb-4">

    <div class="card-body">

        <div class="row align-items-end">

            <div class="col-md-4">

                <label class="form-label">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    value="{{ $fecha }}"
                    class="form-control">

            </div>

            <div class="col-md-2">

                <button
                    class="btn btn-primary w-100">
                    Buscar
                </button>

            </div>

        </div>

    </div>

</form>

<div class="card border-0 shadow-sm rounded-4">

    <div class="card-body">

        <table class="table align-middle">

            <thead>

                <tr>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>

            </thead>

            <tbody>

                @forelse($citas as $cita)

                    <tr>

                        <td>
                            {{ $cita->hora }}
                        </td>

                        <td>
                            {{ $cita->usuario->nombre }}
                            {{ $cita->usuario->apellido }}
                        </td>

                        <td>
                            ${{ number_format($cita->total, 0, ',', '.') }}
                        </td>

                        <td>

                            @if($cita->estado == 'pendiente')
                                <span class="badge bg-warning">
                                    Pendiente
                                </span>
                            @elseif($cita->estado == 'confirmada')
                                <span class="badge bg-info">
                                    Confirmada
                                </span>
                            @elseif($cita->estado == 'completada')
                                <span class="badge bg-success">
                                    Completada
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    Cancelada
                                </span>
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4" class="text-center">

                            No existen citas para esta fecha.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection