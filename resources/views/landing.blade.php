@extends('layouts.app')

@section('title', 'AppSalon - Reserva tu cita de belleza')

@php
    // El botón cambia si hay sesión o no
    $ctaPrincipalUrl   = Auth::check() ? route('citas.index') : route('registro');
    $ctaPrincipalTexto = Auth::check() ? 'Reservar mi cita' : 'Crear mi cuenta';

     // Dirección del salón 
    $direccion = 'Cra. 24c # 85-29, Cali, Valle del Cauca'; 
@endphp

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&display=swap" rel="stylesheet">

    <style>
        /* 
           PALETA "ROSA TÉ o asi me dijo chapeto que se llamaba"  (cambien solo estas variables
           para probar mas colores)
        */
        :root {
            --lp-ink: #4a2332;        /* ciruela oscuro: textos y botones */
            --lp-ink-dark: #351723;   /* hover del botón principal */
            --lp-muted: #8a5a69;      /* textos secundarios */
            --lp-rose: #f4a3a8;       /* rosa cálido: selecciones */
            --lp-rose-deep: #c4486a;  /* enlaces y detalles */
            --lp-blush: #fdecee;      /* fondos suaves */
            --lp-line: #efd3d6;       /* bordes */
            --lp-hero-a: #ffe3d8;     /* hero: durazno */
            --lp-hero-b: #fbbcc3;     /* hero: rosa */
        }

        /* Fondo cálido de la página (solo en la portada) */
        body {
            background: linear-gradient(135deg, #fff6f2, #fdeaee);
        }

        /* Barra de navegación en ciruela (solo en la portada).
           Si te gusta, esto se pasa al layout para que aplique en todo el sitio. */
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

        .lp-display {
            font-family: 'Bricolage Grotesque', system-ui, -apple-system, 'Segoe UI', sans-serif;
            letter-spacing: -0.02em;
        }

        /* ---------- HERO ---------- */
        .lp-hero {
            background: linear-gradient(135deg, var(--lp-hero-a) 0%, var(--lp-hero-b) 100%);
            color: var(--lp-ink);
            border-radius: 24px;
            padding: clamp(28px, 5vw, 56px);
            box-shadow: 0 10px 25px rgba(196, 72, 106, .18);
        }

        .lp-hero h1 {
            font-size: clamp(2.1rem, 4.6vw, 3.4rem);
            line-height: 1.06;
            font-weight: 700;
            max-width: 15ch;
        }

        .lp-hero .lp-lead {
            font-size: 1.1rem;
            color: rgba(74, 35, 50, .85);
            max-width: 46ch;
        }

        .lp-btn-main {
            background: var(--lp-ink);
            border: 0;
            color: #fff;
            font-weight: 600;
        }
        .lp-btn-main:hover { background: var(--lp-ink-dark); color: #fff; }
        .lp-btn-main:focus-visible { outline: 3px solid var(--lp-rose-deep); outline-offset: 2px; }

        .lp-link {
            color: var(--lp-ink);
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        .lp-link:hover { color: var(--lp-rose-deep); }

        /* Entrada única al cargar la página (solo el hero) */
        .lp-in {
            opacity: 0;
            transform: translateY(14px);
            animation: lpIn .7s ease forwards;
            animation-delay: var(--d, 0s);
        }
        @keyframes lpIn {
            to { opacity: 1; transform: none; }
        }

        /* Etiqueta de dirección sobre el título */
        .lp-address {
           display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.1rem;
            padding: 6px 14px 6px 10px;
            background: rgb(223, 188, 207);
            border-radius: 999px;
            color: var(--lp-ink);
            font-size: .92rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s ease;
       }
        .lp-address:hover { background: #fff; color: var(--lp-ink); }
        .lp-address:focus-visible { outline: 3px solid var(--lp-rose-deep); outline-offset: 2px; }
         .lp-address svg { flex: 0 0 auto; color: var(--lp-rose-deep); }

        /* ---------- SIMULADOR DE RESERVA ---------- */
        .lp-book {
            background: #fff;
            color: var(--lp-ink);
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 18px 40px rgba(120, 40, 70, .22);
        }

        .lp-book-title { font-weight: 700; }

        .lp-book .text-muted { color: var(--lp-muted) !important; }

        .lp-demo-tag {
            font-size: .78rem;
            color: var(--lp-muted);
            border: 1px dashed #d8a9b3;
            border-radius: 999px;
            padding: 2px 10px;
        }

        .lp-days {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .lp-day, .lp-time {
            border: 1px solid var(--lp-line);
            background: #fff;
            color: var(--lp-ink);
            border-radius: 12px;
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease, transform .15s ease;
        }
        .lp-day:hover, .lp-time:hover { border-color: var(--lp-rose-deep); }
        .lp-day:active, .lp-time:active { transform: scale(.97); }

        .lp-day {
            padding: 8px 0;
            text-align: center;
            line-height: 1.15;
        }
        .lp-day .lp-dow { display: block; font-size: .78rem; color: var(--lp-muted); text-transform: capitalize; }
        .lp-day .lp-num { display: block; font-size: 1.25rem; font-weight: 700; }

        .lp-times {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .lp-time { padding: 9px 4px; font-size: .92rem; }

        .lp-day[aria-pressed="true"],
        .lp-time[aria-pressed="true"] {
            background: var(--lp-rose);
            border-color: var(--lp-rose);
            color: var(--lp-ink);
            font-weight: 600;
        }
        .lp-day[aria-pressed="true"] .lp-dow { color: var(--lp-ink); }

        .lp-day:focus-visible,
        .lp-time:focus-visible {
            outline: 3px solid var(--lp-rose-deep);
            outline-offset: 2px;
        }

        .lp-summary {
            background: var(--lp-blush);
            border-radius: 12px;
            padding: 12px 14px;
            font-weight: 600;
        }

        /* ---------- PASOS ---------- */
        .lp-steps { list-style: none; padding: 0; margin: 0; }
        .lp-steps .text-muted { color: var(--lp-muted) !important; }
        .lp-step-num {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            border-radius: 50%;
            background: var(--lp-rose);
            color: var(--lp-ink);
            display: grid;
            place-items: center;
            font-weight: 700;
        }

        /* ---------- FAQ ---------- */
        .lp-faq {
            --bs-accordion-btn-active-icon: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%234a2332'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        .lp-faq .accordion-item {
            border: 0;
            border-bottom: 1px solid var(--lp-line);
            background: transparent;
        }
        .lp-faq .accordion-button {
            background: transparent;
            color: var(--lp-ink);
            font-weight: 600;
            box-shadow: none;
            padding-left: 0;
        }
        .lp-faq .accordion-button:not(.collapsed) {
            color: var(--lp-ink);
            background: transparent;
        }
        .lp-faq .accordion-button:focus-visible {
            outline: 3px solid var(--lp-rose-deep);
            outline-offset: 2px;
        }
        .lp-faq .accordion-body { padding-left: 0; color: var(--lp-muted); }
        .lp-faq .accordion-body a { color: var(--lp-rose-deep); }
        .lp-faq .accordion-body a:hover { color: var(--lp-ink); }

        /* Títulos y textos secundarios de las secciones */
        main .text-muted { color: var(--lp-muted) !important; }

        /* ---------- CIERRE (por si vuelves a usar la sección) ---------- */
        .lp-cta {
            background: linear-gradient(135deg, var(--lp-hero-a) 0%, var(--lp-hero-b) 100%);
            color: var(--lp-ink);
            border-radius: 24px;
            padding: clamp(24px, 4vw, 44px);
        }

        @media (prefers-reduced-motion: reduce) {
            .lp-in { animation: none; opacity: 1; transform: none; }
            .lp-day, .lp-time { transition: none; }
        }
    </style>
@endpush

@section('content')

    {{-- ================= HERO ================= --}}
    <section class="lp-hero mb-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($direccion) }}"
                target="_blank" rel="noopener"
                class="lp-address lp-in" style="--d:0s">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
             <path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>
             
            </svg>
             {{ $direccion }}
              </a>

             <h1 class="lp-display mb-3 lp-in" style="--d:.05s">
                  Tu cita de belleza, sin llamadas ni esperas.
                </h1>

                <h1 class="lp-display mb-3 lp-in" style="--d:.05s">
                    Tu cita de belleza, sin llamadas ni esperas.
                </h1>

                <p class="lp-lead mb-4 lp-in" style="--d:.15s">
                    Elige el día y la hora que mejor te queden, recibe la confirmación
                    en tu correo y cambia tu cita cuando lo necesites.
                </p>

                <div class="d-flex flex-wrap align-items-center gap-3 lp-in" style="--d:.25s">
                    <a href="{{ $ctaPrincipalUrl }}" class="btn btn-lg lp-btn-main">{{ $ctaPrincipalTexto }}</a>

                    @guest
                        <a href="{{ route('login') }}" class="lp-link">Ya tengo cuenta</a>
                    @endguest
                    @auth
                        <a href="{{ route('citas.mis') }}" class="lp-link">Ver mis citas</a>
                    @endauth
                </div>
            </div>

            {{-- Simulador interactivo (solo demostración) --}}
            <div class="col-lg-6 lp-in" style="--d:.35s">
                <div class="lp-book" role="group" aria-label="Ejemplo de cómo se reserva una cita">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="lp-book-title lp-display fs-5">Tu próxima cita</span>
                        <span class="lp-demo-tag">Vista de ejemplo</span>
                    </div>

                    <p class="small text-muted mb-2">Elige un día</p>
                    <div class="lp-days mb-3" id="lpDays"></div>

                    <p class="small text-muted mb-2">Elige una hora</p>
                    <div class="lp-times mb-3" id="lpTimes"></div>

                    <div class="lp-summary mb-3" id="lpSummary" aria-live="polite"></div>

                    <a href="{{ $ctaPrincipalUrl }}" class="btn lp-btn-main w-100">
                        {{ Auth::check() ? 'Reservar así' : 'Crear cuenta para reservar' }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CÓMO FUNCIONA ================= --}}
    <section class="mb-5 py-3">
        <h2 class="lp-display fw-bold mb-1">Reservar una cita solo toma tres pasos</h2>
        <p class="text-muted mb-4">Sin llamadas y sin escribir por mensajes.</p>

        <ol class="lp-steps row g-4">
            <li class="col-md-4 d-flex gap-3">
                <span class="lp-step-num">1</span>
                <div>
                    <h3 class="h5 fw-bold">Crea tu cuenta</h3>
                    <p class="text-muted mb-0">Regístrate con tu correo. Ahí quedarán guardadas todas tus citas.</p>
                </div>
            </li>
            <li class="col-md-4 d-flex gap-3">
                <span class="lp-step-num">2</span>
                <div>
                    <h3 class="h5 fw-bold">Elige servicio, día y hora</h3>
                    <p class="text-muted mb-0">Escoge lo que necesitas y el horario disponible que más te convenga.</p>
                </div>
            </li>
            <li class="col-md-4 d-flex gap-3">
                <span class="lp-step-num">3</span>
                <div>
                    <h3 class="h5 fw-bold">Recibe la confirmación</h3>
                    <p class="text-muted mb-0">Te avisamos por correo y puedes revisar el estado en “Mis citas”.</p>
                </div>
            </li>
        </ol>
    </section>

    {{-- ================= PREGUNTAS FRECUENTES ================= --}}
    <section class="mb-5 py-3">
        <div class="row">
            <div class="col-lg-4 mb-3">
                <h2 class="lp-display fw-bold">Preguntas frecuentes</h2>
                <p class="text-muted">Lo que suelen preguntar antes de reservar.</p>
            </div>

            <div class="col-lg-8">
                <div class="accordion lp-faq" id="lpFaq">

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                ¿Necesito una cuenta para reservar?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#lpFaq">
                            <div class="accordion-body">
                                Sí. Con tu cuenta tus citas quedan guardadas en “Mis citas” y puedes
                                consultarlas cuando quieras.
                                <a href="{{ route('registro') }}">Crear cuenta</a>.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                ¿Puedo cambiar o cancelar mi cita?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#lpFaq">
                            <div class="accordion-body">
                                Sí. Desde “Mis citas” puedes editar la cita o cancelarla.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                ¿Recibiré una confirmación?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#lpFaq">
                            <div class="accordion-body">
                                Sí. Te enviamos un correo cuando reservas y otro cuando cambia el
                                estado de tu cita.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                Olvidé mi contraseña, ¿qué hago?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#lpFaq">
                            <div class="accordion-body">
                                Puedes restablecerla con tu correo desde
                                <a href="{{ route('password.request') }}">recuperar contraseña</a>.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                                ¿Qué servicios ofrecen y cuánto cuestan?
                            </button>
                        </h3>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#lpFaq">
                            <div class="accordion-body">
                                Encuentras la lista con precios y duración en
                                <a href="{{ route('servicios.index') }}">Servicios</a>.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


@endsection

@push('scripts')
<script>
(function () {
    var daysBox  = document.getElementById('lpDays');
    var timesBox = document.getElementById('lpTimes');
    var summary  = document.getElementById('lpSummary');
    if (!daysBox || !timesBox || !summary) return;

    var fmtShort = new Intl.DateTimeFormat('es-CO', { weekday: 'short' });
    var fmtLong  = new Intl.DateTimeFormat('es-CO', { weekday: 'long', day: 'numeric', month: 'long' });
    var horas    = ['9:00 a. m.', '10:00 a. m.', '11:00 a. m.', '3:00 p. m.', '4:00 p. m.', '5:00 p. m.'];

    var fechas = [];
    for (var i = 1; i <= 5; i++) {
        var d = new Date();
        d.setDate(d.getDate() + i);
        fechas.push(d);
    }

    var sel = { dia: 1, hora: 4 };

    function cap(t) { return t.charAt(0).toUpperCase() + t.slice(1); }

    function pintarResumen() {
        summary.textContent = cap(fmtLong.format(fechas[sel.dia])) + ' · ' + horas[sel.hora];
    }

    function marcar(box, indice) {
        var botones = box.querySelectorAll('button');
        for (var k = 0; k < botones.length; k++) {
            botones[k].setAttribute('aria-pressed', k === indice ? 'true' : 'false');
        }
    }

    fechas.forEach(function (f, idx) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'lp-day';
        b.innerHTML = '<span class="lp-dow"></span><span class="lp-num"></span>';
        b.querySelector('.lp-dow').textContent = fmtShort.format(f).replace('.', '');
        b.querySelector('.lp-num').textContent = f.getDate();
        b.setAttribute('aria-pressed', idx === sel.dia ? 'true' : 'false');
        b.addEventListener('click', function () {
            sel.dia = idx;
            marcar(daysBox, idx);
            pintarResumen();
        });
        daysBox.appendChild(b);
    });

    horas.forEach(function (h, idx) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'lp-time';
        b.textContent = h;
        b.setAttribute('aria-pressed', idx === sel.hora ? 'true' : 'false');
        b.addEventListener('click', function () {
            sel.hora = idx;
            marcar(timesBox, idx);
            pintarResumen();
        });
        timesBox.appendChild(b);
    });

    pintarResumen();
})();
</script>
@endpush