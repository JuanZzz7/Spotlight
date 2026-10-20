@extends('layouts.app')
@section('title', $company->name . ' — Spotlight')
@section('content')

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg bg-white border-bottom border-sl py-2">
    <div class="container py-1 d-flex align-items-center justify-content-between">
        <a class="d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 36px; height: 36px; background: linear-gradient(135deg, #7F1D3F, #D98C9A); font-size: 1.1rem;">S</div>
            <div>
                <span class="fw-bold fs-5 text-dark" style="font-family: var(--sl-font-display); letter-spacing: -0.5px;">SPOTLIGHT</span>
                <span class="d-block text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 0.08em; color: var(--sl-primary) !important;">LA ALDEA DIGITAL</span>
            </div>
        </a>
        <div class="d-flex gap-2 align-items-center">
            @auth
                @php $dash = match(auth()->user()->role) { 'admin'=>'admin.dashboard','business'=>'business.dashboard',default=>'user.dashboard'}; @endphp
                <a href="{{ route($dash) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">Mi Panel</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-pill-light px-3">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary px-4 rounded-pill shadow-sm">Registrarse</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Hero de la empresa --}}
<div style="position: relative; height: 260px; background: linear-gradient(135deg, #7F1D3F 0%, #D98C9A 100%); overflow: hidden;">
    @if($company->banner)
        <img src="{{ asset('storage/'.$company->banner) }}" class="w-100 h-100" style="object-fit:cover; opacity:0.4;">
    @endif
    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(24, 18, 38, 0.75) 0%, transparent 60%);"></div>

    <div class="container h-100 d-flex align-items-end pb-4" style="position: relative; z-index: 2;">
        <div class="d-flex align-items-center gap-4">
            <div class="bg-white rounded-circle shadow-lg d-flex align-items-center justify-content-center flex-shrink-0" style="width:90px;height:90px;border:4px solid white;">
                @if($company->logo)
                    <img src="{{ asset('storage/'.$company->logo) }}" class="rounded-circle w-100 h-100" style="object-fit:cover;">
                @else
                    <i class="bi bi-building text-muted fs-3"></i>
                @endif
            </div>
            <div class="text-white">
                <h1 class="fw-bold mb-1" style="font-size:1.8rem; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">{{ $company->name }}</h1>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge rounded-pill px-3 py-1" style="background:rgba(255,255,255,0.2); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.3); font-size:0.8rem;">
                        {{ $company->category }}
                    </span>
                    @if($company->phone)
                        <span class="text-white-75 small"><i class="bi bi-telephone-fill me-1"></i>{{ $company->phone }}</span>
                    @endif
                    @if($company->address || ($company->latitude && $company->longitude))
                        <span class="text-white-75 small">
                            <i class="bi bi-geo-alt-fill me-1"></i>
                            <span id="hero-geo-address">
                                @if($company->address)
                                    {{ $company->address }}
                                @else
                                    <span style="opacity:0.7;font-size:0.85em;">Cargando dirección...</span>
                                @endif
                            </span>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Barra info + Redes --}}
