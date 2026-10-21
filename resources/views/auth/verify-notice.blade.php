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
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(circle at 15% 20%, rgba(127, 29, 63, 0.05) 0%, transparent 45%),
                        radial-gradient(circle at 85% 80%, rgba(217, 140, 154, 0.09) 0%, transparent 45%),
                        #FFFCF7;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #29252A;
        }

        /* Elementos decorativos de fondo sutiles */
        body::before {
            content: '';
            position: fixed;
            top: -120px; right: -120px;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(127, 29, 63, 0.04) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -90px; left: -90px;
            width: 340px; height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 140, 154, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-verify {
            background: #ffffff;
            border: 1px solid #EEDDDC;
            border-radius: 28px;
            padding: 48px 42px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 24px 60px rgba(127, 29, 63, 0.08), 0 4px 14px rgba(0,0,0,0.03);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Logo */
        .logo-wrap {
            margin-bottom: 28px;
        }
        .logo-wrap img {
            height: 52px;
            width: auto;
            object-fit: contain;
        }

        /* Icono animado de correo */
        .mail-icon-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 96px;
            height: 96px;
            margin-bottom: 24px;
        }
        .mail-icon-wrap .ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid rgba(127, 29, 63, 0.16);
            animation: ring-pulse 2.5s ease-out infinite;
        }
        .mail-icon-wrap .ring-1 { width: 96px; height: 96px; animation-delay: 0s; }
        .mail-icon-wrap .ring-2 { width: 122px; height: 122px; animation-delay: 0.6s; }
        .mail-icon-wrap .ring-3 { width: 148px; height: 148px; animation-delay: 1.2s; }
        
        .mail-icon-wrap .icon-inner {
            width: 68px; height: 68px;
            background: linear-gradient(135deg, #7F1D3F 0%, #5E122C 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            z-index: 2;
            box-shadow: 0 8px 24px rgba(127, 29, 63, 0.3);
        }
        .mail-icon-wrap .icon-inner i {
            color: #ffffff;
            font-size: 1.75rem;
            animation: bounce-icon 2s ease-in-out infinite;
        }

        @keyframes ring-pulse {
            0%   { transform: scale(0.85); opacity: 0.8; }
            50%  { transform: scale(1);    opacity: 0.35; }
            100% { transform: scale(1.18); opacity: 0; }
        }
        @keyframes bounce-icon {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-4px); }
        }

        .title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #29252A;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 0.92rem;
            color: #736B75;
            line-height: 1.6;
            margin-bottom: 26px;
        }
        .subtitle strong { 
            color: #7F1D3F; 
            font-weight: 700;
        }

        /* Info box email */
        .email-box {
            background: #FAF0F2;
            border: 1.5px solid rgba(127, 29, 63, 0.12);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-align: left;
        }
        .email-box .icon {
            width: 42px; height: 42px; flex-shrink: 0;
            background: #7F1D3F;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(127, 29, 63, 0.2);
        }
        .email-box .label { 
            font-size: 0.72rem; 
            color: #9E96A0; 
            text-transform: uppercase; 
            letter-spacing: 0.06em; 
            margin-bottom: 2px;
            font-weight: 600;
        }
        .email-box .value { 
            font-size: 0.92rem; 
            font-weight: 700; 
            color: #7F1D3F; 
            word-break: break-all; 
        }

        /* Expiración */
        .expiry-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.82rem;
            color: #736B75;
            margin-bottom: 26px;
        }
        .expiry-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #7F1D3F;
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.35; transform: scale(0.8); }
        }

        /* Botones */
        .btn-spotlight-primary {
            background: #7F1D3F;
            color: #ffffff !important;
            border: none;
            border-radius: 9999px;
            padding: 14px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(127, 29, 63, 0.25);
        }
        .btn-spotlight-primary:hover {
            background: #5E122C;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(127, 29, 63, 0.35);
            color: #ffffff !important;
        }
        
        .btn-spotlight-ghost {
            background: transparent;
            color: #7F1D3F;
            border: 1.5px solid #EEDDDC;
            border-radius: 9999px;
            padding: 13px 28px;
            font-size: 0.88rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: block;
        }
        .btn-spotlight-ghost:hover {
            background: #FAF0F2;
            border-color: #7F1D3F;
            color: #5E122C;
            transform: translateY(-1px);
        }

        /* Alert flash */
        .alert-vs {
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 0.86rem;
            margin-bottom: 22px;
            text-align: left;
            border: none;
        }
        .alert-vs.alert-success {
            background: #EBF7F1;
            color: #1A6342;
            border: 1px solid rgba(42, 135, 93, 0.2);
        }
        .alert-vs.alert-danger {
            background: #FDECEF;
            color: #BA2D42;
            border: 1px solid rgba(186, 45, 66, 0.2);
        }

        /* Divider */
        .divider-text {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: #9E96A0;
            font-size: 0.78rem;
            font-weight: 500;
        }
        .divider-text::before, .divider-text::after {
            content: ''; flex: 1;
            border-top: 1px solid #EEDDDC;
        }
    </style>
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
