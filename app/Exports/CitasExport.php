<?php

namespace App\Exports;

use App\Models\Appointment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CitasExport implements FromArray, WithStyles, WithColumnWidths, WithColumnFormatting, WithEvents
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio = null, $fechaFin = null)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function array(): array
    {
        $consulta = Appointment::query()
            ->with(['citasServicios.servicio'])
            ->select(
                'id',
                'fecha',
                'hora',
                'hora_fin',
                'total',
                'estado'
            );

        if ($this->fechaInicio) {
            $consulta->whereDate('fecha', '>=', $this->fechaInicio);
        }

        if ($this->fechaFin) {
            $consulta->whereDate('fecha', '<=', $this->fechaFin);
        }

        $citas = $consulta
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        $totalCitas = $citas->count();

        $completadas = $citas->where('estado', 'completada')->count();
        $canceladas = $citas->where('estado', 'cancelada')->count();
        $pendientes = $citas->where('estado', 'pendiente')->count();
        $confirmadas = $citas->where('estado', 'confirmada')->count();

        // Solo las citas completadas generan ingresos.
        $ingresos = $citas
            ->where('estado', 'completada')
            ->sum('total');

        if ($this->fechaInicio || $this->fechaFin) {
            $inicio = $this->fechaInicio
                ? Carbon::parse($this->fechaInicio)->format('d/m/Y')
                : 'Sin fecha inicial';

            $fin = $this->fechaFin
                ? Carbon::parse($this->fechaFin)->format('d/m/Y')
                : 'Sin fecha final';

            $periodo = $inicio . ' - ' . $fin;
        } else {
            $periodo = 'Todas las fechas registradas';
        }

        $filas = [
            ['RELUX - REPORTE ADMINISTRATIVO DE CITAS'],
            ['Periodo consultado:', $periodo],
            [],
            ['RESUMEN GENERAL'],
            ['Total de citas:', $totalCitas],
            ['Citas completadas:', $completadas],
            ['Citas pendientes:', $pendientes],
            ['Citas confirmadas:', $confirmadas],
            ['Citas canceladas:', $canceladas],
            ['Ingresos realizados:', (float) $ingresos],
            [],
            [
                'ID',
                'Fecha',
                'Hora inicial',
                'Hora final',
                'Servicios y precios',
                'Total',
                'Estado',
            ],
        ];

        foreach ($citas as $cita) {
            $detalleServicios = $cita->citasServicios
                ->map(function ($detalle) {
                    $nombre = $detalle->servicio->nombre
                        ?? 'Servicio no disponible';

                    return $nombre . ' ($' .
                        number_format((float) $detalle->precio_unitario, 0, ',', '.') .
                        ')';
                })
                ->implode("\n");

            $filas[] = [
                $cita->id,
                Carbon::parse($cita->fecha)->format('d/m/Y'),
                $cita->hora ? substr($cita->hora, 0, 5) : '--',
                $cita->hora_fin ? substr($cita->hora_fin, 0, 5) : '--',
                $detalleServicios ?: 'Sin servicios registrados',
                (float) $cita->total,
                ucfirst($cita->estado),
            ];
        }

        return $filas;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 16,
            'C' => 16,
            'D' => 16,
            'E' => 42,
            'F' => 18,
            'G' => 18,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"$"#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 17,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '512536'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],

            2 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '762D4A'],
                ],
            ],

            4 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => '762D4A'],
                ],
            ],

            'A5:A10' => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '593B47'],
                ],
            ],

            12 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '762D4A'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $ultimaFila = $sheet->getHighestRow();

                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('B2:G2');

                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getRowDimension(12)->setRowHeight(25);

                $sheet->freezePane('A13');

                $sheet->setAutoFilter('A12:G' . max(12, $ultimaFila));

                if ($ultimaFila >= 13) {

                    $sheet->getStyle('A13:G' . $ultimaFila)
                        ->getAlignment()
                        ->setVertical('center');

                    $sheet->getStyle('E13:E' . $ultimaFila)
                        ->getAlignment()
                        ->setWrapText(true);

                    $sheet->getStyle('A13:G' . $ultimaFila)
                        ->getBorders()
                        ->getBottom()
                        ->setBorderStyle('thin')
                        ->getColor()
                        ->setRGB('EADDE2');

                    for ($fila = 13; $fila <= $ultimaFila; $fila++) {

                        $estado = strtolower(
                            (string) $sheet->getCell('G' . $fila)->getValue()
                        );

                        $colores = [
                            'completada' => '28784B',
                            'pendiente' => '98701E',
                            'confirmada' => '4166A5',
                            'cancelada' => 'A33F59',
                        ];

                        if (isset($colores[$estado])) {
                            $sheet->getStyle('G' . $fila)
                                ->getFont()
                                ->setBold(true)
                                ->getColor()
                                ->setRGB($colores[$estado]);
                        }
                    }
                }
            },
        ];
    }
}
