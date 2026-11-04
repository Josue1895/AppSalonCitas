<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CitasExport;

class ReportController extends Controller
{
    /**
     * Aplica los filtros de fechas a una consulta de citas.
     */
    private function consultaCitas($fechaInicio = null, $fechaFin = null)
    {
        $consulta = Appointment::query();

        if ($fechaInicio) {
            $consulta->whereDate('fecha', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $consulta->whereDate('fecha', '<=', $fechaFin);
        }

        return $consulta;
    }

    /**
     * Panel principal de reportes.
     */
    public function index(Request $request)
    {
        $request->validate([
            'fecha_inicio' => ['nullable', 'date_format:Y-m-d'],
            'fecha_fin' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_inicio',
            ],
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $citas = $this->consultaCitas($fechaInicio, $fechaFin)
            ->with('usuario')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        $totalCitas = $citas->count();

        $citasCompletadas = $citas->where('estado', 'completada')->count();
        $citasCanceladas = $citas->where('estado', 'cancelada')->count();
        $citasPendientes = $citas->where('estado', 'pendiente')->count();
        $citasConfirmadas = $citas->where('estado', 'confirmada')->count();

        // Los ingresos se calculan con el total histórico de cada cita.
        $citasRealizadas = $citas->where('estado', 'completada');

        $ingresos = $citasRealizadas->sum('total');

        $ingresosPorFecha = $citasRealizadas
            ->groupBy(function ($cita) {
                return $cita->fecha->format('Y-m-d');
            })
            ->map(function ($grupo, $fecha) {
                return [
                    'fecha' => $fecha,
                    'ingresos' => (float) $grupo->sum('total'),
                ];
            })
            ->sortKeys()
            ->values();

        $serviciosMasSolicitados = Service::query()
            ->withCount([
                'citasServicios as cantidad_solicitudes' => function ($query) use ($fechaInicio, $fechaFin) {
                    $query->whereHas('cita', function ($citaQuery) use ($fechaInicio, $fechaFin) {
                        $citaQuery->where('estado', '!=', 'cancelada');

                        if ($fechaInicio) {
                            $citaQuery->whereDate('fecha', '>=', $fechaInicio);
                        }

                        if ($fechaFin) {
                            $citaQuery->whereDate('fecha', '<=', $fechaFin);
                        }
                    });
                },
            ])
            ->orderByDesc('cantidad_solicitudes')
            ->take(5)
            ->get();

        return view('admin.reportes', compact(
            'citas',
            'ingresos',
            'fechaInicio',
            'fechaFin',
            'totalCitas',
            'citasCompletadas',
            'citasCanceladas',
            'citasPendientes',
            'citasConfirmadas',
            'ingresosPorFecha',
            'serviciosMasSolicitados'
        ));
    }

    /**
     * Descargar reporte en PDF.
     */
    public function pdf(Request $request)
    {
        $request->validate([
            'fecha_inicio' => ['nullable', 'date_format:Y-m-d'],
            'fecha_fin' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_inicio',
            ],
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        // Cargar los servicios y sus precios históricos.
        $citas = $this->consultaCitas($fechaInicio, $fechaFin)
            ->with('citasServicios.servicio')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        $ingresos = $citas
            ->where('estado', 'completada')
            ->sum('total');

        $citasCompletadas = $citas->where('estado', 'completada')->count();
        $citasCanceladas = $citas->where('estado', 'cancelada')->count();

        $pdf = Pdf::loadView('admin.reportes-pdf', compact(
            'citas',
            'ingresos',
            'fechaInicio',
            'fechaFin',
            'citasCompletadas',
            'citasCanceladas'
        ));

        return $pdf->download('reporte-citas.pdf');
    }

    /**
     * Descargar reporte en Excel.
     */
    public function excel(Request $request)
    {
        $request->validate([
            'fecha_inicio' => ['nullable', 'date_format:Y-m-d'],
            'fecha_fin' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_inicio',
            ],
        ]);

        return Excel::download(
            new CitasExport($request->fecha_inicio, $request->fecha_fin),
            'reporte-citas.xlsx'
        );
    }
}
