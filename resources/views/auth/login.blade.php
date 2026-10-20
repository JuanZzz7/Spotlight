@extends('layouts.app')
@section('title','Iniciar Sesión - Spotlight')
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body, h1, h2, h3, h4, h5, h6, p, a, button, input, label { font-family: var(--sl-font-body) !important; }
    
    .btn-spotlight {
        background: var(--sl-primary);
        color: var(--sl-text-on-dark) !important;
        border: none;
        border-radius: var(--sl-radius-pill);
        transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.25);
    }
    .btn-spotlight:hover { 
        background: var(--sl-primary-dark);
        transform: translateY(-2px); 
        box-shadow: 0 8px 22px rgba(127, 29, 63, 0.35);
        color: var(--sl-text-on-dark) !important;
    }
    .text-spotlight-magenta { color: var(--sl-primary) !important; }
    .btn-outline-spotlight {
        border: 2px solid var(--sl-primary);
        color: var(--sl-primary);
        border-radius: var(--sl-radius-pill);
    }
    .btn-outline-spotlight:hover {
        background: var(--sl-primary-soft);
        color: var(--sl-primary-dark);
    }
    .btn-check:checked + .btn-outline-primary {
        background: var(--sl-primary) !important;
        border-color: var(--sl-primary) !important;
        color: #ffffff !important;
    }
    .btn-outline-primary {
        border-color: var(--sl-primary) !important;
        color: var(--sl-primary) !important;
        border-radius: var(--sl-radius-pill) !important;
    }

    /* Translation Logic */
    html[lang="es"] .lang-en { display: none !important; }
    html[lang="en"] .lang-es { display: none !important; }
    
    .role-circle-label {
        width: 80px; height: 80px;
        border: 2px solid transparent;
        background: var(--sl-primary-soft);
        color: var(--sl-text-muted);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-check:checked + .role-circle-label {
        border-color: var(--sl-primary);
        background: #ffffff;
        color: var(--sl-primary);
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.2);
    }
    .role-text { font-size: 0.75rem; font-weight: 700; color: var(--sl-text-heading); margin-top: 8px; text-align: center; }

    /* Subtle background ambient */
    .blob {
        position: absolute;
        filter: blur(80px);
        z-index: 0;
        opacity: 0.7;
        border-radius: 50%;
        animation: floatBlob 15s infinite alternate ease-in-out;
    }
    @keyframes floatBlob {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(60px, 40px) scale(1.08); }
        100% { transform: translate(-40px, 60px) scale(0.92); }
    }
