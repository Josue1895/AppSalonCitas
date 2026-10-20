@extends('layouts.app')

@section('title', 'Nueva Contraseña')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-6">

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-4">

                <h2 class="fw-bold mb-3">
                    Nueva Contraseña
                </h2>

                <form action="{{ route('password.update') }}" method="POST">

                    @csrf

                    <input
                        type="hidden"
                        name="token"
                        value="{{ $token }}">

                    <input
                        type="hidden"
                        name="email"
                        value="{{ $email }}">

                    <div class="mb-3">

                        <label class="form-label">
                            Nueva contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Confirmar contraseña
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-success">

                        Actualizar contraseña

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection