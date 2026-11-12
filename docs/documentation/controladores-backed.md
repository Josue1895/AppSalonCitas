# Controladores Backed

## Controladores del backend

### 1. Introducción

El backend de **RELUX – AppSalonCitas** contiene la lógica que permite gestionar los usuarios, los servicios, las citas, los horarios de atención y los reportes del sistema.

Está desarrollado con **PHP y Laravel**. Los controladores reciben las solicitudes realizadas desde las vistas, validan la información, interactúan con los modelos y la base de datos y devuelven respuestas al usuario mediante vistas o redirecciones.

Los controladores principales se encuentran en:

```
app/Http/Controllers/
```

### 2. Estructura de los controladores

El proyecto cuenta con los siguientes controladores:

| Controlador                   | Responsabilidad                                              |
| ----------------------------- | ------------------------------------------------------------ |
| `AuthController.php`          | Registro, inicio y cierre de sesión.                         |
| `AppointmentController.php`   | Consulta, creación, edición y cancelación de citas.          |
| `ServiceController.php`       | Consulta y administración de servicios.                      |
| `AdminController.php`         | Panel administrativo, usuarios, estados de citas y horarios. |
| `ReportController.php`        | Consulta y exportación de reportes en PDF y Excel.           |
| `PasswordResetController.php` | Recuperación y actualización de contraseñas.                 |

También se encuentra el controlador base `Controller.php`, del cual heredan los demás controladores.

***

### 3. AuthController

**Archivo:** `app/Http/Controllers/AuthController.php`

Este controlador administra los procesos de autenticación de los usuarios. Permite mostrar los formularios de registro e inicio de sesión, registrar nuevas cuentas, verificar credenciales y cerrar sesiones.

#### Funciones principales

* Mostrar el formulario de registro.
* Registrar usuarios nuevos.
* Mostrar el formulario de inicio de sesión.
* Validar las credenciales del usuario.
* Redirigir según el rol del usuario.
* Cerrar la sesión de manera segura.

#### Ejemplo de código: inicio de sesión

```
public function login(LoginRequest $request)
{
    $credenciales = [
        'email' => $request->email,
        'password' => $request->password
    ];

    if (Auth::attempt($credenciales)) {
        $request->session()->regenerate();
        $usuario = Auth::user();

        return redirect()->route(
            $usuario->admin ? 'admin.index' : 'home'
        )->with('success', 'Bienvenido');
    }

    return back()->withErrors([
        'email' => 'Credenciales incorrectas'
    ])->onlyInput('email');
}
```

#### Explicación

El método recibe las credenciales mediante `LoginRequest` y utiliza `Auth::attempt()` para comprobarlas.

Cuando la autenticación es correcta, regenera la sesión y consulta el usuario autenticado. Después, verifica su atributo `admin` para dirigirlo al panel administrativo o a la página principal.

Si las credenciales no son válidas, devuelve al usuario al formulario con un mensaje de error.

#### Cierre de sesión

El método `logout()` utiliza `Auth::logout()` para cerrar la sesión, invalida los datos de sesión y genera un nuevo token de seguridad.

***

### 4. AppointmentController

**Archivo:** `app/Http/Controllers/AppointmentController.php`

Este controlador gestiona las operaciones relacionadas con las citas de los clientes. Es uno de los componentes principales del sistema, porque coordina la selección de servicios, la disponibilidad de horarios, la duración de las citas y el registro de la información.

#### Funciones principales

* Mostrar los servicios disponibles y los horarios.
* Registrar una nueva cita.
* Consultar y editar citas propias.
* Validar que solo se puedan editar citas pendientes.
* Calcular el precio y la duración de los servicios seleccionados.
* Comprobar que la cita se encuentre dentro del horario de atención.
* Evitar cruces con otras citas.
* Registrar los servicios asociados a cada cita.
* Enviar el correo de confirmación cuando se registra una cita.
* Permitir cancelar citas pendientes.

#### Ejemplo de código: consulta de servicios y horarios

