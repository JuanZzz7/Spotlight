@extends('layouts.dashboard')

@section('page-title', 'Mis Solicitudes Internacionales')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">Mis Compras Internacionales Asistidas</h4>
            <p class="text-muted small mb-0">Seguimiento y coordinación de envíos transfronterizos entre Colombia 🇨🇴 y México 🇲🇽.</p>
        </div>
        <div>
            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-shop me-1"></i> Explorar Catálogo Multinacional
            </a>
        </div>
    </div>

    {{-- Filtro de Estado --}}
    <div class="vs-card p-3 mb-4 border-0 shadow-sm">
        <form action="{{ route('user.assisted_purchases.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if(request('status'))
            <div class="col-auto">
                <a href="{{ route('user.assisted_purchases.index') }}" class="btn btn-sm btn-link text-muted">Limpiar filtro</a>
            </div>
            @endif
        </form>
    </div>

    {{-- Listado de Solicitudes --}}
    @if($requests->isEmpty())
        <div class="vs-card p-5 text-center border-0 shadow-sm">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: #F6E7EB; color: #7F1D3F;">
                <i class="bi bi-globe-americas fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark">No tienes solicitudes internacionales activas</h5>
            <p class="text-muted small mb-4">Cuando te interese un producto de otro país (Colombia o México), pulsa en "Compra Internacional Asistida" para cotizar envíos y coordinar la compra.</p>
            <a href="{{ route('user.dashboard') }}" class="btn btn-spotlight rounded-pill px-4 py-2 text-white">
                Ver Tienda
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($requests as $item)
            <div class="col-12">
                <div class="vs-card p-4 border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item->product ? $item->product->image_url : asset('images/logo.png') }}" 
                                 alt="{{ $item->product?->name }}" 
                                 class="rounded-3" style="width: 72px; height: 72px; object-fit: cover; border: 1px solid #e2e8f0;">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="fw-bold text-dark font-monospace">{{ $item->request_code }}</span>
                                    <span class="badge {{ $item->status_badge_class }} rounded-pill px-3 py-1">
                                        {{ $item->status_label }}
                                    </span>
                                    <span class="badge bg-light text-dark border rounded-pill">
                                        {{ $item->origin_flag }} {{ $item->origin_country }} ➔ {{ $item->destination_flag }} {{ $item->destination_country }}
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">{{ $item->product?->name }}</h6>
                                <div class="text-muted small">
                                    Vendedor: <strong>{{ $item->company?->name }}</strong> • Cantidad: {{ $item->quantity }} • Fecha: {{ $item->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-3 text-md-end">
                            <div>
                                <div class="text-muted small">Importe Acordado:</div>
                                <div class="fw-bold fs-5" style="color: #7F1D3F;">
                                    {{ $item->formatted_total_agreed }}
                                </div>
                                @if($item->shipping_cost_agreed > 0)
                                    <div class="text-success small" style="font-size: 0.75rem;">Flete cotizado incluido</div>
                                @else
                                    <div class="text-warning small" style="font-size: 0.75rem;">Flete en cotización</div>
                                @endif
                            </div>

                            <div>
                                <a href="{{ route('user.assisted_purchases.show', $item) }}" class="btn btn-spotlight btn-sm rounded-pill px-4 py-2 text-white fw-semibold">
                                    Ver Detalle <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
