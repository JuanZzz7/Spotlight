<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Muestra la vista para solicitar el enlace de restablecimiento.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envía por correo electrónico el enlace de recuperación con el token.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email'    => 'Ingresa un formato de correo válido.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', trans($status));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($status)]);
    }

    /**
     * Muestra la vista con el formulario para fijar una nueva contraseña.
     */
    public function showResetForm(Request $request, ?string $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Valida el token y actualiza la contraseña del usuario.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ], [
            'email.required'     => 'El correo electrónico es requerido.',
            'email.email'        => 'Ingresa un correo electrónico válido.',
            'password.required'  => 'La nueva contraseña es requerida.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', trans($status));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($status)]);
    }
}