<div class="bg-white border-bottom py-3">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="text-muted small">
            @if($company->description)
                <i class="bi bi-info-circle me-1"></i>{{ Str::limit($company->description, 120) }}
            @endif
        </div>
        <div class="d-flex gap-3 align-items-center flex-wrap">
            @if($company->instagram)
                <a href="{{ $company->instagram }}" target="_blank" class="text-muted fs-5"><i class="bi bi-instagram"></i></a>
            @endif
            @if($company->facebook)
                <a href="{{ $company->facebook }}" target="_blank" class="text-muted fs-5"><i class="bi bi-facebook"></i></a>
            @endif
            @if($company->website)
                <a href="{{ $company->website }}" target="_blank" class="text-muted fs-5"><i class="bi bi-globe2"></i></a>
            @endif
            @if($company->email)
                <a href="mailto:{{ $company->email }}" class="text-muted fs-5"><i class="bi bi-envelope-fill"></i></a>
            @endif

            {{-- Botón ver en mapa --}}
            @if($company->latitude && $company->longitude)
                <button onclick="scrollToMap()" class="btn btn-sm rounded-pill px-3 fw-semibold d-flex align-items-center gap-2"
                        style="background: var(--sl-primary); color:#fff; border:none; box-shadow: 0 4px 12px rgba(127, 29, 63, 0.25);">
                    <i class="bi bi-map-fill"></i> Ver en el mapa
                </button>
            @endif

            @auth
                @if(auth()->user()->role === 'user')
                    <a href="{{ route('chat.show', $company->user_id) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="bi bi-chat-dots-fill me-1"></i>Chatear
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                    <i class="bi bi-chat-dots-fill me-1"></i>Chatear
                </a>
            @endauth
        </div>
    </div>
</div>

{{-- Productos --}}
<section class="py-5" style="background: var(--sl-bg);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Productos de {{ $company->name }}</h2>
                <p class="text-muted small mb-0">{{ $products->total() }} producto{{ $products->total() !== 1 ? 's' : '' }} disponible{{ $products->total() !== 1 ? 's' : '' }}</p>
            </div>
            <a href="{{ route('home') }}#empresas" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver al inicio
            </a>
        </div>

        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="vs-card h-100 d-flex flex-column overflow-hidden"
                         style="transition: transform 0.2s, box-shadow 0.2s;"
                         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--sl-shadow-hover)'"
                         onmouseout="this.style.transform='';this.style.boxShadow=''">
                        <a href="{{ route('public.product', $product) }}">
                            <img src="{{ $product->image_url }}" class="card-img-top" style="height:200px;object-fit:cover;" alt="{{ $product->name }}">
                        </a>
                        <div class="card-body d-flex flex-column p-4 flex-fill">
                            <h6 class="fw-bold mb-1 text-truncate" title="{{ $product->name }}">
                                <a href="{{ route('public.product', $product) }}" class="text-dark text-decoration-none">{{ $product->name }}</a>
                            </h6>
                            <div class="d-flex align-items-center gap-1 mb-2">
                                <i class="bi bi-star-fill text-warning" style="font-size: 0.75rem;"></i>
                                <span class="fw-bold text-dark text-xs">{{ $product->average_rating > 0 ? number_format($product->average_rating, 1) : '5.0' }}</span>
                                <span class="text-muted text-xs">({{ $product->reviews_count }})</span>
                                <a href="{{ route('public.product', $product) }}#reviews-section" class="text-xs text-primary text-decoration-none ms-1">Reseñas</a>
                            </div>
                            @if($product->description)
                                <p class="text-muted text-sm mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $product->description }}</p>
                            @endif
                            <div class="fw-bold fs-4 mt-auto mb-3" style="color:var(--sl-primary);">
                                ${{ number_format($product->price, 0, ',', '.') }}
                            </div>
                            @if($product->stock > 0)
                                <span class="badge bg-success-subtle text-success rounded-pill mb-3" style="font-size:0.75rem;">
                                    <i class="bi bi-check-circle me-1"></i>En stock ({{ $product->stock }})
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill mb-3" style="font-size:0.75rem;">
                                    <i class="bi bi-x-circle me-1"></i>Sin stock
                                </span>
                            @endif
                            @auth
                                @if(auth()->user()->role === 'user')
                                    <form action="{{ route('user.cart.add', $product) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary w-100 rounded-pill py-2 fw-medium shadow-sm" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-cart-plus me-1"></i>Comprar
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-primary w-100 rounded-pill py-2 fw-medium disabled">Comprar</button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn w-100 rounded-pill py-2 fw-medium shadow-sm text-white"
                                   style="background: var(--sl-primary); border: none;">
                                    <i class="bi bi-person-lock me-1"></i>Inicia sesión para comprar
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size:4rem;opacity:0.3;"></i>
                <h5 class="text-muted mt-3">Esta empresa aún no tiene productos publicados.</h5>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3 rounded-pill px-4">Explorar otras empresas</a>
            </div>
        @endif
    </div>
