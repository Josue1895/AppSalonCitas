@extends('layouts.app')

@section('title', 'Gestión de Citas - AppSalon')

@section('content')
    <div class="mb-4">
        <h2 class="fw-bold">Gestión de Citas</h2>
        <p class="text-muted">Administra el estado de las citas registradas</p>
    </div>

    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary mb-4">Volver al panel admin</a>

    @forelse($citas as $cita)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <strong>ID</strong>
                        <p>{{ $cita->id }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Usuario</strong>
                        <p>
                            @if($cita->usuario)
                                {{ $cita->usuario->nombre }} {{ $cita->usuario->apellido }}
                            @else
                                Sin usuario
                            @endif
                        </p>
                    </div>
                    <div class="col-md-2">
                        <strong>Fecha</strong>
                        <p>{{ $cita->fecha }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Hora</strong>
                        <p>{{ $cita->hora }}</p>
                    </div>
                    <div class="col-md-1">
                        <strong>Total</strong>
                        <p>${{ $cita->total }}</p>
                    </div>
                    <div class="col-md-2">
                        <form action="{{ route('admin.citas.estado', $cita->id) }}" method="POST">
                            @csrf
                            <label class="form-label"><strong>Estado</strong></label>
                            <select name="estado" class="form-select form-select-sm mb-2">
                                <option value="pendiente" @selected($cita->estado == 'pendiente')>Pendiente</option>
                                <option value="confirmada" @selected($cita->estado == 'confirmada')>Confirmada</option>
                                <option value="completada" @selected($cita->estado == 'completada')>Completada</option>
                                <option value="cancelada" @selected($cita->estado == 'cancelada')>Cancelada</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No hay citas registradas.
        </div>
    @endforelse
@endsection