</style>
@endpush
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center position-relative overflow-hidden" style="background-color: var(--sl-bg);">
    
    <!-- Animated Gradient Blobs -->
    <div class="blob" style="top: -10%; left: -10%; width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(127,29,63,0.08) 0%, rgba(127,29,63,0) 70%); animation-delay: 0s;"></div>
    <div class="blob" style="bottom: -20%; right: -10%; width: 60vw; height: 60vw; background: radial-gradient(circle, rgba(217,140,154,0.12) 0%, rgba(217,140,154,0) 70%); animation-delay: -5s;"></div>
    <div class="blob" style="top: 20%; left: 30%; width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(246,231,235,0.4) 0%, rgba(246,231,235,0) 70%); animation-delay: -10s;"></div>

    <!-- Top Bar with Return Home & Language Toggle -->
    <div class="position-absolute top-0 start-0 w-100 p-3 p-md-4 d-flex justify-content-between align-items-center" style="z-index: 10;">
        <div>
            <a href="{{ route('home') }}" class="btn btn-sm btn-pill-light fw-bold px-3 py-1 d-inline-flex align-items-center gap-2 shadow-xs border text-decoration-none" style="font-size: 0.78rem;">
                <i class="bi bi-arrow-left"></i>
                <span class="lang-en">Home</span><span class="lang-es">Inicio</span>
            </a>
        </div>
        <div>
            <button id="btnSwitchLang" class="btn btn-sm btn-white fw-bold border shadow-sm rounded-pill px-3 py-1" style="color: var(--sl-text-heading); font-size: 0.78rem;">EN / ES</button>
        </div>
    </div>

    <div class="vs-card border-0" style="width: 100%; max-width: 820px; border-radius: 20px; overflow: hidden; box-shadow: var(--sl-shadow-xl); margin: 60px 15px 30px; z-index: 2;">
        <div class="row g-0">
            <!-- Left Side: Image/Branding with animated wave motion -->
            <div class="col-md-5 d-none d-md-block overflow-hidden" style="background: radial-gradient(circle at 30% 30%, #5E122C 0%, #29252A 100%); position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080" preserveAspectRatio="none" class="login-bg-svg position-absolute top-0 start-0 w-100 h-100" style="opacity: 0; z-index: 0;">
                  <defs>
                    <linearGradient id="SVGID_1_" x1="2880" x2="2880" y1="909.66" y2="170.6" gradientUnits="userSpaceOnUse">
                      <stop offset="0" stop-color="#D98C9A" />
                      <stop offset="1" stop-color="#7F1D3F" />
                    </linearGradient>
                  </defs>
                  <g>
                    <g class="g"><path class="path" fill="url(#SVGID_1_)" d="m1920 538.5 8.6-.4h8.6l8.6.3 4.3.3 4.3.4c11.4 1.1 22.8 2.9 34 5.4 22.5 5 44.3 12.4 65.6 21a730.8 730.8 0 0 1 62.3 28.7c20.3 10.3 40.3 21 60.2 31.7l29.8 15.9c9.9 5.2 19.9 10.3 29.9 15.3a727 727 0 0 0 60.7 27.2 360.6 360.6 0 0 0 62.6 18.3c10.6 2 21.2 3.4 31.8 4l8 .3 7.7-.1c2.6-.2 5.1-.2 7.7-.5l3.9-.4 1.9-.2 1.9-.3a177 177 0 0 0 30.8-7.4c10.2-3.3 20.2-7.5 30.1-12.1a448 448 0 0 0 57.5-33.2c37.2-24.9 72.1-53.7 106.7-82.8 34.6-29.1 68.7-59.1 105.7-85.8 9.3-6.6 18.7-13.1 28.5-19.1 9.8-6.1 19.8-11.7 30.3-16.7s21.3-9.4 32.7-12.6c11.4-3.2 23.2-5.3 35.3-5.7 12.1-.3 24.2 1.1 35.8 3.7 11.7 2.6 23 6.4 33.8 11 21.8 9.1 42 20.9 61.2 33.7 9.6 6.4 19 13.1 28.2 20a934 934 0 0 1 27.1 21.1c17.8 14.3 35.1 29 52.4 43.6 34.6 29.2 68.9 58.3 104.9 83.9 9 6.4 18.2 12.5 27.4 18.3 9.3 5.8 18.7 11.2 28.2 16.1 19.1 9.7 39 17.4 59 20.9l3.8.6 3.8.5 7.5.7 3.8.1 3.8.1h5.9l2-.1a243.7 243.7 0 0 0 63-10.6 388 388 0 0 0 31-10.3 706.6 706.6 0 0 0 60.9-26.6c20.1-9.8 40-20.3 60-30.8 20-10.6 40-21.2 60.4-31.4a875.4 875.4 0 0 1 62.3-28.5 430.2 430.2 0 0 1 65.5-20.9c11.2-2.5 22.6-4.3 34-5.5l4.3-.4 4.3-.3 8.6-.4 4.3-.1h4.3c2.9 0 5.7.2 8.6.3v3c-22.5 1.4-44.5 6-65.7 12.8a449 449 0 0 0-61.8 25.5c-40.1 19.7-78.4 43.3-117.4 66.4a1413.4 1413.4 0 0 1-59.3 33.9 675.1 675.1 0 0 1-62.4 29.9c-21.5 8.8-44 16.3-67.5 20.8a250.2 250.2 0 0 1-35.8 4.5l-2.3.1-2.3.1-4.6.1h-4.7l-4.7-.1-9.4-.7-4.6-.6-4.6-.7c-12.3-2-24.2-5.3-35.6-9.4-11.4-4.1-22.4-9-32.9-14.5a454.2 454.2 0 0 1-59.3-37.5c-37.3-27.5-71.2-58-105.1-88-16.9-15-33.8-30-50.8-44.5a845.7 845.7 0 0 0-52.2-41.8c-8.9-6.5-18-12.8-27.3-18.7a297 297 0 0 0-28.5-16.1c-9.7-4.7-19.7-8.8-30-11.8a128 128 0 0 0-31.2-5.2c-21.1-.5-42.6 4.8-62.9 13.4-20.3 8.6-39.7 20-58.2 32.6a590.9 590.9 0 0 0-27.3 19.7l-13.3 10.4-13.1 10.6a1788.2 1788.2 0 0 0-51.4 44.2c-33.9 29.9-67.2 60.8-103.3 89-18.1 14-37 27.3-57.5 38.7a267 267 0 0 1-31.9 15.2 191.4 191.4 0 0 1-34.6 10.1l-2.2.4-2.3.3-4.5.7-9.2 1-4.6.2c-1.5 0-3.1.2-4.6.1l-9-.1c-12-.3-23.9-1.6-35.5-3.7a340 340 0 0 1-67.4-19.9 674.5 674.5 0 0 1-62.6-29.3c-10.1-5.3-20.1-10.9-30-16.5l-14.8-8.5-14.7-8.6c-39.1-22.9-77.5-46.3-117.6-66.1-20-9.8-40.6-18.7-61.8-25.5a279.8 279.8 0 0 0-65.6-13.2v-2.9z" /><path class="path" fill="url(#SVGID_1_)" d="m0 538.5 8.6-.4h8.6l8.6.3 4.3.3 4.3.4c11.4 1.1 22.8 2.9 34 5.4 22.5 5 44.3 12.4 65.6 21a730.8 730.8 0 0 1 62.3 28.7c20.3 10.3 40.3 21 60.2 31.7l29.8 15.9c9.9 5.2 19.9 10.3 29.9 15.3a727 727 0 0 0 60.7 27.2 360.6 360.6 0 0 0 62.6 18.3c10.6 2 21.2 3.4 31.8 4l8 .3 7.7-.1c2.6-.2 5.1-.2 7.7-.5l3.9-.4 1.9-.2 1.9-.3a177 177 0 0 0 30.8-7.4c10.2-3.3 20.2-7.5 30.1-12.1a448 448 0 0 0 57.5-33.2c37.2-24.9 72.1-53.7 106.7-82.8 34.6-29.1 68.7-59.1 105.7-85.8 9.3-6.6 18.7-13.1 28.5-19.1 9.8-6.1 19.8-11.7 30.3-16.7s21.3-9.4 32.7-12.6c11.4-3.2 23.2-5.3 35.3-5.7 12.1-.3 24.2 1.1 35.8 3.7 11.7 2.6 23 6.4 33.8 11 21.8 9.1 42 20.9 61.2 33.7 9.6 6.4 19 13.1 28.2 20a934 934 0 0 1 27.1 21.1c17.8 14.3 35.1 29 52.4 43.6 34.6 29.2 68.9 58.3 104.9 83.9 9 6.4 18.2 12.5 27.4 18.3 9.3 5.8 18.7 11.2 28.2 16.1 19.1 9.7 39 17.4 59 20.9l3.8.6 3.8.5 7.5.7 3.8.1 3.8.1h5.9l2-.1a243.7 243.7 0 0 0 63-10.6 388 388 0 0 0 31-10.3 706.6 706.6 0 0 0 60.9-26.6c20.1-9.8 40-20.3 60-30.8 20-10.6 40-21.2 60.4-31.4a875.4 875.4 0 0 1 62.3-28.5 430.2 430.2 0 0 1 65.5-20.9c11.2-2.5 22.6-4.3 34-5.5l4.3-.4 4.3-.3 8.6-.4 4.3-.1h4.3c2.9 0 5.7.2 8.6.3v3c-22.5 1.4-44.5 6-65.7 12.8a449 449 0 0 0-61.8 25.5c-40.1 19.7-78.4 43.3-117.4 66.4a1413.4 1413.4 0 0 1-59.3 33.9 675.1 675.1 0 0 1-62.4 29.9c-21.5 8.8-44 16.3-67.5 20.8a250.2 250.2 0 0 1-35.8 4.5l-2.3.1-2.3.1-4.6.1h-4.7l-4.7-.1-9.4-.7-4.6-.6-4.6-.7c-12.3-2-24.2-5.3-35.6-9.4-11.4-4.1-22.4-9-32.9-14.5a454.2 454.2 0 0 1-59.3-37.5c-37.3-27.5-71.2-58-105.1-88-16.9-15-33.8-30-50.8-44.5a845.7 845.7 0 0 0-52.2-41.8c-8.9-6.5-18-12.8-27.3-18.7a297 297 0 0 0-28.5-16.1c-9.7-4.7-19.7-8.8-30-11.8a128 128 0 0 0-31.2-5.2c-21.1-.5-42.6 4.8-62.9 13.4-20.3 8.6-39.7 20-58.2 32.6a590.9 590.9 0 0 0-27.3 19.7l-13.3 10.4-13.1 10.6a1788.2 1788.2 0 0 0-51.4 44.2c-33.9 29.9-67.2 60.8-103.3 89-18.1 14-37 27.3-57.5 38.7a267 267 0 0 1-31.9 15.2 191.4 191.4 0 0 1-34.6 10.1l-2.2.4-2.3.3-4.5.7-9.2 1-4.6.2c-1.5 0-3.1.2-4.6.1l-9-.1c-12-.3-23.9-1.6-35.5-3.7a340 340 0 0 1-67.4-19.9 674.5 674.5 0 0 1-62.6-29.3c-10.1-5.3-20.1-10.9-30-16.5l-14.8-8.5-14.7-8.6c-39.1-22.9-77.5-46.3-117.6-66.1-20-9.8-40.6-18.7-61.8-25.5A278.2 278.2 0 0 0 0 541.5v-3z" /></g>
                  </g>
                </svg>
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(41, 37, 42, 0.35); z-index: 1;"></div>
                <div class="position-relative d-flex flex-column align-items-center justify-content-center h-100 p-4 text-white text-center" style="z-index: 2;">
                    <div class="bg-white rounded-circle shadow-lg mb-3 d-flex align-items-center justify-content-center" style="width: 84px; height: 84px; padding: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.25);">
                        <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <h3 class="fw-bold text-white mb-2 fs-4" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">SPOTLIGHT</h3>
                    <p class="text-white-50 small px-2 mb-0" style="max-width: 250px; line-height: 1.5;">
                        <span class="lang-en">Connecting creative entrepreneurs and unique brands.</span>
                        <span class="lang-es">Conectando emprendimientos creativos y marcas únicas.</span>
                    </p>
                </div>
            </div>
            
            <!-- Right Side: Login Form -->
            <div class="col-md-7 p-3 p-md-4 bg-white position-relative">
                <div class="text-center d-md-none mb-3">
                    <div class="d-inline-flex bg-white rounded-circle p-2 shadow-sm border" style="width: 58px; height: 58px; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                </div>
                
                <!-- Tabs Top -->
                <div class="d-flex mb-5">
                    <a href="{{ route('register') }}" class="text-muted fw-bold text-decoration-none text-center py-3" style="flex: 1; border-bottom: 2px solid var(--sl-border);">
                        <span class="lang-en">Register</span><span class="lang-es">Registro</span>
                    </a>
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none text-center py-3" style="flex: 1; color: var(--sl-primary); border-bottom: 3px solid var(--sl-primary);">
                        <span class="lang-en">Login</span><span class="lang-es">Iniciar Sesión</span>
                    </a>
                </div>
                
                <div class="mb-4 text-center">
                    <h3 class="fw-bold text-dark fs-3 mb-2" style="font-family: 'Outfit', sans-serif;">
                        <span class="lang-en">Welcome back</span><span class="lang-es">Bienvenido de nuevo</span>
                    </h3>
                    <p class="text-muted fw-normal">
                        <span class="lang-en">Enter your credentials to access your dashboard.</span>
                        <span class="lang-es">Ingresa tus credenciales para acceder a tu panel.</span>
                    </p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 d-flex align-items-center rounded-4" style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-sm fw-semibold text-dark mb-2">
                            <span class="lang-en">Email Address</span><span class="lang-es">Correo electrónico</span>
                        </label>
                        <input type="email" name="email" class="form-control form-control-lg text-sm rounded-pill px-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" placeholder="ejemplo@correo.com" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label text-sm fw-semibold text-dark mb-0">
                                <span class="lang-en">Password</span><span class="lang-es">Contraseña</span>
                            </label>
                            <a href="#" class="text-decoration-none text-sm fw-semibold" style="color: var(--sl-primary);">
                                <span class="lang-en">Forgot password?</span><span class="lang-es">¿Olvidaste tu contraseña?</span>
                            </a>
                        </div>
                        <input type="password" name="password" class="form-control form-control-lg text-sm rounded-pill px-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" placeholder="••••••••" required>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label text-sm fw-semibold text-dark mb-3">
                            <span class="lang-en">Select your role to get started.</span><span class="lang-es">Selecciona tu rol para comenzar.</span>
                        </label>
                        <div class="d-flex justify-content-center gap-4">
                            @foreach(['user'=>['en'=>'USER','es'=>'USUARIO', 'icon'=>'bi-person-fill'], 'business'=>['en'=>'ENTERPRISE','es'=>'EMPRESA', 'icon'=>'bi-shop']] as $val=>$data)
                            <div class="text-center">
                                <input type="radio" class="btn-check" name="role" id="role_{{ $val }}" value="{{ $val }}" {{ old('role')===$val || (!old('role') && $val==='user') ? 'checked' : '' }}>
                                <label class="role-circle-label" for="role_{{ $val }}">
                                    <i class="bi {{ $data['icon'] }} fs-4 mb-1"></i>
                                </label>
                                <div class="role-text"><span class="lang-en">{{ $data['en'] }}</span><span class="lang-es">{{ $data['es'] }}</span></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-spotlight btn-lg w-100 fw-semibold shadow-sm mb-4 py-3">
                        <span class="lang-en">Log in</span><span class="lang-es">Ingresar a mi Panel</span>
                    </button>

                    <div class="position-relative mb-4 text-center">
                        <hr class="text-muted opacity-25">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted text-sm fw-medium">
                            <span class="lang-en">Or continue with</span><span class="lang-es">O continuar con</span>
                        </span>
                    </div>

                    <a href="{{ route('google.redirect') }}" class="btn btn-light rounded-pill border btn-lg w-100 fw-semibold mb-4 d-flex align-items-center justify-content-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                        <span class="lang-en">Continue with Google</span><span class="lang-es">Continuar con Google</span>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- LANGUAGE TOGGLE SCRIPT ---
    const btn = document.getElementById("btnSwitchLang");
    const html = document.documentElement;

    const savedLang = localStorage.getItem('spotlight-lang') || 'es';
    html.setAttribute("lang", savedLang);

    if (btn) {
        btn.addEventListener("click", () => {
            const current = html.getAttribute("lang");
            const nextLang = current === 'en' ? 'es' : 'en';
            html.setAttribute("lang", nextLang);
            localStorage.setItem('spotlight-lang', nextLang);
        });
    }

    // --- GSAP Animation for Background SVG ---
    const Line = ($el) => {
      const $paths = $el.querySelectorAll(".path");
      const tl = gsap.timeline();
      const duration = gsap.utils.random(40, 80);
      const y = gsap.utils.random(-250, 250);
      const rotate = gsap.utils.random(-20, 20);
      const scaleXFrom = gsap.utils.random(2, 2.5);
      const scaleXTo = gsap.utils.random(1.5, 1.75);
      const scaleYFrom = gsap.utils.random(1.5, 2);
      const scaleYTo = gsap.utils.random(0.6, 0.7);
      const opacityFrom = gsap.utils.random(0.75, 0.85);
      const opacityTo = gsap.utils.random(0.85, 1);
      const ease = gsap.utils.random([
        "power2.inOut",
        "power3.inOut",
        "power4.inOut",
        "sine.inOut"
      ]);
      tl.to($paths, {
        xPercent: -100,
        duration: duration,
        ease: "none",
        repeat: -1
      });

      tl.fromTo(
        $el,
        {
          y,
          opacity: opacityFrom,
          rotate,
          scaleY: scaleYFrom,
          scaleX: scaleXFrom,
          transformOrigin: "50% 50%"
        },
        {
          y: y * -1,
          opacity: opacityTo,
          rotate: rotate * -1,
          scaleY: scaleYTo,
          scaleX: scaleXTo,
          repeat: -1,
          yoyo: true,
          yoyoEase: ease,
          duration: duration * 0.25,
          ease: ease,
          transformOrigin: "50% 50%"
        },
        0
      );

      tl.seek(gsap.utils.random(10, 20));
    };

    if (typeof gsap !== 'undefined') {
        gsap.utils.toArray(".login-bg-svg .g").forEach(($el) => Line($el));
        gsap.to(".login-bg-svg", { opacity: 0.9, duration: 1 });
    }
});
</script>
@endpush
@endsection
