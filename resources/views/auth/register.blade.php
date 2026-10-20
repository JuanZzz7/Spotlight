@extends('layouts.app')
@section('title','Registrarse - Spotlight')
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<style>
    body, h1, h2, h3, h4, h5, h6, p, a, button, input, label { font-family: 'Plus Jakarta Sans', sans-serif; }
    
    /* Interactive Map Styles */
    #company_reg_map {
        width: 100%;
        height: 100%;
        min-height: 230px;
        z-index: 1;
    }
    .leaflet-pane { z-index: 10 !important; }
    .leaflet-top, .leaflet-bottom { z-index: 20 !important; }
    
    .btn-spotlight {
        background: var(--sl-primary);
        color: var(--sl-text-on-dark) !important;
        border: none;
        border-radius: var(--sl-radius-pill, 9999px);
        transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
        box-shadow: 0 4px 15px rgba(127, 29, 63, 0.25);
    }
    .btn-spotlight:hover { 
        background: var(--sl-primary-dark);
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px rgba(127, 29, 63, 0.35);
        color: var(--sl-text-on-dark) !important;
    }
    .text-spotlight-magenta { color: var(--sl-primary) !important; }
    .btn-outline-spotlight {
        border: 2px solid var(--sl-primary);
        color: var(--sl-primary) !important;
        border-radius: var(--sl-radius-pill, 9999px);
        background: transparent;
        transition: all 0.2s;
    }
    .btn-outline-spotlight:hover {
        background: var(--sl-primary-soft);
        color: var(--sl-primary-dark);
        transform: translateY(-1px);
    }
    
    /* Mockup specifics */
    .register-glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--sl-border);
        border-radius: 16px;
        padding: 1.5rem;
    }

    /* Translation Logic */
    html[lang="es"] .lang-en { display: none !important; }
    html[lang="en"] .lang-es { display: none !important; }
    
    .role-circle-label {
        width: 80px; height: 80px;
        border: 2px solid var(--sl-border);
        background: var(--sl-primary-soft);
        color: var(--sl-text-muted);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .role-circle-label:hover {
        border-color: var(--sl-accent);
        transform: translateY(-2px);
    }
    .btn-check:checked + .role-circle-label {
        border-color: var(--sl-primary);
        background: #ffffff;
        color: var(--sl-primary);
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.2);
    }
    .role-text { font-size: 0.75rem; font-weight: 700; color: var(--sl-text-heading); margin-top: 8px; text-align: center; }

    /* Country Selection Cards */
    .country-select-card {
        border: 2px solid var(--sl-border, #e2e8f0);
        background: #ffffff;
        border-radius: 14px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        color: var(--sl-text-body, #475569);
    }
    .country-select-card:hover {
        border-color: var(--sl-accent, #D98C9A);
        background: #FFFCF7;
        transform: translateY(-2px);
    }
    .btn-check:checked + .country-select-card {
        border-color: var(--sl-primary, #7F1D3F) !important;
        background-color: #FFFCF7 !important;
        color: var(--sl-primary, #7F1D3F) !important;
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.15);
    }
    .btn-check:checked + .country-select-card .fw-bold {
        color: var(--sl-primary, #7F1D3F) !important;
    }

    /* Moving background blobs */
    .blob {
        position: absolute;
        filter: blur(80px);
        z-index: 0;
        opacity: 0.9;
        border-radius: 50%;
        animation: floatBlob 15s infinite alternate ease-in-out;
    }
    @keyframes floatBlob {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(100px, 50px) scale(1.1); }
        100% { transform: translate(-50px, 100px) scale(0.9); }
    }
</style>
@endpush
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center position-relative py-5" style="background-color: var(--sl-bg); min-height: 100vh;">
    
    <!-- Animated Gradient Blobs in separate overflow-hidden container -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none; z-index: 0;">
        <div class="blob" style="top: -10%; left: -10%; width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(127,29,63,0.08) 0%, rgba(127,29,63,0) 70%); animation-delay: 0s;"></div>
        <div class="blob" style="bottom: -20%; right: -10%; width: 60vw; height: 60vw; background: radial-gradient(circle, rgba(217,140,154,0.12) 0%, rgba(217,140,154,0) 70%); animation-delay: -5s;"></div>
        <div class="blob" style="top: 20%; left: 30%; width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(246,231,235,0.4) 0%, rgba(246,231,235,0) 70%); animation-delay: -10s;"></div>
    </div>

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
            <!-- Left Side: Branding with animated wave motion -->
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
                        <span class="lang-en">Join the creative entrepreneurial community.</span>
                        <span class="lang-es">Únete a la comunidad de emprendedores creativos.</span>
                    </p>
                </div>
            </div>

            <!-- Right Side: Register Form -->
            <div class="col-md-7 p-3 p-md-4 bg-white position-relative">
                <div class="text-center d-md-none mb-3">
                    <div class="d-inline-flex bg-white rounded-circle p-2 shadow-sm border" style="width: 58px; height: 58px; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                </div>
                
                <!-- Tabs Top -->
                <div class="d-flex mb-4">
                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none text-center py-2" style="flex: 1; color: var(--sl-primary); border-bottom: 3px solid var(--sl-primary);">
                        <span class="lang-en">Register</span><span class="lang-es">Registro</span>
                    </a>
                    <a href="{{ route('login') }}" class="text-muted fw-bold text-decoration-none text-center py-2" style="flex: 1; border-bottom: 2px solid var(--sl-border);">
                        <span class="lang-en">Login</span><span class="lang-es">Iniciar Sesión</span>
                    </a>
                </div>

                <div class="mb-3 text-center">
                    <h3 class="fw-bold text-dark fs-4 mb-1" style="font-family: 'Outfit', sans-serif;">
                        <span class="lang-en">Create Account</span><span class="lang-es">Crea tu cuenta</span>
                    </h3>
                    <p class="text-muted small mb-0">
                        <span class="lang-en">Select your role to get started.</span><span class="lang-es">Completa los campos para registrarte en la plataforma.</span>
                    </p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 d-flex align-items-center rounded-4 py-2 px-3 mb-3" style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626; font-size: 0.85rem;">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" id="mainRegisterForm" enctype="multipart/form-data">
                    @csrf
                    <div id="step-1">
                        <!-- Selector de País -->
                        <div class="mb-3">
                            <label class="form-label text-sm fw-semibold text-dark mb-2">
                                <span class="lang-en">Select Country</span><span class="lang-es">Selecciona tu País</span>
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check country-radio" name="country" id="country_co" value="CO" {{ old('country', 'CO') === 'CO' ? 'checked' : '' }}>
                                    <label class="country-select-card text-center w-100 p-2 rounded-3 h-100 d-flex flex-column align-items-center justify-content-center" for="country_co" style="cursor: pointer;">
                                        <span class="fs-4 d-block mb-1">🇨🇴</span>
                                        <span class="fw-bold small d-block">Colombia</span>
                                        <span class="text-muted text-xs d-block">Ubaté (COP $)</span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check country-radio" name="country" id="country_mx" value="MX" {{ old('country') === 'MX' ? 'checked' : '' }}>
                                    <label class="country-select-card text-center w-100 p-2 rounded-3 h-100 d-flex flex-column align-items-center justify-content-center" for="country_mx" style="cursor: pointer;">
                                        <span class="fs-4 d-block mb-1">🇲🇽</span>
                                        <span class="fw-bold small d-block">México</span>
                                        <span class="text-muted text-xs d-block">Puebla (MXN $)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm fw-semibold text-dark mb-1">
                                <span class="lang-en">Full Name</span><span class="lang-es">Nombre completo</span>
                            </label>
                            <input type="text" name="name" class="form-control text-sm rounded-pill px-3 py-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" placeholder="Tu nombre completo" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-sm fw-semibold text-dark mb-1">
                                <span class="lang-en">Email Address</span><span class="lang-es">Correo electrónico</span>
                            </label>
                            <input type="email" name="email" class="form-control text-sm rounded-pill px-3 py-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" placeholder="ejemplo@correo.com" value="{{ old('email') }}" required>
                        </div>
                        
                        <!-- Teléfono con Indicativo Dinámico -->
                        <div class="mb-3">
                            <label class="form-label text-sm fw-semibold text-dark mb-1">
                                <span class="lang-en">Phone Number</span><span class="lang-es">Número de teléfono</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-sm fw-bold border-end-0 rounded-start-pill px-3" id="step1_phone_badge">{{ old('country', 'CO') === 'MX' ? '+52' : '+57' }}</span>
                                <input type="hidden" name="phone_prefix" id="step1_phone_prefix" value="{{ old('phone_prefix', old('country', 'CO') === 'MX' ? '+52' : '+57') }}">
                                <input type="tel" name="phone" id="step1_phone_input" class="form-control text-sm border-start-0 rounded-end-pill px-3 py-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" placeholder="{{ old('country', 'CO') === 'MX' ? '222 123 4567' : '300 123 4567' }}" value="{{ old('phone') }}">
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                <span class="lang-en">For notifications and purchase tracking</span><span class="lang-es">Para notificaciones y seguimiento de compras</span>
                            </small>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-sm fw-semibold text-dark mb-1">
                                    <span class="lang-en">Password</span><span class="lang-es">Contraseña</span>
                                </label>
                                <input type="password" name="password" class="form-control text-sm rounded-pill px-3 py-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" placeholder="••••••••" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-sm fw-semibold text-dark mb-1">
                                    <span class="lang-en">Confirm Password</span><span class="lang-es">Confirmar contraseña</span>
                                </label>
                                <input type="password" name="password_confirmation" class="form-control text-sm rounded-pill px-3 py-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" placeholder="••••••••" required>
                            </div>
                        </div>



                        <!-- Role Selection UI matching Login -->
                        <div class="mb-3">
                            <label class="form-label text-sm fw-semibold text-dark mb-2">
                                <span class="lang-en">Select your role to get started.</span><span class="lang-es">Selecciona tu rol para comenzar.</span>
                            </label>
                            <div class="d-flex justify-content-center gap-4">
                                @foreach(['user'=>['en'=>'USER','es'=>'USUARIO', 'icon'=>'bi-person-fill'], 'business'=>['en'=>'ENTERPRISE','es'=>'EMPRESA', 'icon'=>'bi-shop']] as $val=>$data)
                                <div class="text-center">
                                    <input type="radio" class="btn-check role-radio" name="role_ui" id="role_ui_{{ $val }}" value="{{ $val }}" {{ old('role', 'user')===$val ? 'checked' : '' }}>
                                    <label class="role-circle-label" for="role_ui_{{ $val }}">
                                        <i class="bi {{ $data['icon'] }} fs-4 mb-1"></i>
                                    </label>
                                    <div class="role-text"><span class="lang-en">{{ $data['en'] }}</span><span class="lang-es">{{ $data['es'] }}</span></div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="role" id="role_input" value="{{ old('role', 'user') }}">

                        <!-- Action Buttons Step 1 -->
                        <div id="action-buttons-step1" class="mt-3">
                            <!-- This button is shown if user role is selected -->
                            <button type="submit" id="btn-submit-user" class="btn btn-spotlight w-100 fw-semibold shadow-sm mb-3 py-2" style="{{ old('role', 'user') === 'user' ? '' : 'display: none;' }}">
                                <span class="lang-en">Register as User</span><span class="lang-es">Registrarse como Usuario</span>
                            </button>
                            <!-- This button is shown if business role is selected -->
                            <button type="button" id="btn-next-step" class="btn btn-spotlight w-100 fw-semibold shadow-sm mb-3 py-2" style="{{ old('role', 'user') === 'business' ? '' : 'display: none;' }}">
                                <span class="lang-en">Register as Entrepreneur</span><span class="lang-es">Continuar registro de empresa</span> <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <div id="google-registration-section">
                            <div class="position-relative mb-3 text-center">
                                <hr class="text-muted opacity-25 my-2">
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted text-xs fw-medium">
                                    <span class="lang-en">Or register with</span><span class="lang-es">O registrarse con</span>
                                </span>
                            </div>

                            <a href="{{ route('google.redirect') }}" class="btn btn-light rounded-pill border w-100 fw-semibold mb-3 d-flex align-items-center justify-content-center gap-2 py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                                Google
                            </a>
                        </div>
                    </div>

                    <!-- Step 2: Business Fields Container -->
                    <div id="step-2" style="display: none;">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <button type="button" id="btn-prev-step" class="btn btn-light rounded-circle p-2 me-3 shadow-sm border" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-arrow-left"></i></button>
                            <h4 class="fw-bold text-dark mb-0 fs-4">
                                <span class="lang-en">Business Information</span><span class="lang-es">Información de tu Negocio</span>
                            </h4>
                        </div>
                        <div id="business-registration-fields" class="mb-4">
                            @include('business.partials.register-form')
                        </div>
                        <button type="submit" id="btn-submit-business" class="btn btn-spotlight btn-lg w-100 fw-semibold shadow-sm mb-4 py-3">
                            <span class="lang-en">Register as Entrepreneur</span><span class="lang-es">Finalizar y Crear cuenta</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
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
      const opacityFrom = gsap.utils.random(0.75, 0.8);
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
        gsap.to(".login-bg-svg", { opacity: 1, duration: 1 });
    }

    // --- Stepper & Country Logic ---
    const roleInput = document.getElementById('role_input');
    const btnNextStep = document.getElementById('btn-next-step');
    const btnSubmitUser = document.getElementById('btn-submit-user');
    const btnPrevStep = document.getElementById('btn-prev-step');
    
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const businessFieldsContainer = document.getElementById('business-registration-fields');

    // Country & Buyer References
    const countryRadios = document.querySelectorAll('.country-radio');
    const phoneBadge = document.getElementById('step1_phone_badge');
    const phonePrefixInput = document.getElementById('step1_phone_prefix');
    const phoneInput = document.getElementById('step1_phone_input');
    function getSelectedCountry() {
        const checked = document.querySelector('.country-radio:checked');
        return checked ? checked.value : 'CO';
    }

    function handleCountryChange(country) {
        const isMX = (country === 'MX');
        if (phoneBadge) phoneBadge.textContent = isMX ? '+52' : '+57';
        if (phonePrefixInput) phonePrefixInput.value = isMX ? '+52' : '+57';
        if (phoneInput) phoneInput.placeholder = isMX ? '222 123 4567' : '300 123 4567';

        // Inform Step 2 partial about country switch
        if (typeof window.switchRegistrationCountry === 'function') {
            window.switchRegistrationCountry(country);
        }
    }

    countryRadios.forEach(r => {
        r.addEventListener('change', () => handleCountryChange(r.value));
    });

    function goToStep2() {
        const nameInput = document.querySelector('#step-1 input[name="name"]');
        const emailInput = document.querySelector('#step-1 input[name="email"]');
        const pwdInput = document.querySelector('#step-1 input[name="password"]');
        const pwdConfInput = document.querySelector('#step-1 input[name="password_confirmation"]');
        
        // Validation before proceeding
        if (!nameInput.value || !emailInput.value || !pwdInput.value || !pwdConfInput.value) {
            alert('Por favor, completa todos los campos de tu cuenta antes de continuar.');
            return;
        }
        
        if (pwdInput.value.length < 8) {
            alert('La contraseña debe tener al menos 8 caracteres.');
            return;
        }

        if (pwdInput.value !== pwdConfInput.value) {
            alert('Las contraseñas no coinciden.');
            return;
        }

        const selectedCountry = getSelectedCountry();

        // Remove required attributes from step 1 so browser doesn't block hidden inputs
        nameInput.removeAttribute('required');
        emailInput.removeAttribute('required');
        pwdInput.removeAttribute('required');
        pwdConfInput.removeAttribute('required');

        roleInput.value = 'business';

        // Hide Step 1, Show Step 2
        gsap.to(step1, { opacity: 0, x: -20, duration: 0.3, onComplete: () => {
            step1.style.display = 'none';
            step2.style.display = 'block';
            gsap.fromTo(step2, { opacity: 0, x: 20 }, { opacity: 1, x: 0, duration: 0.3, ease: "power2.out" });

            // Activate business fields for the selected country
            if (typeof window.switchRegistrationCountry === 'function') {
                window.switchRegistrationCountry(selectedCountry);
            }

            // Initialize / recalculate map size once step-2 is visible (if Colombia)
            if (selectedCountry === 'CO') {
                setTimeout(() => {
                    if (typeof window.initCompanyRegMap === 'function') {
                        window.initCompanyRegMap();
                    } else if (window._regMapInstance) {
                        window._regMapInstance.invalidateSize();
                    }
                }, 300);
            }
        }});
    }

    function goToStep1() {
        roleInput.value = 'user';
        
        // Add required attributes back for Step 1
        document.querySelector('#step-1 input[name="name"]').setAttribute('required', 'required');
        document.querySelector('#step-1 input[name="email"]').setAttribute('required', 'required');
        document.querySelector('#step-1 input[name="password"]').setAttribute('required', 'required');
        document.querySelector('#step-1 input[name="password_confirmation"]').setAttribute('required', 'required');

        // Hide Step 2, Show Step 1
        gsap.to(step2, { opacity: 0, x: 20, duration: 0.3, onComplete: () => {
            step2.style.display = 'none';
            step1.style.display = 'block';
            gsap.fromTo(step1, { opacity: 0, x: -20 }, { opacity: 1, x: 0, duration: 0.3, ease: "power2.out" });
        }});

        // Disable all business fields in Step 2 so browser does not validate them
        if (businessFieldsContainer) {
            const allInputs = businessFieldsContainer.querySelectorAll('input, select, textarea');
            allInputs.forEach(input => {
                input.disabled = true;
            });
        }
    }

    // UI Radio logic to update the hidden input and buttons
    const roleUIRadios = document.querySelectorAll('.role-radio');

    roleUIRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            roleInput.value = this.value;
            if (this.value === 'user') {
                btnSubmitUser.style.display = 'block';
                btnNextStep.style.display = 'none';
            } else {
                btnSubmitUser.style.display = 'none';
                btnNextStep.style.display = 'block';
            }
        });
    });

    // Initialize state
    handleCountryChange(getSelectedCountry());

    if (roleInput.value === 'business') {
        step1.style.display = 'none';
        step2.style.display = 'block';
        if (typeof window.switchRegistrationCountry === 'function') {
            window.switchRegistrationCountry(getSelectedCountry());
        }
        if (getSelectedCountry() === 'CO') {
            setTimeout(() => {
                if (typeof window.initCompanyRegMap === 'function') {
                    window.initCompanyRegMap();
                } else if (window._regMapInstance) {
                    window._regMapInstance.invalidateSize();
                }
            }, 300);
        }
    } else {
        // Disable business fields initially
        if (businessFieldsContainer) {
            const allInputs = businessFieldsContainer.querySelectorAll('input, select, textarea');
            allInputs.forEach(input => {
                input.disabled = true;
            });
        }
    }

    // Listeners
    if (btnNextStep) btnNextStep.addEventListener('click', goToStep2);
    if (btnPrevStep) btnPrevStep.addEventListener('click', goToStep1);
});
</script>
@endpush
@endsection
