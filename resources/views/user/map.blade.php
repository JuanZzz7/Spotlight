@extends('layouts.dashboard')
@section('title','Mapa Geo-Comercial - Spotlight')

@section('page-title')
<div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between w-100 gap-3">
    <div>
        <div class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">PORTAL / EXPLORAR COMERCIOS LOCALES</div>
        <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Mapa Geo-Comercial de Tiendas y Comercios</h3>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">Explora negocios locales verificados, consulta sus horarios en tiempo real y agrega productos directo a tu carrito.</p>
    </div>
    <div class="d-flex align-items-center flex-wrap gap-3">
        <span class="badge bg-light text-dark shadow-sm px-3 py-2 border rounded-pill" style="font-size: 0.75rem;">
            <span class="text-success me-1">●</span> {{ count($companies) }} Comercios Activos
        </span>
        <div class="bg-light p-1 rounded-pill d-flex border shadow-sm">
            <button type="button" id="btnToggleMap" onclick="switchView('map')" class="btn btn-sm rounded-pill px-3 text-white fw-bold shadow-sm" style="background: var(--sl-primary, #7F1D3F); font-size: 0.75rem;">
                <i class="bi bi-map me-1"></i> Mapa
            </button>
            <button type="button" id="btnToggleList" onclick="switchView('list')" class="btn btn-sm rounded-pill px-3 text-muted fw-bold border-0 bg-transparent hover-bg" style="font-size: 0.75rem;">
                <i class="bi bi-list-ul me-1"></i> Lista
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<style>
    .map-container-rel {
        position: relative;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        height: 560px;
        background: #f1f5f9;
        margin-bottom: 24px;
        z-index: 1;
        border: 1px solid #e2e8f0;
    }
    
    #map { height: 100%; width: 100%; z-index: 1; }

    /* Custom UI Overlays */
    .map-overlay-filters {
        position: absolute;
        top: 18px;
        left: 18px;
        z-index: 1000;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        max-width: calc(100% - 36px);
        pointer-events: none;
    }
    
    .map-overlay-filters > * {
        pointer-events: auto;
    }

    .cat-filter-btn {
        background: #ffffff;
        color: #334155;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }
    .cat-filter-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .cat-filter-btn.active {
        background: var(--sl-primary, #7F1D3F) !important;
        color: #ffffff !important;
        border-color: var(--sl-primary, #7F1D3F) !important;
        box-shadow: 0 4px 12px rgba(127, 29, 63, 0.25) !important;
    }

    /* Buscador flotante en el mapa */
    .map-search-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 50rem;
        display: flex;
        align-items: center;
        padding: 4px 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .map-search-input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.8rem;
        color: #1e293b;
        padding: 4px 8px;
        width: 170px;
    }

    /* Modern Popup Card (Glassmorphism) */
    .company-detail-card {
        position: absolute;
        top: 18px;
        right: 18px;
        z-index: 1000;
        width: 390px;
        max-width: calc(100% - 36px);
        max-height: calc(100% - 36px);
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.97);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 1.5rem;
        padding: 22px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        transform: translateX(120%);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease;
    }
    
    .company-detail-card::-webkit-scrollbar { width: 4px; }
    .company-detail-card::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .company-detail-card.active {
        transform: translateX(0);
        opacity: 1;
        pointer-events: auto;
    }

    /* Map Marker styling with Store Logo */
    .custom-company-marker-wrapper {
        background: transparent;
        border: none;
    }
    .company-marker-pin {
        position: relative;
        width: 44px;
        height: 44px;
        background: #ffffff;
        border-radius: 50%;
        border: 3px solid #7F1D3F;
        box-shadow: 0 5px 15px rgba(127, 29, 63, 0.38);
        cursor: pointer;
        transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.25s ease, border-color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .company-marker-pin:hover, .company-marker-pin.is-active {
        transform: scale(1.22) translateY(-4px);
        border-color: #D98C9A;
        box-shadow: 0 8px 24px rgba(127, 29, 63, 0.55);
        z-index: 9999 !important;
    }
    .company-marker-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }
    .company-marker-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .company-marker-arrow {
        position: absolute;
        bottom: -7px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 8px solid #7F1D3F;
        transition: border-top-color 0.2s ease;
    }
    .company-marker-pin:hover .company-marker-arrow,
    .company-marker-pin.is-active .company-marker-arrow {
        border-top-color: #D98C9A;
    }
    .company-marker-indicator {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        z-index: 3;
    }
    .company-marker-indicator.is-online {
        background-color: #10b981;
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
    }
    .company-marker-indicator.is-offline {
        background-color: #ef4444;
    }

    .close-btn {
        position: absolute;
        top: 18px;
        right: 18px;
        background: #f1f5f9;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: all 0.2s ease;
        z-index: 5;
    }
    .close-btn:hover { background: #e2e8f0; color: #0f172a; }
    
    /* Stats Grid (2 Columns: Horario & Calificación) */
    .store-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        text-align: center;
        margin: 16px 0;
        padding: 12px 0;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .product-mini-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 0;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s ease;
    }
    .product-mini-item:last-child {
        border-bottom: none;
    }
    .product-mini-img-wrap {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border-radius: 10px;
        overflow: hidden;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }
    .btn-add-cart-mini {
        background: var(--sl-primary-soft, #F6E7EB);
        color: var(--sl-primary, #7F1D3F);
        font-size: 0.68rem;
        padding: 3px 10px;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-add-cart-mini:hover {
        background: var(--sl-primary, #7F1D3F);
        color: #ffffff;
    }

    /* Toast flotante para carrito */
    .cart-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 99999;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 50rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }
    .cart-toast.show {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.08) !important;
    }
    .hover-bg:hover {
        background: #f8fafc !important;
    }
</style>
@endpush

@section('dashboard-content')
{{-- Toast de confirmación de carrito --}}
<div id="cartToast" class="cart-toast shadow-lg">
    <i class="bi bi-check-circle-fill text-success fs-5"></i>
    <span id="cartToastMessage">Producto agregado al carrito</span>
</div>

{{-- VISTA 1: MAPA GEO-COMERCIAL --}}
<div id="viewMapWrapper">
    <div class="map-container-rel">
        {{-- Filtros Flotantes y Categorías Disponibles --}}
        <div class="map-overlay-filters">
            {{-- Buscador compacto --}}
            <div class="map-search-pill">
                <i class="bi bi-search text-muted" style="font-size: 0.85rem;"></i>
                <input type="text" id="mapSearchInput" class="map-search-input" placeholder="Buscar comercio o producto...">
                <i class="bi bi-x-circle-fill text-muted" id="clearSearchBtn" style="cursor: pointer; display: none; font-size: 0.8rem;"></i>
            </div>

            {{-- Botón Todas --}}
            <button type="button" class="btn cat-filter-btn active rounded-pill shadow-sm px-3 fw-bold" data-category="all" onclick="filterByCategory('all')">
                <span class="text-success me-1">●</span> Todas ({{ count($companies) }})
            </button>

            {{-- Categorías Disponibles únicamente --}}
            @foreach($categories as $cat)
                <button type="button" class="btn cat-filter-btn rounded-pill shadow-sm px-3 fw-bold" data-category="{{ $cat }}" onclick="filterByCategory('{{ $cat }}')">
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        {{-- Contenedor del Mapa Leaflet --}}
        <div id="map"></div>

        {{-- Tarjeta de Detalle del Comercio (Se activa con poner el cursor encima o dar click) --}}
        <div id="companyDetail" class="company-detail-card">
            <button type="button" class="close-btn shadow-sm" id="closeDetail" title="Cerrar"><i class="bi bi-x-lg"></i></button>
            
            {{-- Cabecera con Foto / Logo y Nombre --}}
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="rounded-circle bg-light border shadow-sm overflow-hidden flex-shrink-0" style="width: 58px; height: 58px;">
                    <img src="" id="detImg" class="w-100 h-100 object-fit-cover" alt="Comercio">
                </div>
                <div class="min-w-0 flex-fill">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.55rem; font-weight: 800; letter-spacing: 0.5px;">VERIFICADO</span>
                        <span class="text-muted fw-bold text-uppercase text-truncate" style="font-size: 0.6rem;" id="detCat">Comercio</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1 text-truncate" id="detName" style="letter-spacing: -0.5px;">Nombre de la Tienda</h5>
                    <div class="text-muted text-truncate" style="font-size: 0.75rem;" id="detAddr">Ubicación</div>
                </div>
            </div>
            
            {{-- Grid de 2 Columnas: Estado de Horario y Calificación Real --}}
            <div class="store-stats-grid">
                {{-- Horario (Abierto / Cerrado) --}}
                <div>
                    <div class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.58rem; letter-spacing: 0.5px;">ESTADO DE ATENCIÓN</div>
                    <div id="detStatusBadge" class="mb-1">
                        {{-- Badge Abierto / Cerrado dinámico --}}
                    </div>
                    <div class="text-muted text-truncate" style="font-size: 0.68rem;" id="detScheduleHours">Horario</div>
                </div>

                {{-- Calificación de Reseñas de Usuarios --}}
                <div class="border-start border-light ps-2">
                    <div class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.58rem; letter-spacing: 0.5px;">CALIFICACIÓN</div>
                    <div id="detRatingContainer" class="fw-bold text-dark mb-0 d-flex align-items-center justify-content-center gap-1" style="font-size: 0.95rem;">
                        {{-- Puntuación dinámica --}}
                    </div>
                    <div class="text-muted" style="font-size: 0.68rem;" id="detReviewsCount">(0 reseñas)</div>
                </div>
            </div>
            
            {{-- Sección de Productos --}}
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="bi bi-bag-check me-1" style="color: var(--sl-primary, #7F1D3F);"></i> PRODUCTOS
                </h6>
                <a href="#" id="detAllProductsLink" class="text-decoration-none" style="font-size: 0.7rem; color: var(--sl-primary, #7F1D3F); font-weight: 600;">Ver catálogo completo</a>
            </div>
            
            {{-- Lista de productos dinámica con botón + Carrito --}}
            <div class="mb-3" id="detProductsContainer" style="max-height: 200px; overflow-y: auto;">
                {{-- Renderizado con JS --}}
            </div>

            {{-- Botones de Acción --}}
            <a href="#" id="detStore" class="btn text-white w-100 fw-bold rounded-pill shadow-sm mb-2" style="background: linear-gradient(135deg, #7F1D3F, #D98C9A); font-size: 0.85rem; padding: 9px;">
                <i class="bi bi-shop me-1"></i> Ir a la Tienda
            </a>
            
            <div class="d-flex gap-2">
                <a href="#" id="detRoute" target="_blank" class="btn btn-light border w-50 fw-bold rounded-pill text-muted hover-bg" style="font-size: 0.78rem; padding: 7px;">
                    <i class="bi bi-signpost-split text-primary me-1"></i> Cómo llegar
                </a>
                <a href="#" id="detChat" class="btn btn-light border w-50 fw-bold rounded-pill text-muted hover-bg" style="font-size: 0.78rem; padding: 7px;">
                    <i class="bi bi-chat-dots me-1" style="color: var(--sl-primary, #7F1D3F);"></i> Chat Directo
                </a>
            </div>
            
            <div class="text-center mt-3 pt-2 border-top">
                <span class="text-muted" style="font-size: 0.6rem;"><i class="bi bi-shield-check text-success me-1"></i>Red de Comercios Locales Verificados • SPOTLIGHT Marketplace</span>
            </div>
        </div>
    </div>
</div>

{{-- VISTA 2: LISTA ALTERNATIVA DE COMERCIOS --}}
<div id="viewListWrapper" style="display: none;" class="mb-4">
    <div class="row g-3">
        @forelse($companies as $c)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 rounded-4 shadow-sm p-3 hover-lift bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-light border shadow-sm overflow-hidden flex-shrink-0" style="width: 52px; height: 52px;">
                            <img src="{{ $c['logo_url'] }}" class="w-100 h-100 object-fit-cover" alt="{{ $c['name'] }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($c['name']) }}&background=7F1D3F&color=fff'">
                        </div>
                        <div class="min-w-0 flex-fill">
                            <span class="badge text-uppercase mb-1" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.6rem;">{{ $c['category'] }}</span>
                            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $c['name'] }}</h6>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $c['address'] }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light mb-3">
                        <div>
                            @if($c['is_open'])
                                <span class="badge rounded-pill bg-success-subtle text-success fw-bold px-2 py-1" style="font-size: 0.7rem;">
                                    <span class="text-success me-1">●</span> Abierto
                                </span>
                            @else
                                <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-2 py-1" style="font-size: 0.7rem;">
                                    <span class="text-danger me-1">●</span> Cerrado
                                </span>
                            @endif
                            <div class="text-muted mt-1" style="font-size: 0.65rem;">{{ $c['schedule_hours'] }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark d-flex align-items-center justify-content-end gap-1" style="font-size: 0.85rem;">
                                <i class="bi bi-star-fill text-warning" style="font-size: 0.75rem;"></i> {{ $c['rating'] ?? '5.0' }}
                            </div>
                            <div class="text-muted" style="font-size: 0.65rem;">({{ $c['reviews_count'] }} reseñas)</div>
                        </div>
                    </div>

                    <div class="mb-3 flex-fill">
                        <div class="fw-bold text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">PRODUCTOS DESTACADOS:</div>
                        @if(count($c['products']) > 0)
                            <div class="d-flex flex-column gap-2">
                                @foreach(array_slice($c['products']->toArray(), 0, 2) as $prod)
                                    <div class="d-flex align-items-center justify-content-between gap-2 p-2 rounded border bg-white">
                                        <div class="text-truncate" style="font-size: 0.78rem; font-weight: 600;">{{ $prod['name'] }}</div>
                                        <div class="text-end flex-shrink-0">
                                            <span class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $prod['formatted_price'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted small fst-italic py-1" style="font-size: 0.72rem;">Sin productos en catálogo aún.</div>
                        @endif
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <a href="{{ $c['store_url'] }}" class="btn text-white fw-bold rounded-pill w-100" style="background: var(--sl-primary, #7F1D3F); font-size: 0.78rem; padding: 7px;">
                            <i class="bi bi-shop me-1"></i> Ver Tienda
                        </a>
                        <a href="{{ $c['chat_url'] }}" class="btn btn-light border fw-bold rounded-pill px-3" style="font-size: 0.78rem; padding: 7px;" title="Chat con el vendedor">
                            <i class="bi bi-chat-dots" style="color: var(--sl-primary, #7F1D3F);"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-shop-window text-muted fs-1 mb-2"></i>
                <div class="fw-bold text-dark">No hay comercios disponibles en este momento.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- Tarjetas Informativas Inferiores (Sin mención de Ubaté y Sin la tarjeta de puntos) --}}
