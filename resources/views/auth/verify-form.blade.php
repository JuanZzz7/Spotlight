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
    <link href="{{ asset('css/tokens.css') }}?v=2.8" rel="stylesheet">
    <link href="{{ asset('css/auth-verify.css') }}?v=2.8" rel="stylesheet">
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
