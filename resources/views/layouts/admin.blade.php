<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Administración - RELUX')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&display=swap" rel="stylesheet">

    <style>
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

        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #fff6f2, #fdeaee);
            color: var(--lp-ink);
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        h1, h2, h3, h4, h5, .admin-brand, .mobile-brand {
            font-family: 'Bricolage Grotesque', sans-serif;
            letter-spacing: -0.02em;
        }

        .admin-shell {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 250px;
            min-width: 250px;
            background: var(--lp-ink);
            color: #fff;
            padding: 25px 16px;
            display: flex;
            flex-direction: column;
        }

        .admin-brand {
            display: block;
            color: #fff;
            font-size: 27px;
            font-weight: 700;
            text-decoration: none;
            padding: 5px 12px 28px;
        }

        .admin-section-label {
            color: #d9aeb9;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            padding: 12px;
            text-transform: uppercase;
        }

        .admin-nav {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .admin-nav .nav-link {
            color: #f8e8ed;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            transition: .2s ease;
        }

        .admin-nav .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.10);
        }

        .admin-nav .nav-link.active {
            color: var(--lp-ink);
            background: var(--lp-rose);
            font-weight: 700;
        }

        .admin-sidebar-footer {
            margin-top: auto;
            padding: 20px 10px 0;
            border-top: 1px solid rgba(255,255,255,.15);
        }

        .admin-user {
            color: #f8e8ed;
            font-size: 13px;
            margin-bottom: 12px;
            overflow-wrap: anywhere;
        }

        .admin-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .admin-topbar {
            min-height: 75px;
            padding: 16px 30px;
            background: rgba(255,255,255,.75);
            border-bottom: 1px solid var(--lp-line);
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .admin-content {
            padding: 30px;
            flex: 1;
            min-width: 0;
        }

        .admin-content .card {
            border: 1px solid rgba(239,211,214,.7);
            transition: box-shadow .2s ease;
        }

        .admin-content .card:hover {
            box-shadow: 0 10px 24px rgba(74,35,50,.10) !important;
        }

        .admin-content .text-muted {
            color: var(--lp-muted) !important;
        }

        .btn-primary {
            --bs-btn-bg: var(--lp-ink);
            --bs-btn-border-color: var(--lp-ink);
            --bs-btn-hover-bg: var(--lp-ink-dark);
            --bs-btn-hover-border-color: var(--lp-ink-dark);
        }

        .btn-outline-primary {
            --bs-btn-color: var(--lp-rose-deep);
            --bs-btn-border-color: var(--lp-rose-deep);
            --bs-btn-hover-bg: var(--lp-rose-deep);
            --bs-btn-hover-border-color: var(--lp-rose-deep);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--lp-rose);
            box-shadow: 0 0 0 .25rem rgba(244,163,168,.30);
        }

        .form-check-input:checked {
            background-color: var(--lp-rose-deep);
            border-color: var(--lp-rose-deep);
        }

        /* Menú móvil: botón a la izquierda y panel lateral desde la izquierda. */
        .mobile-admin-header {
            display: none;
        }

        .mobile-menu-button {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(74,35,50,.16);
            border-radius: 10px;
            background: #fff;
            color: var(--lp-ink);
            font-size: 24px;
            line-height: 1;
        }

        .mobile-brand {
            color: var(--lp-ink);
            font-size: 22px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: .5px;
        }

        .admin-mobile-drawer {
            width: min(310px, 86vw);
            background: var(--lp-ink);
            color: #fff;
        }

        .admin-mobile-drawer .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: .9;
        }

        .admin-mobile-drawer .admin-brand {
            padding: 0 0 22px;
        }

        .admin-mobile-drawer .admin-section-label {
            padding-left: 0;
        }

        .admin-mobile-drawer .admin-sidebar-footer {
            margin-top: 28px;
        }

        @media (max-width: 767.98px) {
            .admin-shell {
                display: block;
                min-height: 100vh;
            }

            .admin-sidebar-desktop {
                display: none !important;
            }

            .mobile-admin-header {
                min-height: 64px;
                padding: 10px 16px;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                gap: 13px;
                background: rgba(255,255,255,.94);
                border-bottom: 1px solid var(--lp-line);
                position: sticky;
                top: 0;
                z-index: 1020;
            }

            .admin-topbar {
                min-height: 52px;
                padding: 12px 18px;
                justify-content: flex-start;
                background: rgba(255,255,255,.72);
            }

            .admin-content {
                padding: 20px 14px;
                overflow-x: hidden;
            }

            /* Evita que tablas anchas rompan el ancho de la pantalla. */
            .admin-content .table-responsive {
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .admin-content img,
            .admin-content canvas {
                max-width: 100%;
            }
        }

        @media (min-width: 768px) {
            .admin-mobile-drawer,
            .mobile-admin-header {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="admin-shell">

    {{-- Barra lateral permanente para escritorio. --}}
    <aside class="admin-sidebar admin-sidebar-desktop">

        <a href="{{ route('admin.index') }}" class="admin-brand">
            RELUX
        </a>

        <div class="admin-section-label">
            Administración
        </div>

        <nav class="admin-nav">
            <a href="{{ route('admin.index') }}"
               class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                Panel
            </a>

            <a href="{{ route('admin.citas') }}"
               class="nav-link {{ request()->routeIs('admin.citas*') ? 'active' : '' }}">
                Citas
            </a>

            <a href="{{ route('admin.servicios.index') }}"
               class="nav-link {{ request()->routeIs('admin.servicios.*') ? 'active' : '' }}">
                Servicios
            </a>

            <a href="{{ route('admin.horarios') }}"
               class="nav-link {{ request()->routeIs('admin.horarios*') ? 'active' : '' }}">
                Horarios
            </a>

            <a href="{{ route('admin.reportes') }}"
               class="nav-link {{ request()->routeIs('admin.reportes*') ? 'active' : '' }}">
                Reportes
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <div class="admin-user">
                {{ Auth::user()->nombre ?? 'Administrador' }}
            </div>

            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Menú móvil: el botón queda a la izquierda. --}}
    <header class="mobile-admin-header">
        <button
            class="mobile-menu-button"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#adminMobileMenu"
            aria-controls="adminMobileMenu"
            aria-label="Abrir menú de administración"
        >
            &#9776;
        </button>

        <a href="{{ route('admin.index') }}" class="mobile-brand">RELUX</a>
    </header>

    {{-- Panel lateral móvil que se desliza desde la izquierda. --}}
    <div
        class="offcanvas offcanvas-start admin-mobile-drawer"
        tabindex="-1"
        id="adminMobileMenu"
        aria-labelledby="adminMobileMenuLabel"
    >
        <div class="offcanvas-header">
            <a href="{{ route('admin.index') }}" class="admin-brand mb-0" id="adminMobileMenuLabel">
                RELUX
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar menú"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column pt-0">
            <div class="admin-section-label">Administración</div>

            <nav class="admin-nav">
                <a href="{{ route('admin.index') }}"
                   class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    Panel
                </a>

                <a href="{{ route('admin.citas') }}"
                   class="nav-link {{ request()->routeIs('admin.citas*') ? 'active' : '' }}">
                    Citas
                </a>

                <a href="{{ route('admin.servicios.index') }}"
                   class="nav-link {{ request()->routeIs('admin.servicios.*') ? 'active' : '' }}">
                    Servicios
                </a>

                <a href="{{ route('admin.horarios') }}"
                   class="nav-link {{ request()->routeIs('admin.horarios*') ? 'active' : '' }}">
                    Horarios
                </a>

                <a href="{{ route('admin.reportes') }}"
                   class="nav-link {{ request()->routeIs('admin.reportes*') ? 'active' : '' }}">
                    Reportes
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <div class="admin-user">
                    {{ Auth::user()->nombre ?? 'Administrador' }}
                </div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="admin-main">

        <header class="admin-topbar">
            <span class="fw-semibold">
                Panel administrativo
            </span>
        </header>

        <main class="admin-content">

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

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>
