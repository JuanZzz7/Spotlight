@extends('layouts.dashboard')
@section('title','Mercado de Tiendas - Spotlight')

@section('dashboard-content')
{{-- Area de Filtros y Búsqueda Compacta --}}
<div class="market-hero-search p-3 p-md-3 mb-4 rounded-4 position-relative overflow-hidden shadow-xs" style="background: linear-gradient(135deg, #ffffff 0%, #FFFCF7 60%, #F6E7EB 100%); border: 1px solid #D98C9A;">
    <div class="position-relative z-1">
        <!-- Formulario de Búsqueda y Filtros Compacto -->
        <form action="{{ route('user.dashboard') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-lg-5 col-md-6">
                <label class="form-label text-xs mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px; font-size: 0.7rem; color: #7F1D3F;">
                    <i class="bi bi-search me-1"></i> BUSCAR PRODUCTO
                </label>
                <div class="input-group shadow-xs rounded-pill overflow-hidden border" style="border-color: #D98C9A !important;">
                    <span class="input-group-text bg-white border-0 text-muted ps-3 py-1">
                        <i class="bi bi-search" style="color: #7F1D3F;"></i>
                    </span>
                    <input type="text" name="q" class="form-control bg-white border-0 ps-2 py-1 fw-medium" style="box-shadow: none; font-size: 0.88rem; height: 38px;" placeholder="¿Qué deseas buscar? Ej: Café, Queso, Ruana..." value="{{ request('q') }}">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label text-xs mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px; font-size: 0.7rem; color: #7F1D3F;">
                    <i class="bi bi-tag-fill me-1"></i> CATEGORÍA
                </label>
                <div class="shadow-xs rounded-pill overflow-hidden border" style="border-color: #D98C9A !important;">
                    <select name="category" class="form-select bg-white border-0 py-1 fw-medium" style="box-shadow: none; font-size: 0.88rem; height: 38px; padding-left: 14px;">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category')===$cat?'selected':'' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-md-8">
                <label class="form-label text-xs mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px; font-size: 0.7rem; color: #7F1D3F;">
                    <i class="bi bi-shop me-1"></i> COMERCIO O TIENDA
                </label>
                <div class="shadow-xs rounded-pill overflow-hidden border" style="border-color: #D98C9A !important;">
                    <select name="company_id" class="form-select bg-white border-0 py-1 fw-medium" style="box-shadow: none; font-size: 0.88rem; height: 38px; padding-left: 14px;">
                        <option value="">Todos los comercios</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id')==$company->id?'selected':'' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-1 col-md-4 d-flex">
                <button type="submit" class="btn text-white w-100 rounded-pill d-flex align-items-center justify-content-center shadow-xs btn-filter-spotlight" style="background: linear-gradient(135deg, #7F1D3F 0%, #5E122C 100%); height: 38px; border: none; font-weight: 700; transition: transform 0.2s, box-shadow 0.2s;" title="Filtrar">
                    <i class="bi bi-funnel-fill me-md-0 me-1"></i>
                    <span class="d-inline d-lg-none ms-1">Filtrar</span>
                </button>
            </div>
        </form>

        <!-- Selector Multinacional de Mercados (Colombia y México) -->
        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom flex-wrap" style="border-color: rgba(217, 140, 154, 0.25) !important;">
            <span class="text-uppercase fw-bold me-1" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-globe2 me-1"></i> MERCADO:
            </span>
            <a href="{{ route('user.dashboard', array_merge(request()->except('country', 'page'), [])) }}" class="badge rounded-pill px-3 py-2 text-decoration-none shadow-xs {{ empty($market) ? 'bg-dark text-white' : 'bg-white text-dark border' }}" style="font-size: 0.76rem;">
                🌐 Todos los mercados
            </a>
            <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['country' => 'CO'])) }}" class="badge rounded-pill px-3 py-2 text-decoration-none shadow-xs {{ $market === 'CO' ? 'bg-dark text-white' : 'bg-white text-dark border' }}" style="font-size: 0.76rem;">
                🇨🇴 Colombia (Ubaté y Nacional)
            </a>
            <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['country' => 'MX'])) }}" class="badge rounded-pill px-3 py-2 text-decoration-none shadow-xs {{ $market === 'MX' ? 'bg-dark text-white' : 'bg-white text-dark border' }}" style="font-size: 0.76rem;">
                🇲🇽 México
            </a>
        </div>

        <!-- Filtros Rápidos Compactos -->
        <div class="d-flex align-items-center gap-2 mt-2 pt-1 border-top flex-wrap" style="border-color: rgba(217, 140, 154, 0.25) !important;">
            <span class="text-uppercase fw-bold me-1" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-stars me-1"></i> CATEGORÍAS:
            </span>
            @foreach($categories->take(5) as $cat)
                <a href="{{ route('user.dashboard', array_merge(request()->except('page'), ['category' => $cat])) }}" class="badge rounded-pill px-2 py-1 text-decoration-none shadow-xs category-quick-pill {{ request('category') === $cat ? 'active-pill' : '' }}" style="font-size: 0.72rem;">
                    {{ $cat }}
                </a>
            @endforeach
            @if(request()->hasAny(['q', 'category', 'company_id', 'country']))
                <a href="{{ route('user.dashboard') }}" class="badge rounded-pill px-2 py-1 text-decoration-none bg-white text-danger border shadow-xs ms-auto" style="border-color: #fca5a5 !important; font-size: 0.72rem;">
                    <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Encabezado del Catálogo y Controles de Vista --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div class="d-flex align-items-baseline gap-2">
        <h4 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px; font-family: 'Outfit', sans-serif;">Catálogo de Productos</h4>
        <span class="text-muted d-none d-sm-inline" style="font-size: 0.82rem;">
            ({{ $products->total() }} productos disponibles en {{ $companies->count() }} comercios)
        </span>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <!-- Selector de Orden -->
        <div class="d-flex align-items-center gap-1">
            <span class="text-muted fw-semibold d-none d-sm-inline" style="font-size: 0.8rem;">Ordenar:</span>
            <select class="form-select form-select-sm border rounded-pill bg-white fw-bold text-dark shadow-xs px-3 py-1" style="width: auto; font-size: 0.8rem; border-color: #e2e8f0;">
                <option>Más populares</option>
                <option>Menor Precio</option>
                <option>Mayor Precio</option>
                <option>Recientes</option>
            </select>
        </div>

        <!-- Botones de Cambio de Vista (Bloque vs Lista) FUNCIONAL -->
        <div class="d-flex gap-1 bg-white rounded-pill p-1 shadow-xs border" style="border-color: #D98C9A;">
            <button type="button" id="btnViewGrid" onclick="setProductView('grid')" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center view-btn active-view-btn" style="width: 32px; height: 32px;" title="Ver en bloque (cuadrícula)">
                <i class="bi bi-grid-fill"></i>
            </button>
            <button type="button" id="btnViewList" onclick="setProductView('list')" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center view-btn" style="width: 32px; height: 32px;" title="Ver en lista">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
    </div>
</div>

{{-- Contenedor de Productos (Soporta Cuadrícula y Lista) --}}
<div id="productsContainer" class="row g-4 view-mode-grid">
@forelse($products as $product)
    @php
        $tagClass = ''; $tagText = ''; $tagIcon = '';
        if($product->id % 4 == 0) { $tagClass = 'bg-danger text-white'; $tagText = 'TOP VENTAS'; }
        elseif($product->id % 3 == 0) { $tagClass = 'bg-primary text-white'; $tagText = 'NUEVO'; }
        elseif($product->id % 5 == 0) { $tagClass = 'text-dark'; $tagText = 'ENVÍO HOY'; $tagIcon = '<i class="bi bi-lightning-fill text-warning me-1"></i>'; }
        else { $tagClass = 'text-dark'; $tagText = 'LOCAL'; }
    @endphp

    <div class="product-item-col col-sm-6 col-lg-4 col-xl-3">
        <div class="product-card vs-card h-100 overflow-hidden d-flex flex-column border-0 bg-white rounded-4 shadow-sm hover-lift" style="border: 1px solid #f1f5f9;">
            <!-- Contenedor de Imagen -->
            <div class="product-image-container position-relative" style="padding: 10px;">
                <div class="overflow-hidden position-relative rounded-4" style="background: #f8fafc;">
                    <a href="{{ route('public.product', $product) }}" class="d-block text-center">
                        <img src="{{ $product->image_url }}" class="w-100 product-img" style="height: 200px; object-fit: contain; padding: 1rem; mix-blend-mode: multiply;" alt="{{ $product->name }}">
                    </a>
                    
                    <!-- Badges superiores -->
                    <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1">
                        @if($product->isMexican() || ($product->company && $product->company->isMexican()))
                            <span class="badge shadow-xs rounded-pill px-2 py-1 fw-bold" style="background: #006847; color: #fff; font-size: 0.65rem;">
                                🇲🇽 México
                            </span>
                        @else
                            <span class="badge shadow-xs rounded-pill px-2 py-1 fw-bold" style="background: #003893; color: #fff; font-size: 0.65rem;">
                                🇨🇴 Colombia
                            </span>
                        @endif

                        @if($tagClass == 'text-dark')
                            <span class="badge shadow-xs rounded-pill px-2 py-1 fw-bold d-flex align-items-center" style="background: #F6E7EB; color: #7F1D3F; font-size: 0.65rem;">
                                {!! $tagIcon !!} {{ $tagText }}
                            </span>
                        @else
                            <span class="badge {{ $tagClass }} shadow-xs rounded-pill px-2 py-1 fw-bold" style="font-size: 0.65rem;">{{ $tagText }}</span>
                        @endif
                    </div>
                    
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-white text-muted border shadow-xs rounded-pill px-2 py-1 fw-bold" style="font-size: 0.65rem;">
                            Stock: {{ $product->stock }}
                        </span>
                    </div>

                    @if($product->stock <= 0)
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center backdrop-blur">
                            <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill shadow-sm">Agotado</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Cuerpo de Información del Producto -->
            <div class="product-card-body card-body d-flex flex-column flex-fill px-4 pb-4 pt-2">
                <div class="product-info-section">
                    <div class="d-flex align-items-center text-muted mb-2" style="font-size: 0.72rem;">
                        <i class="bi bi-shop me-1" style="color: #7F1D3F;"></i> 
                        <span class="text-truncate fw-semibold">{{ $product->company->name }}</span>
                        <span class="text-muted ms-1 text-xs">({{ $product->company->city ?? 'Ubaté' }})</span>
                        <i class="bi bi-patch-check-fill ms-1" style="color: #7F1D3F; font-size: 0.65rem;"></i>
                    </div>
                    
                    <h6 class="fw-bold mb-1 text-truncate product-title" title="{{ $product->name }}" style="font-size: 0.95rem;">
                        <a href="{{ route('public.product', $product) }}" class="text-dark text-decoration-none">{{ $product->name }}</a>
                    </h6>

                    <!-- Rating Stars -->
                    <div class="d-flex align-items-center gap-1 mb-2">
                        <i class="bi bi-star-fill text-warning" style="font-size: 0.75rem;"></i>
                        <span class="fw-bold text-dark text-xs">{{ $product->average_rating > 0 ? number_format($product->average_rating, 1) : '5.0' }}</span>
                        <span class="text-muted text-xs">({{ $product->reviews_count }})</span>
                        <a href="{{ route('public.product', $product) }}#reviews-section" class="text-xs text-decoration-none ms-1" style="color: #7F1D3F;">Ver reseñas</a>
                    </div>

                    <p class="text-muted mb-3 text-truncate product-desc" style="font-size: 0.76rem;">{{ $product->description ?? 'Sin descripción disponible.' }}</p>
                </div>
                
                <!-- Sección de Precio y Botón de Añadir -->
                <div class="product-action-section mt-auto pt-2 border-top border-light">
                    <div class="fw-bold fs-4 mb-3 text-dark d-flex align-items-end gap-1 lh-1 product-price-tag">
                        ${{ number_format($product->price, ($product->currency === 'MXN' ? 2 : 0), ',', '.') }} <span class="text-muted fw-bold" style="font-size: 0.7rem; margin-bottom: 2px;">{{ $product->currency ?? 'COP' }}</span>
                    </div>
                    
                    <div class="product-buttons-wrap">
                        @if($product->stock > 0)
                        <form action="{{ route('user.cart.add', $product) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <div class="input-group border rounded-pill overflow-hidden shadow-xs" style="width: 95px; border-color: #D98C9A !important;">
                                <button type="button" class="btn btn-light border-0 px-2 py-1 text-muted fw-bold" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">-</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control text-center border-0 p-1 fw-bold bg-white" style="font-size: 0.85rem;" readonly>
                                <button type="button" class="btn btn-light border-0 px-2 py-1 text-muted fw-bold" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">+</button>
                            </div>
                            <!-- Botón de añadir en color borgoña oscuro -->
                            <button type="submit" class="btn btn-burgundy-add flex-fill fw-bold rounded-pill shadow-xs d-flex align-items-center justify-content-center gap-1" style="font-size: 0.85rem; padding: 8px 14px;">
                                <i class="bi bi-cart-plus fs-6 me-1"></i>
                                <span>Añadir</span>
                            </button>
                        </form>
                        @else
                            <button class="btn btn-light w-100 text-muted fw-bold rounded-pill" disabled><i class="bi bi-x-circle me-1"></i>Sin inventario</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 py-5 text-center mt-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4 bg-light shadow-sm" style="width: 100px; height: 100px;">
            <i class="bi bi-search fs-1" style="color: #7F1D3F;"></i>
        </div>
        <h4 class="fw-bold text-dark">No se encontraron productos</h4>
        <p class="text-muted fs-6">Intenta cambiar los términos de búsqueda o explorar otras categorías del mercado.</p>
        <a href="{{ route('user.dashboard') }}" class="btn text-white rounded-pill mt-3 px-4 fw-bold shadow-xs" style="background: #7F1D3F;">Limpiar filtros</a>
    </div>
