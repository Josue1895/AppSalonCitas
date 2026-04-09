<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        if (!Auth::user()->admin) {
            return redirect()->route('home')->with('error', 'No tienes permisos para entrar al panel admin');
        }

        return $next($request);
    }
}