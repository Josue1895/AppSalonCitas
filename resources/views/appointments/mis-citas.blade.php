@extends('layouts.app')

@section('title', 'Mis Citas - AppSalon')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Mis Citas</h2>
        <a href="{{ route('citas.index') }}" class="btn btn-primary">Reservar nueva cita</a>
    </div>

    @forelse($citas as $cita)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <p class="mb-1"><strong>ID</strong></p>
                        <p>{{ $cita->id }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>Fecha</strong></p>
                        <p>{{ $cita->fecha }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>Hora</strong></p>
                        <p>{{ $cita->hora }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>Total</strong></p>
                        <p>${{ $cita->total }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>Estado</strong></p>
                        <p>
                            @if($cita->estado == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($cita->estado == 'confirmada')
                                <span class="badge bg-primary">Confirmada</span>
                            @elseif($cita->estado == 'completada')
                                <span class="badge bg-success">Completada</span>
                            @else
                                <span class="badge bg-danger">Cancelada</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        @if($cita->estado == 'pendiente')
                            <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">Cancelar</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No tienes citas registradas.
        </div>
    @endforelse
@endsection