
@extends('layouts.admin')

@section('title', 'Panel administrativo - Relux')

@section('content')

<style>
    .dashboard-relux {
        color: #432b35;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 26px;
    }

    .dashboard-header h2 {
        font-size: 26px;
        font-weight: 750;
        margin-bottom: 5px;
    }

    .dashboard-header p {
        color: #927d85;
        margin: 0;
    }

    .dashboard-fecha {
        color: #765966;
        background: #fff;
        border: 1px solid #f0e3e8;
        padding: 10px 15px;
        border-radius: 10px;
        font-size: 13px;
    }

    .dashboard-card {
        background: #fff;
        border: 1px solid #f1e6ea;
        border-radius: 16px;
        padding: 21px;
        height: 100%;
        box-shadow: 0 5px 20px rgba(80, 35, 52, .035);
    }

    .dashboard-indicador {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .dashboard-icono {
        width: 49px;
        height: 49px;
        min-width: 49px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 21px;
    }

    .dashboard-indicador small {
        display: block;
        color: #99858d;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .dashboard-indicador h3 {
        font-size: 23px;
        font-weight: 750;
        color: #482c37;
        margin: 0;
    }

    .dashboard-seccion-titulo {
        font-size: 16px;
        font-weight: 750;
        color: #492d38;
        margin-bottom: 5px;
    }

    .dashboard-subtitulo {
        font-size: 12px;
        color: #a18c94;
    }

    .dashboard-grafica {
        height: 280px;
        position: relative;
        margin-top: 18px;
    }

    .dashboard-cita {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1e8ec;
    }

    .dashboard-cita:last-child {
        border-bottom: none;
    }

    .dashboard-cita strong {
        font-size: 13px;
        color: #543945;
    }

    .dashboard-cita small {
        display: block;
        font-size: 11px;
        color: #a18c94;
        margin-top: 5px;
    }

    .dashboard-hora {
        color: #762d4a;
        font-weight: 700;
        font-size: 12px;
        white-space: nowrap;
    }

    .dashboard-estado {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 30px;
        font-size: 10px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .dashboard-estado.pendiente {
        background: #fff3d9;
        color: #98701e;
    }

    .dashboard-estado.confirmada {
        background: #e9f0ff;
        color: #4166a5;
    }

    .dashboard-estado.completada {
        background: #e8f5ed;
        color: #28784b;
    }

    .dashboard-estado.cancelada {
        background: #fbe9ed;
        color: #a33f59;
    }

    .dashboard-acceso {
        text-decoration: none;
        background: #fff;
        border: 1px solid #f1e6ea;
        border-radius: 13px;
        padding: 17px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #593b47;
        font-weight: 650;
        font-size: 13px;
        transition: .2s ease;
    }

    .dashboard-acceso:hover {
        border-color: #c88da2;
        background: #fff9fb;
        color: #762d4a;
        transform: translateY(-2px);
    }

    .dashboard-acceso i {
        font-size: 20px;
        color: #762d4a;
    }

    .dashboard-vacio {
        padding: 30px 10px;
        text-align: center;
        color: #a18c94;
        font-size: 13px;
    }

    @media(max-width: 768px) {
        .dashboard-header h2 {
            font-size: 22px;
        }

        .dashboard-card {
            padding: 17px;
        }
    }
</style>

<div class="dashboard-relux">

    {{-- Encabezado --}}
    <div class="dashboard-header">
        <div>
            <h2>Panel de administración</h2>
            <p>Bienvenido al centro de gestión de RELUX.</p>
        </div>

        <div class="dashboard-fecha">
            <i class="bi bi-calendar3 me-2"></i>
            {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
        </div>
    </div>

    {{-- Indicadores --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-indicador">
                    <div class="dashboard-icono" style="background:#f8eaf0;color:#762d4a;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <small>Usuarios registrados</small>
                        <h3>{{ $totalUsuarios }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-indicador">
                    <div class="dashboard-icono" style="background:#eeeaff;color:#7561ad;">
                        <i class="bi bi-scissors"></i>
                    </div>
                    <div>
                        <small>Servicios registrados</small>
                        <h3>{{ $totalServicios }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-indicador">
                    <div class="dashboard-icono" style="background:#e9f0ff;color:#4166a5;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <small>Citas de hoy</small>
                        <h3>{{ $citasHoy }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-indicador">
                    <div class="dashboard-icono" style="background:#e8f5ed;color:#328052;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <small>Ingresos realizados</small>
                        <h3>${{ number_format((float) $ingresosTotales, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Resumen y gráfica --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="dashboard-seccion-titulo">Resumen general</h5>
                <span class="dashboard-subtitulo">Actividad total del negocio.</span>

                <div class="dashboard-cita">
                    <span>Total de citas</span>
                    <strong>{{ $totalCitas }}</strong>
                </div>

                <div class="dashboard-cita">
                    <span>Citas completadas</span>
                    <strong>{{ $citasCompletadas }}</strong>
                </div>

                <div class="dashboard-cita">
                    <span>Citas pendientes</span>
                    <strong>{{ $citasPorEstado['pendiente'] }}</strong>
                </div>

                <div class="dashboard-cita">
                    <span>Citas confirmadas</span>
                    <strong>{{ $citasPorEstado['confirmada'] }}</strong>
                </div>

                <div class="dashboard-cita">
                    <span>Citas canceladas</span>
                    <strong>{{ $citasPorEstado['cancelada'] }}</strong>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="dashboard-card">
                <h5 class="dashboard-seccion-titulo">Distribución de citas</h5>
                <span class="dashboard-subtitulo">
                    Cantidad de citas según su estado actual.
                </span>

                <div class="dashboard-grafica">
                    <canvas id="graficaDashboard"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- Próximas citas y recientes --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="dashboard-card">
                <h5 class="dashboard-seccion-titulo">Próximas citas</h5>
                <span class="dashboard-subtitulo">
                    Reservas futuras que no están canceladas.
                </span>

                @forelse($proximasCitas as $cita)
                    <div class="dashboard-cita">
                        <div>
                            <strong>
                                {{ $cita->usuario->nombre ?? $cita->usuario->name ?? 'Cliente' }}
                            </strong>

                            <small>
                                {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                ·
                                {{ $cita->hora ? substr($cita->hora, 0, 5) : '--' }}
                                @if($cita->hora_fin)
                                    - {{ substr($cita->hora_fin, 0, 5) }}
                                @endif
                            </small>
                        </div>

                        <span class="dashboard-estado {{ $cita->estado }}">
                            {{ $cita->estado }}
                        </span>
                    </div>
                @empty
                    <div class="dashboard-vacio">
                        No hay próximas citas registradas.
                    </div>
                @endforelse

                <a href="{{ route('admin.citas') }}" class="dashboard-acceso mt-3">
                    <i class="bi bi-calendar2-week"></i>
                    Gestionar todas las citas
                    <i class="bi bi-arrow-right ms-auto"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card">
                <h5 class="dashboard-seccion-titulo">Actividad reciente</h5>
                <span class="dashboard-subtitulo">
                    Últimas citas registradas en el sistema.
                </span>

                @forelse($citasRecientes as $cita)
                    <div class="dashboard-cita">
                        <div>
                            <strong>
                                Cita #{{ $cita->id }}
                                ·
                                {{ $cita->usuario->nombre ?? $cita->usuario->name ?? 'Cliente' }}
                            </strong>

                            <small>
                                {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                ·
                                {{ $cita->hora ? substr($cita->hora, 0, 5) : '--' }}
                            </small>
                        </div>

                        <span class="dashboard-estado {{ $cita->estado }}">
                            {{ $cita->estado }}
                        </span>
                    </div>
                @empty
                    <div class="dashboard-vacio">
                        Todavía no hay citas registradas.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Accesos rápidos --}}
    <div class="mb-3">
        <h5 class="dashboard-seccion-titulo">Accesos rápidos</h5>
        <span class="dashboard-subtitulo">
            Gestiona los módulos principales de RELUX.
        </span>
    </div>

    <div class="row g-3">

        <div class="col-md-3 col-6">
            <a href="{{ route('admin.servicios.index') }}" class="dashboard-acceso">
                <i class="bi bi-scissors"></i>
                Servicios
                <i class="bi bi-arrow-right ms-auto"></i>
            </a>
        </div>

        <div class="col-md-3 col-6">
            <a href="{{ route('admin.citas') }}" class="dashboard-acceso">
                <i class="bi bi-calendar-check"></i>
                Citas
                <i class="bi bi-arrow-right ms-auto"></i>
            </a>
        </div>

        <div class="col-md-3 col-6">
            <a href="{{ route('admin.horarios') }}" class="dashboard-acceso">
                <i class="bi bi-clock"></i>
                Horarios
                <i class="bi bi-arrow-right ms-auto"></i>
            </a>
        </div>

        <div class="col-md-3 col-6">
            <a href="{{ route('admin.reportes') }}" class="dashboard-acceso">
                <i class="bi bi-bar-chart"></i>
                Reportes
                <i class="bi bi-arrow-right ms-auto"></i>
            </a>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const contexto = document.getElementById('graficaDashboard');

    if (contexto) {
        new Chart(contexto, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Confirmadas', 'Completadas', 'Canceladas'],
                datasets: [{
                    data: [
                        {{ $citasPorEstado['pendiente'] }},
                        {{ $citasPorEstado['confirmada'] }},
                        {{ $citasPorEstado['completada'] }},
                        {{ $citasPorEstado['cancelada'] }}
                    ],
                    backgroundColor: [
                        '#e8b95c',
                        '#7295d0',
                        '#65a982',
                        '#c66b83'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '67%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
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
