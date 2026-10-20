@extends('layouts.app')
@section('title', 'Estado de la Transacción - Spotlight')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --sl-burgundy: #7F1D3F;
        --sl-dusty-rose: #D98C9A;
        --sl-soft-rose: #F6E7EB;
        --sl-ivory: #FFFCF7;
        --sl-charcoal: #29252A;
    }
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #F8F9FA;
        color: var(--sl-charcoal);
    }
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
    .result-card {
        background-color: var(--sl-ivory);
        border: 1px solid #ECE7E1;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="py-5" style="min-height: 85vh;">
    <div class="container" style="max-width: 860px;">

        @if($paymentStatus === \App\Models\Order::PAYMENT_APPROVED || $orders->first()->status === 'paid')
            {{-- ESTADO: APROBADO --}}
            <div class="result-card p-4 p-md-5 shadow-sm text-center mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; background: #ECFDF5; color: #10B981; border: 2px solid #A7F3D0;">
                    <i class="bi bi-check2-circle fs-1"></i>
                </div>
                <span class="badge rounded-pill px-3 py-1.5 fw-bold mb-2" style="background: var(--sl-soft-rose); color: var(--sl-burgundy); font-size: 0.8rem;">
                    TRANSACCIÓN COMPLETADA
                </span>
                <h2 class="fw-black mb-2 font-outfit" style="color: var(--sl-charcoal);">¡Gracias por tu compra!</h2>
                <p class="text-muted mb-4" style="max-width: 550px; margin: 0 auto;">
                    Tu pago ha sido procesado de forma exitosa a través de Wompi Colombia. Los comercios han recibido la confirmación y ya están preparando tus productos.
                </p>

                <div class="p-3 rounded-4 d-inline-flex flex-wrap gap-4 justify-content-center text-start mb-4 bg-white border" style="border-color: #E2E8F0 !important;">
                    <div>
                        <span class="text-muted small d-block">Referencia de Pago:</span>
                        <strong class="font-outfit text-dark">{{ $paymentReference }}</strong>
                    </div>
                    @if($transactionId)
                        <div>
                            <span class="text-muted small d-block">ID Transacción Wompi:</span>
                            <code class="text-dark">{{ $transactionId }}</code>
                        </div>
                    @endif
                    <div>
                        <span class="text-muted small d-block">Total Pagado:</span>
                        <strong class="font-outfit" style="color: var(--sl-burgundy);">${{ number_format($totalPaid, 0, ',', '.') }} COP</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('user.orders.index') }}" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: var(--sl-burgundy);">
                        <i class="bi bi-bag-check me-1"></i> Ver Mis Pedidos
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
                        <i class="bi bi-shop me-1"></i> Seguir Comprando
                    </a>
                </div>
            </div>

        @elseif($paymentStatus === \App\Models\Order::PAYMENT_PENDING)
            {{-- ESTADO: PENDIENTE (PSE / Nequi / Corresponsal) --}}
            <div class="result-card p-4 p-md-5 shadow-sm text-center mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; background: #FEF3C7; color: #D97706; border: 2px solid #FDE68A;">
                    <i class="bi bi-hourglass-split fs-1"></i>
                </div>
                <span class="badge rounded-pill px-3 py-1.5 fw-bold mb-2 bg-warning bg-opacity-10 text-warning" style="font-size: 0.8rem;">
                    PAGO PENDIENTE DE VALIDACIÓN
                </span>
                <h2 class="fw-black mb-2 font-outfit" style="color: var(--sl-charcoal);">Transacción en Proceso</h2>
                <p class="text-muted mb-4" style="max-width: 550px; margin: 0 auto;">
                    Tu entidad financiera está procesando la solicitud (PSE, Nequi o corresponsal). En cuanto Wompi nos confirme la acreditación, actualizaremos tus pedidos automáticamente.
                </p>

                <div class="p-3 rounded-4 d-inline-flex flex-wrap gap-4 justify-content-center text-start mb-4 bg-white border" style="border-color: #E2E8F0 !important;">
                    <div>
                        <span class="text-muted small d-block">Referencia:</span>
                        <strong class="font-outfit text-dark">{{ $paymentReference }}</strong>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Monto:</span>
                        <strong class="font-outfit" style="color: var(--sl-burgundy);">${{ number_format($totalPaid, 0, ',', '.') }} COP</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <button onclick="window.location.reload();" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: var(--sl-burgundy);">
                        <i class="bi bi-arrow-clockwise me-1"></i> Actualizar Estado
                    </button>
                    <a href="{{ route('user.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
                        Ver Mis Pedidos
                    </a>
                </div>
            </div>

        @else
            {{-- ESTADO: RECHAZADO / CANCELADO --}}
            <div class="result-card p-4 p-md-5 shadow-sm text-center mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; background: #FEE2E2; color: #DC2626; border: 2px solid #FECACA;">
                    <i class="bi bi-x-circle fs-1"></i>
                </div>
                <span class="badge rounded-pill px-3 py-1.5 fw-bold mb-2 bg-danger bg-opacity-10 text-danger" style="font-size: 0.8rem;">
                    TRANSACCIÓN NO APROBADA
                </span>
                <h2 class="fw-black mb-2 font-outfit" style="color: var(--sl-charcoal);">El pago no pudo completarse</h2>
                <p class="text-muted mb-4" style="max-width: 550px; margin: 0 auto;">
                    La pasarela Wompi o tu entidad bancaria declinaron la operación. No se ha realizado ningún cobro a tu cuenta.
                </p>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('checkout.index') }}" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: var(--sl-burgundy);">
                        <i class="bi bi-arrow-repeat me-1"></i> Intentar Nuevamente
                    </a>
                    <a href="{{ route('user.cart.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
                        Regresar al Carrito
                    </a>
                </div>
            </div>
        @endif

        {{-- DESGLOSE DE PEDIDOS CREADOS POR COMERCIO --}}
        <div class="result-card p-4 p-md-5 shadow-sm">
            <h5 class="fw-bold mb-3 font-outfit text-dark border-bottom pb-3">
                <i class="bi bi-box-seam me-2" style="color: var(--sl-burgundy);"></i>
                Pedidos Generados por Comercio ({{ $orders->count() }})
            </h5>

            <div class="row g-3">
                @foreach($orders as $ord)
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-white border" style="border-color: #ECE7E1 !important;">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pb-2 mb-2 border-bottom border-light gap-2">
                                <div>
                                    <span class="badge rounded-pill fw-bold" style="background: var(--sl-soft-rose); color: var(--sl-burgundy);">
                                        {{ $ord->company->name ?? 'Comercio Local' }}
                                    </span>
                                    <span class="text-muted small ms-2">Orden #{{ $ord->id }} ({{ $ord->reference }})</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark font-outfit">${{ number_format($ord->total, 0, ',', '.') }} COP</span>
                                    @if($ord->shipment)
                                        <a href="{{ route('user.orders.tracking', $ord) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small">
                                            <i class="bi bi-geo-alt"></i> Rastrear
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Productos de esta orden --}}
                            <div class="small">
                                @foreach($ord->details as $d)
                                    <div class="d-flex justify-content-between py-1 text-muted">
                                        <span>&bull; {{ $d->quantity }} &times; {{ $d->product_name }}</span>
                                        <span class="fw-semibold text-dark">${{ number_format($d->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @if($ord->shipment)
                                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center small text-muted">
                                    <span>Guía de rastreo: <strong class="text-dark">{{ $ord->shipment->tracking_number }}</strong> ({{ $ord->shipment->carrier_name }})</span>
                                    <span class="badge bg-light text-muted border">Estado: {{ ucfirst($ord->shipment->status) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
