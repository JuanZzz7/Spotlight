@extends('layouts.app')
@section('title', 'Autorización PSE - Spotlight Marketplace')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --sl-burgundy: #7F1D3F;
        --sl-dusty-rose: #D98C9A;
        --sl-soft-rose: #F6E7EB;
        --sl-ivory: #FFFCF7;
        --sl-charcoal: #29252A;
        --pse-blue: #0A3A60;
    }
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: linear-gradient(180deg, #F0F4F8 0%, #FFFFFF 100%);
        color: var(--sl-charcoal);
    }
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
    .pse-header-card {
        background: linear-gradient(135deg, #0A3A60 0%, #155E95 100%);
        color: #ffffff;
        border-radius: 20px 20px 0 0;
    }
    .pse-body-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-top: none;
        border-radius: 0 0 20px 20px;
    }
    .btn-approve {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        font-weight: 700;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
        color: white;
    }
    .btn-cancel {
        background: #F1F5F9;
        color: #64748B;
        font-weight: 600;
        border: 1px solid #CBD5E1;
        transition: all 0.2s ease;
    }
    .btn-cancel:hover {
        background: #E2E8F0;
        color: #334155;
    }
    .bank-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        padding: 6px 16px;
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container" style="max-width: 720px;">
        <!-- Header de la Aplicación Bancaria PSE -->
        <div class="pse-header-card p-4 p-md-5 shadow-lg position-relative overflow-hidden">
            <div class="position-absolute end-0 top-0 opacity-10 pe-4 pt-3">
                <i class="bi bi-bank" style="font-size: 8rem;"></i>
            </div>
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative z-1 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-3 p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-bank2 fs-2 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white font-outfit">Portal Bancario PSE</h5>
                        <small class="text-white-50">ACH Colombia - Débito Seguro en Línea</small>
                    </div>
                </div>
                <div class="bank-badge text-white">
                    <span class="small opacity-75 d-block" style="font-size: 0.7rem;">ENTIDAD FINANCIERA</span>
                    <strong class="fs-6"><i class="bi bi-shield-lock-fill me-1 text-warning"></i> {{ $bankName }}</strong>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top border-white border-opacity-15 position-relative z-1">
                <div class="row g-3">
                    <div class="col-6 col-sm-3">
                        <small class="text-white-50 d-block" style="font-size: 0.72rem;">COMERCIO</small>
                        <span class="fw-bold text-white small">Spotlight Marketplace</span>
                    </div>
                    <div class="col-6 col-sm-3">
                        <small class="text-white-50 d-block" style="font-size: 0.72rem;">NIT COMERCIO</small>
                        <span class="fw-bold text-white small">901.849.201-3</span>
                    </div>
                    <div class="col-6 col-sm-3">
                        <small class="text-white-50 d-block" style="font-size: 0.72rem;">REFERENCIA</small>
                        <span class="fw-bold text-white small font-monospace">{{ $paymentReference }}</span>
                    </div>
                    <div class="col-6 col-sm-3">
                        <small class="text-white-50 d-block" style="font-size: 0.72rem;">VALOR TOTAL</small>
                        <span class="fw-bold text-warning fs-5 font-outfit">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cuerpo del Portal y Confirmación -->
        <div class="pse-body-card p-4 p-md-5 shadow-sm">
            <div class="alert alert-info border-0 rounded-4 d-flex align-items-center gap-3 p-3 mb-4" style="background-color: #EBF5FF;">
                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                <div class="small text-dark">
                    Estás en el entorno seguro de autorización bancaria para <strong>{{ $bankName }}</strong>. Al hacer clic en <strong>Aceptar y Autorizar Débito</strong>, los fondos serán debitados de tu cuenta y tu orden se confirmará de inmediato.
                </div>
            </div>

            <!-- Resumen del Pagador -->
            <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">
                <i class="bi bi-person-badge me-1"></i> Datos de la Transacción Bancaria
            </h6>
            <div class="bg-light rounded-4 p-3 mb-4 border">
                <div class="row g-3 small">
                    <div class="col-sm-6">
                        <span class="text-muted d-block">Titular de la Cuenta:</span>
                        <strong class="text-dark">{{ $shipping['recipient_name'] ?? 'Usuario Spotlight' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block">Documento de Identidad:</span>
                        <strong class="text-dark">{{ ($shipping['doc_type'] ?? 'CC') . ' ' . ($shipping['doc_number'] ?? '') }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block">Correo Registrado en PSE:</span>
                        <strong class="text-dark">{{ $shipping['email'] ?? 'correo@ejemplo.com' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block">Teléfono Móvil:</span>
                        <strong class="text-dark">+57 {{ $shipping['phone'] ?? '' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Desglose de Comercios del Marketplace -->
            <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">
                <i class="bi bi-shop me-1"></i> Comercios Vendedores Spotlight
            </h6>
            <div class="border rounded-4 p-3 mb-4 bg-white">
                @foreach($orders as $order)
                <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <strong class="text-dark small d-block">{{ $order->company->name ?? 'Comercio Local' }}</strong>
                        <span class="text-muted" style="font-size: 0.75rem;">Sub-orden: {{ $order->reference }} ({{ $order->details->count() }} producto{{ $order->details->count() > 1 ? 's' : '' }})</span>
                    </div>
                    <span class="fw-bold text-dark">${{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top">
                    <strong class="fs-6 text-dark font-outfit">Total Débito Bancario:</strong>
                    <strong class="fs-4 font-outfit text-primary">${{ number_format($total, 0, ',', '.') }} COP</strong>
                </div>
            </div>

            <!-- Acciones: Aceptar y Autorizar / Cancelar -->
            <div class="d-flex flex-column gap-3">
                <form action="{{ route('checkout.pse.approve', ['reference' => $paymentReference]) }}" method="POST" id="approvePseForm">
                    @csrf
                    <button type="submit" class="btn btn-approve btn-lg w-100 py-3 rounded-pill d-flex align-items-center justify-content-center gap-2 shadow" id="btnApproveSubmit">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>Aceptar y Autorizar Débito (${{ number_format($total, 0, ',', '.') }} COP)</span>
                    </button>
                </form>

                <form action="{{ route('checkout.pse.cancel', ['reference' => $paymentReference]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-cancel w-100 py-2.5 rounded-pill small" onclick="return confirm('¿Seguro que deseas cancelar esta transacción en tu banco?')">
                        <i class="bi bi-x-circle me-1"></i> Cancelar y Rechazar Transacción
                    </button>
                </form>
            </div>

            <div class="mt-4 pt-3 border-top text-center text-muted small" style="font-size: 0.75rem;">
                <i class="bi bi-shield-check text-success me-1"></i> Conexión Cifrada SSL/TLS 256-bit con {{ $bankName }} y ACH Colombia.
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('approvePseForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnApproveSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Autorizando con tu banco...';
});
</script>
@endsection
