
@extends('layouts.admin')

@section('title', 'Gestión de Citas - Relux')

@section('content')
    <div class="container-fluid">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Gestión de citas</h2>
                <p class="text-muted mb-0">
                    Consulta y administra las citas registradas en Relux.
                </p>
            </div>
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

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Citas registradas</h5>
                        <p class="text-muted small mb-0">
                            Listado general de reservas.
                        </p>
                    </div>

                    <span class="badge rounded-pill bg-light text-dark px-3 py-2">
                        Total: {{ $citas->count() }}
                    </span>
                </div>

                @forelse($citas as $cita)
                    @if($loop->first)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                    @endif

                    <tr>
                        <td class="fw-semibold">
                            #{{ $cita->id }}
                        </td>

                        <td>
                            @if($cita->usuario)
                                <div class="fw-semibold">
                                    {{ $cita->usuario->nombre }}
                                    {{ $cita->usuario->apellido }}
                                </div>
                            @else
                                <span class="text-muted">Sin usuario</span>
                            @endif
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ substr($cita->hora, 0, 5) }}
                                @if($cita->hora_fin)
                                    - {{ substr($cita->hora_fin, 0, 5) }}
                                @else
                                    <span class="text-muted small">- Sin hora final</span>
                                @endif
                            </div>
                        </td>

                        <td class="fw-semibold">
                            ${{ number_format((float) $cita->total, 2, ',', '.') }}
                        </td>

                        <td>
                            @php
                                $coloresEstado = [
                                    'pendiente' => 'warning',
                                    'confirmada' => 'info',
                                    'completada' => 'success',
                                    'cancelada' => 'danger',
                                ];

                                $colorEstado = $coloresEstado[$cita->estado] ?? 'secondary';
                            @endphp

                            <span class="badge rounded-pill bg-{{ $colorEstado }} text-dark">
                                {{ ucfirst($cita->estado) }}
                            </span>
                        </td>

                        <td>
                            <form
                                action="{{ route('admin.citas.estado', $cita->id) }}"
                                method="POST"
                                class="d-flex gap-2 align-items-center"
                            >
                                @csrf

                                <select
                                    name="estado"
                                    class="form-select form-select-sm"
                                    aria-label="Estado de la cita {{ $cita->id }}"
                                >
                                    <option value="pendiente" @selected($cita->estado == 'pendiente')>
                                        Pendiente
                                    </option>

                                    <option value="confirmada" @selected($cita->estado == 'confirmada')>
                                        Confirmada
                                    </option>

                                    <option value="completada" @selected($cita->estado == 'completada')>
                                        Completada
                                    </option>

                                    <option value="cancelada" @selected($cita->estado == 'cancelada')>
                                        Cancelada
                                    </option>
                                </select>

                                <button type="submit" class="btn btn-sm btn-primary">
                                    Guardar
                                </button>
                            </form>
                        </td>
                    </tr>

                    @if($loop->last)
                                </tbody>
                            </table>
                        </div>
                    @endif

                @empty
                    <div class="text-center py-5">
                        <h5 class="fw-bold">No hay citas registradas</h5>
                        <p class="text-muted mb-0">
                            Cuando los clientes realicen reservas, aparecerán aquí.
                        </p>
                    </div>
                @endforelse

            </div>
        </div>

    </div>
@endsection
