@extends('layouts.app')

@section('title', 'Panel Admin - AppSalon')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-4">

    <h2 class="fw-bold">
        Panel de Administración
    </h2>

    <p class="text-muted">
        Gestiona usuarios, servicios y citas del sistema
    </p>

</div>

{{-- Estadísticas --}}
<div class="row mb-4">

    {{-- Usuarios --}}
    <div class="col-md-3 mb-3">

        <div class="card border-0 shadow-sm rounded-4 text-center h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Usuarios
                </h6>

                <p class="display-6 fw-bold mb-0">
                    {{ $totalUsuarios }}
                </p>

            </div>

        </div>

    </div>

    {{-- Servicios --}}
    <div class="col-md-3 mb-3">

        <div class="card border-0 shadow-sm rounded-4 text-center h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Servicios
                </h6>

                <p class="display-6 fw-bold mb-0">
                    {{ $totalServicios }}
                </p>

            </div>

        </div>

    </div>

    {{-- Citas --}}
    <div class="col-md-3 mb-3">

        <div class="card border-0 shadow-sm rounded-4 text-center h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Total Citas
                </h6>

                <p class="display-6 fw-bold mb-0">
                    {{ $totalCitas }}
                </p>

            </div>

        </div>

    </div>

    {{-- Ingresos --}}
    <div class="col-md-3 mb-3">

        <div class="card border-0 shadow-sm rounded-4 text-center h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Ingresos
                </h6>

                <p class="display-6 fw-bold mb-0 text-success">
                    ${{ $ingresosTotales }}
                </p>

            </div>

        </div>

    </div>

</div>

{{-- Segunda fila --}}
<div class="row mb-4">

    {{-- Citas hoy --}}
    <div class="col-md-6 mb-3">

        <div class="card border-0 shadow-sm rounded-4 text-center h-100">

            <div class="card-body">

                <h5 class="fw-bold">
                    Citas Hoy
                </h5>

                <p class="display-5 text-primary fw-bold">
                    {{ $citasHoy }}
                </p>

            </div>

        </div>

    </div>

    {{-- Pendientes --}}
    <div class="col-md-6 mb-3">

        <div class="card border-0 shadow-sm rounded-4 text-center h-100">

            <div class="card-body">

                <h5 class="fw-bold">
                    Citas Pendientes
                </h5>

                <p class="display-5 text-warning fw-bold">
                    {{ $citasPendientes }}
                </p>

            </div>

        </div>

    </div>

</div>

{{-- Gráfica --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-body">

        <h5 class="fw-bold mb-4">
            Estado de las Citas
        </h5>

        <canvas id="graficaCitas"></canvas>

    </div>

</div>

{{-- Acciones rápidas --}}
<div class="d-flex gap-2 flex-wrap">

```
<a href="{{ route('admin.servicios.index') }}"
   class="btn btn-outline-dark rounded-3">
    Gestionar Servicios
</a>

<a href="{{ route('admin.citas') }}"
   class="btn btn-outline-primary rounded-3">
    Gestionar Citas
</a>
<a href="{{ route('admin.agenda') }}"
   class="btn btn-outline-warning rounded-3">

    Agenda del Día

</a>

<a href="{{ route('admin.usuarios') }}"
   class="btn btn-outline-warning rounded-3">
    Gestionar Usuarios
</a>

<a href="{{ route('admin.reportes') }}"
   class="btn btn-outline-success rounded-3">
    Reportes
</a>

<a href="{{ route('admin.horarios') }}"
   class="btn btn-outline-dark rounded-3">

    Horarios

</a>

</div>


{{-- Chart.js --}}
<script>

    const ctx = document.getElementById('graficaCitas');

    new Chart(ctx, {

        type: 'bar',

        data: {

            labels: [
                'Pendientes',
                'Completadas',
                'Canceladas'
            ],

            datasets: [{

                label: 'Cantidad de citas',

                data: [
                    {{ $citasPendientes }},
                    {{ $citasCompletadas }},
                    {{ $citasCanceladas }}
                ],

                borderWidth: 1

            }]

        },

        options: {

            responsive: true,

            scales: {

                y: {
                    beginAtZero: true
                }

            }

        }

    });

</script>

@endsection

