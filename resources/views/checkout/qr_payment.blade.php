@extends('layouts.app')
@section('title', 'Pago con Código QR Bancolombia / Nequi - Spotlight Marketplace')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --sl-burgundy: #7F1D3F;
        --sl-dusty-rose: #D98C9A;
        --sl-soft-rose: #F6E7EB;
        --sl-ivory: #FFFCF7;
        --sl-charcoal: #29252A;
        --bancolombia-yellow: #FDDA24;
    }
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: linear-gradient(180deg, #F8F9FA 0%, #FFFFFF 100%);
        color: var(--sl-charcoal);
    }
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
    .qr-card {
        background: #ffffff;
        border: 1px solid #ECE7E1;
        border-radius: 24px;
        box-shadow: 0 12px 35px rgba(41, 37, 42, 0.08);
    }
    .qr-box {
        background: #ffffff;
        padding: 20px;
        border-radius: 20px;
        border: 2px dashed #CBD5E1;
        display: inline-block;
        position: relative;
    }
    .badge-timer {
        background-color: #FEF3C7;
        color: #92400E;
        border: 1px solid #FDE68A;
        border-radius: 50rem;
        padding: 6px 16px;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .pulse-dot {
        width: 10px;
        height: 10px;
        background-color: #10B981;
        border-radius: 50%;
        display: inline-block;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .btn-confirm-qr {
        background: linear-gradient(135deg, var(--sl-burgundy) 0%, #A52A4A 100%);
        color: white;
        font-weight: 700;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-confirm-qr:hover {
        background: linear-gradient(135deg, #671532 0%, var(--sl-burgundy) 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(127, 29, 63, 0.35);
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container" style="max-width: 680px;">
        <div class="qr-card p-4 p-md-5 text-center">
            <!-- Header con logos de Bancolombia / Nequi -->
            <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                <div class="px-3 py-1.5 rounded-pill bg-light border d-inline-flex align-items-center gap-2">
                    <span class="pulse-dot"></span>
                    <span class="small fw-semibold text-muted">Generador Dinámico QR Bancolombia & Nequi</span>
                </div>
            </div>

            <h3 class="fw-bold font-outfit mb-1" style="color: var(--sl-charcoal);">Escanea para Pagar</h3>
            <p class="text-muted small mb-4">Código generado exclusivamente por el valor exacto de tu compra en Spotlight</p>

            <!-- Monto Destacado -->
            <div class="p-3 rounded-4 mb-4" style="background: linear-gradient(135deg, var(--sl-soft-rose) 0%, #FFF5F7 100%); border: 1.5px solid var(--sl-dusty-rose);">
                <small class="text-uppercase fw-bold text-muted d-block" style="font-size: 0.75rem; letter-spacing: 1px;">TOTAL A TRANSFERIR</small>
                <div class="fs-1 fw-bold font-outfit my-1" style="color: var(--sl-burgundy);">
                    ${{ number_format($total, 0, ',', '.') }} <span class="fs-5 text-muted fw-normal">COP</span>
                </div>
                <div class="small text-muted">
                    Referencia: <span class="fw-bold text-dark font-monospace">{{ $paymentReference }}</span>
                </div>
            </div>

            <!-- Código QR Dinámico -->
            <div class="mb-4">
                <div class="qr-box shadow-sm">
                    @php
                        $qrData = "bancolombia://qr/transfer?merchant=SPOTLIGHT&amount=" . (int)$total . "&ref=" . urlencode($paymentReference);
                        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=" . urlencode($qrData);
                    @endphp
                    <img src="{{ $qrApiUrl }}" alt="Código QR de Pago ${{ number_format($total, 0, ',', '.') }} COP" width="240" height="240" class="img-fluid rounded-3" onerror="this.onerror=null; this.src='https://chart.googleapis.com/chart?chs=240x240&cht=qr&chl={{ urlencode($qrData) }}&choe=UTF-8';">
                    
                    <div class="mt-2 text-center">
                        <span class="badge-timer d-inline-flex align-items-center gap-1">
                            <i class="bi bi-clock-history"></i>
                            Expira en: <span id="countdown">14:59</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Instrucciones -->
            <div class="text-start bg-light rounded-4 p-4 mb-4 border">
                <h6 class="fw-bold mb-3 font-outfit text-dark"><i class="bi bi-phone-fill me-2 text-primary"></i>¿Cómo pagar con tu aplicación?</h6>
                <div class="d-flex flex-column gap-2.5 small text-muted">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-white text-dark border rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">1</span>
                        <span>Abre tu aplicación móvil de <strong>Bancolombia</strong>, <strong>Bancolombia A la Mano</strong> o <strong>Nequi</strong>.</span>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-white text-dark border rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">2</span>
                        <span>Selecciona la opción <strong>Escanear código QR</strong> o <strong>Transferir con QR</strong>.</span>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-white text-dark border rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">3</span>
                        <span>Verifica el valor exacto de <strong>${{ number_format($total, 0, ',', '.') }} COP</strong> con destino a <strong>Spotlight Marketplace</strong>.</span>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-white text-dark border rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">4</span>
                        <span>Autoriza la transacción con tu clave dinámica o biometría.</span>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <form action="{{ route('checkout.qr.confirm', ['reference' => $paymentReference]) }}" method="POST" id="confirmQrForm" class="mb-3">
                @csrf
                <button type="submit" class="btn btn-confirm-qr btn-lg w-100 py-3 rounded-pill shadow d-flex align-items-center justify-content-center gap-2" id="btnConfirmQr">
                    <i class="bi bi-check2-circle fs-4"></i>
                    <span>Ya realicé el pago / Confirmar Pago QR</span>
                </button>
            </form>

            <div class="d-flex align-items-center justify-content-between text-muted small px-2">
                <span class="d-inline-flex align-items-center gap-1.5" id="pollingStatus">
                    <span class="spinner-grow spinner-grow-sm text-primary" style="width: 0.6rem; height: 0.6rem;"></span>
                    Esperando confirmación en tiempo real...
                </span>
                <a href="{{ route('checkout.index') }}" class="text-decoration-none text-muted hover-underline">
                    <i class="bi bi-arrow-left"></i> Cambiar método
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Temporizador regresivo de 15 minutos
    let duration = 15 * 60;
    const display = document.getElementById('countdown');
    const timer = setInterval(function() {
        let minutes = parseInt(duration / 60, 10);
        let seconds = parseInt(duration % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--duration < 0) {
            clearInterval(timer);
            display.textContent = "EXPIRADO";
            alert("El código QR ha expirado. Por favor genera una nueva orden.");
            window.location.href = "{{ route('checkout.index') }}";
        }
    }, 1000);

    // 2. Polling en tiempo real cada 3 segundos
    const checkUrl = "{{ route('checkout.qr.status', ['reference' => $paymentReference]) }}";
    const pollInterval = setInterval(function() {
        fetch(checkUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.paid && data.redirect_url) {
                clearInterval(pollInterval);
                document.getElementById('pollingStatus').innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> ¡Pago detectado! Redirigiendo...';
                window.location.href = data.redirect_url;
            }
        })
        .catch(() => {});
    }, 3000);

    // 3. Feedback visual al pulsar confirmar
    document.getElementById('confirmQrForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnConfirmQr');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verificando pago con tu banco...';
    });
});
</script>
@endsection