```
public function index()
{
    $servicios = Service::where('activo', 1)->get();

    $horarios = Horario::orderBy('dia_semana')
        ->get()
        ->keyBy('dia_semana');

    return view(
        'appointments.index',
        compact('servicios', 'horarios')
    );
}
```

#### Explicación

El método consulta los servicios que se encuentran activos y obtiene los horarios organizados por día de la semana.

Después, envía ambas colecciones a la vista `appointments.index`, donde el cliente puede consultar la información necesaria para realizar una reserva.

#### Ejemplo de código: validación de disponibilidad y duración

El método privado `guardarCita()` concentra la lógica utilizada para registrar una cita nueva o actualizar una existente.

Uno de sus procesos principales consiste en calcular la duración total de los servicios seleccionados y determinar la hora de finalización:

```
$duracionTotal = (int) $servicios->sum('duracion');
$total = $servicios->sum('precio');

if ($duracionTotal <= 0) {
    throw ValidationException::withMessages([
        'servicios' => 'La duración de los servicios seleccionados no es válida.',
    ]);
}

$horaInicio = Carbon::createFromFormat(
    'H:i',
    $request->hora
);

$horaFin = $horaInicio->copy()->addMinutes(
    $duracionTotal
);
```

Posteriormente, el sistema consulta el horario correspondiente al día seleccionado y comprueba que la duración de la cita no supere la hora de cierre:

```
$horario = Horario::where('dia_semana', $diaSemana)
    ->lockForUpdate()
    ->first();

if (! $horario || ! $horario->activo) {
    throw ValidationException::withMessages([
        'fecha' => 'No hay atención disponible para el día seleccionado.',
    ]);
}

$horaCierre = Carbon::createFromFormat(
    'H:i:s',
    $horario->hora_fin
);

if ($horaFin->greaterThan($horaCierre)) {
    throw ValidationException::withMessages([
        'hora' => 'La duración de los servicios supera el horario de atención. Selecciona una hora más temprana.',
    ]);
}
```

#### Explicación

El sistema suma las duraciones y los precios de los servicios seleccionados. Con esa información calcula la hora de finalización y el total de la cita.

Luego consulta la configuración de horarios para el día correspondiente. Si el día no está habilitado o la duración supera la hora de cierre, se impide el registro.

La operación se realiza dentro de una transacción de base de datos para mantener agrupadas las operaciones de consulta y almacenamiento.

#### Control de cruces entre citas

El controlador también consulta las citas existentes de la misma fecha, excluyendo las canceladas. Compara el intervalo de la nueva cita con los intervalos ya registrados.

```
if (
    $inicioNuevo < $finExistente->format('H:i:s') &&
    $finNuevo > $inicioExistente->format('H:i:s')
) {
    throw ValidationException::withMessages([
        'hora' => 'Ese horario ya está ocupado. Selecciona otra hora para tu cita.',
    ]);
}
```

#### Registro de la cita y sus servicios

Cuando las validaciones se cumplen, se almacena la cita y se registran los servicios asociados en la tabla intermedia mediante el modelo `AppointmentService`.

```
$cita = Appointment::create($datosCita);

foreach ($servicios as $servicio) {
    AppointmentService::create([
        'citaId' => $cita->id,
        'servicioId' => $servicio->id,
        'precio_unitario' => $servicio->precio,
    ]);
}
```

El precio unitario se guarda junto con cada servicio asociado a la cita, conservando el valor registrado en el momento de la reserva.

#### Cancelación de citas

El método `cancelar($id)` verifica que el usuario tenga una sesión activa, que la cita le pertenezca y que su estado sea `pendiente`.

Si cumple las condiciones, cambia el estado a `cancelada` y guarda la modificación.

***

### 5. ServiceController

**Archivo:** `app/Http/Controllers/ServiceController.php`

Este controlador administra la consulta y gestión de los servicios ofrecidos por RELUX.

#### Funciones principales

* Mostrar los servicios activos a los clientes.
* Mostrar todos los servicios en el panel administrativo.
* Mostrar formularios para crear y editar servicios.
* Registrar nuevos servicios.
* Actualizar información de servicios existentes.
* Eliminar servicios.

#### Ejemplo de código: consulta de servicios activos

