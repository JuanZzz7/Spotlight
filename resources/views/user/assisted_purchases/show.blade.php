@extends('layouts.dashboard')

@section('page-title', 'Solicitud ' . $assistedPurchase->request_code)

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('user.assisted_purchases.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1">
            <i class="bi bi-arrow-left me-1"></i> Volver a Mis Solicitudes
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="badge {{ $assistedPurchase->status_badge_class }} rounded-pill px-3 py-2 fs-6">
                {{ $assistedPurchase->status_label }}
            </span>
        </div>
    </div>

    {{-- Cabecera con datos de la solicitud --}}
    <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="border-radius: 18px;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="text-muted small">Código Único de Seguimiento Internacional</span>
                <h3 class="fw-bold text-dark font-monospace mb-1">{{ $assistedPurchase->request_code }}</h3>
                <div class="text-muted small">
                    Ruta: <strong>{{ $assistedPurchase->origin_flag }} {{ $assistedPurchase->origin_country === 'MX' ? 'México' : 'Colombia' }}</strong> ➔ 
                    <strong>{{ $assistedPurchase->destination_flag }} {{ $assistedPurchase->destination_country === 'MX' ? 'México' : 'Colombia' }}</strong> • 
                    Registrada el {{ $assistedPurchase->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="text-md-end">
                <div class="text-muted small">Importe Total Confirmado:</div>
                <div class="fw-bold fs-3" style="color: #7F1D3F;">
                    {{ $assistedPurchase->formatted_total_agreed }}
                </div>
                <div class="badge bg-light text-dark border px-2 py-1 rounded-pill small">
                    Moneda de Pago: {{ $assistedPurchase->payment_currency ?: $assistedPurchase->original_currency }}
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Columna Izquierda: Producto e Instantánea de Precios --}}
        <div class="col-lg-7">
            {{-- Producto --}}
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <h6 class="fw-bold text-dark mb-3">Producto Solicitado</h6>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $assistedPurchase->product ? $assistedPurchase->product->image_url : asset('images/logo.png') }}" 
                         alt="{{ $assistedPurchase->product?->name }}" 
                         class="rounded-3" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #e2e8f0;">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">{{ $assistedPurchase->product?->name }}</h6>
                        <div class="text-muted small">
                            Vendido por: <strong>{{ $assistedPurchase->company?->name }}</strong>
                        </div>
                        <div class="text-muted small">
                            Ubicación: {{ $assistedPurchase->company?->city }}, {{ $assistedPurchase->company?->state ?? '' }} ({{ $assistedPurchase->origin_country }})
                        </div>
                    </div>
                </div>

                {{-- Instantánea Financiera Acordada --}}
                <div class="p-3 rounded-3 bg-light border small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Precio unitario congelado (instantánea):</span>
                        <span class="fw-semibold">{{ $assistedPurchase->original_currency }} ${{ number_format($assistedPurchase->original_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cantidad solicitada:</span>
                        <span class="fw-semibold">{{ $assistedPurchase->quantity }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal producto:</span>
                        <span class="fw-semibold">{{ $assistedPurchase->original_currency }} ${{ number_format($assistedPurchase->original_price * $assistedPurchase->quantity, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Flete internacional cotizado:</span>
                        <span class="fw-semibold text-primary">
                            @if($assistedPurchase->shipping_cost_agreed > 0)
                                {{ $assistedPurchase->original_currency }} ${{ number_format($assistedPurchase->shipping_cost_agreed, 2) }}
                            @else
                                <span class="badge bg-warning text-dark">Pendiente de cotizar por vendedor</span>
                            @endif
                        </span>
                    </div>
                    @if($assistedPurchase->other_fees_agreed > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Gestión aduanal / otros cargos:</span>
                        <span class="fw-semibold">{{ $assistedPurchase->original_currency }} ${{ number_format($assistedPurchase->other_fees_agreed, 2) }}</span>
                    </div>
                    @endif
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fs-6 fw-bold text-dark">
                        <span>Total de la Operación:</span>
                        <span style="color: #7F1D3F;">{{ $assistedPurchase->formatted_total_agreed }}</span>
                    </div>
                </div>
            </div>

            {{-- Estado del Despacho y Guía si aplica --}}
            @if($assistedPurchase->tracking_number || $assistedPurchase->carrier_name)
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-truck text-primary me-2"></i>Información de Despacho y Envío</h6>
                <div class="row g-2 small">
                    <div class="col-sm-6">
                        <span class="text-muted">Empresa Transportadora:</span>
                        <div class="fw-bold">{{ $assistedPurchase->carrier_name ?: 'Transportadora Internacional' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Número de Guía / Rastreo:</span>
                        <div class="fw-bold font-monospace text-primary fs-6">{{ $assistedPurchase->tracking_number ?: 'Por asignar' }}</div>
                    </div>
                    @if($assistedPurchase->estimated_delivery_days)
                    <div class="col-12 mt-2">
                        <span class="text-muted">Tiempo estimado de entrega:</span>
                        <div class="fw-semibold">{{ $assistedPurchase->estimated_delivery_days }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Notas del Vendedor --}}
            @if($assistedPurchase->seller_notes)
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-chat-left-quote text-success me-2"></i>Mensaje del Comercio</h6>
                <p class="mb-0 text-muted small bg-light p-3 rounded-3 border">
                    {{ $assistedPurchase->seller_notes }}
                </p>
            </div>
            @endif
        </div>

        {{-- Columna Derecha: Destino, Pago y Acciones --}}
        <div class="col-lg-5">
            {{-- Datos de Entrega en Destino --}}
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-geo-alt text-danger me-2"></i>Dirección de Entrega</h6>
                <div class="small">
                    <div class="mb-1"><strong>Destinatario:</strong> {{ $assistedPurchase->recipient_name }}</div>
                    <div class="mb-1"><strong>Teléfono:</strong> {{ $assistedPurchase->recipient_phone }}</div>
                    <div class="mb-1"><strong>Email:</strong> {{ $assistedPurchase->recipient_email }}</div>
                    <div class="mb-1"><strong>Destino:</strong> {{ $assistedPurchase->destination_city }}, {{ $assistedPurchase->destination_state }} ({{ $assistedPurchase->destination_country }})</div>
                    @if($assistedPurchase->destination_postal_code)
                    <div class="mb-1"><strong>Código Postal:</strong> {{ $assistedPurchase->destination_postal_code }}</div>
                    @endif
                    <div class="text-muted mt-2"><strong>Dirección:</strong> {{ $assistedPurchase->destination_address }}</div>
                </div>
            </div>

            {{-- Pago y Coordinación --}}
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-credit-card text-success me-2"></i>Método de Pago y Coordinación</h6>
                <div class="small mb-3">
                    <div class="mb-1"><strong>Medio acordado:</strong> {{ $assistedPurchase->payment_method_agreed ?: 'Por definir' }}</div>
                    <div class="mb-1"><strong>Moneda de pago:</strong> {{ $assistedPurchase->payment_currency ?: $assistedPurchase->original_currency }}</div>
                </div>

                @if(in_array($assistedPurchase->status, ['pending_payment', 'in_negotiation']))
                <div class="border p-3 rounded-3 bg-light mb-3">
                    <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-wallet2 me-1"></i>Registrar Comprobante / Referencia de Pago</h6>
                    <p class="text-muted" style="font-size: 0.75rem;">
                        Una vez hayas completado la transferencia o pago acordado con el comercio, ingresa la referencia o código de la transacción para que el vendedor verifique y proceda a preparar tu envío.
                    </p>
                    <form action="{{ route('user.assisted_purchases.payment', $assistedPurchase) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="payment_reference" class="form-control form-control-sm" 
                                   placeholder="Ej: TRX-98234710 o Folio SPEI" value="{{ old('payment_reference', $assistedPurchase->payment_reference) }}" required>
                        </div>
                        <div class="mb-2">
                            <textarea name="payment_notes" rows="2" class="form-control form-control-sm" placeholder="Observaciones o banco emisor (opcional)..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-spotlight btn-sm w-100 rounded-pill text-white fw-semibold">
                            Notificar Pago al Vendedor
                        </button>
                    </form>
                </div>
                @endif

                @if($assistedPurchase->payment_reference)
                <div class="alert alert-success border-0 small py-2 px-3 mb-3">
                    <i class="bi bi-check-circle-fill me-1"></i> Referencia registrada: <strong>{{ $assistedPurchase->payment_reference }}</strong>
                </div>
                @endif

                {{-- Contactar al Comercio --}}
                <div class="d-grid gap-2">
                    @if($assistedPurchase->company && $assistedPurchase->company->user_id)
                    <a href="{{ route('chat.show', $assistedPurchase->company->user_id) }}" class="btn btn-outline-primary btn-sm rounded-pill py-2">
                        <i class="bi bi-chat-dots me-1"></i> Abrir Chat con el Comercio
                    </a>
                    @endif

                    @if(!in_array($assistedPurchase->status, ['shipped', 'delivered', 'cancelled', 'rejected']))
                    <form action="{{ route('user.assisted_purchases.cancel', $assistedPurchase) }}" method="POST" onsubmit="return confirm('¿Deseas cancelar esta solicitud internacional?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-2">
                            <i class="bi bi-x-circle me-1"></i> Cancelar Solicitud
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
