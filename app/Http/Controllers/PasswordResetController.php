<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario) {

            return back()->withErrors([
                'email' => 'No existe una cuenta con ese correo'
            ]);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')
            ->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

        $link = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        Mail::html(
            "
            <h2>Recuperación de contraseña</h2>

            <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>

            <p>
                <a href='{$link}'>
                    Restablecer contraseña
                </a>
            </p>
            ",
            function ($message) use ($request) {

                $message->to($request->email)
                    ->subject('Recuperación de contraseña - AppSalon');

            }
        );

        return back()->with(
            'success',
            'Se ha enviado un enlace de recuperación a su correo'
        );
    }

    public function showResetForm($token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $registro = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$registro) {

            return back()->withErrors([
                'email' => 'Solicitud inválida'
            ]);
        }

        if (!Hash::check($request->token, $registro->token)) {

            return back()->withErrors([
                'email' => 'Token inválido'
            ]);
        }

        $usuario = User::where(
            'email',
            $request->email
        )->first();

        $usuario->update([
            'password' => $request->password
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Contraseña actualizada correctamente'
            );
    }
}