```
public function index()
{
    $servicios = Service::where('activo', 1)->get();

    return view('services.index', compact('servicios'));
}
```

#### Explicación

El método consulta los servicios cuyo campo `activo` tiene el valor `1` y envía los resultados a la vista `services.index`.

De esta manera, los clientes pueden consultar los servicios disponibles sin mostrar los que se encuentran desactivados.

#### Ejemplo de código: creación de servicios

```
public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|max:60',
        'precio' => 'required|numeric',
        'descripcion' => 'nullable',
        'duracion' => 'required|integer',
    ]);

    Service::create([
        'nombre' => htmlspecialchars($request->nombre),
        'precio' => $request->precio,
        'descripcion' => htmlspecialchars($request->descripcion),
        'duracion' => $request->duracion,
        'activo' => 1
    ]);

    return redirect()
        ->route('admin.servicios.index')
        ->with('success', 'Servicio creado correctamente');
}
```

#### Explicación

Antes de registrar el servicio, se validan los datos recibidos. Se exige un nombre, un precio numérico y una duración entera. La descripción es opcional.

Si los datos cumplen las reglas, se crea el servicio con estado activo y se redirige al listado administrativo mostrando un mensaje de confirmación.

Los métodos `edit()`, `update()` y `destroy()` completan las operaciones de edición, actualización y eliminación de servicios.

***

### 6. AdminController

**Archivo:** `app/Http/Controllers/AdminController.php`

Este controlador concentra las funciones principales del panel administrativo.

Permite consultar indicadores generales, administrar usuarios, modificar roles, consultar las citas, actualizar sus estados y configurar los horarios de atención.

#### Funciones principales

* Mostrar los indicadores del panel administrativo.
* Consultar y buscar usuarios registrados.
* Asignar o retirar el rol de administrador.
* Consultar las citas registradas.
* Cambiar el estado de las citas.
* Enviar notificaciones por correo cuando cambia el estado de una cita.
* Consultar y actualizar los horarios semanales.

#### Ejemplo de código: indicadores del panel

```
$totalUsuarios = User::count();
$totalServicios = Service::count();
$totalCitas = Appointment::count();

$citasHoy = Appointment::whereDate(
    'fecha',
    today()
)->count();

$citasCompletadas = Appointment::where(
    'estado',
    'completada'
)->count();

$ingresosTotales = Appointment::where(
    'estado',
    'completada'
)->sum('total');
```

#### Explicación

El método `index()` obtiene indicadores generales mediante consultas a los modelos `User`, `Service` y `Appointment`.

También calcula la cantidad de citas del día, las citas completadas y los ingresos asociados a las citas completadas. Finalmente, envía los resultados a la vista `admin.index`.

#### Ejemplo de código: actualización del estado de una cita

```
$datos = $request->validate([
    'estado' => 'required|in:pendiente,confirmada,completada,cancelada'
]);

$cita = Appointment::with([
    'usuario',
    'citasServicios.servicio'
])->findOrFail($id);

$estadoAnterior = $cita->estado;
$nuevoEstado = $datos['estado'];

if ($estadoAnterior === $nuevoEstado) {
    return redirect()->route('admin.citas')
        ->with('success', 'La cita conserva el mismo estado. No fue necesario enviar una notificación.');
}

$cita->estado = $nuevoEstado;
$cita->save();
```

#### Explicación

El método `cambiarEstado()` valida que el nuevo estado pertenezca a los valores permitidos.

Después, busca la cita y sus relaciones, compara el estado anterior con el nuevo y guarda el cambio cuando corresponde.

Si el estado cambia, el controlador intenta enviar un correo al cliente mediante la plantilla `emails.cita-estado`. Si el envío falla, el cambio de estado se conserva y se informa que no fue posible enviar la notificación.

#### Administración de horarios

El método `actualizarHorarios()` recibe la configuración semanal y valida que se hayan enviado los siete días.

También comprueba que los horarios activos tengan una hora de inicio y una hora de cierre válidas, y que la apertura sea anterior al cierre.

Los cambios se guardan mediante una transacción para actualizar la configuración semanal.

***

### 7. ReportController