<div class="row g-4 mt-1">
    <div class="col-md-6">
        <div class="vs-card bg-white border-0 rounded-4 p-4 d-flex align-items-start gap-3 h-100 shadow-sm hover-lift">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); flex-shrink: 0;">
                <i class="bi bi-bicycle fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-2">Rutas de Entrega Activas</h6>
                <p class="text-muted mb-2" style="font-size: 0.75rem; line-height: 1.4;">Reparto express en moto y bici con cobertura para tus compras locales (30 a 50 minutos promedio).</p>
                <div class="fw-bold text-danger" style="font-size: 0.75rem;">Tarifa plana de despacho local: $4.500 COP</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="vs-card bg-white border-0 rounded-4 p-4 d-flex align-items-start gap-3 h-100 shadow-sm hover-lift">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10" style="color: var(--sl-primary, #7F1D3F); width: 48px; height: 48px; flex-shrink: 0;">
                <i class="bi bi-shield-check fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-2">Garantía y Denominación Local</h6>
                <p class="text-muted mb-2" style="font-size: 0.75rem; line-height: 1.4;">Productos auténticos con garantía directa de calidad, elaborados y distribuidos por comercios registrados.</p>
                <div class="fw-bold" style="font-size: 0.75rem; color: var(--sl-primary, #7F1D3F);">Comercio 100% Justo y Formal</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
let currentView = 'map';
let activeCategory = 'all';

function switchView(view) {
    currentView = view;
    const mapWrap = document.getElementById('viewMapWrapper');
    const listWrap = document.getElementById('viewListWrapper');
    const btnMap = document.getElementById('btnToggleMap');
    const btnList = document.getElementById('btnToggleList');

    if (view === 'map') {
        mapWrap.style.display = 'block';
        listWrap.style.display = 'none';
        btnMap.className = 'btn btn-sm rounded-pill px-3 text-white fw-bold shadow-sm';
        btnMap.style.background = 'var(--sl-primary, #7F1D3F)';
        btnList.className = 'btn btn-sm rounded-pill px-3 text-muted fw-bold border-0 bg-transparent hover-bg';
        btnList.style.background = 'transparent';
        if (window.spotlightMap) {
            setTimeout(() => window.spotlightMap.invalidateSize(), 200);
        }
    } else {
        mapWrap.style.display = 'none';
        listWrap.style.display = 'block';
        btnList.className = 'btn btn-sm rounded-pill px-3 text-white fw-bold shadow-sm';
        btnList.style.background = 'var(--sl-primary, #7F1D3F)';
        btnMap.className = 'btn btn-sm rounded-pill px-3 text-muted fw-bold border-0 bg-transparent hover-bg';
        btnMap.style.background = 'transparent';
    }
}

// Agregar al carrito vía AJAX con Toast
function addToCartAjax(event, productId, addUrl) {
    event.stopPropagation();
    event.preventDefault();

    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                   || '{{ csrf_token() }}';

    fetch(addUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al agregar al carrito');
        return response.json();
    })
    .then(data => {
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Listo';
        btn.classList.remove('btn-add-cart-mini');
        btn.classList.add('btn-success', 'text-white');
        
        showCartToast(data.message || 'Producto agregado al carrito');

        // Actualizar contador del carrito en el navbar si existe
        const cartBadges = document.querySelectorAll('.cart-counter, [data-cart-counter]');
        cartBadges.forEach(b => {
            b.textContent = data.cart_count;
            b.style.display = 'inline-block';
        });

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('btn-success', 'text-white');
            btn.classList.add('btn-add-cart-mini');
        }, 1500);
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = originalText;
        btn.disabled = false;
        showCartToast('No se pudo agregar al carrito');
    });
}

function showCartToast(message) {
    const toast = document.getElementById('cartToast');
    const msgEl = document.getElementById('cartToastMessage');
    if (!toast || !msgEl) return;
    msgEl.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

document.addEventListener('DOMContentLoaded', function() {
    const rawCompanies = @json($companies);
    const companies = Array.isArray(rawCompanies) ? rawCompanies : Object.values(rawCompanies);

    // Inicializar mapa
    const map = L.map('map', {
        zoomControl: false
    }).setView([5.3086, -73.8149], 14);

    window.spotlightMap = map;

    // Control de Zoom
    L.control.zoom({ position: 'bottomleft' }).addTo(map);

    // Capa de mapa base
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 20
    }).addTo(map);

    const markerGroup = L.featureGroup().addTo(map);
    const markersRegistry = [];

    // Función auxiliar para escapar texto
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Crear marcadores con la foto / logo de cada comercio
    companies.forEach(company => {
        if (!company.latitude || !company.longitude) return;

        const onlineClass = company.is_open ? 'is-online' : 'is-offline';
        const logoUrl = company.logo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(company.name)}&background=7F1D3F&color=fff`;

        const markerHtml = `
            <div class="company-marker-pin" id="marker-pin-${company.id}" title="${escapeHtml(company.name)}">
                <div class="company-marker-avatar">
                    <img src="${logoUrl}" alt="${escapeHtml(company.name)}" class="company-marker-img" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(company.name)}&background=7F1D3F&color=fff'">
                </div>
                <div class="company-marker-indicator ${onlineClass}" title="${company.is_open ? 'Abierto' : 'Cerrado'}"></div>
                <div class="company-marker-arrow"></div>
            </div>
        `;

        const customIcon = L.divIcon({
            className: 'custom-company-marker-wrapper',
            html: markerHtml,
            iconSize: [44, 52],
            iconAnchor: [22, 50],
            popupAnchor: [0, -48]
        });

        const marker = L.marker([company.latitude, company.longitude], {
            icon: customIcon
        });

        // 🌟 REQUERIMIENTO PRINCIPAL: Ver información con solo poner el cursor encima (mouseover)
        marker.on('mouseover', () => {
            highlightMarker(company.id);
            showCompanyDetails(company);
        });

        // También soporte para clic en móviles y centrado
        marker.on('click', () => {
            highlightMarker(company.id);
            showCompanyDetails(company);
            map.panTo([company.latitude, company.longitude]);
        });

        marker.addTo(markerGroup);

        markersRegistry.push({
            company: company,
            marker: marker
        });
    });

    if (markersRegistry.length > 0) {
        map.fitBounds(markerGroup.getBounds().pad(0.12));
    }

    const detailCard = document.getElementById('companyDetail');
    const closeBtn = document.getElementById('closeDetail');

    function highlightMarker(companyId) {
        document.querySelectorAll('.company-marker-pin').forEach(pin => {
            pin.classList.remove('is-active');
        });
        const activePin = document.getElementById(`marker-pin-${companyId}`);
        if (activePin) {
            activePin.classList.add('is-active');
        }
    }

    // Renderizar detalles de la empresa
    function showCompanyDetails(company) {
        document.getElementById('detName').textContent = company.name;
        document.getElementById('detCat').textContent = company.category || 'Comercio Local';
        document.getElementById('detAddr').textContent = company.address || 'Ubicación física';
        
        const logoImg = document.getElementById('detImg');
        logoImg.src = company.logo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(company.name)}&background=7F1D3F&color=fff`;
        logoImg.onerror = () => {
            logoImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(company.name)}&background=7F1D3F&color=fff`;
        };

        // Estado de Horario Dinámico (Abierto / Cerrado según horarios de la empresa)
        const statusBadgeContainer = document.getElementById('detStatusBadge');
        if (company.is_open) {
            statusBadgeContainer.innerHTML = `
                <span class="badge rounded-pill bg-success-subtle text-success fw-bold px-2 py-1" style="font-size: 0.72rem;">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Abierto
                </span>
                <div class="text-success fw-semibold mt-1" style="font-size: 0.68rem;">${escapeHtml(company.schedule_details)}</div>
            `;
        } else {
            statusBadgeContainer.innerHTML = `
                <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-2 py-1" style="font-size: 0.72rem;">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Cerrado
                </span>
                <div class="text-danger fw-semibold mt-1" style="font-size: 0.68rem;">${escapeHtml(company.schedule_details)}</div>
            `;
        }
        document.getElementById('detScheduleHours').textContent = `Horario: ${company.schedule_hours}`;

        // Calificación y Reseñas de los Usuarios
        const ratingContainer = document.getElementById('detRatingContainer');
        const reviewsCountEl = document.getElementById('detReviewsCount');
        if (company.reviews_count && company.reviews_count > 0) {
            const formattedRating = Number(company.rating).toFixed(1);
            ratingContainer.innerHTML = `<i class="bi bi-star-fill text-warning" style="font-size: 0.85rem;"></i> ${formattedRating}`;
            reviewsCountEl.textContent = `(${company.reviews_count} ${company.reviews_count === 1 ? 'reseña' : 'reseñas'})`;
        } else {
            ratingContainer.innerHTML = `<i class="bi bi-star text-muted" style="font-size: 0.85rem;"></i> 5.0`;
            reviewsCountEl.textContent = `(Sin reseñas aún)`;
        }

        // Renderizado de Productos de la empresa con botón "+ Carrito"
        const productsContainer = document.getElementById('detProductsContainer');
        const allProductsLink = document.getElementById('detAllProductsLink');
        allProductsLink.href = company.store_url;

        if (company.products && company.products.length > 0) {
            productsContainer.innerHTML = company.products.map(p => `
                <div class="product-mini-item">
                    <div class="product-mini-img-wrap">
                        <img src="${p.image_url}" class="w-100 h-100 object-fit-cover" alt="${escapeHtml(p.name)}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(p.name)}&background=f1f5f9'">
                    </div>
                    <div class="flex-fill min-w-0">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;" title="${escapeHtml(p.name)}">${escapeHtml(p.name)}</div>
                        <div class="text-muted" style="font-size: 0.65rem;">Stock: ${p.stock > 0 ? p.stock + ' unidades' : '<span class=\"text-danger\">Agotado</span>'}</div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold text-dark mb-1" style="font-size: 0.78rem;">${p.formatted_price}</div>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold btn-add-cart-mini" onclick="addToCartAjax(event, ${p.id}, '${p.add_cart_url}')" ${p.stock <= 0 ? 'disabled' : ''}>
                            <i class="bi bi-cart-plus me-1"></i>+ Carrito
                        </button>
                    </div>
                </div>
            `).join('');
        } else {
            productsContainer.innerHTML = `
                <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">
                    <i class="bi bi-box-seam fs-4 d-block mb-1 opacity-50"></i>
                    No hay productos disponibles en este momento.
                </div>
            `;
        }

        // Enlaces de la tienda
        document.getElementById('detStore').href = company.store_url;
        document.getElementById('detChat').href = company.chat_url;
        document.getElementById('detRoute').href = `https://www.google.com/maps/dir/?api=1&destination=${company.latitude},${company.longitude}`;

        detailCard.classList.add('active');
    }

    closeBtn.addEventListener('click', () => {
        detailCard.classList.remove('active');
        document.querySelectorAll('.company-marker-pin').forEach(p => p.classList.remove('is-active'));
    });

    // Filtros por Categoría Disponibles
    window.filterByCategory = function(category) {
        activeCategory = category;

        document.querySelectorAll('.cat-filter-btn').forEach(btn => {
            if (btn.getAttribute('data-category') === category) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        applyFilters();
    };

    // Buscador en Vivo en el Mapa
    const searchInput = document.getElementById('mapSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    searchInput.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }
        applyFilters();
    });

    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearBtn.style.display = 'none';
        applyFilters();
    });

    function applyFilters() {
        const query = (searchInput.value || '').toLowerCase().trim();
        const visibleMarkers = [];

        markersRegistry.forEach(item => {
            const matchesCategory = (activeCategory === 'all' || item.company.category === activeCategory);
            
            // Buscar por nombre de comercio o producto
            let matchesQuery = true;
            if (query.length > 0) {
                const nameMatch = item.company.name.toLowerCase().includes(query);
                const descMatch = (item.company.description || '').toLowerCase().includes(query);
                const prodMatch = (item.company.products || []).some(p => p.name.toLowerCase().includes(query));
                matchesQuery = nameMatch || descMatch || prodMatch;
            }

            if (matchesCategory && matchesQuery) {
                if (!map.hasLayer(item.marker)) {
                    item.marker.addTo(map);
                }
                visibleMarkers.push(item.marker);
            } else {
                if (map.hasLayer(item.marker)) {
                    map.removeLayer(item.marker);
                }
            }
        });

        if (visibleMarkers.length > 0) {
            const tempGroup = L.featureGroup(visibleMarkers);
            map.fitBounds(tempGroup.getBounds().pad(0.15));
        }
    }

    setTimeout(() => map.invalidateSize(), 400);
});
</script>
@endpush
