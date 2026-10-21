<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresa tu código — Spotlight</title>
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
        body::before {
            content: '';
            position: fixed;
            top: -100px; right: -100px;
            width: 380px; height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(127, 29, 63, 0.04) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -70px; left: -70px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 140, 154, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-verify {
            background: #ffffff;
            border: 1px solid #EEDDDC;
            border-radius: 28px;
            padding: 48px 42px;
            max-width: 490px;
            width: 100%;
            box-shadow: 0 24px 60px rgba(127, 29, 63, 0.08), 0 4px 14px rgba(0,0,0,0.03);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Logo */
        .logo-wrap { margin-bottom: 22px; }
        .logo-wrap img { 
            height: 50px; 
            width: auto; 
            object-fit: contain;
        }

        /* Badge de paso */
        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(127, 29, 63, 0.08);
            border: 1px solid rgba(127, 29, 63, 0.15);
            border-radius: 9999px;
            padding: 6px 14px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #7F1D3F;
            margin-bottom: 20px;
        }

        .title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            color: #29252A;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 0.88rem;
            color: #736B75;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .subtitle strong { 
            color: #7F1D3F; 
            font-weight: 700;
        }

        /* ── 6 inputs OTP ── */
        .otp-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }
        .otp-input {
            width: 54px;
            height: 64px;
            border: 2px solid #EEDDDC;
            border-radius: 14px;
            text-align: center;
            font-size: 1.6rem;
            font-weight: 800;
            color: #29252A;
            background: #FFFCF7;
            transition: all 0.2s ease;
            outline: none;
            caret-color: #7F1D3F;
        }
        .otp-input:focus {
            border-color: #7F1D3F;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(127, 29, 63, 0.12);
            transform: translateY(-2px);
        }
        .otp-input.filled {
            border-color: #7F1D3F;
            background: #FAF0F2;
            color: #7F1D3F;
        }
        .otp-input.error {
            border-color: #BA2D42;
            background: #FDECEF;
            color: #BA2D42;
            animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }

        /* Separador entre dígitos 3 y 4 */
        .otp-sep {
            display: flex;
            align-items: center;
            color: #D98C9A;
            font-size: 1.2rem;
            font-weight: 300;
            user-select: none;
        }

        /* Hint de email */
        .email-hint {
            font-size: 0.8rem;
            color: #736B75;
            margin-bottom: 26px;
        }
        .email-hint strong { 
            color: #7F1D3F; 
            font-weight: 600;
        }

        /* Botón verificar */
        .btn-spotlight-verify {
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
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(127, 29, 63, 0.25);
            letter-spacing: 0.01em;
            display: block;
            text-decoration: none;
        }
        .btn-spotlight-verify:hover {
            background: #5E122C;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(127, 29, 63, 0.35);
            color: #ffffff !important;
        }
        .btn-spotlight-verify:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-spotlight-back {
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
        .btn-spotlight-back:hover { 
            background: #FAF0F2; 
            border-color: #7F1D3F; 
            color: #5E122C; 
        }

        /* Progress dots */
        .progress-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .progress-dots .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #EEDDDC;
            transition: all 0.3s ease;
        }
        .progress-dots .dot.active {
            background: linear-gradient(135deg, #7F1D3F, #9E2D53);
            width: 24px;
            border-radius: 9999px;
        }

        /* Alert error global */
        .alert-vs {
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 0.86rem;
            margin-bottom: 22px;
            text-align: left;
            border: none;
        }
        .alert-vs.alert-danger {
            background: #FDECEF;
            color: #BA2D42;
            border: 1px solid rgba(186, 45, 66, 0.2);
        }

        /* Countdown */
        .countdown {
            font-size: 0.8rem;
            color: #9E96A0;
            margin-top: 18px;
            margin-bottom: 0;
        }
        .countdown span { 
            color: #7F1D3F; 
            font-weight: 700; 
        }
    </style>
</head>
<body>

<div class="card-verify">
    {{-- Logo --}}
    <div class="logo-wrap">
        <img src="{{ asset('images/logo.png') }}" alt="Spotlight">
    </div>

    {{-- Indicador de paso --}}
    <div class="step-badge">
        <i class="bi bi-shield-lock-fill"></i> Paso 2 de 2 — Ingresa tu código
    </div>

    {{-- Progress --}}
    <div class="progress-dots">
        <div class="dot"></div>
        <div class="dot active"></div>
    </div>

    <h1 class="title">Código de verificación</h1>
    <p class="subtitle">
        Ingresa los <strong>6 dígitos</strong> que enviamos a tu correo.<br>
        No compartas este código con nadie.
    </p>

    {{-- Error global --}}
    @if($errors->has('code'))
        <div class="alert-vs alert alert-danger d-flex align-items-center">
            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
            <div>{{ $errors->first('code') }}</div>
        </div>
    @endif

    {{-- Formulario OTP --}}
    <form action="{{ route('verify.submit') }}" method="POST" id="otp-form">
        @csrf

        <div class="otp-container" id="otp-container">
            <input class="otp-input {{ $errors->has('code') ? 'error' : '' }}" type="text" name="d1" id="d1" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" autofocus>
            <input class="otp-input {{ $errors->has('code') ? 'error' : '' }}" type="text" name="d2" id="d2" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input {{ $errors->has('code') ? 'error' : '' }}" type="text" name="d3" id="d3" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <span class="otp-sep">—</span>
            <input class="otp-input {{ $errors->has('code') ? 'error' : '' }}" type="text" name="d4" id="d4" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input {{ $errors->has('code') ? 'error' : '' }}" type="text" name="d5" id="d5" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input {{ $errors->has('code') ? 'error' : '' }}" type="text" name="d6" id="d6" maxlength="1" inputmode="numeric" pattern="[0-9]">
        </div>

        <p class="email-hint">
            Código enviado a: <strong>{{ $email }}</strong>
        </p>

        <button type="submit" class="btn-spotlight-verify" id="btn-verify" disabled>
            <i class="bi bi-patch-check-fill me-2"></i>Verificar y continuar
        </button>
    </form>

    <a href="{{ route('verify.notice') }}" class="btn-spotlight-back">
        <i class="bi bi-arrow-left me-2"></i>Volver · Reenviar código
    </a>

    {{-- Countdown --}}
    <p class="countdown">
        Código expira en: <span id="timer">10:00</span>
    </p>
</div>

<script>
(function () {
    const inputs   = Array.from(document.querySelectorAll('.otp-input'));
    const btnVerify = document.getElementById('btn-verify');

    // ── Auto-focus y navegación entre inputs ──
    inputs.forEach((inp, idx) => {
        inp.addEventListener('input', (e) => {
            // Filtrar solo dígitos
            inp.value = inp.value.replace(/\D/g, '').slice(-1);
            inp.classList.toggle('filled', inp.value !== '');
            inp.classList.remove('error');

            if (inp.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
            checkAllFilled();
        });

        inp.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !inp.value && idx > 0) {
                inputs[idx - 1].value = '';
                inputs[idx - 1].classList.remove('filled');
                inputs[idx - 1].focus();
                checkAllFilled();
            }
            // Permitir flechas
            if (e.key === 'ArrowLeft' && idx > 0) inputs[idx - 1].focus();
            if (e.key === 'ArrowRight' && idx < inputs.length - 1) inputs[idx + 1].focus();
        });

        // Soporte para pegar código completo (ej. desde el correo)
        inp.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((char, i) => {
                if (inputs[i]) {
                    inputs[i].value = char;
                    inputs[i].classList.add('filled');
                    inputs[i].classList.remove('error');
                }
            });
            const next = inputs[Math.min(pasted.length, 5)];
            if (next) next.focus();
            checkAllFilled();
        });
    });

    function checkAllFilled() {
        const allFilled = inputs.every(i => i.value.length === 1);
        btnVerify.disabled = !allFilled;
    }

    // ── Countdown de 10 minutos ──
    const timerEl = document.getElementById('timer');
    let totalSeconds = 10 * 60;

    const ticker = setInterval(() => {
        totalSeconds--;
        if (totalSeconds <= 0) {
            clearInterval(ticker);
            timerEl.textContent = '00:00';
            timerEl.style.color = '#BA2D42';
            inputs.forEach(i => {
                i.disabled = true;
                i.classList.add('error');
            });
            btnVerify.disabled = true;
            btnVerify.textContent = 'Código expirado — Reenvíe el correo';
            return;
        }
        const m = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
        const s = (totalSeconds % 60).toString().padStart(2, '0');
        timerEl.textContent = `${m}:${s}`;
        if (totalSeconds < 60) timerEl.style.color = '#BA2D42';
    }, 1000);

    // Submit con loading
    document.getElementById('otp-form').addEventListener('submit', () => {
        btnVerify.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verificando...';
        btnVerify.disabled = true;
    });
})();
</script>

</body>
</html>
