@extends('layouts.app')

@section('title', 'Panel Admin - AppSalon')

@section('content')
    <div class="mb-4">
        <h2 class="fw-bold">Panel de Administración</h2>
        <p class="text-muted">Gestiona usuarios, servicios y citas del sistema</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="display-6 fw-bold">{{ $totalUsuarios }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h5 class="card-title">Servicios</h5>
                    <p class="display-6 fw-bold">{{ $totalServicios }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h5 class="card-title">Citas</h5>
                    <p class="display-6 fw-bold">{{ $totalCitas }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-dark">Gestionar Servicios</a>
        <a href="{{ route('admin.citas') }}" class="btn btn-outline-primary">Gestionar Citas</a>
    </div>
@endsection