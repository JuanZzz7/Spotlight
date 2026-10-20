@extends('layouts.dashboard')
@section('title', 'Solicitudes Internacionales - ' . $company->name)
@section('page-title', 'Compras Internacionales Asistidas')

@section('dashboard-content')
{{-- Header Area --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Compras Internacionales Asistidas</h3>
            <span class="badge {{ $company->isMexican() ? 'bg-success' : 'badge-purple' }} rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                {{ $company->country_flag }} {{ $company->country === 'MX' ? 'Mercado México' : 'Mercado Colombia' }}
            </span>
        </div>
        <p class="text-muted mb-0" style="font-size: 0.88rem;">
            Gestiona solicitudes de compradores internacionales, cotiza costos de envío y coordina entregas entre Colombia y México.
        </p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('business.products.index') }}" class="btn bg-white border text-dark shadow-xs rounded-pill px-3 py-2 d-flex align-items-center gap-2 fw-semibold" style="font-size: 0.85rem;">
            <i class="bi bi-box-seam"></i> Mis Productos
        </a>
    </div>
</div>

{{-- Barra de Estadísticas --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); width: 44px; height: 44px;">
                <i class="bi bi-globe2 fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">TOTAL RECIBIDAS</div>
                <div class="fw-bold text-dark lh-1 fs-4 mt-1">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); color: #d97706; width: 44px; height: 44px;">
                <i class="bi bi-hourglass-split fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">EN NEGOCIACIÓN</div>
                <div class="fw-bold text-warning lh-1 fs-4 mt-1">{{ $stats['pending'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.15); color: #059669; width: 44px; height: 44px;">
                <i class="bi bi-credit-card-2-front-fill fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">PAGADAS</div>
                <div class="fw-bold text-success lh-1 fs-4 mt-1">{{ $stats['paid'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(99, 102, 241, 0.15); color: #4f46e5; width: 44px; height: 44px;">
                <i class="bi bi-truck fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">ENTREGADAS</div>
                <div class="fw-bold text-dark lh-1 fs-4 mt-1">{{ $stats['completed'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros por estado --}}
<div class="bg-white p-3 rounded-4 border shadow-xs mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted text-uppercase fw-bold me-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">ESTADO:</span>
            <a href="{{ route('business.assisted_purchases.index') }}" class="btn btn-sm rounded-pill px-3 {{ empty($status) ? 'btn-dark' : 'btn-light border' }}" style="font-size: 0.8rem;">
                Todas ({{ $stats['total'] }})
            </a>
            @foreach($statuses as $key => $label)
                <a href="{{ route('business.assisted_purchases.index', ['status' => $key]) }}" class="btn btn-sm rounded-pill px-3 {{ $status === $key ? 'btn-dark' : 'btn-light border' }}" style="font-size: 0.8rem;">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 shadow-xs mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

{{-- Tabla de Solicitudes --}}
<div class="bg-white rounded-4 border shadow-xs overflow-hidden">
    @if($requests->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light" style="font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; color: #64748b;">
                    <tr>
                        <th class="ps-4 py-3">Código</th>
                        <th class="py-3">Producto Solicitado</th>
                        <th class="py-3">Comprador</th>
                        <th class="py-3">Destino</th>
                        <th class="py-3">Importe Congelado</th>
                        <th class="py-3">Total Acordado</th>
                        <th class="py-3 text-center">Estado</th>
                        <th class="pe-4 py-3 text-end">Acción</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @foreach($requests as $req)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="fw-bold font-monospace text-dark">{{ $req->request_code }}</span>
                                <div class="text-muted text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $req->product->image_url ?? asset('images/logo.png') }}" alt="" class="rounded-3 border" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $req->product_name }}</div>
                                        <span class="text-muted text-xs">Cant: <strong>{{ $req->quantity }}</strong> ud(s)</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold text-dark">{{ $req->recipient_name }}</div>
                                <div class="text-muted text-xs">{{ $req->user->email ?? $req->recipient_phone }}</div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border">
                                    {{ $req->destination_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }}
                                </span>
                                <div class="text-muted text-xs">{{ $req->destination_city }}, {{ $req->destination_state }}</div>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold text-dark">{{ $req->formatted_original_price }}</span>
                                <div class="text-muted text-xs">{{ $req->original_currency }}</div>
                            </td>
                            <td class="py-3">
                                <span class="fw-black" style="color: var(--sl-primary, #7F1D3F);">
                                    {{ $req->formatted_total_agreed }}
                                </span>
                                <div class="text-muted text-xs">Envío: {{ number_format($req->shipping_cost_agreed, 2) }} {{ $req->payment_currency }}</div>
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge {{ $req->status_badge }} rounded-pill px-3 py-1">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <a href="{{ route('business.assisted_purchases.show', $req) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-xs">
                                    <i class="bi bi-pencil-square"></i> Cotizar / Gestionar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $requests->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5 p-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F);">
                <i class="bi bi-globe fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark">No hay solicitudes internacionales en esta sección</h5>
            <p class="text-muted mb-0" style="max-width: 480px; margin: 0 auto; font-size: 0.9rem;">
                Cuando compradores de {{ $company->country === 'MX' ? 'Colombia' : 'México' }} soliciten tus productos, aparecerán aquí para que acuerdes costos de flete, métodos de pago y tiempos de entrega.
            </p>
        </div>
    @endif
</div>
@endsection