</section>

{{-- ══════ Sección del Mapa Interactivo ══════ --}}
@if($company->latitude && $company->longitude)
<section id="seccion-mapa" class="py-5 bg-white border-top">
    <div class="container">
        <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="color:var(--sl-text-heading);">
                    <i class="bi bi-geo-alt-fill me-2" style="color:var(--sl-primary);"></i>Ubicación de {{ $company->name }}
                </h3>
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt-fill me-1" style="color:var(--sl-primary);"></i>
                    <span id="map-section-address">
                        @if($company->address)
                            {{ $company->address }}
                        @else
                            <span style="color:#94a3b8;">Obteniendo dirección...</span>
                        @endif
                    </span>
                </p>
            </div>
            <a href="https://www.google.com/maps?q={{ $company->latitude }},{{ $company->longitude }}"
               target="_blank"
               class="btn btn-sm rounded-pill px-4 d-flex align-items-center gap-2 fw-medium"
               style="background:var(--sl-navy); color:#fff; border:none;">
                <i class="bi bi-box-arrow-up-right"></i> Abrir en Google Maps
            </a>
        </div>

        {{-- Wrapper del mapa --}}
        <div style="position:relative; border-radius:20px; box-shadow:0 16px 48px rgba(41,37,42,0.15); height:420px;">

            {{-- Mapa Leaflet: siempre visible para que pueda calcular dimensiones --}}
            <div id="company-map" style="height:420px; width:100%; border-radius:20px;"></div>

            {{-- Skeleton: overlay absoluto encima del mapa --}}
            <div id="map-skeleton" style="
                position:absolute; inset:0; border-radius:20px;
                background: linear-gradient(135deg, #7F1D3F 0%, #5E122C 40%, #29252A 100%);
                display:flex; flex-direction:column;
                align-items:center; justify-content:center; gap:12px;
                color:rgba(255,255,255,0.9);
                z-index:500; transition: opacity 0.5s ease;
            ">
                <div style="font-size:3rem;"><i class="bi bi-map"></i></div>
                <div style="font-weight:600; font-size:1rem;">Cargando mapa...</div>
                <div style="display:flex; gap:6px;">
                    <span class="map-dot"></span>
                    <span class="map-dot" style="animation-delay:.2s"></span>
                    <span class="map-dot" style="animation-delay:.4s"></span>
                </div>
                <div style="font-size:0.78rem; opacity:0.75;">
                    <i class="bi bi-geo-alt-fill me-1"></i>
                    <span id="skeleton-address">
                        @if($company->address)
                            {{ $company->address }}
                        @else
                            Buscando dirección...
                        @endif
                    </span>
                </div>
            </div>

            {{-- Badge dirección (visible tras cargar el mapa) --}}
            <div id="map-coords-badge" style="display:none; position:absolute; bottom:12px; left:12px; z-index:1000;
                        background:rgba(41,37,42,0.88); color:#fff; padding:8px 14px;
                        border-radius:10px; font-size:0.75rem; backdrop-filter:blur(4px); max-width:280px;">
                <i class="bi bi-geo-alt-fill me-1"></i>
                <span id="badge-address">
                    @if($company->address)
                        {{ $company->address }}
                    @else
                        Cargando dirección...
                    @endif
                </span>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Footer --}}
<footer class="text-white py-4" style="background-color: var(--sl-navy);">
    <div class="container text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Spotlight" class="vs-brand-logo mb-2">
        <p class="text-white-50 mb-0 small">© {{ date('Y') }} Spotlight. Todos los derechos reservados.</p>
    </div>
