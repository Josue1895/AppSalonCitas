```php
@extends('layouts.app')

@section('title', 'Mis Citas - AppSalon')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">Mis Citas</h2>

    <a href="{{ route('citas.index') }}" class="btn btn-primary rounded-3">
        Reservar nueva cita
    </a>
</div>

{{-- Mensajes --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@forelse($citas as $cita)

    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body p-4">

            <div class="row gy-3">

                <div class="col-md-2">
                    <p class="mb-1 fw-bold">ID</p>
                    <p class="text-muted">{{ $cita->id }}</p>
                </div>

                <div class="col-md-2">
                    <p class="mb-1 fw-bold">Fecha</p>
                    <p class="text-muted">{{ $cita->fecha }}</p>
                </div>

                <div class="col-md-2">
                    <p class="mb-1 fw-bold">Hora</p>
                    <p class="text-muted">{{ $cita->hora }}</p>
                </div>

                <div class="col-md-2">
                    <p class="mb-1 fw-bold">Total</p>
                    <p class="text-primary fw-semibold">
                        ${{ $cita->total }}
                    </p>
                </div>

                <div class="col-md-2">

                    <p class="mb-1 fw-bold">Estado</p>

                    @if($cita->estado == 'pendiente')

                        <span class="badge bg-warning text-dark px-3 py-2">
                            Pendiente
                        </span>

                    @elseif($cita->estado == 'confirmada')

                        <span class="badge bg-primary px-3 py-2">
                            Confirmada
                        </span>

                    @elseif($cita->estado == 'completada')

                        <span class="badge bg-success px-3 py-2">
                            Completada
                        </span>

                    @else

                        <span class="badge bg-danger px-3 py-2">
                            Cancelada
                        </span>

                    @endif

                </div>

                <div class="col-md-2 d-flex align-items-center">

                    @if($cita->estado == 'pendiente')

                        <div class="w-100 d-grid gap-2">

                            {{-- Editar --}}
                            <a href="{{ route('citas.editar', $cita->id) }}"
                               class="btn btn-outline-primary rounded-3">
                                Editar
                            </a>

                            {{-- Cancelar --}}
                            <form action="{{ route('citas.cancelar', $cita->id) }}"
                                  method="POST">

                                @csrf

                                <button type="submit"
                                        class="btn btn-outline-danger w-100 rounded-3">
                                    Cancelar
                                </button>

                            </form>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

@empty

    <div class="alert alert-info rounded-4">
        No tienes citas registradas.
    </div>

@endforelse

@endsection
```
