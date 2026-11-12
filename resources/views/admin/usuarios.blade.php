@extends('layouts.admin')

@section('title', 'Administrar usuarios - RELUX')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Administrar usuarios</h2>
                <p class="text-muted mb-0">
                    Asigna o quita el rol de administrador a usuarios registrados. Las cuentas no se eliminan.
                </p>
            </div>
            <span class="badge rounded-pill bg-light text-dark px-3 py-2">
                Usuarios: {{ $usuarios->total() }}
            </span>
        </div>

        <form method="GET" action="{{ route('admin.usuarios') }}" class="mb-4">
            <div class="input-group">
                <input
                    type="search"
                    name="buscar"
                    value="{{ $busqueda }}"
                    class="form-control"
                    placeholder="Buscar por nombre, apellido o correo"
                    aria-label="Buscar usuarios"
                >
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
                @if($busqueda !== '')
                    <a href="{{ route('admin.usuarios') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                @if($usuarios->isEmpty())
                    <div class="text-center py-5">
                        <h5 class="fw-bold">No se encontraron usuarios</h5>
                        <p class="text-muted mb-0">Prueba con otro nombre o correo.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Usuario</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $usuario->nombre }} {{ $usuario->apellido }}
                                            @if($usuario->id === Auth::id())
                                                <span class="text-muted small">(Tú)</span>
                                            @endif
                                        </td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            @if($usuario->admin)
                                                <span class="badge rounded-pill bg-primary">Admin</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">Cliente</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($usuario->id !== Auth::id())
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.usuarios.rol', $usuario->id) }}"
                                                    class="d-inline"
                                                    onsubmit="return confirm('{{ $usuario->admin ? '¿Quitar el rol de administrador a este usuario?' : '¿Asignar el rol de administrador a este usuario?' }}');"
                                                >
                                                    @csrf
                                                    @method('PUT')
                                                    <input
                                                        type="hidden"
                                                        name="admin"
                                                        value="{{ $usuario->admin ? 0 : 1 }}"
                                                    >
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm {{ $usuario->admin ? 'btn-outline-danger' : 'btn-outline-primary' }}"
                                                    >
                                                        {{ $usuario->admin ? 'Quitar rol Admin' : 'Asignar rol Admin' }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">No modificable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $usuarios->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
