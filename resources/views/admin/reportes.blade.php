@extends('layouts.app')

@section('title', 'Reportes - AppSalon')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Reportes</h2>
    <p class="text-muted">Consulta citas e ingresos por rango de fechas</p>
</div>

<a href="{{ route('admin.index') }}" class="btn btn-outline-secondary mb-4">
    Volver al Panel
</a>

{{-- Filtros y descargas --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">

        <form method="GET" action="{{ route('admin.reportes') }}">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control">
                </div>

                <div class="col-md-4 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary w-100">Generar Reporte</button>
                </div>

            </div>
        </form>

        <div class="mt-3">
            <div class="row">

                <div class="col-md-6 mb-2">
                    <a href="{{ route('admin.reportes.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                       class="btn btn-danger w-100">
                        Descargar PDF
                    </a>
                </div>

                <div class="col-md-6 mb-2">
                    <a href="{{ route('admin.reportes.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                       class="btn btn-success w-100">
                        Descargar Excel
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Ingresos --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <h4 class="fw-bold">Ingresos Totales</h4>
        <h2 class="text-success">${{ $ingresos }}</h2>
    </div>
</div>

{{-- Tabla de citas --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">

        <h4 class="fw-bold mb-3">Citas Encontradas</h4>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas as $cita)
                        <tr>
                            <td>{{ $cita->id }}</td>
                            <td>{{ $cita->fecha }}</td>
                            <td>{{ $cita->hora }}</td>
                            <td>${{ $cita->total }}</td>
                            <td>{{ $cita->estado }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection