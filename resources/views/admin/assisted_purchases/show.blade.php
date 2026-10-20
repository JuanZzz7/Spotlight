@extends('layouts.dashboard')
@section('title', 'Auditoría Solicitud ' . $assistedPurchase->request_code . ' - Admin')
@section('page-title', 'Auditoría de Compra Internacional Asistida')

@section('dashboard-content')
<div class="mb-4">
    <a href="{{ route('admin.assisted_purchases.index') }}" class="text-decoration-none text-muted fw-semibold d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Volver a la supervisión de compras internacionales
    </a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold text-dark mb-0 font-monospace">{{ $assistedPurchase->request_code }}</h3>
                <span class="badge {{ $assistedPurchase->status_badge }} rounded-pill px-3 py-1">
                    {{ $assistedPurchase->status_label }}
                </span>
            </div>
            <div class="text-muted mt-1" style="font-size: 0.85rem;">
                Registrada el {{ $assistedPurchase->created_at->format('d/m/Y \a \l\a\s H:i') }} • 
                Ruta: {{ $assistedPurchase->origin_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }} 
                <i class="bi bi-arrow-right mx-1"></i> 
                {{ $assistedPurchase->destination_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Columna Izquierda: Información de la Operación y Comprador --}}
    <div class="col-lg-6">
        <div class="bg-white rounded-4 border p-4 shadow-xs mb-4">
            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-box-seam me-1"></i> Producto y Vendedor
            </h6>
            <div class="d-flex align-items-center gap-3 pb-3 border-bottom">
                <img src="{{ $assistedPurchase->product->image_url ?? asset('images/logo.png') }}" alt="" class="rounded-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                <div>
                    <h6 class="fw-bold text-dark mb-1">{{ $assistedPurchase->product_name }}</h6>
                    <div class="text-muted text-xs">SKU: {{ $assistedPurchase->product_sku ?: 'N/A' }}</div>
                    <div class="text-muted text-xs">
                        Emprendimiento: <strong>{{ $assistedPurchase->company->name ?? 'N/A' }}</strong> ({{ $assistedPurchase->origin_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }})
                    </div>
                </div>
            </div>

            <div class="pt-3">
                <div class="row g-2">
                    <div class="col-6">
                        <span class="text-muted text-xs text-uppercase fw-bold">Cantidad:</span>
                        <div class="fw-bold text-dark">{{ $assistedPurchase->quantity }} unidades</div>
                    </div>
                    <div class="col-6">
                        <span class="text-muted text-xs text-uppercase fw-bold">Precio Unitario Congelado:</span>
                        <div class="fw-bold text-dark">{{ $assistedPurchase->formatted_original_price }}</div>
                    </div>
                    <div class="col-6">
                        <span class="text-muted text-xs text-uppercase fw-bold">Flete Acordado:</span>
                        <div class="fw-bold text-primary">{{ $assistedPurchase->payment_currency }} ${{ number_format($assistedPurchase->shipping_cost_agreed, 2) }}</div>
                    </div>
                    <div class="col-6">
                        <span class="text-muted text-xs text-uppercase fw-bold">Importe Total Acordado:</span>
                        <div class="fw-black text-success fs-5">{{ $assistedPurchase->formatted_total_agreed }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-4 border p-4 shadow-xs">
            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-geo-alt-fill me-1"></i> Destino y Comprador
            </h6>
            <div class="mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Comprador:</span>
                <div class="fw-bold text-dark">{{ $assistedPurchase->recipient_name }} ({{ $assistedPurchase->user->email ?? 'N/A' }})</div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <span class="text-muted text-xs text-uppercase fw-bold">Teléfono:</span>
                    <div class="fw-semibold text-dark">{{ $assistedPurchase->recipient_phone }}</div>
                </div>
                <div class="col-6">
                    <span class="text-muted text-xs text-uppercase fw-bold">Identificación:</span>
                    <div class="fw-semibold text-dark">{{ $assistedPurchase->recipient_tax_id ?: 'N/A' }}</div>
                </div>
            </div>
            <div class="mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Destino:</span>
                <div class="fw-semibold text-dark">
                    {{ $assistedPurchase->destination_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }} - {{ $assistedPurchase->destination_city }}, {{ $assistedPurchase->destination_state }} (CP: {{ $assistedPurchase->destination_postal_code ?: 'N/A' }})
                </div>
            </div>
            <div class="mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Dirección:</span>
                <div class="fw-semibold text-dark">{{ $assistedPurchase->destination_address }}</div>
            </div>
            @if($assistedPurchase->delivery_notes)
                <div class="p-2 bg-light rounded-3 mt-2 border text-xs">
                    <strong>Nota del comprador:</strong> {{ $assistedPurchase->delivery_notes }}
                </div>
            @endif
        </div>
    </div>

    {{-- Columna Derecha: Logística y Transparencia Financiera --}}
    <div class="col-lg-6">
        <div class="bg-white rounded-4 border p-4 shadow-xs mb-4">
            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-wallet2 me-1"></i> Condiciones Financieras y Pagos
            </h6>
            <div class="mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Método Acordado:</span>
                <div class="fw-semibold text-dark">{{ $assistedPurchase->payment_method_agreed ?: 'Directo entre comprador y vendedor' }}</div>
            </div>
            <div class="mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Referencia / Comprobante:</span>
                <div class="fw-bold font-monospace text-dark">{{ $assistedPurchase->payment_reference ?: 'Sin referencia registrada aún' }}</div>
            </div>
            @if($assistedPurchase->paid_at)
                <div class="mb-2">
                    <span class="text-muted text-xs text-uppercase fw-bold">Fecha de Pago Confirmado:</span>
                    <div class="fw-semibold text-success">{{ $assistedPurchase->paid_at->format('d/m/Y H:i') }}</div>
                </div>
            @endif
            @if($assistedPurchase->seller_notes)
                <div class="p-3 bg-light rounded-3 mt-3 border">
                    <span class="text-muted text-xs text-uppercase fw-bold d-block mb-1">Instrucciones / Notas del Emprendimiento:</span>
                    <div class="text-dark text-sm">{{ $assistedPurchase->seller_notes }}</div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-4 border p-4 shadow-xs">
            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-truck me-1"></i> Despacho Internacional
            </h6>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <span class="text-muted text-xs text-uppercase fw-bold">Empresa de Transporte:</span>
                    <div class="fw-semibold text-dark">{{ $assistedPurchase->carrier_name ?: 'Pendiente' }}</div>
                </div>
                <div class="col-6">
                    <span class="text-muted text-xs text-uppercase fw-bold">Número de Guía:</span>
                    <div class="fw-bold font-monospace text-dark">{{ $assistedPurchase->tracking_number ?: 'Sin guía asignada' }}</div>
                </div>
            </div>
            <div class="mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Tiempo Estimado:</span>
                <div class="fw-semibold text-dark">{{ $assistedPurchase->estimated_delivery_days ?: 'No especificado' }}</div>
            </div>
            @if($assistedPurchase->shipped_at)
                <div class="mb-2">
                    <span class="text-muted text-xs text-uppercase fw-bold">Fecha de Envío:</span>
                    <div class="fw-semibold text-dark">{{ $assistedPurchase->shipped_at->format('d/m/Y H:i') }}</div>
                </div>
            @endif
            @if($assistedPurchase->delivered_at)
                <div class="mb-2">
                    <span class="text-muted text-xs text-uppercase fw-bold">Fecha de Entrega:</span>
                    <div class="fw-semibold text-success">{{ $assistedPurchase->delivered_at->format('d/m/Y H:i') }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
