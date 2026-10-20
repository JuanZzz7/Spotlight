@extends('layouts.dashboard')
@section('title','Mis Compras - Spotlight')
@section('page-title','Mis Compras')

@section('dashboard-content')
@forelse($orders as $order)
<div class="vs-card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="p-4 bg-light d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-bold text-dark fs-5">Orden #{{ $order->id }}</span>
                <span class="text-muted text-sm ms-3"><i class="bi bi-calendar3 me-1"></i> {{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('user.orders.tracking', $order) }}" class="btn btn-sm btn-spotlight rounded-pill px-3 py-2 fw-bold shadow-2xs d-inline-flex align-items-center gap-2">
                    <i class="bi bi-truck"></i>
                    <span>Rastrear Envío</span>
                    @if($order->shipment)
                        <span class="badge bg-white text-dark rounded-pill text-xs fw-bold">{{ $order->shipment->status_label }}</span>
                    @else
                        <span class="badge bg-white text-dark rounded-pill text-xs fw-bold">En preparación</span>
                    @endif
                </a>

                @if($order->electronicDocument && $order->electronicDocument->isValidated())
                    <a href="{{ route('user.orders.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 shadow-sm">
                        <i class="bi bi-receipt me-1"></i> Factura DIAN
                    </a>
                @endif

                <span class="fw-bold fs-4 ms-2" style="color: var(--sl-primary, #7F1D3F); font-family: 'Outfit', sans-serif;">${{ number_format($order->total,0,',','.') }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle border-light mb-0">
                <thead class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                    <tr>
                        <th class="ps-4 py-3 border-0">Producto</th>
                        <th class="py-3 border-0">Empresa</th>
                        <th class="py-3 border-0">Cant.</th>
                        <th class="py-3 border-0">Precio unit.</th>
                        <th class="py-3 border-0">Subtotal</th>
                        <th class="pe-4 py-3 border-0 text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($order->details as $detail)
                <tr>
                    <td class="ps-4 py-4 fw-bold text-dark">
                        @if($detail->product && $detail->product->active)
                            <a href="{{ route('public.product', $detail->product) }}" class="text-dark text-decoration-none">
                                {{ $detail->effective_product_name }}
                            </a>
                        @else
                            {{ $detail->effective_product_name }}
                        @endif
                    </td>
                    <td class="text-muted py-4"><i class="bi bi-shop me-1" style="color: var(--sl-primary, #7F1D3F);"></i> {{ $detail->company ? $detail->company->name : ($detail->product->company->name ?? '-') }}</td>
                    <td class="text-dark fw-medium py-4">{{ $detail->quantity }}</td>
                    <td class="text-muted py-4">${{ number_format($detail->unit_price,0,',','.') }}</td>
                    <td class="fw-bold text-dark py-4">${{ number_format($detail->subtotal,0,',','.') }}</td>
                    <td class="pe-4 py-4 text-end">
                        @if($detail->product && $detail->product->active)
                            <a href="{{ route('public.product', $detail->product) }}#form-review" class="btn btn-sm btn-outline-warning rounded-pill px-3 shadow-2xs fw-semibold text-dark">
                                <i class="bi bi-star-fill text-warning me-1"></i> Calificar
                            </a>
                        @else
                            <span class="text-muted text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<div class="text-center py-5 mt-4">
    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 120px; height: 120px; background: var(--sl-primary-soft, #F6E7EB);">
        <i class="bi bi-bag-x" style="font-size: 4rem; color: var(--sl-primary, #7F1D3F);"></i>
    </div>
    <h3 class="fw-bold text-dark mt-3" style="font-family: 'Outfit', sans-serif;">Aún no has realizado compras</h3>
    <p class="text-muted fs-5 mb-4">Explora la plataforma y encuentra productos que te gusten.</p>
    <a href="{{ route('user.dashboard') }}" class="btn btn-spotlight btn-lg px-5 shadow-sm rounded-pill fw-bold">Ir al Marketplace</a>
</div>
@endforelse
<div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
@endsection