**Archivo:** `app/Http/Controllers/ReportController.php`

Este controlador administra la consulta y generación de reportes del sistema.

Permite filtrar las citas por fechas, consultar estadísticas y descargar reportes en formato PDF y Excel.

#### Funciones principales

* Mostrar el panel de reportes.
* Filtrar citas por fecha inicial y fecha final.
* Calcular citas por estado.
* Calcular ingresos de citas completadas.
* Consultar los servicios más solicitados.
* Generar reportes en PDF.
* Exportar información a Excel.

#### Ejemplo de código: consulta de citas por fechas

```
private function consultaCitas(
    $fechaInicio = null,
    $fechaFin = null
) {
    $consulta = Appointment::query();

    if ($fechaInicio) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $fechaInicio
        );
    }

    if ($fechaFin) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $fechaFin
        );
    }

    return $consulta;
}
```

#### Explicación

Este método privado construye una consulta de citas y aplica los filtros de fecha cuando se proporcionan.

Se reutiliza en los métodos de reportes para evitar repetir la misma lógica de filtrado.

#### Ejemplo de código: exportación a PDF

```
$pdf = Pdf::loadView(
    'admin.reportes-pdf',
    compact(
        'citas',
        'ingresos',
        'fechaInicio',
        'fechaFin',
        'citasCompletadas',
        'citasCanceladas'
    )
);

return $pdf->download('reporte-citas.pdf');
```

#### Explicación

El método `pdf()` obtiene las citas según el periodo solicitado, calcula los indicadores correspondientes y utiliza la vista `admin.reportes-pdf` para generar el documento descargable.

#### Exportación a Excel

El método `excel()` utiliza la clase `CitasExport` y la herramienta de exportación instalada en el proyecto para generar el archivo `reporte-citas.xlsx`.

***

### 8. PasswordResetController

**Archivo:** `app/Http/Controllers/PasswordResetController.php`

Este controlador administra el proceso de recuperación de contraseñas.

Permite solicitar un enlace de recuperación, generar un token, enviarlo al correo registrado y actualizar la contraseña cuando el usuario presenta un token válido.

#### Funciones principales

* Mostrar el formulario para solicitar la recuperación.
* Validar el correo electrónico.
* Generar un token de recuperación.
* Guardar el token asociado al correo.
* Enviar el enlace de recuperación.
* Mostrar el formulario para establecer una nueva contraseña.
* Verificar el token recibido.
* Actualizar la contraseña y eliminar el registro del token utilizado.

#### Ejemplo de código: generación y almacenamiento del token

```
$token = Str::random(64);

DB::table('password_reset_tokens')
    ->updateOrInsert(
        ['email' => $request->email],
        [
            'token' => Hash::make($token),
            'created_at' => now()
        ]
    );

$link = url(
    '/reset-password/' . $token .
    '?email=' . urlencode($request->email)
);
```

#### Explicación

El sistema genera una cadena aleatoria de 64 caracteres y guarda su versión cifrada mediante `Hash::make()` en la tabla `password_reset_tokens`.

El enlace enviado al usuario contiene el token original y el correo asociado a la solicitud.

#### Ejemplo de código: verificación del token

```
if (!Hash::check($request->token, $registro->token)) {
    return back()->withErrors([
        'email' => 'Token inválido'
    ]);
}
```

#### Explicación

Cuando el usuario envía el formulario de recuperación, el controlador compara el token recibido con el valor almacenado mediante `Hash::check()`.

Si el token no coincide, el proceso se detiene y se muestra un mensaje de error. Si es válido y el usuario existe, se actualiza su contraseña y se elimina el registro del token utilizado.

***

### 9. Protección de las funciones administrativas

Además de los controladores, el proyecto utiliza un middleware para restringir el acceso al panel administrativo.

**Archivo:** `app/Http/Middleware/AdminMiddleware.php`

Este middleware comprueba que exista una sesión autenticada y que el usuario tenga el atributo `admin` habilitado.

#### Ejemplo de código

