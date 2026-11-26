@extends('layouts.app')

@section('title', 'Gestión de Usuarios - AppSalon')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Gestión de Usuarios</h2>
    <p class="text-muted">
        Administra usuarios, estados y roles
    </p>
</div>

<a href="{{ route('admin.index') }}"
class="btn btn-outline-secondary mb-4">
Volver al Panel </a>

<div class="card border-0 shadow-sm rounded-4">

```
<div class="card-body">

    <div class="table-responsive">

        <table class="table table-striped align-middle">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                @foreach($usuarios as $usuario)

                    <tr>

                        <td>{{ $usuario->id }}</td>

                        <td>
                            {{ $usuario->nombre }}
                            {{ $usuario->apellido }}
                        </td>

                        <td>{{ $usuario->email }}</td>

                        <td>

                            @if($usuario->confirmado)

                                <span class="badge bg-success">
                                    Activo
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Inactivo
                                </span>

                            @endif

                        </td>

                        <td>

                            @if($usuario->admin)

                                <span class="badge bg-warning text-dark">
                                    Admin
                                </span>

                            @else

                                <span class="badge bg-info text-dark">
                                    Cliente
                                </span>

                            @endif

                        </td>

                        <td>

                            <div class="d-flex gap-2">

                                <form method="POST"
                                      action="{{ route('admin.usuarios.estado', $usuario->id) }}">
                                    @csrf

                                    <button class="btn btn-sm btn-outline-primary">

                                        {{ $usuario->confirmado ? 'Desactivar' : 'Activar' }}

                                    </button>

                                </form>

                                <form method="POST"
                                      action="{{ route('admin.usuarios.rol', $usuario->id) }}">
                                    @csrf

                                    <button class="btn btn-sm btn-outline-dark">

                                        {{ $usuario->admin ? 'Quitar Admin' : 'Hacer Admin' }}

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
```

</div>

@endsection