@endforelse
</div>

<div class="mt-4 d-flex justify-content-center pagination-spotlight">
    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>

@push('styles')
<style>
    /* Estilos de Paginación Compacta y Elegante Spotlight */
    .pagination-spotlight .pagination {
        margin-bottom: 0;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pagination-spotlight .page-link {
        color: #7F1D3F;
        border-radius: 10px !important;
        border: 1px solid #D98C9A;
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        background-color: #ffffff;
        transition: all 0.2s ease;
        line-height: 1.4;
    }
    .pagination-spotlight .page-link:hover {
        background-color: #F6E7EB;
        color: #7F1D3F;
        border-color: #7F1D3F;
    }
    .pagination-spotlight .page-item.active .page-link {
        background: linear-gradient(135deg, #7F1D3F, #5E122C) !important;
        border-color: #7F1D3F !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(127, 29, 63, 0.25);
    }
    .pagination-spotlight .page-item.disabled .page-link {
        color: #94a3b8;
        border-color: #e2e8f0;
        background-color: #f8fafc;
    }
    /* Eliminar cualquier SVG gigante desbordado de paginación */
    .pagination-spotlight svg,
    nav[role="navigation"] svg {
        display: none !important;
        max-width: 14px !important;
        max-height: 14px !important;
    }

    /* Estilos del botón de añadir en color borgoña oscuro */
    .btn-burgundy-add {
        background-color: #7F1D3F !important;
        color: #ffffff !important;
        border: none !important;
        transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-burgundy-add:hover {
        background-color: #5E122C !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(127, 29, 63, 0.35) !important;
    }

    /* Chips de categorías */
    .category-quick-pill {
        background: #ffffff;
        color: #7F1D3F;
        border: 1px solid #D98C9A;
        transition: all 0.2s ease;
    }
    .category-quick-pill:hover, .category-quick-pill.active-pill {
        background: #7F1D3F !important;
        color: #ffffff !important;
        border-color: #7F1D3F !important;
        transform: translateY(-1px);
    }

    /* Botones de cambio de vista (Grid vs Lista) */
    .view-btn {
        color: #64748b;
        background: transparent;
        border: none;
        transition: all 0.2s ease;
    }
    .view-btn.active-view-btn {
        background-color: #7F1D3F !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(127, 29, 63, 0.3) !important;
    }

    /* Tarjetas hover */
    .hover-lift {
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(127, 29, 63, 0.08) !important;
        border-color: #D98C9A !important;
    }

    /* Vista en Lista (Layout dinámico activado con botón) */
    .view-mode-list .product-item-col {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    .view-mode-list .product-card {
        flex-direction: row !important;
        align-items: center !important;
        padding: 1rem 1.25rem !important;
        border-radius: 20px !important;
    }
    .view-mode-list .product-image-container {
        width: 200px !important;
        min-width: 200px !important;
        height: 155px !important;
        padding: 0 !important;
    }
    .view-mode-list .product-image-container .overflow-hidden {
        height: 100% !important;
        border-radius: 16px !important;
    }
    .view-mode-list .product-img {
        height: 140px !important;
        padding: 0.5rem !important;
    }
    .view-mode-list .product-card-body {
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0.5rem 1.5rem !important;
        width: 100% !important;
    }
    .view-mode-list .product-info-section {
        flex: 1 !important;
        max-width: 60% !important;
        padding-right: 1.5rem;
    }
    .view-mode-list .product-info-section .product-desc {
        white-space: normal !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
    }
    .view-mode-list .product-action-section {
        min-width: 250px !important;
        border-top: none !important;
        border-left: 1px solid #f1f5f9 !important;
        padding-left: 1.5rem !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-end !important;
    }
    .view-mode-list .product-buttons-wrap {
        width: 100%;
    }

    @media (max-width: 768px) {
        .view-mode-list .product-card {
            flex-direction: column !important;
        }
        .view-mode-list .product-image-container {
            width: 100% !important;
            height: 190px !important;
        }
        .view-mode-list .product-img {
            height: 180px !important;
        }
        .view-mode-list .product-card-body {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 1rem 0 0 0 !important;
        }
        .view-mode-list .product-info-section {
            max-width: 100% !important;
            padding-right: 0;
        }
        .view-mode-list .product-action-section {
            width: 100% !important;
            align-items: stretch !important;
            border-left: none !important;
            border-top: 1px solid #f1f5f9 !important;
            margin-top: 0.75rem !important;
            padding-top: 0.75rem !important;
            padding-left: 0 !important;
        }
    }

    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
</style>
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
            col.classList.remove('col-sm-6', 'col-lg-4', 'col-xl-3');
            col.classList.add('col-12');
        });
        localStorage.setItem('spotlight_product_view', 'list');
    } else {
        container.classList.remove('view-mode-list');
        container.classList.add('view-mode-grid');
        if (btnGrid) btnGrid.classList.add('active-view-btn');
        if (btnList) btnList.classList.remove('active-view-btn');
        productCols.forEach(col => {
            col.classList.add('col-sm-6', 'col-lg-4', 'col-xl-3');
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