```
public function handle(
    Request $request,
    Closure $next
): Response {
    if (!Auth::check()) {
        return redirect()->route('login')
            ->with('error', 'Debes iniciar sesión');
    }

    if (!Auth::user()->admin) {
        return redirect()->route('home')
            ->with('error', 'No tienes permisos para entrar al panel admin');
    }

    return $next($request);
}
```

#### Explicación

El middleware realiza dos comprobaciones:

1. Verifica que el usuario haya iniciado sesión. Si no está autenticado, lo redirige al formulario de inicio de sesión.
2. Comprueba que el usuario tenga permisos de administrador. Si no los tiene, lo devuelve a la página principal.

Si ambas condiciones se cumplen, permite continuar con la solicitud.

Las rutas administrativas están agrupadas utilizando este middleware en `routes/web.php`.

***

### 10. Validación de solicitudes

El proyecto también utiliza clases especializadas para validar la información antes de ejecutar determinadas operaciones.

Estas clases se encuentran en:

```
app/Http/Requests/
```

Entre ellas se encuentran:

* `LoginRequest.php`
* `RegisterRequest.php`
* `AppointmentRequest.php`

Por ejemplo, `AppointmentRequest` valida que la fecha no sea anterior al día actual, que la hora tenga un formato válido y que se seleccione al menos un servicio.

Además, comprueba que el día seleccionado esté habilitado y que la hora se encuentre dentro del horario de atención configurado.

Esto permite separar parte de las reglas de validación de la lógica principal del controlador.

***

### 11. Relación entre rutas y controladores

Las rutas del sistema se encuentran definidas en:

```
routes/web.php
```

Desde este archivo se conectan las solicitudes recibidas con los métodos de los controladores.

Algunos ejemplos:

| Método HTTP | Ruta                    | Controlador                      |
| ----------- | ----------------------- | -------------------------------- |
| GET         | `/login`                | `AuthController@showLogin`       |
| POST        | `/login`                | `AuthController@login`           |
| POST        | `/logout`               | `AuthController@logout`          |
| GET         | `/servicios`            | `ServiceController@index`        |
| GET         | `/citas`                | `AppointmentController@index`    |
| POST        | `/citas`                | `AppointmentController@store`    |
| GET         | `/mis-citas`            | `AppointmentController@misCitas` |
| GET         | `/admin`                | `AdminController@index`          |
| GET         | `/admin/reportes`       | `ReportController@index`         |
| GET         | `/admin/reportes/pdf`   | `ReportController@pdf`           |
| GET         | `/admin/reportes/excel` | `ReportController@excel`         |

Las rutas administrativas se encuentran protegidas por el middleware `admin`.

***

### 12. Resumen del funcionamiento del backend

El backend de RELUX permite coordinar los procesos principales del sistema:

* **Autenticación:** registro, inicio y cierre de sesión.
* **Gestión de servicios:** consulta, creación, modificación y eliminación.
* **Gestión de citas:** reserva, validación de disponibilidad, edición y cancelación.
* **Administración:** gestión de usuarios, roles, citas y horarios.
* **Reportes:** consulta de indicadores y generación de archivos PDF y Excel.
* **Recuperación de contraseñas:** generación y validación de tokens enviados por correo.
* **Seguridad:** validación de solicitudes y protección de las rutas administrativas.

Los controladores trabajan junto con los modelos, las clases de validación, el middleware y las vistas para ejecutar las operaciones de la aplicación.

### 13. Archivos relacionados

Para consultar la implementación completa, se pueden revisar los siguientes archivos del proyecto:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AppointmentController.php
│   │   ├── AuthController.php
│   │   ├── Controller.php
│   │   ├── PasswordResetController.php
│   │   ├── ReportController.php
│   │   └── ServiceController.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   └── Requests/
│       ├── AppointmentRequest.php
│       ├── LoginRequest.php
│       └── RegisterRequest.php
└── Models/
    ├── Appointment.php
    ├── AppointmentService.php
    ├── Horario.php
    ├── Service.php
    └── User.php
```

**Nota:** Los fragmentos de código de esta documentación son extractos de los archivos del proyecto. Para consultar el comportamiento completo de cada método, se debe revisar el archivo original en el repositorio.
