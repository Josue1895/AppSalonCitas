<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>

    <h2>¡Cita reservada correctamente!</h2>

    <p>
        Hola {{ $cita->usuario->nombre }},
    </p>

    <p>
        Su cita ha sido registrada exitosamente.
    </p>

    <ul>
        <li><strong>Fecha:</strong> {{ $cita->fecha }}</li>
        <li><strong>Hora:</strong> {{ $cita->hora }}</li>
        <li><strong>Estado:</strong> {{ ucfirst($cita->estado) }}</li>
        <li><strong>Total:</strong> ${{ $cita->total }}</li>
    </ul>

    <p>
        Gracias por utilizar AppSalon.
    </p>

</body>
</html>