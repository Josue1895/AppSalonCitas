<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>Reporte de Citas</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        .ingresos {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }

    </style>

</head>

<body>

    <h1>Reporte de Citas - AppSalon</h1>

    @if($fechaInicio && $fechaFin)

        <p>
            Período:
            {{ $fechaInicio }}
            -
            {{ $fechaFin }}
        </p>

    @endif

    <table>

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

            @foreach($citas as $cita)

                <tr>

                    <td>{{ $cita->id }}</td>
                    <td>{{ $cita->fecha }}</td>
                    <td>{{ $cita->hora }}</td>
                    <td>${{ $cita->total }}</td>
                    <td>{{ $cita->estado }}</td>

                </tr>

            @endforeach

        </tbody>

    </table>

    <div class="ingresos">

        Ingresos Totales:
        ${{ $ingresos }}

    </div>

</body>

</html>