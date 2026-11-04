
@extends('layouts.admin')

@section('title', 'Administrar Servicios - Relux')

@section('content')
    <div class="container-fluid">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Administrar servicios</h2>
                <p class="text-muted mb-0">
                    Crea, consulta, edita y elimina los servicios de Relux.
                </p>
            </div>

            <a href="{{ route('admin.servicios.create') }}"
               class="btn btn-primary">
                Crear servicio
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if($servicios->isEmpty())
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <h5 class="fw-bold">No hay servicios registrados</h5>
                    <p class="text-muted mb-3">
                        Puedes comenzar registrando el primer servicio.
                    </p>
                    <a href="{{ route('admin.servicios.create') }}" class="btn btn-primary">
                        Crear primer servicio
                    </a>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($servicios as $servicio)
                    <div class="col-md-6 col-xl-4 mb-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <h5 class="fw-bold mb-0">
                                        {{ $servicio->nombre }}
                                    </h5>
                                </div>

                                <p class="text-muted mb-4">
                                    {{ $servicio->descripcion ?: 'Sin descripción disponible.' }}
                                </p>

                                <div class="mb-3">
                                    <span class="text-muted small">Precio</span>
                                    <div class="fw-semibold">
                                        ${{ number_format((float) $servicio->precio, 2, ',', '.') }}
                                    </div>
                                </div>

                                <div>
                                    <span class="text-muted small">Duración</span>
                                    <div class="fw-semibold">
                                        {{ $servicio->duracion }} minutos
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                                <div class="d-flex gap-2">
                                    <a
                                        href="{{ route('admin.servicios.edit', $servicio->id) }}"
                                        class="btn btn-outline-secondary w-50"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="{{ route('admin.servicios.destroy', $servicio->id) }}"
                                        method="POST"
                                        class="w-50"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este servicio?');"
                                    >
                                        @csrf

                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