</footer>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #company-map .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: 0 8px 28px rgba(0,35,111,0.2);
    }
    #company-map .leaflet-tile-container img {
        border-radius: 0 !important;
    }
    /* Dots animados del skeleton */
    @@keyframes dot-pulse {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
        40%            { transform: scale(1);   opacity: 1;   }
    }
    .map-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: rgba(255,255,255,0.9);
        display: inline-block;
        animation: dot-pulse 1.4s ease-in-out infinite;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($company->latitude && $company->longitude)
<script>
@php
    $mapAddress = $company->address
        ?? (number_format($company->latitude, 4) . ', ' . number_format($company->longitude, 4));
@endphp
document.addEventListener('DOMContentLoaded', function () {
    var lat     = {{ $company->latitude }};
    var lng     = {{ $company->longitude }};
    var name    = @json($company->name);
    var address = @json($mapAddress);

    var skeleton = document.getElementById('map-skeleton');
    var badge    = document.getElementById('map-coords-badge');
    var revealed = false;

    // ─── Ocultar skeleton con fade (el mapa ya es visible) ─────────
    function revealMap() {
        if (revealed) return;
        revealed = true;
        skeleton.style.opacity = '0';
        setTimeout(function () {
            skeleton.style.display = 'none';
            if (badge) badge.style.display = 'block';
            map.invalidateSize();
        }, 500);
    }

    // ─── Mapa Leaflet ─────────────────────────────────────────────
    var map = L.map('company-map', {
        zoomControl: true,
        scrollWheelZoom: false
    }).setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Forzar dimensiones inmediatamente (el div ya tiene tamaño)
    map.invalidateSize();

    // Revelar al cargar el primer tile (o máximo 3s después)
    map.once('load', revealMap);
    setTimeout(revealMap, 3000);

    // ─── Marcador corporativo ──────────────────────────────────────
    var pin = L.divIcon({
        html: '<div style="background:linear-gradient(135deg,#00236f,#0d9488);width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 4px 14px rgba(0,35,111,0.45);"></div>',
        className: '',
        iconSize:    [36, 36],
        iconAnchor:  [18, 36],
        popupAnchor: [0, -38]
    });

    var marker = L.marker([lat, lng], { icon: pin }).addTo(map);
    marker.bindPopup(
        '<div style="min-width:170px;text-align:center;padding:8px 4px;">'
        + '<strong style="color:#00236f;font-size:0.92rem;">' + name + '</strong><br>'
        + '<span id="popup-addr" style="font-size:0.77rem;color:#64748b;display:block;margin-top:4px;">'
        + '<i class="bi bi-geo-alt-fill" style="color:#0d9488;margin-right:4px;"></i>'
        + address + '</span>'
        + '</div>'
    ).openPopup();

    // ─── Geocodificación inversa (solo si no hay dirección en BD) ──
    @if(!$company->address)
    fetch('https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=es')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var a = data.address || {};
            var label = '';
            if (a.road)         label += a.road;
            if (a.house_number) label += ' ' + a.house_number;
            var city = a.city || a.town || a.municipality || a.village || '';
            if (city) label += (label ? ', ' : '') + city;
            if (!label && data.display_name)
                label = data.display_name.split(',').slice(0, 3).join(', ');
            if (!label) return; // si falla, dejar coordenadas

            // Actualizar todos los puntos donde se muestra la dirección
            var ids = ['hero-geo-address', 'map-section-address', 'skeleton-address', 'popup-addr', 'badge-address'];
            ids.forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.textContent = label;
            });
        })
        .catch(function() {
            // Si geocoding falla, mostrar texto genérico (no coordenadas)
            ['hero-geo-address', 'map-section-address', 'skeleton-address', 'badge-address'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.textContent = 'Ver ubicación en el mapa';
            });
        });
    @endif

    // ─── Scroll suave al mapa ─────────────────────────────────────
    window.scrollToMap = function () {
        var sec = document.getElementById('seccion-mapa');
        if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(function () { map.invalidateSize(); }, 500);
    };
});
</script>
@endif
@endpush

@endsection

