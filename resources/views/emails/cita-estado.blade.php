<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de tu cita - RELUX</title>
</head>
<body style="margin:0;padding:0;background-color:#fff6f2;font-family:Arial,Helvetica,sans-serif;color:#4a2332;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        Tu cita en RELUX ha cambiado de estado. Consulta los detalles aquí.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fff6f2;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 8px 28px rgba(74,35,50,.10);">
                    <tr>
                        <td align="center" style="background:#4a2332;padding:30px 24px;">
                            <div style="font-family:Georgia,serif;font-size:30px;letter-spacing:4px;font-weight:bold;color:#ffe3d8;">RELUX</div>
                            <div style="margin-top:7px;font-size:11px;letter-spacing:3px;color:#e9b9bd;">SALÓN DE BELLEZA</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:34px 32px 18px;">
                            <p style="margin:0 0 12px;font-size:14px;color:#8a5a69;">Hola, {{ $usuario->nombre ?? 'cliente' }}.</p>
                            <h1 style="margin:0 0 16px;font-family:Georgia,serif;font-size:27px;line-height:1.25;color:#4a2332;">
                                Tu cita tiene una actualización
                            </h1>
                            <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#6f5260;">
                                Queremos mantenerte al tanto de tu experiencia en RELUX. El estado de tu cita ha cambiado.
                            </p>

                            <div style="background:#fdecee;border:1px solid #efd3d6;border-radius:12px;padding:18px 20px;margin-bottom:24px;">
                                <div style="font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#8a5a69;margin-bottom:8px;">Nuevo estado</div>
                                <div style="font-family:Georgia,serif;font-size:24px;font-weight:bold;color:#c4486a;">
                                    {{ ucfirst($nuevoEstado) }}
                                </div>
                                <div style="margin-top:8px;font-size:12px;color:#8a5a69;">
                                    Estado anterior: {{ ucfirst($estadoAnterior) }}
                                </div>
                            </div>

                            <h2 style="margin:0 0 14px;font-family:Georgia,serif;font-size:19px;color:#4a2332;">Detalles de tu cita #{{ $cita->id }}</h2>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;">
                                <tr>
                                    <td style="padding:9px 0;color:#8a5a69;border-bottom:1px solid #f3e4e5;">Fecha</td>
                                    <td align="right" style="padding:9px 0;color:#4a2332;font-weight:bold;border-bottom:1px solid #f3e4e5;">
                                        {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0;color:#8a5a69;border-bottom:1px solid #f3e4e5;">Horario</td>
                                    <td align="right" style="padding:9px 0;color:#4a2332;font-weight:bold;border-bottom:1px solid #f3e4e5;">
                                        {{ substr($cita->hora, 0, 5) }}@if($cita->hora_fin) – {{ substr($cita->hora_fin, 0, 5) }}@endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0;color:#8a5a69;">Total</td>
                                    <td align="right" style="padding:9px 0;color:#4a2332;font-weight:bold;">
                                        ${{ number_format((float) $cita->total, 0, ',', '.') }} COP
                                    </td>
                                </tr>
                            </table>

                            @if($cita->citasServicios && $cita->citasServicios->isNotEmpty())
                                <h2 style="margin:24px 0 10px;font-family:Georgia,serif;font-size:18px;color:#4a2332;">Servicios reservados</h2>
                                <ul style="margin:0;padding-left:20px;color:#6f5260;font-size:14px;line-height:1.8;">
                                    @foreach($cita->citasServicios as $detalle)
                                        <li>{{ $detalle->servicio->nombre ?? 'Servicio' }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <p style="margin:26px 0 8px;font-size:14px;line-height:1.7;color:#6f5260;">
                                Puedes consultar tus reservas y sus estados desde la sección <strong>Mis Citas</strong> de tu cuenta.
                            </p>
                            <p style="margin:0;font-size:14px;color:#6f5260;">Gracias por elegir RELUX.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="background:#fff9f7;padding:22px 24px;border-top:1px solid #f3e4e5;">
                            <div style="font-family:Georgia,serif;font-size:16px;letter-spacing:2px;color:#4a2332;font-weight:bold;">RELUX</div>
                            <p style="margin:7px 0 0;font-size:11px;line-height:1.6;color:#9a7b85;">
                                Este es un mensaje automático. Por favor, no respondas a este correo.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
