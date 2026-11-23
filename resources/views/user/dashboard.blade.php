@extends('layouts.dashboard')
@section('title', 'Mercado de Tiendas - Spotlight')

@section('dashboard-content')
@php
    $activeCategory = request('category');
    $activeCompanyId = request('company_id');
    $activeCompany = $activeCompanyId ? $companies->firstWhere('id', $activeCompanyId) : null;
    $activeQuery = request('q');
    $activeSort = request('sort', 'latest');
    $hasActiveFilters = !empty($activeQuery) || !empty($activeCategory) || !empty($activeCompanyId) || !empty($market) || ($activeSort !== 'latest');

    // Mapeo temático para animación de categorías
    $themeAccent = $activeTag?->accent_color ?? '#7F1D3F';
    $tagColorMap = isset($allTags) ? $allTags->pluck('accent_color', 'label')->toArray() : [];
@endphp

<div class="consumer-split-wrapper">
    {{-- =======================================================================
         COLUMNA IZQUIERDA: VITRINA DE PRODUCTOS (3 COLUMNAS SEGÚN WIREFRAME)
         ======================================================================= --}}
    <div class="consumer-products-canvas">
        
        {{-- Cabecera compacta y original del lienzo de productos --}}
        <div class="consumer-canvas-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="consumer-section-kicker">
                        <i class="bi bi-compass-fill me-1"></i> VITRINA LOCAL
                    </span>
                    @if($market === 'CO')
                        <span class="badge rounded-pill consumer-market-tag">🇨🇴 Mercado Colombia</span>
                    @elseif($market === 'MX')
                        <span class="badge rounded-pill consumer-market-tag">🇲🇽 Mercado México</span>
                    @endif
                </div>
                <h3 class="consumer-canvas-title mb-0 mt-1">
                    @if($activeCompany)
                        {{ $activeCompany->name }}
                    @elseif($activeCategory)
                        {{ $activeCategory }}
                    @elseif($activeQuery)
                        Resultados para “{{ $activeQuery }}”
                    @else
                        Productos Destacados
                    @endif
                </h3>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="consumer-count-pill">
                    <strong>{{ $products->total() }}</strong> {{ $products->total() === 1 ? 'producto' : 'productos' }}
                </span>

                {{-- Botones de vista (Cuadrícula 3 col vs Lista) --}}
                <div class="consumer-view-switcher d-flex align-items-center p-1 rounded-pill">
                    <button type="button" id="btnViewGrid" onclick="setProductView('grid')" class="view-btn active-view-btn rounded-circle d-flex align-items-center justify-content-center" title="Vista de 3 columnas">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </button>
                    <button type="button" id="btnViewList" onclick="setProductView('list')" class="view-btn rounded-circle d-flex align-items-center justify-content-center" title="Vista detallada en lista">
                        <i class="bi bi-view-stacked"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Chips de filtros activos (si el usuario filtró algo desde la columna derecha) --}}
        @if($hasActiveFilters)
        <div class="consumer-active-filters d-flex align-items-center flex-wrap gap-2 mb-4">
            <span class="text-xs fw-bold text-uppercase consumer-filter-label">
                <i class="bi bi-sliders2 me-1"></i> Activos:
            </span>

            @if($activeQuery)
                <a href="{{ route('user.dashboard', request()->except(['q', 'page'])) }}" class="consumer-active-chip text-decoration-none">
                    <span>Búsqueda: “{{ $activeQuery }}”</span>
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif

            @if($activeCategory)
                <a href="{{ route('user.dashboard', request()->except(['category', 'page'])) }}" class="consumer-active-chip text-decoration-none">
                    <span>Categoría: {{ $activeCategory }}</span>
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif

            @if($activeCompany)
                <a href="{{ route('user.dashboard', request()->except(['company_id', 'page'])) }}" class="consumer-active-chip text-decoration-none">
                    <span>Comercio: {{ $activeCompany->name }}</span>
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif

            @if($market)
                <a href="{{ route('user.dashboard', request()->except(['country', 'page'])) }}" class="consumer-active-chip text-decoration-none">
                    <span>País: {{ $market === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }}</span>
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif

            <a href="{{ route('user.dashboard') }}" class="consumer-clear-all ms-auto text-decoration-none">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer todo
            </a>
        </div>
        @endif

        {{-- Banner Temático Dinámico con Animación de Entrada --}}
        @if($activeCategory)
        <div class="category-theme-banner mb-4 p-3 rounded-4 text-white shadow-sm d-flex align-items-center justify-content-between"
             style="background: linear-gradient(135deg, {{ $themeAccent }} 0%, color-mix(in srgb, {{ $themeAccent }} 65%, #1f1d24) 100%); border-left: 6px solid #ffffff;">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="rounded-circle p-2.5 bg-white bg-opacity-20 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi {{ $activeTag?->icon ?: 'bi-tag-fill' }} text-white"></i>
                </div>
                <div class="min-w-0">
                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2.5 py-0.5 text-2xs fw-bold text-uppercase mb-1 d-inline-block">
                        Categoría Seleccionada
                    </span>
                    <h5 class="fw-bold mb-0 text-white text-truncate">{{ $activeTag?->label ?: $activeCategory }}</h5>
                    <small class="text-white text-opacity-80 text-xs">
                        Vitrina temática con productos filtrados para esta sección
                    </small>
                </div>
            </div>
            <a href="{{ route('user.dashboard', request()->except(['category', 'page'])) }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 fw-bold text-xs shadow-xs text-nowrap flex-shrink-0 ms-2" title="Quitar filtro">
                <i class="bi bi-x-circle me-1"></i> Ver todas
            </a>
        </div>
        @endif

        {{-- Cuadrícula de Productos (3 por fila en escritorio como en el boceto) --}}
        <div id="productsContainer" class="row g-4 view-mode-grid" style="--theme-accent: {{ $themeAccent }};">
            @forelse($products as $product)
                @php
                    $tagText = 'ARTESANAL / LOCAL';
                    $tagStyle = 'tag-local';
                    if ($product->id % 4 == 0) {
                        $tagText = '🔥 TOP VENTAS';
                        $tagStyle = 'tag-hot';
                    } elseif ($product->id % 3 == 0) {
                        $tagText = '✨ NUEVO';
                        $tagStyle = 'tag-new';
                    } elseif ($product->id % 5 == 0) {
                        $tagText = '⚡ ENVÍO HOY';
                        $tagStyle = 'tag-fast';
                    }
                @endphp

                <div class="product-item-col col-12 col-sm-6 col-xl-4 category-animated-card" style="--item-index: {{ $loop->index }};">
                    <article class="product-card editorial-product-card h-100 d-flex flex-column">
                        {{-- Lienzo de Imagen con marco cálido --}}
                        <div class="product-image-container position-relative">
                            <div class="editorial-img-frame position-relative overflow-hidden">
                                <a href="{{ route('public.product', $product) }}" class="d-block text-center product-img-link">
                                    @if($product->hasCustomImage())
                                        <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}">
                                    @else
                                        <div class="editorial-placeholder-showcase">
                                            <div class="editorial-placeholder-halo">
                                                <div class="editorial-placeholder-medallion">
                                                    <i class="bi {{ $product->placeholder_icon }}"></i>
                                                </div>
                                            </div>
                                            <span class="editorial-placeholder-caption">
                                                <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="" class="editorial-placeholder-mini-logo">
                                                <span>Spotlight</span>
                                            </span>
                                        </div>
                                    @endif
                                </a>

                                {{-- Etiquetas flotantes superiores --}}
                                <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1 z-2">
                                    @if($product->primary_tag)
                                        <span class="badge rounded-pill editorial-badge shadow-xs" style="background-color: {{ $product->primary_tag->accent_color }}; color: #ffffff;">
                                            <i class="bi {{ $product->primary_tag->icon ?: 'bi-tag-fill' }} me-1"></i> {{ $product->primary_tag->label }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill editorial-badge {{ $tagStyle }}">
                                            {{ $tagText }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Bandera de origen y stock --}}
                                <div class="position-absolute top-0 end-0 m-2 d-flex align-items-center gap-1 z-2">
                                    @if($product->isMexican() || ($product->company && $product->company->isMexican()))
                                        <span class="editorial-origin-pill" title="Comercio de México">🇲🇽 MX</span>
                                    @else
                                        <span class="editorial-origin-pill" title="Comercio de Colombia">🇨🇴 CO</span>
                                    @endif
                                </div>

                                {{-- Píldora flotante del comercio sobre la esquina inferior de la imagen --}}
                                <div class="position-absolute bottom-0 start-0 end-0 p-2 d-flex justify-content-between align-items-end z-2 editorial-img-footer">
                                    <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['company_id' => $product->company_id])) }}" class="editorial-store-chip text-decoration-none text-truncate" title="Filtrar productos de {{ $product->company->name }}">
                                        <i class="bi bi-shop-window me-1"></i>
                                        <span class="text-truncate">{{ $product->company->name }}</span>
                                        <i class="bi bi-patch-check-fill ms-1 verified-icon"></i>
                                    </a>

                                    <span class="editorial-stock-chip {{ $product->stock <= 5 ? 'low-stock' : '' }}">
                                        {{ $product->stock > 0 ? $product->stock . ' disp.' : 'Agotado' }}
                                    </span>
                                </div>

                                @if($product->stock <= 0)
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center editorial-soldout-overlay z-3">
                                        <span class="badge bg-danger px-3 py-2 rounded-pill shadow">Agotado</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Cuerpo Editorial del Producto --}}
                        <div class="product-card-body d-flex flex-column flex-fill">
                            <div class="product-info-section">
                                {{-- Categoría + Calificación --}}
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="editorial-cat-label text-truncate" style="{{ $product->primary_tag ? 'color: ' . $product->primary_tag->accent_color . ' !important; font-weight: 700;' : '' }}">
                                        @if($product->primary_tag)
                                            <i class="bi {{ $product->primary_tag->icon ?: 'bi-tag-fill' }} me-1"></i>{{ $product->primary_tag->label }}
                                        @else
                                            {{ $product->company?->category ?: 'General' }}
                                        @endif
                                    </span>
                                    <a href="{{ route('public.product', $product) }}#reviews-section" class="editorial-rating-pill text-decoration-none" title="Ver reseñas">
                                        <i class="bi bi-star-fill"></i>
                                        <span>{{ $product->average_rating > 0 ? number_format($product->average_rating, 1) : '5.0' }}</span>
                                        <small>({{ $product->reviews_count }})</small>
                                    </a>
                                </div>

                                {{-- Nombre del Producto --}}
                                <h5 class="product-title mb-1">
                                    <a href="{{ route('public.product', $product) }}" class="text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h5>

                                {{-- Descripción breve --}}
                                <p class="product-desc mb-3">
                                    {{ $product->description ?? 'Producto elaborado y distribuido por comercios locales verificados en Spotlight.' }}
                                </p>
                            </div>

                            {{-- Barra inferior de Precio + Acción de Compra --}}
                            <div class="product-action-section mt-auto pt-3">
                                <div class="d-flex align-items-baseline justify-content-between mb-2">
                                    <div>
                                        <span class="editorial-price-label d-block">Precio unitario</span>
                                        @if($product->effective_currency === 'MXN')
                                            <div class="product-price-tag"
                                                 data-cop="${{ number_format($product->price_in_cop, 0, ',', '.') }}"
                                                 data-mxn="${{ number_format($product->price, 2, ',', '.') }}"
                                                 data-mode="COP">
                                                <span class="js-card-price-val">${{ number_format($product->price_in_cop, 0, ',', '.') }}</span>
                                                <small class="js-card-price-cur">COP</small>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-sm p-0 border-0 bg-transparent text-start d-inline-flex align-items-center gap-1 mt-0.5"
                                                    style="font-size: 0.7rem; color: #7F1D3F; font-weight: 600;"
                                                    onclick="const tag = this.previousElementSibling; const isCop = tag.dataset.mode === 'COP'; tag.dataset.mode = isCop ? 'MXN' : 'COP'; tag.querySelector('.js-card-price-val').textContent = isCop ? tag.dataset.mxn : tag.dataset.cop; tag.querySelector('.js-card-price-cur').textContent = isCop ? 'MXN' : 'COP'; this.querySelector('.js-alt-currency-label').textContent = isCop ? ('🇨🇴 ≈ ' + tag.dataset.cop + ' COP') : ('🇲🇽 ' + tag.dataset.mxn + ' MXN');"
                                                    title="Haz clic para alternar entre Pesos Colombianos (COP) y Pesos Mexicanos (MXN)">
                                                <i class="bi bi-currency-exchange"></i>
                                                <span class="js-alt-currency-label">🇲🇽 ${{ number_format($product->price, 2, ',', '.') }} MXN</span>
                                            </button>
                                        @else
                                            <div class="product-price-tag">
                                                ${{ number_format($product->price, (floor($product->price) != $product->price ? 2 : 0), ',', '.') }}
                                                <small>COP</small>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="editorial-city-tag">
                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $product->company->city ?? 'Ubaté' }}
                                    </span>
                                </div>

                                <div class="product-buttons-wrap">
                                    @if($product->stock > 0)
                                        <form action="{{ route('user.cart.add', $product) }}" method="POST" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            <div class="editorial-qty-stepper d-flex align-items-center">
                                                <button type="button" class="qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" aria-label="Disminuir cantidad">−</button>
                                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="qty-input" readonly>
                                                <button type="button" class="qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" aria-label="Aumentar cantidad">+</button>
                                            </div>
                                            <button type="submit" class="btn btn-burgundy-add flex-fill rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2">
                                                <i class="bi bi-bag-plus-fill"></i>
                                                <span>Añadir</span>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-light w-100 text-muted fw-bold rounded-pill border" disabled>
                                            <i class="bi bi-slash-circle me-1"></i> Sin inventario
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="consumer-empty-state text-center py-5 px-4">
                        <div class="consumer-empty-icon mx-auto mb-3">
                            <i class="bi bi-basket2"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">No encontramos productos con esos criterios</h4>
                        <p class="text-muted mb-4 mx-auto consumer-empty-desc">
                            Prueba buscando con otra palabra clave o explora todas las categorías y comercios desde el panel derecho.
                        </p>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-burgundy-add rounded-pill px-4 py-2 fw-bold">
                            <i class="bi bi-arrow-repeat me-1"></i> Ver todo el catálogo
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="mt-5 d-flex justify-content-center pagination-spotlight">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- =======================================================================
         COLUMNA DERECHA: PANEL CON LAS 3 CÁPSULAS OVALADAS DEL WIREFRAME
         (1. Buscar | 2. Categoría | 3. Comercios)
         ======================================================================= --}}
    <aside class="consumer-sidebar-dock" aria-label="Controles de exploración del mercado">
        <div class="consumer-dock-sticky">
            
            <div class="consumer-dock-header mb-3">
                <span class="consumer-dock-subtitle">PANEL DE EXPLORACIÓN</span>
                <h5 class="consumer-dock-title mb-0">Filtra a tu medida</h5>
            </div>

            <form action="{{ route('user.dashboard') }}" method="GET" id="consumerDockForm" class="d-flex flex-column gap-3">
                @if(!empty($market))
                    <input type="hidden" name="country" value="{{ $market }}">
                @endif

                {{-- ===========================================================
                     OVAL 1: BUSCAR (Menú desplegable + estático)
                     =========================================================== --}}
                <div class="wireframe-oval-group">
                    <label class="wireframe-oval-caption">
                        <span>01 · Búsqueda directa</span>
                        @if($activeQuery)
                            <a href="{{ route('user.dashboard', request()->except(['q', 'page'])) }}" class="wireframe-caption-reset">Borrar</a>
                        @endif
                    </label>
                    <div class="dropdown w-100">
                        <button class="wireframe-oval-pill pill-buscar text-start w-100 d-flex align-items-center justify-content-between {{ $activeQuery ? 'is-filled' : '' }}" 
                                type="button" 
                                id="dropdownSearchBtn" 
                                data-bs-toggle="dropdown" 
                                data-bs-auto-close="outside" 
                                aria-expanded="false">
                            <div class="d-flex align-items-center gap-2 overflow-hidden flex-fill me-2">
                                <i class="bi bi-search wireframe-pill-icon m-0"></i>
                                <span class="text-truncate fw-bold wireframe-pill-text-label">
                                    {{ $activeQuery ? $activeQuery : 'Buscar...' }}
                                </span>
                            </div>
                            <i class="bi bi-chevron-down wireframe-pill-chevron position-static"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-sl rounded-4 p-3 mt-2 w-100 wireframe-dropdown-panel" aria-labelledby="dropdownSearchBtn">
                            <div class="d-flex align-items-center gap-2">
                                <input
                                    type="text"
                                    id="dockSearchInput"
                                    name="q"
                                    class="form-control rounded-pill border-sl px-3 py-2 fw-semibold"
                                    placeholder="Buscar producto..."
                                    value="{{ $activeQuery }}"
                                    autocomplete="off"
                                >
                                <button type="submit" class="wireframe-pill-action m-0" title="Buscar ahora">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                            @if($activeQuery)
                                <div class="mt-2 text-end">
                                    <a href="{{ route('user.dashboard', request()->except(['q', 'page'])) }}" class="small text-danger fw-bold text-decoration-none">
                                        <i class="bi bi-x-circle me-1"></i> Limpiar búsqueda
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ===========================================================
                     OVAL 2: CATEGORÍA (Píldora ovalada + selector rápido)
                     =========================================================== --}}
                <div class="wireframe-oval-group">
                    <label for="dockCategorySelect" class="wireframe-oval-caption">
                        <span>02 · Categoría</span>
                        <small>{{ $categories->count() }} disponibles</small>
                    </label>
                    <div class="wireframe-oval-pill pill-categoria {{ $activeCategory ? 'is-filled' : '' }}">
                        <i class="bi bi-tag-fill wireframe-pill-icon"></i>
                        <select
                            id="dockCategorySelect"
                            name="category"
                            class="wireframe-pill-select"
                            onchange="document.getElementById('consumerDockForm').submit()"
                        >
                            <option value="">Categoría (Todas)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $activeCategory === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down wireframe-pill-chevron"></i>
                    </div>

                    {{-- Chips táctiles de categorías debajo de la píldora Categoría --}}
                    @if($categories->count() > 0)
                        <div class="dock-quick-chips mt-2">
                            <a href="{{ route('user.dashboard', request()->except(['category', 'page'])) }}"
                               class="dock-chip {{ empty($activeCategory) ? 'active' : '' }}">
                                Todas
                            </a>
                            @foreach($categories->take(7) as $cat)
                                @php
                                    $chipAccent = $tagColorMap[$cat] ?? '#7F1D3F';
                                @endphp
                                <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['category' => $cat])) }}"
                                   class="dock-chip themed-chip {{ $activeCategory === $cat ? 'active' : '' }}"
                                   style="--chip-accent: {{ $chipAccent }};">
                                    {{ $cat }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ===========================================================
                     OVAL 3: COMERCIOS (Píldora ovalada + directorio interactivo)
                     =========================================================== --}}
                <div class="wireframe-oval-group">
                    <label for="dockCompanySelect" class="wireframe-oval-caption">
                        <span>03 · Comercios</span>
                        <small>{{ $companies->count() }} tiendas</small>
                    </label>
                    <div class="wireframe-oval-pill pill-comercios {{ $activeCompanyId ? 'is-filled' : '' }}">
                        <i class="bi bi-shop wireframe-pill-icon"></i>
                        <select
                            id="dockCompanySelect"
                            name="company_id"
                            class="wireframe-pill-select"
                            onchange="document.getElementById('consumerDockForm').submit()"
                        >
                            <option value="">Comercios (Todos)</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ (string)$activeCompanyId === (string)$company->id ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->products_count ?? 0 }})
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down wireframe-pill-chevron"></i>
                    </div>

                    {{-- Mini lista interactiva de comercios locales para explorar con 1 clic --}}
                    <div class="dock-stores-list mt-2">
                        @foreach($companies->take(3) as $store)
                            @php
                                $isStoreSelected = (string)$activeCompanyId === (string)$store->id;
                            @endphp
                            <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['company_id' => $isStoreSelected ? null : $store->id])) }}"
                               class="dock-store-item text-decoration-none {{ $isStoreSelected ? 'active' : '' }}">
                                <div class="dock-store-avatar">
                                    {{ strtoupper(substr($store->name, 0, 1)) }}
                                </div>
                                <div class="flex-fill text-truncate">
                                    <div class="dock-store-name text-truncate">{{ $store->name }}</div>
                                    <div class="dock-store-meta text-truncate">
                                        {{ $store->city ?? 'Ubaté' }} · {{ $store->products_count ?? 0 }} prod.
                                    </div>
                                </div>
                                <i class="bi {{ $isStoreSelected ? 'bi-check-circle-fill' : 'bi-arrow-up-right' }} dock-store-arrow"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- ===========================================================
                     CONTROLES COMPLEMENTARIOS DEL PANEL DERECHO (ORDEN Y PAÍS)
                     =========================================================== --}}
                <div class="consumer-dock-divider my-1"></div>

                {{-- Ordenamiento funcional --}}
                <div class="wireframe-oval-group">
                    <label for="dockSortSelect" class="wireframe-oval-caption">
                        <span>Ordenar vitrina</span>
                    </label>
                    <div class="wireframe-oval-pill pill-secondary">
                        <i class="bi bi-sort-down wireframe-pill-icon"></i>
                        <select
                            id="dockSortSelect"
                            name="sort"
                            class="wireframe-pill-select"
                            onchange="document.getElementById('consumerDockForm').submit()"
                        >
                            <option value="latest" {{ $activeSort === 'latest' ? 'selected' : '' }}>Más recientes primero</option>
                            <option value="price_asc" {{ $activeSort === 'price_asc' ? 'selected' : '' }}>Menor a mayor precio</option>
                            <option value="price_desc" {{ $activeSort === 'price_desc' ? 'selected' : '' }}>Mayor a menor precio</option>
                            <option value="name_asc" {{ $activeSort === 'name_asc' ? 'selected' : '' }}>Nombre (A - Z)</option>
                        </select>
                        <i class="bi bi-chevron-down wireframe-pill-chevron"></i>
                    </div>
                </div>

                {{-- Selector de Mercado (Todos / Colombia / México) --}}
                <div class="wireframe-oval-group">
                    <span class="wireframe-oval-caption d-block mb-1">
                        <span>Mercado regional</span>
                    </span>
                    <div class="dock-market-grid">
                        <a href="{{ route('user.dashboard', request()->except(['country', 'page'])) }}"
                           class="dock-market-btn {{ empty($market) ? 'active' : '' }}">
                            🌐 Todos
                        </a>
                        <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['country' => 'CO'])) }}"
                           class="dock-market-btn {{ $market === 'CO' ? 'active' : '' }}">
                            🇨🇴 CO
                        </a>
                        <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['country' => 'MX'])) }}"
                           class="dock-market-btn {{ $market === 'MX' ? 'active' : '' }}">
                            🇲🇽 MX
                        </a>
                    </div>
                </div>

                {{-- Acceso directo al Mapa Geo-Comercial --}}
                <a href="{{ route('user.map') }}" class="dock-map-banner text-decoration-none mt-1">
                    <div class="dock-map-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="fw-bold text-dark dock-map-title">Explorar en el Mapa</div>
                        <div class="text-muted dock-map-sub">Ubica comercios cerca de ti</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </form>
        </div>
    </aside>
</div>

@push('styles')
<link href="{{ asset('css/user.css') }}?v=2.5" rel="stylesheet">
@endpush

@push('scripts')
<script>
window.setProductView = function (mode) {
    const btnGrid = document.getElementById('btnViewGrid');
    const btnList = document.getElementById('btnViewList');
    const container = document.getElementById('productsContainer');
    const productCols = document.querySelectorAll('.product-item-col');

    if (!container) return;

    if (mode === 'list') {
        container.classList.remove('view-mode-grid');
        container.classList.add('view-mode-list');
        if (btnList) btnList.classList.add('active-view-btn');
        if (btnGrid) btnGrid.classList.remove('active-view-btn');
        productCols.forEach(col => {
            col.classList.remove('col-sm-6', 'col-xl-4');
            col.classList.add('col-12');
        });
        localStorage.setItem('spotlight_product_view', 'list');
    } else {
        container.classList.remove('view-mode-list');
        container.classList.add('view-mode-grid');
        if (btnGrid) btnGrid.classList.add('active-view-btn');
        if (btnList) btnList.classList.remove('active-view-btn');
        productCols.forEach(col => {
            col.classList.add('col-sm-6', 'col-xl-4');
            col.classList.remove('col-12');
        });
        localStorage.setItem('spotlight_product_view', 'grid');
    }
};

function initProductView() {
    const savedView = localStorage.getItem('spotlight_product_view') || 'grid';
    if (savedView === 'list') {
        window.setProductView('list');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductView);
} else {
    initProductView();
}
</script>
@endpush
@endsection
