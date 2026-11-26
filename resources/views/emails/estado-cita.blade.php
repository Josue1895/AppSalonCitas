<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>

    <h2>Actualización de su cita</h2>

    <p>
        Hola {{ $cita->usuario->nombre }},
    </p>

    <p>
        El estado de su cita ha sido actualizado.
    </p>

    <ul>
        <li><strong>Fecha:</strong> {{ $cita->fecha }}</li>
        <li><strong>Hora:</strong> {{ $cita->hora }}</li>
        <li><strong>Estado:</strong> {{ ucfirst($cita->estado) }}</li>
    </ul>

    <p>
        Gracias por utilizar AppSalon.
    </p>

</body>
</html>