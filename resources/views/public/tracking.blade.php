@extends('layouts.app')
@section('title', 'Rastreo de Envíos en Tiempo Real - Spotlight')

@push('styles')
<link href="{{ asset('css/public.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Header Navbar Mini -->
<header class="bg-white border-bottom shadow-xs py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
            <img src="{{ asset('images/logo.png') }}" alt="Spotlight" style="height: 38px;" class="me-2">
            <span class="fw-bold fs-4" style="color: var(--sl-primary, #7F1D3F); font-family: 'Outfit', sans-serif;">SPOTLIGHT</span>
            <span class="badge badge-purple ms-2 d-none d-sm-inline-block">PORTAL LOGÍSTICO</span>
        </a>
        <div class="d-flex gap-2 align-items-center">
            <button id="btnSwitchLang" type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold js-switch-lang">
                <i class="bi bi-translate me-1"></i> EN / ES
            </button>
            @auth
                <a href="{{ route('user.orders.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                    <i class="bi bi-bag-check me-1"></i> Mis Compras
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-light border rounded-pill px-3">Iniciar Sesión</a>
            @endauth
        </div>
    </div>
</header>

<main class="py-5" style="background: var(--sl-bg, #FFFCF7); min-height: 85vh;">
    <div class="container">
        <!-- Hero Search Section -->
        <div class="text-center mb-5 mx-auto" style="max-width: 680px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white border shadow-xs text-xs fw-bold mb-3" style="color: var(--sl-primary, #7F1D3F);">
                <span class="pulse-live"></span> SISTEMA DE RASTREO LOGÍSTICO EN VIVO
            </div>
            <h1 class="display-6 fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">
                Rastrea tu Paquete en Tiempo Real
            </h1>
            <p class="text-muted" style="font-size: 0.95rem;">
                Ingresa el número de guía asignado (Coordinadora, Servientrega, Inter Rapidísimo o Spotlight Express) para ver la ubicación y estado actual de tu pedido.
            </p>

            <form action="{{ route('public.tracking') }}" method="GET" class="mt-4">
                <div class="p-2 bg-white rounded-pill shadow-sm border d-flex align-items-center gap-2">
                    <i class="bi bi-search text-muted ms-3 fs-5"></i>
                    <input type="text" name="guia" value="{{ $trackingNumber }}" placeholder="Ej: CRD-261019-8A1B2 o número de guía..." class="form-control border-0 bg-transparent fw-semibold" style="font-size: 1rem; box-shadow: none;" required>
                    <button type="submit" class="btn btn-spotlight rounded-pill px-4 py-2 fw-bold shadow-xs">
                        Rastrear
                    </button>
                </div>
            </form>
        </div>

        @if($error)
            <div class="alert alert-danger border-0 rounded-4 shadow-sm p-4 mx-auto mb-5 text-center" style="max-width: 680px; background: rgba(239, 68, 68, 0.1); color: #991b1b;">
                <i class="bi bi-exclamation-circle-fill fs-3 text-danger d-block mb-2"></i>
                <h6 class="fw-bold mb-1">Guía no encontrada</h6>
                <p class="mb-0 text-sm">{{ $error }}</p>
            </div>
        @endif

        @if($shipment)
            <!-- Shipment Details Card -->
            <div class="vs-card border-0 shadow-sm p-4 p-md-5 mb-5 rounded-4 bg-white mx-auto" style="max-width: 900px;">
                <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 border-bottom gap-3 mb-4">
                    <div>
                        <span class="text-muted text-xs text-uppercase fw-bold">Número de Guía Oficial</span>
                        <h3 class="fw-bold text-dark mb-0 font-monospace">{{ $shipment->tracking_number }}</h3>
                        <span class="text-muted text-sm">Transportadora: <strong>{{ $shipment->carrier_name }}</strong></span>
                    </div>

                    <div class="text-md-end">
                        <span class="badge {{ $shipment->status_badge_class }} rounded-pill px-3 py-2 fw-bold fs-6">
                            {{ $shipment->status_label }}
                        </span>
                        <div class="text-muted text-xs mt-1">
                            <span class="pulse-live me-1"></span> Actualizado en tiempo real
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between text-xs fw-bold text-muted mb-2">
                        <span>Preparación</span>
                        <span>Despacho</span>
                        <span>En Tránsito</span>
                        <span>En Reparto</span>
                        <span>Entregado</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 10px; background-color: #f1f5f9;">
                        <div class="progress-bar" style="width: {{ $shipment->progress_percentage }}%; background: linear-gradient(90deg, var(--sl-primary, #7F1D3F), var(--sl-accent, #D98C9A)); border-radius: 10px;"></div>
                    </div>
                </div>

                <!-- Key Metrics Grid -->
                <div class="row g-3 p-3 bg-light rounded-4 border mb-4">
                    <div class="col-sm-4">
                        <span class="text-muted text-xs text-uppercase fw-bold">Origen</span>
                        <div class="fw-bold text-dark">{{ $shipment->origin_city }}</div>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted text-xs text-uppercase fw-bold">Destino</span>
                        <div class="fw-bold text-dark">{{ $shipment->destination_city }}</div>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted text-xs text-uppercase fw-bold">Ubicación Actual</span>
                        <div class="fw-bold text-primary"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $shipment->current_location ?: $shipment->origin_city }}</div>
                    </div>
                </div>

                <!-- Timeline -->
                <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-clock-history text-primary me-2"></i> Eventos Logísticos Registrados
                </h5>

                <div class="timeline-log">
                    @forelse($shipment->events as $event)
                        <div class="timeline-item">
                            <div class="timeline-dot"><i class="bi {{ $event->icon }}"></i></div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold text-dark mb-0">{{ $event->title }}</h6>
                                <span class="text-muted text-xs">{{ $event->occurred_at?->format('d/m/Y h:i A') }}</span>
                            </div>
                            <p class="text-muted text-sm mb-1">{{ $event->description }}</p>
                            @if($event->location)
                                <span class="badge bg-light text-muted border text-xs">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $event->location }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No se registran eventos aún.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</main>
@endsection
