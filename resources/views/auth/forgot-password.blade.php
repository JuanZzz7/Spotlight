@extends('layouts.app')
@section('title', 'Recuperar Contraseña - Spotlight')
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

    /* Translation Logic */
    html[lang="es"] .lang-en { display: none !important; }
    html[lang="en"] .lang-es { display: none !important; }

    .lang-btn-auth {
        position: absolute;
        top: 24px;
        right: 24px;
        z-index: 100;
        border: 1px solid var(--sl-border);
        background: #ffffff;
        color: var(--sl-text-main);
        font-size: 13px;
        font-weight: 600;
        border-radius: 9999px;
        padding: 6px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .lang-btn-auth:hover {
        background: var(--sl-primary-soft);
        color: var(--sl-primary);
        border-color: var(--sl-primary);
    }
</style>
@endpush

@section('content')
<button type="button" class="lang-btn-auth" id="btnSwitchLang" title="Cambiar idioma / Switch language">
    <i class="bi bi-translate"></i>
    <span class="lang-en">ES</span>
    <span class="lang-es">EN</span>
</button>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-3 p-md-4" style="background-color: var(--sl-bg-page, #f8fafc);">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 960px; background: #ffffff;">
        <div class="row g-0">
            <!-- Left Side: Brand Visual -->
            <div class="col-md-5 d-none d-md-flex flex-column align-items-center justify-content-center p-5 text-center text-white position-relative" style="background: linear-gradient(135deg, #7F1D3F 0%, #5A122B 100%);">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.08) 0%, transparent 60%); z-index: 1;"></div>
                
                <div class="position-relative d-flex flex-column align-items-center justify-content-center h-100 p-4 text-white text-center" style="z-index: 2;">
                    <div class="bg-white rounded-circle shadow-lg mb-4 d-flex align-items-center justify-content-center" style="width: 88px; height: 88px; padding: 16px;">
                        <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <h3 class="fw-bold text-white mb-2 fs-3" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">SPOTLIGHT</h3>
                    <p class="text-white-50 small px-2 mb-0" style="max-width: 270px; line-height: 1.6;">
                        <span class="lang-en">Protecting and securing access for creative businesses and buyers.</span>
                        <span class="lang-es">Seguridad y acceso confiable para emprendedores y compradores locales.</span>
                    </p>
                </div>
            </div>

            <!-- Right Side: Reset Form -->
            <div class="col-md-7 p-4 p-md-5 bg-white position-relative">
                <div class="text-center d-md-none mb-3">
                    <div class="d-inline-flex bg-white rounded-circle p-2 shadow-sm border" style="width: 58px; height: 58px; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                </div>

                <div class="mb-4">
                    <a href="{{ route('login') }}" class="text-decoration-none text-sm fw-semibold d-inline-flex align-items-center" style="color: var(--sl-primary);">
                        <i class="bi bi-arrow-left me-2"></i>
                        <span class="lang-en">Back to login</span>
                        <span class="lang-es">Volver a Iniciar Sesión</span>
                    </a>
                </div>

                <div class="mb-4">
                    <h3 class="fw-bold text-dark fs-3 mb-2" style="font-family: 'Outfit', sans-serif;">
                        <span class="lang-en">Forgot your password?</span>
                        <span class="lang-es">¿Olvidaste tu contraseña?</span>
                    </h3>
                    <p class="text-muted fw-normal" style="font-size: 0.95rem; line-height: 1.6;">
                        <span class="lang-en">No problem. Enter your registered email address and we'll send you a link to reset your password.</span>
                        <span class="lang-es">No te preocupes. Ingresa el correo electrónico asociado a tu cuenta y te enviaremos un enlace seguro para restablecerla.</span>
                    </p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success border-0 d-flex align-items-center rounded-4 mb-4" style="background-color: rgba(16, 185, 129, 0.12); color: #047857;">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 d-flex align-items-center rounded-4 mb-4" style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-sm fw-semibold text-dark mb-2">
                            <span class="lang-en">Email Address</span><span class="lang-es">Correo electrónico</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3" style="border-color: #e2e8f0; color: #94a3b8;">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control form-control-lg text-sm border-start-0 rounded-end-pill pe-4" style="background-color: #f8fafc; border-color: #e2e8f0;" placeholder="ejemplo@correo.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-spotlight btn-lg w-100 fw-semibold shadow-sm mb-4 py-3">
                        <span class="lang-en">Send Password Reset Link</span>
                        <span class="lang-es">Enviar enlace de recuperación</span>
                    </button>

                    <div class="text-center text-sm text-muted">
                        <span class="lang-en">Remembered your password?</span>
                        <span class="lang-es">¿Recordaste tu contraseña?</span>
                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none ms-1" style="color: var(--sl-primary);">
                            <span class="lang-en">Sign in</span>
                            <span class="lang-es">Inicia sesión</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Language toggle script
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
</script>
@endpush
