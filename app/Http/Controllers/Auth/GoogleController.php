<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\GoogleVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'email' => 'El inicio con Google no está activo porque faltan GOOGLE_CLIENT_ID y GOOGLE_CLIENT_SECRET en el archivo .env. Consulta la guía de configuración de Google Cloud Console.'
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // ─── Caso 1: Ya tiene google_id → login directo ───────────────────
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                Auth::login($user);
                return redirect()->intended('/');
            }

            // ─── Caso 2: Email ya existe → vincular google_id y login directo ──
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $existingUser->avatar ?? $googleUser->getAvatar(),
                ]);
                Auth::login($existingUser);
                return redirect()->intended('/');
            }

            // ─── Caso 3: Usuario NUEVO → verificación por email ───────────────
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Guardar datos en sesión (NO crear usuario aún)
            session([
                'google_pending' => [
                    'name'       => $googleUser->getName(),
                    'email'      => $googleUser->getEmail(),
                    'google_id'  => $googleUser->getId(),
                    'avatar'     => $googleUser->getAvatar(),
                    'code'       => $code,
                    'expires_at' => now()->addMinutes(10)->timestamp,
                ],
            ]);

            // Enviar correo con el código
            Mail::to($googleUser->getEmail())->send(
                new GoogleVerificationMail($code, $googleUser->getName())
            );

            return redirect()->route('verify.notice');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('login')
                ->withErrors(['email' => 'Error de Google: ' . $e->getMessage()]);
        }
    }
}

