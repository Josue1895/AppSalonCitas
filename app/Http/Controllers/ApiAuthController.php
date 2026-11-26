<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {

            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);

        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'usuario' => $usuario
        ]);
    }
}