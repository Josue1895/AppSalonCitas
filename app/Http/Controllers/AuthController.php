<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.registro');
    }

    public function register(RegisterRequest $request)
    {
        $usuario = User::create([
            'nombre' => htmlspecialchars($request->nombre),
            'apellido' => htmlspecialchars($request->apellido),
            'email' => htmlspecialchars($request->email),
            'password' => $request->password,
            'telefono' => htmlspecialchars($request->telefono),
            'admin' => 0,
            'confirmado' => 1,
            'token' => null
        ]);

        return redirect()->route('login')->with('success', 'Usuario registrado correctamente');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credenciales = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            $usuario = Auth::user();
            return redirect()->route($usuario->admin ? 'admin.index' : 'citas.index')->with('success', 'Bienvenido');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }
}