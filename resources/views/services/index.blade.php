@extends('layouts.app')

@section('title', 'Servicios - AppSalon')

@section('content')
    <div class="hero-box mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-3">Bienvenido a Aurea</h1>
                <p class="mb-3 fs-5">
                    Gestiona tus citas de belleza de forma rápida, segura y sencilla.
                </p>
                <p class="mb-0">
                    Explora nuestros servicios, reserva tu cita y administra tus reservas desde un solo lugar.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('citas.index') }}" class="btn btn-light btn-lg">Reservar ahora</a>
            </div>
        </div>
    </div>

    <div class="text-center mb-5">
        <h2 class="section-title">Nuestros Servicios</h2>
        <p class="text-muted">Selecciona el servicio ideal para ti</p>
    </div>

    <div class="row">
        @foreach($servicios as $servicio)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $servicio->nombre }}</h5>
                        <p class="card-text text-muted">{{ $servicio->descripcion }}</p>
                        <p class="mb-2"><strong>Precio:</strong> ${{ $servicio->precio }}</p>
                        <p class="mb-0"><strong>Duración:</strong> {{ $servicio->duracion }} minutos</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('citas.index') }}" class="btn btn-outline-primary w-100">Reservar este servicio</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection