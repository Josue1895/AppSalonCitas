@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-6">

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-4">

                <h2 class="fw-bold mb-3">
                    Recuperar Contraseña
                </h2>

                <p class="text-muted">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>

                <form action="{{ route('password.email') }}" method="POST">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Enviar enlace

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection