@extends('layouts.app')

@section('title', 'Crear Servicio - AppSalon')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-4">Crear Servicio</h2>

                    <form method="POST" action="{{ route('admin.servicios.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Duración</label>
                            <input type="number" name="duracion" value="{{ old('duracion') }}" class="form-control">
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary w-50">Cancelar</a>
                            <button type="submit" class="btn btn-success w-50">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection