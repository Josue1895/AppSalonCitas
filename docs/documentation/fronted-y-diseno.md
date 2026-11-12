# Fronted y diseño

## Frontend y diseño de la aplicación

### 1. Introducción

El frontend de **RELUX – AppSalonCitas** corresponde a la parte visual de la aplicación. Es la interfaz mediante la cual los clientes pueden consultar los servicios, registrarse, iniciar sesión, reservar citas y administrar sus reservas.

También cuenta con una interfaz administrativa desde la cual se pueden consultar usuarios, servicios, citas, horarios y reportes.

La interfaz está desarrollada utilizando las vistas **Blade de Laravel**, HTML y Bootstrap, complementados con estilos CSS personalizados para mantener una apariencia uniforme en todo el sistema.

### 2. Estructura del frontend

Las vistas principales se encuentran organizadas dentro de:

```
resources/views/
```

La estructura general es la siguiente:

```
resources/views/
├── admin/
│   ├── citas.blade.php
│   ├── horarios.blade.php
│   ├── index.blade.php
│   ├── reportes.blade.php
│   ├── reportes-pdf.blade.php
│   ├── usuarios.blade.php
│   └── servicios/
│       ├── create.blade.php
│       ├── edit.blade.php
│       └── index.blade.php
├── appointments/
│   ├── edit.blade.php
│   ├── index.blade.php
│   └── mis-citas.blade.php
├── auth/
│   ├── forgot-password.blade.php
│   ├── login.blade.php
│   ├── registro.blade.php
│   └── reset-password.blade.php
├── emails/
│   ├── cita-estado.blade.php
│   ├── cita-reservada.blade.php
│   └── password-reset.blade.php
├── layouts/
│   ├── admin.blade.php
│   └── app.blade.php
├── services/
│   └── index.blade.php
├── landing.blade.php
└── welcome.blade.php
```

Esta organización permite separar las vistas de los clientes, las vistas administrativas, los formularios de autenticación y las plantillas de correo electrónico.

### 3. Tecnologías utilizadas

| Tecnología  | Uso dentro del frontend                                                         |
| ----------- | ------------------------------------------------------------------------------- |
| Blade       | Creación de vistas dinámicas e integración de datos enviados por Laravel.       |
| HTML        | Estructura de los elementos de cada página y de los formularios.                |
| Bootstrap 5 | Diseño de componentes, tarjetas, botones, formularios y distribución adaptable. |
| CSS         | Personalización de colores, tipografías, fondos, tamaños y distribución visual. |
| JavaScript  | Recursos de inicialización del proyecto ubicados en `resources/js/`.            |

El proyecto incluye Bootstrap 5.3.7 mediante CDN y utiliza la tipografía Bricolage Grotesque para títulos y elementos destacados.

### 4. Diseño general y distribución visual

El diseño de RELUX utiliza una identidad visual basada en tonos rosados, durazno y ciruela oscuro.

La paleta de colores principal está definida mediante variables CSS:

```
:root {
    --lp-ink: #4a2332;
    --lp-ink-dark: #351723;
    --lp-muted: #8a5a69;
    --lp-rose: #f4a3a8;
    --lp-rose-deep: #c4486a;
    --lp-blush: #fdecee;
    --lp-line: #efd3d6;
    --lp-hero-a: #ffe3d8;
    --lp-hero-b: #fbbcc3;
}
```

#### Explicación

Estas variables permiten mantener los mismos colores en diferentes secciones de la aplicación.

* **Ciruela oscuro:** utilizado en textos principales, botones y elementos de navegación.
* **Rosa cálido:** utilizado en detalles, selecciones y elementos destacados.
* **Durazno y rosa claro:** utilizados en fondos y secciones de bienvenida.
* **Rosa profundo:** utilizado en enlaces y detalles de interacción.

El diseño busca mantener una apariencia visual coherente entre las páginas públicas y las administrativas.

### 5. Layout principal de la aplicación

**Archivo:** `resources/views/layouts/app.blade.php`

El layout principal contiene la estructura compartida por las vistas de los clientes.

Incluye:

* Configuración básica del documento HTML.
* Configuración de idioma español.
* Adaptación de la página al tamaño de pantalla.
* Iconos de la aplicación.
* Inclusión de Bootstrap.
* Definición de estilos generales.
* Barra de navegación.
* Espacio para el contenido de cada vista.

#### Ejemplo de código

```
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'RELUX')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                RELUX
            </a>
        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>

</body>
</html>
```

#### Explicación

El layout utiliza `@yield('title')` para permitir que cada página establezca su propio título y `@yield('content')` para insertar el contenido correspondiente.

Las demás vistas utilizan `@extends('layouts.app')` para reutilizar esta estructura y evitar repetir el código común.

### 6. Página principal o landing page

**Archivo:** `resources/views/landing.blade.php`

La página principal es el primer punto de contacto con el usuario. Presenta la identidad de RELUX y permite acceder a las principales funciones de la aplicación.

Entre sus elementos se encuentran:

* Presentación del salón.
* Mensaje de bienvenida.
* Información general.
* Botones para registrarse o reservar una cita.
* Acceso a las secciones principales.

#### Ejemplo de código

```
@extends('layouts.app')

@section('title', 'RELUX - Reserva tu cita de belleza')

@php
    $ctaPrincipalUrl = Auth::check()
        ? route('citas.index')
        : route('registro');

    $ctaPrincipalTexto = Auth::check()
        ? 'Reservar mi cita'
        : 'Crear mi cuenta';

    $nombreUsuario = Auth::check()
        ? (Auth::user()->nombre ?? Auth::user()->name ?? '')
        : '';
@endphp
```

#### Explicación

La vista utiliza el layout principal y adapta el botón de acción según el estado de autenticación.

Si el usuario ya inició sesión, el acceso principal lo dirige a la reserva de citas. Si no ha iniciado sesión, lo dirige al registro de una cuenta.

### 7. Visualización de servicios

**Archivo:** `resources/views/services/index.blade.php`

Esta vista permite consultar los servicios disponibles en RELUX.

Cada servicio se presenta mediante una tarjeta que muestra:

* Nombre del servicio.
* Descripción.
* Precio.
* Duración estimada.
* Botón para iniciar el proceso de reserva.

#### Ejemplo de código

```
<div class="row">
    @foreach($servicios as $servicio)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold">
                        {{ $servicio->nombre }}
                    </h5>

                    <p class="card-text text-muted">
                        {{ $servicio->descripcion }}
                    </p>

                    <p class="mb-2">
                        <strong>Precio:</strong>
                        ${{ $servicio->precio }}
                    </p>

                    <p class="mb-0">
                        <strong>Duración:</strong>
                        {{ $servicio->duracion }} minutos
                    </p>
                </div>

                <div class="card-footer bg-white border-0">
                    <a href="{{ route('citas.index') }}"
                       class="btn btn-outline-primary w-100">
                        Reservar este servicio
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
```

#### Explicación

La directiva `@foreach` recorre los servicios recibidos desde el controlador y genera una tarjeta por cada registro.

Los valores se muestran mediante expresiones Blade como `{{ $servicio->nombre }}` y `{{ $servicio->precio }}`.

El botón permite continuar hacia el formulario de reserva.

### 8. Formulario de reserva de citas

**Archivo:** `resources/views/appointments/index.blade.php`

Esta vista permite que el cliente seleccione la fecha, la hora y los servicios que desea reservar.

El formulario se conecta con la ruta `citas.store`, encargada de recibir los datos y procesar la solicitud.

#### Elementos principales

* Selección de fecha.
* Selección de hora.
* Selección de servicios.
* Visualización de mensajes de validación.
* Envío de la solicitud.
* Aviso cuando el usuario no ha iniciado sesión.

#### Ejemplo de código

```
<form id="formulario-cita"
      method="POST"
      action="{{ route('citas.store') }}">

    @csrf

    <label class="form-label">Fecha</label>

    <input
        type="date"
        name="fecha"
        value="{{ old('fecha') }}"
        min="{{ now()->toDateString() }}"
        class="form-control @error('fecha') is-invalid @enderror"
        id="fecha-cita"
        required
    >

    @error('fecha')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

    <label class="form-label">Hora</label>

    <input
        type="time"
        name="hora"
        value="{{ old('hora') }}"
        class="form-control @error('hora') is-invalid @enderror"
        id="hora-cita"
        required
    >

    @error('hora')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

    <button type="submit" class="btn btn-primary">
        Reservar cita
    </button>

</form>
```

#### Explicación

El formulario utiliza el método POST para enviar los datos a la ruta `citas.store`.

La directiva `@csrf` incorpora la protección CSRF de Laravel. El atributo `required` establece que los campos deben completarse y `@error` permite mostrar los mensajes de validación enviados por el backend.

El campo de fecha también utiliza `min` para establecer como fecha mínima el día actual.

La validación definitiva de disponibilidad, horarios y servicios se realiza en el backend.

### 9. Inicio de sesión y registro

**Archivos:**

* `resources/views/auth/login.blade.php`
* `resources/views/auth/registro.blade.php`

Estas vistas permiten que los usuarios accedan a sus cuentas o creen una nueva.

#### Inicio de sesión

El formulario solicita el correo electrónico y la contraseña del usuario.

```
<form method="POST" action="{{ route('login.store') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">
            Correo electrónico
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control"
            placeholder="correo@ejemplo.com"
            autocomplete="email"
            required
        >
    </div>

    <div class="mb-2">
        <label for="password" class="form-label">
            Contraseña
        </label>

        <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            required
        >
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Ingresar
    </button>
</form>
```

#### Explicación

El formulario envía las credenciales a `login.store`, donde el backend realiza la autenticación.

También proporciona un enlace para recuperar la contraseña y otro para acceder al registro.

#### Registro

La vista `auth/registro.blade.php` contiene el formulario para crear una cuenta de usuario.

Los datos ingresados son enviados al backend para su validación y almacenamiento.

### 10. Sección Mis Citas

**Archivo:** `resources/views/appointments/mis-citas.blade.php`

Esta vista permite que el cliente consulte las citas que tiene registradas.

La información mostrada incluye:

* Identificador de la cita.
* Fecha.
* Hora.
* Total.
* Estado.
* Opciones de edición y cancelación cuando corresponda.

#### Ejemplo de código

```
@forelse($citas as $cita)
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body">

            <p>
                <strong>ID:</strong>
                {{ $cita->id }}
            </p>

            <p>
                <strong>Fecha:</strong>
                {{ $cita->fecha }}
            </p>

            <p>
                <strong>Hora:</strong>
                {{ $cita->hora }}
            </p>

            <p>
                <strong>Total:</strong>
                ${{ $cita->total }}
            </p>

            @if($cita->estado == 'pendiente')
                <span class="badge bg-warning text-dark">
                    Pendiente
                </span>
            @elseif($cita->estado == 'confirmada')
                <span class="badge bg-primary">
                    Confirmada
                </span>
            @elseif($cita->estado == 'completada')
                <span class="badge bg-success">
                    Completada
                </span>
            @else
                <span class="badge bg-danger">
                    Cancelada
                </span>
            @endif

        </div>
    </div>
@empty
    <p>No tienes citas registradas.</p>
@endforelse
```

#### Explicación

La directiva `@forelse` permite recorrer las citas recibidas desde el controlador. Si no existen registros, se muestra un mensaje alternativo.

El estado de cada cita se representa mediante etiquetas visuales para facilitar su identificación.

Cuando la cita está pendiente, la vista también ofrece opciones para editarla o cancelarla.

### 11. Panel administrativo

**Archivo:** `resources/views/admin/index.blade.php`

El panel administrativo proporciona una vista general del funcionamiento del sistema.

Presenta indicadores y secciones que permiten consultar información importante sobre la operación del salón.

Entre los elementos de esta interfaz se encuentran:

* Indicadores de usuarios.
* Indicadores de servicios.
* Información de citas.
* Citas del día.
* Citas completadas.
* Información de ingresos.
* Secciones de consulta administrativa.

#### Ejemplo de código

```
@extends('layouts.admin')

@section('title', 'Panel administrativo - Relux')

@section('content')

<style>
    .dashboard-relux {
        color: #432b35;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 26px;
    }

    .dashboard-card {
        background: #fff;
        border: 1px solid #f1e6ea;
        border-radius: 16px;
        padding: 21px;
        height: 100%;
    }
</style>

@endsection
```

#### Explicación

El panel utiliza el layout administrativo y define estilos propios para organizar sus indicadores y secciones.

Los datos que aparecen en el panel son enviados por `AdminController`.

### 12. Layout administrativo

**Archivo:** `resources/views/layouts/admin.blade.php`

El layout administrativo tiene una estructura independiente del layout público.

Incluye una barra lateral de navegación, una sección superior y un espacio central donde se muestra el contenido de cada módulo administrativo.

#### Características principales

* Menú lateral para acceder a las secciones administrativas.
* Identificación visual de RELUX.
* Navegación entre usuarios, servicios, citas, horarios y reportes.
* Área principal de contenido.
* Estilos propios para formularios, botones y tarjetas.
* Adaptación del menú para pantallas pequeñas.

El layout también contiene reglas CSS para ocultar la barra lateral de escritorio en pantallas pequeñas y mostrar una navegación móvil.

### 13. Módulos administrativos

Las vistas administrativas se encuentran en `resources/views/admin/`.

