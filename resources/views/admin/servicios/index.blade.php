@extends('layouts.app')

@section('title', 'Servicios Admin - AppSalon')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Administrar Servicios</h2>
            <p class="text-muted mb-0">Crea, edita y elimina servicios</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">Volver</a>
            <a href="{{ route('admin.servicios.create') }}" class="btn btn-success">Crear Servicio</a>
        </div>
    </div>

    <div class="row">
        @foreach($servicios as $servicio)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $servicio->nombre }}</h5>
                        <p class="card-text text-muted">{{ $servicio->descripcion }}</p>
                        <p><strong>Precio:</strong> ${{ $servicio->precio }}</p>
                        <p><strong>Duración:</strong> {{ $servicio->duracion }} min</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('admin.servicios.edit', $servicio->id) }}" class="btn btn-outline-primary w-50">Editar</a>

                        <form action="{{ route('admin.servicios.destroy', $servicio->id) }}" method="POST" class="w-50">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection