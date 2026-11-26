<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CitasExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $citas = Appointment::query();

        if ($fechaInicio && $fechaFin) {
            $citas->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        $citas = $citas->orderBy('fecha', 'desc')->get();

        $ingresos = $citas->sum('total');

        return view('admin.reportes', compact(
            'citas',
            'ingresos',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function pdf(Request $request)
    {
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $citas = Appointment::query();

        if ($fechaInicio && $fechaFin) {
            $citas->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        $citas = $citas->orderBy('fecha', 'desc')->get();

        $ingresos = $citas->sum('total');

        $pdf = Pdf::loadView('admin.reportes-pdf', compact(
            'citas',
            'ingresos',
            'fechaInicio',
            'fechaFin'
        ));

        return $pdf->download('reporte-citas.pdf');
    }


public function excel(Request $request)
{
    return Excel::download(
        new CitasExport(
            $request->fecha_inicio,
            $request->fecha_fin
        ),
        'reporte-citas.xlsx'
    );
}


}