| Archivo                      | Función                                               |
| ---------------------------- | ----------------------------------------------------- |
| `index.blade.php`            | Panel principal e indicadores.                        |
| `usuarios.blade.php`         | Consulta y administración de usuarios.                |
| `citas.blade.php`            | Consulta y actualización del estado de las citas.     |
| `horarios.blade.php`         | Consulta y configuración de horarios.                 |
| `reportes.blade.php`         | Consulta de reportes.                                 |
| `reportes-pdf.blade.php`     | Plantilla visual utilizada para generar reportes PDF. |
| `servicios/index.blade.php`  | Listado administrativo de servicios.                  |
| `servicios/create.blade.php` | Formulario de creación de servicios.                  |
| `servicios/edit.blade.php`   | Formulario de edición de servicios.                   |

Estas vistas permiten al administrador gestionar la información del salón desde una interfaz centralizada.

### 14. Vistas de recuperación de contraseña

**Archivos:**

* `resources/views/auth/forgot-password.blade.php`
* `resources/views/auth/reset-password.blade.php`

Estas páginas permiten solicitar un enlace de recuperación y establecer una contraseña nueva.

La primera vista solicita el correo electrónico del usuario. La segunda permite ingresar la nueva contraseña utilizando el enlace de recuperación recibido.

La validación del correo, del token y de la nueva contraseña es procesada por el backend.

### 15. Plantillas de correo electrónico

El proyecto también cuenta con vistas Blade utilizadas para generar el contenido de los correos electrónicos.

Se encuentran en:

```
resources/views/emails/
```

| Archivo                    | Uso                                                           |
| -------------------------- | ------------------------------------------------------------- |
| `cita-reservada.blade.php` | Contenido del correo relacionado con una cita reservada.      |
| `cita-estado.blade.php`    | Notificación relacionada con el cambio de estado de una cita. |
| `password-reset.blade.php` | Contenido del correo de recuperación de contraseña.           |

Estas plantillas permiten mantener un formato visual para los mensajes enviados por el sistema.

### 16. Recursos CSS y JavaScript

Los recursos del frontend se encuentran organizados en:

```
resources/
├── css/
│   └── app.css
└── js/
    ├── app.js
    └── bootstrap.js
```

#### CSS

El archivo `resources/css/app.css` está disponible para los estilos generales del proyecto. Los layouts y algunas vistas también contienen estilos CSS directamente dentro de sus archivos Blade.

#### JavaScript

El archivo `resources/js/app.js` importa el archivo de inicialización del proyecto:

```
import './bootstrap';
```

El archivo `resources/js/bootstrap.js` contiene la configuración correspondiente a esa inicialización.

### 17. Diseño adaptable

La aplicación utiliza las clases de Bootstrap para organizar el contenido en diferentes tamaños de pantalla.

Por ejemplo:

```
<div class="col-md-6 col-lg-4 mb-4">
    <!-- Contenido de la tarjeta -->
</div>
```

Estas clases permiten distribuir el contenido en columnas según el tamaño de pantalla.

El layout administrativo también incorpora reglas `@media` para modificar la navegación en pantallas pequeñas.

La adaptación visual debe verificarse en los dispositivos y resoluciones contemplados en las pruebas del proyecto.

### 18. Relación entre frontend y backend

El frontend y el backend trabajan de manera integrada.

El funcionamiento general es el siguiente:

1. El usuario ingresa a una página o completa un formulario.
2. La vista Blade envía la solicitud a una ruta de Laravel.
3. La ruta dirige la solicitud al controlador correspondiente.
4. El controlador valida los datos y consulta o modifica la información mediante los modelos.
5. El backend devuelve una vista o una redirección.
6. El usuario recibe la respuesta en la interfaz.

De esta manera, las vistas no se encargan directamente de realizar las operaciones sobre la base de datos, sino que presentan la información y permiten interactuar con las funciones del sistema.

### 19. Resumen del frontend

El frontend de RELUX está organizado en módulos que permiten utilizar las funciones principales del sistema de forma clara.

Sus componentes principales son:

* Página principal y presentación del salón.
* Consulta de servicios.
* Registro e inicio de sesión.
* Formulario de reserva de citas.
* Consulta y administración de citas del cliente.
* Panel administrativo.
* Gestión visual de usuarios, servicios, horarios y reportes.
* Formularios de recuperación de contraseña.
* Plantillas de correo electrónico.

La reutilización de layouts, las vistas Blade y los componentes de Bootstrap permiten mantener una estructura organizada y una identidad visual consistente en la aplicación.

### 20. Archivos relacionados

Para consultar la implementación completa del frontend, se pueden revisar las carpetas y archivos de `resources/views`, `resources/css` y `resources/js` dentro del repositorio de RELUX.
