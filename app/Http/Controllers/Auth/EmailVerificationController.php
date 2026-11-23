<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\GoogleVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * Vista 1: Aviso de que se envió el correo con el código.
     */
    public function showNotice()
    {
        // Si no hay datos pendientes en sesión, redirigir al login
        if (!session()->has('google_pending')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No hay ningún proceso de verificación activo.']);
        }

        $pending = session('google_pending');
        return view('auth.verify-notice', [
            'email'    => $pending['email'],
            'userName' => $pending['name'],
        ]);
    }

    /**
     * Vista 2: Formulario para ingresar el código de 6 dígitos.
     */
    public function showForm()
    {
        if (!session()->has('google_pending')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No hay ningún proceso de verificación activo.']);
        }

        $pending = session('google_pending');
        return view('auth.verify-form', [
            'email' => $pending['email'],
        ]);
    }

    /**
     * Procesa el código ingresado por el usuario.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'd1' => 'required|digits:1',
            'd2' => 'required|digits:1',
            'd3' => 'required|digits:1',
            'd4' => 'required|digits:1',
            'd5' => 'required|digits:1',
            'd6' => 'required|digits:1',
        ], [
            'd1.required' => 'Por favor ingresa el código completo.',
            'd1.digits'   => 'Cada campo debe ser un dígito numérico.',
        ]);

        if (!session()->has('google_pending')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'La sesión de verificación expiró. Inicia sesión de nuevo.']);
        }

        $pending = session('google_pending');
        $code    = $request->d1 . $request->d2 . $request->d3
                 . $request->d4 . $request->d5 . $request->d6;

        // Verificar expiración (10 minutos)
        if (now()->timestamp > $pending['expires_at']) {
            session()->forget('google_pending');
            return redirect()->route('verify.notice')
                ->withErrors(['code' => 'El código de verificación ha expirado. Por favor solicite un nuevo reenvío.']);
        }

        // Verificar código
        if ($code !== $pending['code']) {
            return back()->withErrors(['code' => 'Código de verificación incorrecto. Por favor verifique el correo e intente nuevamente.']);
        }

        // Código correcto → crear o actualizar usuario
        $existingUser = User::where('email', $pending['email'])->first();

        if ($existingUser) {
            $existingUser->update([
                'google_id'         => $pending['google_id'],
                'avatar'            => $existingUser->avatar ?? $pending['avatar'],
                'email_verified_at' => now(),
            ]);
            Auth::login($existingUser);
        } else {
            $newUser = User::create([
                'name'              => $pending['name'],
                'email'             => $pending['email'],
                'google_id'         => $pending['google_id'],
                'avatar'            => $pending['avatar'],
                'password'          => null,
                'role'              => 'user',
                'active'            => true,
                'email_verified_at' => now(),
            ]);
            Auth::login($newUser);
        }

        // Limpiar sesión de verificación
        session()->forget('google_pending');

        $currentUser = Auth::user();
        if ($currentUser->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Cuenta verificada con éxito. Bienvenido al panel de administración.');
        } elseif ($currentUser->isBusiness()) {
            return redirect()->route('business.dashboard')->with('success', 'Cuenta verificada con éxito. Bienvenido a tu panel de comercio.');
        }

        return redirect()->route('user.dashboard')->with('success', 'Tu cuenta ha sido verificada correctamente. ¡Bienvenido a Spotlight!');
    }

    /**
     * Reenvía el correo con un nuevo código.
     */
    public function resend()
    {
        if (!session()->has('google_pending')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No hay ningún proceso de verificación activo.']);
        }

        $pending = session('google_pending');
        $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session()->put('google_pending.code', $newCode);
        session()->put('google_pending.expires_at', now()->addMinutes(10)->timestamp);

        Mail::to($pending['email'])->send(
            new GoogleVerificationMail($newCode, $pending['name'])
        );

        return redirect()->route('verify.notice')
            ->with('success', 'Se ha enviado un nuevo código de verificación a ' . $pending['email']);
    }
}
