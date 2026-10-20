<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;

class CitasExport implements FromCollection
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio = null, $fechaFin = null)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $citas = Appointment::select(
            'id',
            'fecha',
            'hora',
            'total',
            'estado'
        );

        if ($this->fechaInicio && $this->fechaFin) {
            $citas->whereBetween('fecha', [
                $this->fechaInicio,
                $this->fechaFin
            ]);
        }

        return $citas->get();
    }
}