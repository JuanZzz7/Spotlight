<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo — Spotlight</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/tokens.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth-verify.css') }}" rel="stylesheet">
</head>
<body>

<div class="card-verify">
    {{-- Logo Spotlight --}}
    <div class="logo-wrap">
        <img src="{{ asset('images/logo.png') }}" alt="Spotlight">
    </div>

    {{-- Alerta de éxito (reenvío) --}}
    @if(session('success'))
        <div class="alert-vs alert alert-success d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->has('code'))
        <div class="alert-vs alert alert-danger d-flex align-items-center">
            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
            <div>{{ $errors->first('code') }}</div>
        </div>
    @endif

    {{-- Icono de correo animado --}}
    <div class="d-flex justify-content-center mb-2">
        <div class="mail-icon-wrap">
            <div class="ring ring-1"></div>
            <div class="ring ring-2"></div>
            <div class="ring ring-3"></div>
            <div class="icon-inner">
                <i class="bi bi-envelope-paper-fill"></i>
            </div>
        </div>
    </div>

    <h1 class="title">¡Revisa tu correo!</h1>
    <p class="subtitle">
        Enviamos un <strong>código de verificación de 6 dígitos</strong><br>
        a tu cuenta de correo:
    </p>

    {{-- Email box --}}
    <div class="email-box">
        <div class="icon"><i class="bi bi-envelope-check"></i></div>
        <div>
            <div class="label">Correo de verificación</div>
            <div class="value">{{ $email }}</div>
        </div>
    </div>

    {{-- Nota de expiración --}}
    <div class="expiry-note">
        <span class="expiry-dot"></span>
        El código expira en <strong style="color: #7F1D3F; font-weight: 700;">10 minutos</strong>
    </div>

    {{-- Botón principal --}}
    <a href="{{ route('verify.form') }}" class="btn-spotlight-primary">
        <i class="bi bi-shield-check me-2"></i>Ingresar mi código
    </a>

    <div class="divider-text">¿No recibiste el correo?</div>

    {{-- Reenviar --}}
    <form action="{{ route('verify.resend') }}" method="POST">
        @csrf
        <button type="submit" class="btn-spotlight-ghost">
            <i class="bi bi-arrow-clockwise me-2"></i>Reenviar código
        </button>
    </form>
</div>

</body>
</html>
