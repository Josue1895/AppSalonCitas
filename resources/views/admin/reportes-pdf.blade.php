
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Citas - Relux</title>

    <style>
        @page {
            margin: 35px 35px 45px 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #49313b;
        }

        .encabezado {
            background-color: #512536;
            color: #ffffff;
            padding: 22px;
            text-align: center;
        }

        .encabezado h1 {
            font-size: 25px;
            margin: 0 0 6px;
            letter-spacing: 1px;
        }

        .encabezado p {
            margin: 0;
            color: #f4dce5;
            font-size: 10px;
        }

        .periodo {
            margin-top: 20px;
            padding: 12px;
            background-color: #fbf0f4;
            border-left: 4px solid #762d4a;
        }

        .periodo strong {
            color: #762d4a;
        }

        .seccion-titulo {
            color: #762d4a;
            font-size: 14px;
            font-weight: bold;
            margin-top: 24px;
            margin-bottom: 12px;
        }

        .resumen {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7px;
        }

        .resumen td {
            width: 25%;
            padding: 13px 8px;
            background-color: #fbf3f6;
            border: 1px solid #f0dfe6;
            text-align: center;
        }

        .resumen .etiqueta {
            display: block;
            color: #927582;
            font-size: 9px;
            margin-bottom: 7px;
        }

        .resumen .valor {
            display: block;
            color: #512536;
            font-size: 17px;
            font-weight: bold;
        }

        .resumen .ingreso {
            color: #28784b;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .tabla thead {
            background-color: #762d4a;
            color: #ffffff;
        }

        .tabla th {
            padding: 9px 5px;
            font-size: 8px;
            text-align: center;
        }

        .tabla td {
            padding: 8px 5px;
            border-bottom: 1px solid #eadde2;
            text-align: center;
            font-size: 8px;
        }

        .tabla tbody tr:nth-child(even) {
            background-color: #fcf7f9;
        }

        .servicios {
            text-align: left !important;
            line-height: 1.5;
        }

        .estado {
            font-weight: bold;
            text-transform: capitalize;
        }

        .completada {
            color: #28784b;
        }

        .pendiente {
            color: #98701e;
        }

        .confirmada {
            color: #4166a5;
        }

        .cancelada {
            color: #a33f59;
        }

        .sin-registros {
            text-align: center;
            padding: 25px;
            color: #927582;
        }

        .nota-ingresos {
            margin-top: 16px;
            padding: 12px;
            background-color: #eef7f0;
            border: 1px solid #d7ebdc;
            color: #28784b;
            font-size: 10px;
        }

        .pie {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eadde2;
            text-align: center;
            color: #9b8790;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <div class="encabezado">
        <h1>RELUX</h1>
        <p>REPORTE ADMINISTRATIVO DE CITAS</p>
    </div>

    <div class="periodo">
        <strong>Periodo consultado:</strong>

        @if($fechaInicio || $fechaFin)
            {{ $fechaInicio ? \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') : 'Sin fecha inicial' }}
            -
            {{ $fechaFin ? \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') : 'Sin fecha final' }}
        @else
            Todas las fechas registradas
        @endif
    </div>

    <h2 class="seccion-titulo">Resumen general</h2>

    <table class="resumen">
        <tr>
            <td>
                <span class="etiqueta">Total de citas</span>
                <span class="valor">{{ $citas->count() }}</span>
            </td>

            <td>
                <span class="etiqueta">Completadas</span>
                <span class="valor">{{ $citasCompletadas }}</span>
            </td>

            <td>
                <span class="etiqueta">Canceladas</span>
                <span class="valor">{{ $citasCanceladas }}</span>
            </td>

            <td>
                <span class="etiqueta">Ingresos realizados</span>
                <span class="valor ingreso">
                    ${{ number_format((float) $ingresos, 0, ',', '.') }}
                </span>
            </td>
        </tr>
    </table>

    <h2 class="seccion-titulo">Detalle de citas</h2>

    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Hora inicial</th>
                <th>Hora final</th>
                <th>Servicios y precios</th>
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

                    <td class="servicios">
                        @forelse($cita->citasServicios as $detalle)
                            {{ $detalle->servicio->nombre ?? 'Servicio no disponible' }}
                            (${{ number_format((float) $detalle->precio_unitario, 0, ',', '.') }})

                            @if(!$loop->last)
                                <br>
                            @endif
                        @empty
                            Sin servicios registrados
                        @endforelse
                    </td>

                    <td>
                        ${{ number_format((float) $cita->total, 0, ',', '.') }}
                    </td>

                    <td class="estado {{ strtolower($cita->estado) }}">
                        {{ $cita->estado }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="sin-registros">
                        No se encontraron citas para este periodo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="nota-ingresos">
        <strong>Nota:</strong>
        Los ingresos corresponden exclusivamente a las citas completadas.
        Las citas pendientes, confirmadas y canceladas no se contabilizan
        como ingresos realizados.
    </div>

    <div class="pie">
        RELUX · Sistema de gestión de citas · Reporte generado automáticamente
    </div>

</body>
</html>
