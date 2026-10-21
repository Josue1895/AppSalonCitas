<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'relux')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tipografía de títulos (la misma de la portada) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&display=swap" rel="stylesheet">

    <style>
        /*
           PALETA "ROSA TÉ" - aplica a todo el sitio.
           Cambia estas variables para probar otros colores.
        */
        :root {
            --lp-ink: #4a2332;        /* ciruela oscuro: textos, barra y botones */
            --lp-ink-dark: #351723;   /* hover de botones oscuros */
            --lp-muted: #8a5a69;      /* textos secundarios */
            --lp-rose: #f4a3a8;       /* rosa cálido */
            --lp-rose-deep: #c4486a;  /* enlaces y detalles */
            --lp-blush: #fdecee;      /* fondos suaves */
            --lp-line: #efd3d6;       /* bordes */
            --lp-hero-a: #ffe3d8;     /* durazno */
            --lp-hero-b: #fbbcc3;     /* rosa */

            /* Enlaces de Bootstrap en la paleta */
            --bs-link-color: #c4486a;
            --bs-link-color-rgb: 196, 72, 106;
            --bs-link-hover-color: #4a2332;
            --bs-link-hover-color-rgb: 74, 35, 50;
        }

        body {
            background: linear-gradient(135deg,   #fff6f2, #fdeaee);
            min-height: 100vh;
        }

        /* Títulos con la tipografía de la portada */
        h1, h2, .section-title, .navbar-brand {
            font-family: 'Bricolage Grotesque', system-ui, -apple-system, 'Segoe UI', sans-serif;
            letter-spacing: -0.02em;
        }

        .navbar-brand {
            letter-spacing: 0.5px;
        }

        .hero-box {
            background: linear-gradient(135deg, var(--lp-hero-a), var(--lp-hero-b));
            color: var(--lp-ink);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(196, 72, 106, .18);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(120, 40, 70, .18) !important;
        }

        .section-title {
            font-weight: 700;
        }

        .text-muted { color: var(--lp-muted) !important; }

        /* Barra de navegación*/
        .navbar.bg-dark { background-color: var(--lp-ink) !important; }

        .navbar .btn-primary {
            --bs-btn-bg: var(--lp-rose);
            --bs-btn-border-color: var(--lp-rose);
            --bs-btn-color: var(--lp-ink);
            --bs-btn-hover-bg: #f08e95;
            --bs-btn-hover-border-color: #f08e95;
            --bs-btn-hover-color: var(--lp-ink);
            --bs-btn-active-bg: #ec7b84;
            --bs-btn-active-border-color: #ec7b84;
            --bs-btn-active-color: var(--lp-ink);
            font-weight: 600;
        }

        .navbar .btn-success {
            --bs-btn-bg: #ffe3d8;
            --bs-btn-border-color: #ffe3d8;
            --bs-btn-color: var(--lp-ink);
            --bs-btn-hover-bg: #ffd2c2;
            --bs-btn-hover-border-color: #ffd2c2;
            --bs-btn-hover-color: var(--lp-ink);
            --bs-btn-active-bg: #ffc4b0;
            --bs-btn-active-border-color: #ffc4b0;
            --bs-btn-active-color: var(--lp-ink);
            font-weight: 600;
        }

        /* Botones del resto del sitio */
        .btn-primary {
            --bs-btn-bg: var(--lp-ink);
            --bs-btn-border-color: var(--lp-ink);
            --bs-btn-color: #fff;
            --bs-btn-hover-bg: var(--lp-ink-dark);
            --bs-btn-hover-border-color: var(--lp-ink-dark);
            --bs-btn-hover-color: #fff;
            --bs-btn-active-bg: var(--lp-ink-dark);
            --bs-btn-active-border-color: var(--lp-ink-dark);
            --bs-btn-active-color: #fff;
            --bs-btn-focus-shadow-rgb: 196, 72, 106;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--lp-rose-deep);
            --bs-btn-border-color: var(--lp-rose-deep);
            --bs-btn-hover-bg: var(--lp-rose-deep);
            --bs-btn-hover-border-color: var(--lp-rose-deep);
            --bs-btn-hover-color: #fff;
            --bs-btn-active-bg: #a93a58;
            --bs-btn-active-border-color: #a93a58;
            --bs-btn-active-color: #fff;
            --bs-btn-focus-shadow-rgb: 196, 72, 106;
        }

        /* Botón claro dentro de la caja de bienvenida */
        .hero-box .btn-light {
            --bs-btn-bg: var(--lp-ink);
            --bs-btn-border-color: var(--lp-ink);
            --bs-btn-color: #fff;
            --bs-btn-hover-bg: var(--lp-ink-dark);
            --bs-btn-hover-border-color: var(--lp-ink-dark);
            --bs-btn-hover-color: #fff;
            --bs-btn-active-bg: var(--lp-ink-dark);
            --bs-btn-active-border-color: var(--lp-ink-dark);
            --bs-btn-active-color: #fff;
        }

        /* ---------- Formularios (login, registro, citas) ---------- */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--lp-rose);
            box-shadow: 0 0 0 .25rem rgba(244, 163, 168, .35);
        }

        .form-check-input:checked {
            background-color: var(--lp-rose-deep);
            border-color: var(--lp-rose-deep);
        }
        .form-check-input:focus {
            border-color: var(--lp-rose);
            box-shadow: 0 0 0 .25rem rgba(244, 163, 168, .35);
        }
    </style>

    {{-- Estilos extra que agregue para cada vista (por ejemplo la portada) --}}
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">Relux</a>

            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('servicios.index') }}">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('citas.index') }}">vamos a reservar una cita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('citas.mis') }}">Mis Citas</a>
                    </li>

                    @if(Auth::check() && Auth::user()->admin)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.index') }}">Panel Admin</a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(Auth::check())
                        <span class="text-white">
                            {{ Auth::user()->nombre }}
                            @if(Auth::user()->admin)
                                <span class="badge bg-warning text-dark">Admin</span>
                            @else
                                <span class="badge bg-info text-dark">Usuario</span>
                            @endif
                        </span>

                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Cerrar sesión</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                        <a href="{{ route('registro') }}" class="btn btn-success btn-sm">Registro</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success shadow-sm rounded-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger shadow-sm rounded-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-warning shadow-sm rounded-4">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts extra que agregue para cada vista (por ejemplo el simulador que esta en la portada :3 --}}
    @stack('scripts')
</body>
</html>
