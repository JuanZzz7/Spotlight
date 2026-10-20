@extends('layouts.dashboard')
@section('title','Mis Productos - Spotlight')
@section('page-title','Gestión de Productos')

@section('dashboard-content')
{{-- Header Area: solo "Gestión de Productos", botones de Exportar PDF y Nuevo Producto --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Gestión de Productos</h3>
    </div>
    <div class="d-flex gap-2 align-items-center">
        {{-- Exportar Inventario en PDF --}}
        <a href="{{ route('business.products.export_pdf', request()->query()) }}" target="_blank" class="btn bg-white border text-dark shadow-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 fw-semibold" style="font-size: 0.85rem;" title="Generar e imprimir inventario en PDF">
            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i> Exportar Inventario (PDF)
        </a>
        {{-- Nuevo Producto --}}
        <a href="{{ route('business.products.create') }}" class="btn text-white rounded-pill px-4 py-2 d-flex align-items-center gap-2 fw-semibold border-0 shadow-sm" style="background: linear-gradient(135deg, #7F1D3F, #D98C9A); font-size: 0.85rem;">
            <i class="bi bi-plus-lg"></i> Nuevo producto
        </a>
    </div>
</div>

{{-- Barra de Estadísticas de Inventario --}}
<div class="row g-4 mb-4">
    <!-- Stat 1: Total Productos -->
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center p-2" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); width: 44px; height: 44px;">
                <i class="bi bi-boxes fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">TOTAL PRODUCTOS</div>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="fw-bold text-dark lh-1" style="font-size: 1.35rem;">{{ $allProductsCount }}</span>
                    <span class="text-success fw-semibold" style="font-size: 0.7rem;">{{ $activeCount }} activos</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Stat 2: En Stock -->
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center p-2" style="background: rgba(217, 140, 154, 0.15); color: var(--sl-accent, #D98C9A); width: 44px; height: 44px;">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">UNIDADES EN STOCK</div>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="fw-bold text-dark lh-1" style="font-size: 1.35rem;">{{ number_format($totalUnitsInStock, 0, ',', '.') }}</span>
                    <span class="text-muted fw-medium" style="font-size: 0.7rem;">unidades</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Stat 3: Destacados -->
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center p-2" style="background: rgba(245, 158, 11, 0.15); color: #d97706; width: 44px; height: 44px;">
                <i class="bi bi-star-fill fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">DESTACADOS</div>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="fw-bold text-dark lh-1" style="font-size: 1.35rem;">{{ $featuredCount }}</span>
                    <span class="text-warning fw-semibold" style="font-size: 0.7rem;">En vitrina</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Stat 4: Valor Inventario -->
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center p-2" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); width: 44px; height: 44px;">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">VALOR INVENTARIO</div>
                <div class="d-flex align-items-baseline gap-1 mt-1">
                    <span class="fw-bold text-dark lh-1" style="font-size: 1.35rem;">${{ number_format($totalStockValue, 0, ',', '.') }}</span>
                    <span class="text-muted fw-bold" style="font-size: 0.65rem;">COP</span>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="border-light my-3">

{{-- Barra de Filtros, Búsqueda, Ordenamiento y Switch Bloque/Lista --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 bg-white p-3 rounded-4 border shadow-xs">
    <!-- Buscador -->
    <form method="GET" action="{{ route('business.products.index') }}" class="position-relative" style="width: 100%; max-width: 300px;">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.85rem;"></i>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-pill py-2 text-sm" placeholder="Buscar por nombre o SKU..." style="padding-left: 2.4rem; font-size: 0.82rem; border-color: #cbd5e1; box-shadow: none;">
    </form>
    
    <!-- Filtros de Estado: Todos, Activos, Agotados (FUNCIONALES) -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="text-muted fw-bold text-uppercase d-none d-md-inline" style="font-size: 0.68rem; letter-spacing: 0.5px;">ESTADO:</span>
        
        @php
            $currentStatus = request('status', 'all');
            $baseParams = request()->except(['page', 'status']);
        @endphp

        {{-- Todos --}}
        <a href="{{ route('business.products.index', array_merge($baseParams, ['status' => 'all'])) }}" 
           class="btn btn-sm rounded-pill px-3 py-1 fw-bold text-decoration-none {{ $currentStatus === 'all' ? 'active-filter-pill' : 'inactive-filter-pill' }}" style="font-size: 0.78rem;">
            Todos ({{ $allProductsCount }})
        </a>

        {{-- Activos --}}
        <a href="{{ route('business.products.index', array_merge($baseParams, ['status' => 'active'])) }}" 
           class="btn btn-sm rounded-pill px-3 py-1 fw-bold text-decoration-none {{ $currentStatus === 'active' ? 'active-filter-pill' : 'inactive-filter-pill' }}" style="font-size: 0.78rem;">
            <span class="text-success me-1">●</span> Activos ({{ $activeCount }})
        </a>

        {{-- Agotados --}}
        <a href="{{ route('business.products.index', array_merge($baseParams, ['status' => 'out_of_stock'])) }}" 
           class="btn btn-sm rounded-pill px-3 py-1 fw-bold text-decoration-none {{ $currentStatus === 'out_of_stock' ? 'active-filter-pill' : 'inactive-filter-pill' }}" style="font-size: 0.78rem;">
            <span class="text-danger me-1">●</span> Agotados ({{ $outOfStockCount }})
        </a>
    </div>

    <!-- Ordenamiento y Vista (Bloque vs Lista) FUNCIONALES -->
    <div class="d-flex align-items-center gap-3 ms-auto ms-xl-0">
        <!-- Selector de Orden -->
        <div class="d-flex align-items-center gap-1">
            <span class="text-muted fw-medium d-none d-sm-inline" style="font-size: 0.78rem;">Ordenar:</span>
            <select class="form-select form-select-sm rounded-pill bg-white fw-bold text-dark border px-3 py-1 shadow-xs" 
                    onchange="applySort(this.value)" style="font-size: 0.8rem; border-color: #cbd5e1; cursor: pointer;">
                <option value="recent" {{ request('sort', 'recent') === 'recent' ? 'selected' : '' }}>Más recientes</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Menor precio</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Mayor precio</option>
                <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Menor stock (Por agotar)</option>
                <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Mayor stock</option>
                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nombre (A - Z)</option>
            </select>
        </div>

        <!-- Botones de Cambio de Vista (Bloque vs Lista) -->
        <div class="d-flex gap-1 bg-light rounded-pill p-1 border">
            <button type="button" id="btnViewGrid" onclick="setProductView('grid')" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center view-btn active-view-btn" style="width: 32px; height: 32px;" title="Ver en bloque (cuadrícula)">
                <i class="bi bi-grid-fill"></i>
            </button>
            <button type="button" id="btnViewList" onclick="setProductView('list')" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center view-btn text-muted" style="width: 32px; height: 32px;" title="Ver en lista">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
    </div>
</div>

{{-- VISTA 1: EN BLOQUE (CUADRÍCULA / CARDS) --}}
<div id="productsGridView">
    <div class="row g-4 mb-4">
        @forelse($products as $product)
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column bg-white hover-lift">
                <!-- Imagen del Producto -->
                <div class="position-relative bg-light" style="height: 185px;">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-image text-muted opacity-25" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <!-- Badge Activo / Agotado -->
                    <div class="position-absolute" style="top: 12px; left: 12px;">
                        @if($product->stock <= 0)
                            <span class="badge rounded-pill px-2 py-1 d-flex align-items-center gap-1 shadow-sm bg-danger text-white" style="font-size: 0.65rem;">
                                <span class="rounded-circle" style="width: 5px; height: 5px; background: white;"></span> Agotado
                            </span>
                        @elseif($product->active)
                            <span class="badge rounded-pill px-2 py-1 d-flex align-items-center gap-1 shadow-sm bg-success text-white" style="font-size: 0.65rem;">
                                <span class="rounded-circle" style="width: 5px; height: 5px; background: white;"></span> Activo
                            </span>
                        @else
                            <span class="badge rounded-pill px-2 py-1 d-flex align-items-center gap-1 shadow-sm bg-secondary text-white" style="font-size: 0.65rem;">
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Detalles -->
                <div class="p-3 flex-fill d-flex flex-column">
                    @if($product->sku)
                        <span class="text-muted fw-bold text-uppercase mb-1" style="font-size: 0.62rem; letter-spacing: 0.5px;">SKU: {{ $product->sku }}</span>
                    @endif
                    <h6 class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.92rem;" title="{{ $product->name }}">{{ $product->name }}</h6>
                    <div class="fw-bold mb-3 text-dark" style="font-size: 1.25rem; letter-spacing: -0.5px;">
                        ${{ number_format($product->price, 0, ',', '.') }}
                    </div>
                    
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-auto">
                        <div class="rounded px-2 py-1 d-flex align-items-center gap-1 fw-semibold {{ $product->stock <= 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-muted' }}" style="font-size: 0.72rem;">
                            <i class="bi bi-box-seam"></i> Stock: {{ $product->stock }}
                        </div>
                        @if($product->featured)
                            <div class="text-dark rounded px-2 py-1 fw-bold d-flex align-items-center gap-1" style="font-size: 0.7rem; background: #fef08a;">
                                <i class="bi bi-star-fill text-warning"></i> Destacado
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones -->
                <div class="d-flex border-top" style="border-color: #f1f5f9 !important;">
                    <a href="{{ route('business.products.edit', $product) }}" class="btn btn-link text-decoration-none flex-fill py-2 d-flex align-items-center justify-content-center gap-2 fw-semibold" style="color: var(--sl-primary, #7F1D3F); font-size: 0.8rem; border-right: 1px solid #f1f5f9; border-radius: 0;">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form action="{{ route('business.products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')" class="flex-fill d-flex m-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link text-decoration-none w-100 py-2 d-flex align-items-center justify-content-center gap-2 text-danger fw-semibold" style="font-size: 0.8rem; border-radius: 0;">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center bg-white rounded-4 border">
            <i class="bi bi-box-seam text-muted fs-1 mb-2"></i>
            <h6 class="fw-bold text-dark">No se encontraron productos</h6>
            <p class="text-muted small mb-3">No hay productos que coincidan con los filtros seleccionados.</p>
            <a href="{{ route('business.products.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Limpiar filtros</a>
        </div>
        @endforelse

        <!-- Tarjeta para crear nuevo producto -->
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ route('business.products.create') }}" class="text-decoration-none h-100 d-block">
                <div class="vs-card h-100 border-2 border-dashed shadow-sm rounded-4 d-flex flex-column align-items-center justify-content-center text-center p-4 bg-white hover-lift" style="min-height: 280px; border-color: #cbd5e1 !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-xs" style="width: 50px; height: 50px; border: 2px solid var(--sl-primary, #7F1D3F); background: var(--sl-primary-soft, #F6E7EB);">
                        <i class="bi bi-plus-lg fs-4" style="color: var(--sl-primary, #7F1D3F);"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">Añadir nuevo producto</h6>
                    <p class="text-muted mb-3" style="font-size: 0.75rem; line-height: 1.4;">Publica artículos en tu tienda, asigna precios e inventario para tus compradores.</p>
                    <div class="mt-auto fw-bold" style="color: var(--sl-primary, #7F1D3F); font-size: 0.8rem;">
                        Crear publicación <i class="bi bi-arrow-right ms-1"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- VISTA 2: EN LISTA (TABLA COMPACTA) --}}
<div id="productsListView" style="display: none;" class="mb-4">
    <div class="bg-white rounded-4 border shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase">Producto</th>
                        <th class="py-3 text-uppercase">SKU</th>
                        <th class="py-3 text-uppercase">Precio</th>
                        <th class="py-3 text-uppercase text-center">Stock</th>
                        <th class="py-3 text-uppercase text-center">Estado</th>
                        <th class="py-3 text-uppercase text-center">Destacado</th>
                        <th class="pe-4 py-3 text-uppercase text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    @forelse($products as $product)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 bg-light border overflow-hidden flex-shrink-0" style="width: 48px; height: 48px;">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">{{ $product->name }}</div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 250px;">{{ Str::limit($product->description, 50) }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-muted fw-semibold">
                            {{ $product->sku ?: '—' }}
                        </td>
                        <td class="py-3 fw-bold text-dark">
                            ${{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-center">
                            @if($product->stock <= 0)
                                <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-2 py-1">0 (Agotado)</span>
                            @else
                                <span class="badge rounded-pill bg-light text-dark border fw-bold px-2 py-1">{{ $product->stock }} uds</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($product->active)
                                <span class="badge rounded-pill bg-success-subtle text-success fw-bold px-2 py-1">Activo</span>
                            @else
                                <span class="badge rounded-pill bg-secondary-subtle text-muted fw-bold px-2 py-1">Inactivo</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($product->featured)
                                <span class="badge rounded-pill bg-warning-subtle text-warning fw-bold px-2 py-1"><i class="bi bi-star-fill me-1"></i>Sí</span>
                            @else
                                <span class="text-muted small">No</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('business.products.edit', $product) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-semibold text-primary" title="Editar">
                                    <i class="bi bi-pencil me-1"></i> Editar
                                </a>
                                <form action="{{ route('business.products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')" class="d-inline m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            No se encontraron productos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Paginación --}}
@if(method_exists($products, 'links'))
<div class="d-flex justify-content-center mb-4">
    {{ $products->links() }}
</div>
@endif

@push('styles')
<style>
    .active-filter-pill {
        background: var(--sl-primary, #7F1D3F) !important;
        color: #ffffff !important;
        border: 1px solid var(--sl-primary, #7F1D3F) !important;
        box-shadow: 0 4px 10px rgba(127, 29, 63, 0.2) !important;
    }
    .inactive-filter-pill {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .inactive-filter-pill:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .view-btn {
        border: none;
        background: transparent;
        transition: all 0.2s ease;
    }
    .active-view-btn {
        background: var(--sl-primary, #7F1D3F) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(127, 29, 63, 0.3) !important;
    }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(0,0,0,0.08) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Cambio dinámico entre vista Bloque y Lista
    function setProductView(view) {
        const gridView = document.getElementById('productsGridView');
        const listView = document.getElementById('productsListView');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnList = document.getElementById('btnViewList');

        if (view === 'list') {
            gridView.style.display = 'none';
            listView.style.display = 'block';
            btnList.classList.add('active-view-btn');
            btnList.classList.remove('text-muted');
            btnGrid.classList.remove('active-view-btn');
            btnGrid.classList.add('text-muted');
            localStorage.setItem('business_products_view', 'list');
        } else {
            gridView.style.display = 'block';
            listView.style.display = 'none';
            btnGrid.classList.add('active-view-btn');
            btnGrid.classList.remove('text-muted');
            btnList.classList.remove('active-view-btn');
            btnList.classList.add('text-muted');
            localStorage.setItem('business_products_view', 'grid');
        }
    }

    // Aplicar ordenamiento preservando filtros actuales
    function applySort(sortVal) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortVal);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }

    // Restaurar vista preferida al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('business_products_view') || 'grid';
        setProductView(savedView);
    });
</script>
@endpush
@endsection
