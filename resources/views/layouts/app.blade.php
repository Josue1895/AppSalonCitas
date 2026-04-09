<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AppSalon')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #eef2f7);
            min-height: 100vh;
        }

        .navbar-brand {
            letter-spacing: 0.5px;
        }

        .hero-box {
            background: linear-gradient(135deg, #212529, #495057);
            color: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.10) !important;
        }

        .section-title {
            font-weight: 700;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">AppSalon</a>

            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('citas.index') }}">Reservar Cita</a>
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
</body>
</html>