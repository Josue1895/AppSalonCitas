<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClienteMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->admin) {
            return redirect()->route('home')
                ->with('error', 'Solo los clientes pueden reservar citas.');
        }

        return $next($request);
    }
}
