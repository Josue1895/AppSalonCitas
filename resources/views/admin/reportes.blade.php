
@extends('layouts.admin')

@section('title', 'Reportes - Relux')

@section('content')

<style>
    .reportes-page {
        color: #39252e;
    }

    .reportes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 26px;
    }

    .reportes-header h2 {
        font-size: 26px;
        font-weight: 750;
        margin-bottom: 5px;
    }

    .reportes-header p {
        color: #927d85;
        margin: 0;
    }

    .reportes-card {
        background: #fff;
        border: 1px solid #f1e6ea;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 5px 20px rgba(80, 35, 52, .035);
        height: 100%;
    }

    .reportes-card h5 {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #492d38;
    }

    .reportes-muted {
        font-size: 12px;
        color: #a18c94;
    }

    .reporte-kpi {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .reporte-kpi-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 21px;
    }

    .reporte-kpi small {
        color: #99858d;
        font-size: 12px;
        display: block;
        margin-bottom: 5px;
    }

    .reporte-kpi h3 {
        font-size: 23px;
        font-weight: 750;
        margin: 0;
        color: #482c37;
    }

    .reporte-filtros {
        background: #fff;
        border: 1px solid #f1e6ea;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 22px;
    }

    .reporte-filtros label {
        font-size: 12px;
        font-weight: 650;
        color: #765966;
        margin-bottom: 7px;
    }

    .reporte-filtros .form-control {
        border: 1px solid #eadde2;
        border-radius: 9px;
        min-height: 42px;
    }

    .btn-reporte {
        background: #762d4a;
        color: #fff;
        border: none;
        border-radius: 9px;
        padding: 10px 17px;
        font-weight: 600;
    }

    .btn-reporte:hover {
        background: #5e2039;
        color: #fff;
    }

    .btn-reporte-outline {
        border: 1px solid #762d4a;
        color: #762d4a;
        background: #fff;
        border-radius: 9px;
        padding: 9px 15px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .btn-reporte-outline:hover {
        background: #fbf2f5;
        color: #5e2039;
    }

    .grafica-contenedor {
        position: relative;
        height: 290px;
        margin-top: 20px;
    }

    .grafica-contenedor.dona {
        height: 270px;
    }

    .tabla-reportes {
        vertical-align: middle;
        margin-top: 15px;
    }

    .tabla-reportes thead th {
        color: #927b85;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        background: #fcf8fa;
        padding: 14px 12px;
        border-bottom: 1px solid #f0e5e9;
        white-space: nowrap;
    }

    .tabla-reportes tbody td {
        padding: 14px 12px;
        font-size: 13px;
        color: #59434c;
        border-color: #f5edf0;
    }

    .estado-reporte {
        display: inline-block;
        border-radius: 30px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .estado-completada {
        background: #e8f5ed;
        color: #28784b;
    }

    .estado-pendiente {
        background: #fff3d9;
        color: #98701e;
    }

    .estado-confirmada {
        background: #e9f0ff;
        color: #4166a5;
    }

    .estado-cancelada {
        background: #fbe9ed;
        color: #a33f59;
    }

    .servicio-barra {
        margin-top: 19px;
    }

    .servicio-barra-info {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
        margin-bottom: 7px;
    }

    .servicio-barra-info strong {
        color: #583c47;
    }

    .servicio-barra-fondo {
        background: #f6edf1;
        height: 9px;
        border-radius: 20px;
        overflow: hidden;
    }

    .servicio-barra-valor {
        height: 100%;
        border-radius: 20px;
        background: linear-gradient(90deg, #762d4a, #bd7891);
    }

    @media (max-width: 768px) {
        .reportes-card {
            padding: 17px;
        }

        .reportes-header h2 {
            font-size: 22px;
        }

        .grafica-contenedor {
            height: 240px;
        }
    }
</style>

<div class="reportes-page">

    {{-- Encabezado --}}
    <div class="reportes-header">
        <div>
            <h2>Reportes y estadísticas</h2>
            <p>Analiza el rendimiento y los resultados de tu negocio.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="reporte-filtros">
        <form method="GET" action="{{ route('admin.reportes') }}">
            <div class="row align-items-end g-3">

                <div class="col-md-4">
                    <label for="fecha_inicio">Fecha inicial</label>
                    <input
                        type="date"
                        class="form-control"
                        id="fecha_inicio"
                        name="fecha_inicio"
                        value="{{ $fechaInicio }}"
                    >
                </div>

                <div class="col-md-4">
                    <label for="fecha_fin">Fecha final</label>
                    <input
                        type="date"
                        class="form-control"
                        id="fecha_fin"
                        name="fecha_fin"
                        value="{{ $fechaFin }}"
                    >
                </div>

                <div class="col-md-4 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn-reporte">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>

                    <a href="{{ route('admin.reportes') }}" class="btn-reporte-outline">
                        Limpiar
                    </a>
                </div>

            </div>
        </form>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <a
                href="{{ route('admin.reportes.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                class="btn-reporte-outline"
            >
                <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
            </a>

            <a
                href="{{ route('admin.reportes.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                class="btn-reporte-outline"
            >
                <i class="bi bi-file-earmark-excel"></i> Descargar Excel
            </a>
        </div>
    </div>

    {{-- Indicadores principales --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="reportes-card">
                <div class="reporte-kpi">
                    <div class="reporte-kpi-icon" style="background:#f8eaf0;color:#762d4a;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <small>Ingresos realizados</small>
                        <h3>${{ number_format((float) $ingresos, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="reportes-card">
                <div class="reporte-kpi">
                    <div class="reporte-kpi-icon" style="background:#eeeaff;color:#7561ad;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <small>Total de citas</small>
                        <h3>{{ $totalCitas }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="reportes-card">
                <div class="reporte-kpi">
                    <div class="reporte-kpi-icon" style="background:#e8f5ed;color:#328052;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <small>Citas completadas</small>
                        <h3>{{ $citasCompletadas }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="reportes-card">
                <div class="reporte-kpi">
                    <div class="reporte-kpi-icon" style="background:#fbe9ed;color:#ae4961;">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <small>Citas canceladas</small>
                        <h3>{{ $citasCanceladas }}</h3>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Gráfica de ingresos --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-8">
            <div class="reportes-card">
                <div>
                    <h5>Ingresos por día</h5>
                    <span class="reportes-muted">
                        Ingresos generados por citas completadas.
                    </span>
                </div>

                <div class="grafica-contenedor">
                    <canvas id="graficaIngresos"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="reportes-card">
                <h5>Estado de las citas</h5>
                <span class="reportes-muted">
                    Distribución de las citas del periodo.
                </span>

                <div class="grafica-contenedor dona">
                    <canvas id="graficaEstados"></canvas>
                </div>

                <div class="text-center mt-2">
                    <span class="reportes-muted">
                        Pendientes: {{ $citasPendientes }} ·
                        Confirmadas: {{ $citasConfirmadas }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Servicios más solicitados --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="reportes-card">
                <h5>Servicios más solicitados</h5>
                <span class="reportes-muted">
                    Reservas registradas, excluyendo las canceladas.
                </span>

                @php
                    $maxSolicitudes = max(1, (int) $serviciosMasSolicitados->max('cantidad_solicitudes'));
                @endphp

                @forelse($serviciosMasSolicitados as $servicio)
                    <div class="servicio-barra">
                        <div class="servicio-barra-info">
                            <strong>{{ $servicio->nombre }}</strong>
                            <span>{{ $servicio->cantidad_solicitudes }} solicitudes</span>
                        </div>

                        <div class="servicio-barra-fondo">
                            <div
                                class="servicio-barra-valor"
                                style="width: {{ ($servicio->cantidad_solicitudes / $maxSolicitudes) * 100 }}%;"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        No hay servicios solicitados en este periodo.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-6">
            <div class="reportes-card">
                <h5>Resumen de citas</h5>
                <span class="reportes-muted">
                    Estado general de las reservas.
                </span>

                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span><i class="bi bi-hourglass-split text-warning me-2"></i> Pendientes</span>
                    <strong>{{ $citasPendientes }}</strong>
                </div>

                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span><i class="bi bi-calendar-check text-primary me-2"></i> Confirmadas</span>
                    <strong>{{ $citasConfirmadas }}</strong>
                </div>

                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span><i class="bi bi-check-circle text-success me-2"></i> Completadas</span>
                    <strong>{{ $citasCompletadas }}</strong>
                </div>

                <div class="d-flex justify-content-between align-items-center py-3">
                    <span><i class="bi bi-x-circle text-danger me-2"></i> Canceladas</span>
                    <strong>{{ $citasCanceladas }}</strong>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabla de citas --}}
    <div class="reportes-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5>Detalle de citas</h5>
                <span class="reportes-muted">
                    Historial de reservas del periodo seleccionado.
                </span>
            </div>

            <span class="reportes-muted">
                {{ $totalCitas }} registros
            </span>
        </div>

        <div class="table-responsive">
            <table class="table tabla-reportes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Hora inicial</th>
                        <th>Hora final</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($citas as $cita)
                        <tr>
                            <td>#{{ $cita->id }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $cita->hora ? substr($cita->hora, 0, 5) : '--' }}
                            </td>

                            <td>
                                {{ $cita->hora_fin ? substr($cita->hora_fin, 0, 5) : '--' }}
                            </td>

                            <td>
                                ${{ number_format((float) $cita->total, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="estado-reporte estado-{{ $cita->estado }}">
                                    {{ $cita->estado }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No se encontraron citas para el periodo seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const etiquetasIngresos = @json(
        $ingresosPorFecha->pluck('fecha')->values()
    );

    const valoresIngresos = @json(
        $ingresosPorFecha->pluck('ingresos')->values()
    );

    const estados = {
        pendientes: {{ $citasPendientes }},
        confirmadas: {{ $citasConfirmadas }},
        completadas: {{ $citasCompletadas }},
        canceladas: {{ $citasCanceladas }}
    };

    const formatoPesos = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    });

    // Gráfica de ingresos.
    const contextoIngresos = document.getElementById('graficaIngresos');

    if (contextoIngresos) {
        new Chart(contextoIngresos, {
            type: 'bar',
            data: {
                labels: etiquetasIngresos,
                datasets: [{
                    label: 'Ingresos',
                    data: valoresIngresos,
                    backgroundColor: '#9b506c',
                    hoverBackgroundColor: '#762d4a',
                    borderRadius: 7,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(contexto) {
                                return formatoPesos.format(contexto.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(valor) {
                                return '$' + new Intl.NumberFormat('es-CO', {
                                    notation: 'compact',
                                    maximumFractionDigits: 1
                                }).format(valor);
                            }
                        },
                        grid: {
                            color: '#f3ebee'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Gráfica de estados.
    const contextoEstados = document.getElementById('graficaEstados');

    if (contextoEstados) {
        new Chart(contextoEstados, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Confirmadas', 'Completadas', 'Canceladas'],
                datasets: [{
                    data: [
                        estados.pendientes,
                        estados.confirmadas,
                        estados.completadas,
                        estados.canceladas
                    ],
                    backgroundColor: [
                        '#e8b95c',
                        '#7295d0',
                        '#65a982',
                        '#c66b83'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>

@endsection
