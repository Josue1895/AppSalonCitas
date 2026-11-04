<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña - RELUX</title>
</head>
<body style="margin:0;padding:0;background-color:#fff6f2;font-family:Arial,Helvetica,sans-serif;color:#4a2332;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        Recibimos una solicitud para restablecer la contraseña de tu cuenta RELUX.
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
                        <td style="padding:36px 32px 18px;">
                            <p style="margin:0 0 12px;font-size:14px;color:#8a5a69;">
                                Hola, {{ $usuario->nombre ?? 'cliente' }}.
                            </p>

                            <h1 style="margin:0 0 16px;font-family:Georgia,serif;font-size:27px;line-height:1.25;color:#4a2332;">
                                Recuperemos el acceso a tu cuenta
                            </h1>

                            <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#6f5260;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta RELUX. Para crear una nueva contraseña, utiliza el siguiente botón.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:28px auto;">
                                <tr>
                                    <td align="center" bgcolor="#4a2332" style="border-radius:9px;">
                                        <a href="{{ $link }}" target="_blank" style="display:inline-block;padding:15px 30px;font-size:15px;font-weight:bold;color:#ffffff;text-decoration:none;border-radius:9px;">
                                            Restablecer mi contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <div style="background:#fdecee;border:1px solid #efd3d6;border-radius:12px;padding:17px 18px;margin:24px 0;">
                                <p style="margin:0 0 8px;font-size:13px;font-weight:bold;color:#4a2332;">
                                    ¿No funciona el botón?
                                </p>
                                <p style="margin:0 0 9px;font-size:12px;line-height:1.6;color:#8a5a69;">
                                    Copia y pega este enlace en la barra de direcciones de tu navegador:
                                </p>
                                <a href="{{ $link }}" style="font-size:12px;line-height:1.6;color:#c4486a;word-break:break-all;">
                                    {{ $link }}
                                </a>
                            </div>

                            <p style="margin:22px 0 10px;font-size:13px;line-height:1.7;color:#6f5260;">
                                Si no solicitaste este cambio, puedes ignorar este mensaje. Tu contraseña seguirá siendo la misma.
                            </p>

                            <p style="margin:0;font-size:13px;line-height:1.7;color:#6f5260;">
                                Por seguridad, no compartas este enlace con otras personas.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background:#fff9f7;padding:22px 24px;border-top:1px solid #f3e4e5;">
                            <div style="font-family:Georgia,serif;font-size:16px;letter-spacing:2px;color:#4a2332;font-weight:bold;">RELUX</div>
                            <p style="margin:7px 0 0;font-size:11px;line-height:1.6;color:#9a7b85;">
                                Salón de belleza · Este es un mensaje automático, por favor no respondas a este correo.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
