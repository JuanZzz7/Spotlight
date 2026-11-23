@extends('layouts.app')
@section('title', 'Spotlight — Aldea Digital | Descubre Emprendimientos y Productos')

@push('styles')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Ambient Accents -->
<div class="ambient-glow-1"></div>
<div class="ambient-glow-2"></div>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg spotlight-nav">
    <div class="container py-2 d-flex align-items-center justify-content-between">
        
        <!-- Brand -->
        <a class="brand-mark" href="{{ route('home') }}">
            <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" class="brand-logo-img">
            <div>
                <div class="brand-text">SPOTLIGHT</div>
                <div class="brand-tagline">
                    <span class="lang-en">DIGITAL VILLAGE</span><span class="lang-es">ALDEA DIGITAL</span>
                </div>
            </div>
        </a>

        <!-- Nav Center Links -->
        <div class="d-none d-lg-flex align-items-center gap-2">
            <a class="nav-pill-link" href="#productos">
                <i class="bi bi-bag-heart me-1" style="color: var(--sl-primary);"></i>
                <span class="lang-en">Marketplace</span><span class="lang-es">Productos</span>
            </a>
            <a class="nav-pill-link" href="#empresas">
                <i class="bi bi-shop me-1" style="color: var(--sl-accent);"></i>
                <span class="lang-en">Shops</span><span class="lang-es">Tiendas</span>
            </a>
            <a class="nav-pill-link" href="#comunidad">
                <i class="bi bi-stars me-1" style="color: var(--sl-primary);"></i>
                <span class="lang-en">Community</span><span class="lang-es">Comunidad</span>
            </a>
        </div>

        <!-- Auth & Language Actions -->
        <div class="d-flex align-items-center gap-2">
            <!-- Language Switcher Button -->
            <button id="btnSwitchLang" class="btn btn-sm btn-pill-light fw-bold px-3">
                <i class="bi bi-translate me-1"></i> EN / ES
            </button>

            @auth
                @php $dash = match(auth()->user()->role) { 'admin'=>'admin.dashboard','business'=>'business.dashboard',default=>'user.dashboard'}; @endphp
                <a href="{{ route($dash) }}" class="btn btn-sm btn-primary px-4">
                    <i class="bi bi-grid-fill me-1"></i>
                    <span class="lang-en">My Panel</span><span class="lang-es">Mi Panel</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-pill-light px-3">
                    <span class="lang-en">Log in</span><span class="lang-es">Ingresar</span>
                </a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary px-4">
                    <span class="lang-en">Register</span><span class="lang-es">Registrarse</span>
                </a>
            @endauth
        </div>

    </div>
</nav>

{{-- Hero Principal: Spotlight, Aldea Digital --}}
<section class="hero-container">
    <div class="container text-center">
        
        <div class="hero-badge animate-fade-in">
            <span class="pulse-dot"></span>
            <span class="lang-en">SPOTLIGHT · DIGITAL VILLAGE OF ENTREPRENEURS</span>
            <span class="lang-es">SPOTLIGHT · ALDEA DIGITAL DE EMPRENDIMIENTOS</span>
        </div>

        <h1 class="hero-title animate-fade-in">
            <span class="lang-en">Where creative talent <br><span class="highlight-text">comes to life.</span></span>
            <span class="lang-es">Donde el talento creativo <br><span class="highlight-text">cobra vida.</span></span>
        </h1>

        <p class="hero-lead animate-fade-in">
            <span class="lang-en">
                Discover independent creators, bold businesses, and exceptional products. A vibrant community uniting creative energy in one place.
            </span>
            <span class="lang-es">
                Descubre creadores independientes, marcas con identidad y productos con historia. Una comunidad vibrante que conecta el pulso creativo en un solo lugar.
            </span>
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4 animate-fade-in">
            <a href="#productos" class="btn btn-lg btn-coral px-4">
                <i class="bi bi-bag-check-fill me-1"></i>
                <span class="lang-en">Explore Marketplace</span><span class="lang-es">Explorar Productos</span>
            </a>
            <a href="#empresas" class="btn btn-lg btn-pill-light px-4">
                <i class="bi bi-compass me-1 text-primary"></i>
                <span class="lang-en">Discover Businesses</span><span class="lang-es">Descubrir Emprendimientos</span>
            </a>
        </div>

        {{-- Metrics & Community Proof (Reutiliza los conteos existentes de empresas y productos) --}}
        <div class="d-flex flex-wrap justify-content-center gap-4 pt-3 text-muted small fw-semibold">
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-purple fs-6">{{ $companies->count() }}+</span>
                <span class="lang-en">Independent Brands</span><span class="lang-es">Emprendimientos Activos</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-coral fs-6">{{ $products->count() }}+</span>
                <span class="lang-en">Crafted Products</span><span class="lang-es">Productos Disponibles</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-cyan fs-6"><i class="bi bi-check-circle-fill"></i></span>
                <span class="lang-en">Direct & Secure</span><span class="lang-es">Comercio 100% Auténtico</span>
            </div>
        </div>

    </div>
</section>

{{-- Beneficios / Pilares de Aldea Digital --}}
<section class="py-4">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="village-feature-box">
                    <div class="feature-icon-circle bg-sl-primary-soft" style="color: var(--sl-primary);">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">
                            <span class="lang-en">Curated Discovery</span><span class="lang-es">Descubrimiento Curado</span>
                        </h6>
                        <p class="text-muted small mb-0">
                            <span class="lang-en">Unique goods from local makers with passion and soul.</span>
                            <span class="lang-es">Piezas y productos auténticos de creadores con alma y estilo propio.</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="village-feature-box">
                    <div class="feature-icon-circle bg-sl-accent-soft" style="color: var(--sl-primary);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">
                            <span class="lang-en">Direct Connection</span><span class="lang-es">Conexión Directa</span>
                        </h6>
                        <p class="text-muted small mb-0">
                            <span class="lang-en">Chat and buy directly from verified entrepreneurs.</span>
                            <span class="lang-es">Conversa y compra directamente con los emprendedores sin intermediarios.</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="village-feature-box">
                    <div class="feature-icon-circle bg-sl-primary-soft" style="color: var(--sl-accent);">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">
                            <span class="lang-en">Regional Reach</span><span class="lang-es">Alcance Regional</span>
                        </h6>
                        <p class="text-muted small mb-0">
                            <span class="lang-en">Impacting creative commerce and local talent.</span>
                            <span class="lang-es">Impulsando el ecosistema comercial creativo y el talento local.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Sección 1: Obras y Productos Destacados (#productos) --}}
<section id="productos" class="py-5 mt-4">
    <div class="container">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3">
            <div>
                <span class="badge badge-coral mb-2">
                    <span class="lang-en">FEATURED CATALOG</span><span class="lang-es">PRODUCTOS DESTACADOS</span>
                </span>
                <h2 class="fw-bold display-6 mb-0 text-dark">
                    <span class="lang-en">Featured Products</span><span class="lang-es">Productos destacados</span>
                </h2>
                <p class="text-muted mb-0 small mt-1">
                    <span class="lang-en">Handpicked goods from our community of creators</span>
                    <span class="lang-es">Selección especial de productos de nuestra comunidad</span>
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                {{-- Flechas de control de carrusel --}}
                <div class="d-flex align-items-center gap-1 me-2">
                    <button type="button" id="btnCarouselPrev" class="carousel-nav-arrow" aria-label="Anterior">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" id="btnCarouselNext" class="carousel-nav-arrow" aria-label="Siguiente">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <a href="{{ auth()->check() && auth()->user()->role === 'user' ? route('user.dashboard') : route('login') }}" class="btn btn-pill-light fw-bold text-primary d-inline-flex align-items-center gap-2">
                    <span class="lang-en">View more products</span><span class="lang-es">Ver más productos</span>
                    <i class="bi bi-arrow-right fs-5"></i>
                </a>
            </div>
        </div>
        
        {{-- Carrusel de Productos Destacados (Scroll horizontal fluido izquierda a derecha) --}}
        <div class="featured-carousel-wrapper position-relative mb-4">
            <div id="featuredProductsTrack" class="featured-products-track">
                @forelse($products as $product)
                <div class="featured-carousel-item">
                    <div class="product-card h-100">
                        
                        <div class="product-img-wrapper">
                            <a href="{{ route('public.product', $product) }}" class="d-block w-100 h-100 text-decoration-none">
                                @if($product->hasCustomImage())
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="editorial-placeholder-stage">
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
                                    </div>
                                @endif
                            </a>
                            @if($product->category)
                                <span class="badge badge-purple position-absolute top-0 start-0 m-2 shadow-sm" style="font-size: 0.68rem;">
                                    {{ $product->category }}
                                </span>
                            @endif
                            @if($product->isMexican() || ($product->company && $product->company->isMexican()))
                                <span class="badge position-absolute top-0 end-0 m-2 shadow-sm" style="background: #006847; color: #fff; font-size: 0.65rem; font-weight: 700;">
                                    🇲🇽 MX
                                </span>
                            @else
                                <span class="badge position-absolute top-0 end-0 m-2 shadow-sm" style="background: #003893; color: #fff; font-size: 0.65rem; font-weight: 700;">
                                    🇨🇴 CO
                                </span>
                            @endif
                        </div>

                        <div class="d-flex flex-column flex-fill">
                            <div class="d-flex align-items-center gap-1 text-muted small mb-1">
                                <i class="bi bi-shop text-primary" style="font-size: 0.8rem;"></i>
                                <span class="text-truncate fw-medium" style="max-width: 170px;">{{ $product->company->name }}</span>
                            </div>

                            <h5 class="fw-bold text-dark mb-1 text-truncate" title="{{ $product->name }}" style="font-size: 1rem;">
                                <a href="{{ route('public.product', $product) }}" class="text-dark text-decoration-none">{{ $product->name }}</a>
                            </h5>

                            <div class="d-flex align-items-center gap-1 mb-2">
                                <i class="bi bi-star-fill text-warning" style="font-size: 0.75rem;"></i>
                                <span class="fw-bold text-dark text-xs">{{ $product->average_rating > 0 ? number_format($product->average_rating, 1) : '5.0' }}</span>
                                <span class="text-muted text-xs">({{ $product->reviews_count }})</span>
                                <a href="{{ route('public.product', $product) }}#reviews-section" class="text-xs text-primary text-decoration-none ms-1">
                                    <span class="lang-en">Reviews</span><span class="lang-es">Reseñas</span>
                                </a>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                                <div>
                                    @if($product->effective_currency === 'MXN')
                                        <div class="product-price-tag lh-1">
                                            ${{ number_format($product->price_in_cop, 0, ',', '.') }} <span class="text-muted" style="font-size: 0.7rem;">COP</span>
                                        </div>
                                        <small class="d-block text-muted mt-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                            🇲🇽 ${{ number_format($product->price, 2, ',', '.') }} MXN
                                        </small>
                                    @else
                                        <div class="product-price-tag">
                                            ${{ number_format($product->price, (floor($product->price) != $product->price ? 2 : 0), ',', '.') }} <span class="text-muted" style="font-size: 0.7rem;">COP</span>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Botón de Carrito (Lógica 100% intacta) --}}
                                @auth
                                    @if(auth()->user()->role === 'user')
                                        <form action="{{ route('user.cart.add', $product) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button class="btn-cart-circle" title="Agregar al carrito" aria-label="Agregar al carrito">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn-cart-circle" title="Inicia sesión para comprar" aria-label="Inicia sesión para comprar">
                                        <i class="bi bi-cart-plus"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>

                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="py-5 bg-white rounded-4 border p-4">
                        <i class="bi bi-box-seam fs-1 text-muted mb-2 d-block"></i>
                        <p class="text-muted fw-medium mb-0 lang-en">No featured products published yet.</p>
                        <p class="text-muted fw-medium mb-0 lang-es">No hay productos publicados aún en la aldea.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Botón con flecha para ver más productos --}}
        <div class="text-center pt-2 mb-4">
            <a href="{{ auth()->check() && auth()->user()->role === 'user' ? route('user.dashboard') : route('login') }}" class="btn btn-pill-light btn-lg px-4 py-2 fw-bold text-primary d-inline-flex align-items-center gap-2 shadow-sm border">
                <span class="lang-en">View more products</span>
                <span class="lang-es">Ver más productos</span>
                <i class="bi bi-arrow-right fs-5"></i>
            </a>
        </div>

    </div>
</section>

{{-- Sección 2: Manifiesto / Comunidad (#comunidad) --}}
<section id="comunidad" class="py-5" style="background: linear-gradient(180deg, rgba(244, 239, 255, 0.5) 0%, rgba(250, 248, 254, 0.8) 100%);">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            
            <div class="col-lg-6">
                <div class="position-relative">
                    <div class="rounded-4 overflow-hidden shadow-lg border border-white border-4" style="aspect-ratio: 4/3;">
                        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=900&auto=format&fit=crop" class="w-100 h-100 object-fit-cover" alt="Comunidad de Emprendedores">
                    </div>
                    <!-- Floating badge overlay -->
                    <div class="position-absolute bottom-0 end-0 translate-middle-y me-n2 bg-white rounded-4 p-3 shadow-lg border d-flex align-items-center gap-3" style="max-width: 260px;">
                        <div class="rounded-circle bg-sl-primary-soft d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; color: var(--sl-primary);">
                            <i class="bi bi-heart-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">
                                <span class="lang-en">Living Community</span><span class="lang-es">Comunidad Viva</span>
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <span class="lang-en">Creativity without borders</span><span class="lang-es">Creatividad sin fronteras</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <span class="badge badge-purple mb-2">
                    <span class="lang-en">THE DIGITAL VILLAGE</span><span class="lang-es">ALDEA DIGITAL</span>
                </span>
                
                <h2 class="display-5 fw-bold mb-3 text-dark">
                    <span class="lang-en">Empowering Independent Spirit</span>
                    <span class="lang-es">Impulsando el Espíritu Independiente</span>
                </h2>

                <p class="text-muted lead mb-4" style="font-size: 1.05rem; line-height: 1.65;">
                    <span class="lang-en">
                        Spotlight is the bridge between passionate creators and curious connoisseurs. From specialized artisanal ateliers to innovative modern studios, our platform offers a vibrant digital village where local identity shines with international standards.
                    </span>
                    <span class="lang-es">
                        Spotlight es el puente entre emprendedores apasionados y compradores que valoran lo auténtico. Desde talleres tradicionales hasta estudios contemporáneos de diseño, gastronomía y tecnología, nuestra aldea digital da visibilidad y respaldo al talento que impulsa la región.
                    </span>
                </p>

                <div class="row g-3 pt-2">
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-4 border">
                            <h3 class="fw-black mb-0 text-gradient-primary display-6">{{ $companies->count() }}+</h3>
                            <div class="text-muted fw-bold small mt-1">
                                <span class="lang-en">Verified Businesses</span><span class="lang-es">Negocios Registrados</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-4 border">
                            <h3 class="fw-black mb-0 text-gradient-primary display-6">{{ $products->count() }}</h3>
                            <div class="text-muted fw-bold small mt-1">
                                <span class="lang-en">Catalog Products</span><span class="lang-es">Productos Activos</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

{{-- Sección 3: Directorio de Empresas (#empresas) (Conservando 100% funcionalidad) --}}
<section id="empresas" class="py-5">
    <div class="container py-4">
        
        <div class="mb-4">
            <h2 class="fw-bold mb-0 text-dark display-6">
                <span class="lang-en">Meet the Shops</span><span class="lang-es">Conoce las tiendas</span>
            </h2>
        </div>
        
        <div class="row g-4">
            @forelse($companies as $company)
            <div class="col-md-6 col-lg-4">
                {{-- Contenedor con atributos de geolocalización intactos para JS y Tooltip --}}
                <div class="company-card-wrap position-relative h-100"
                     data-company-id="{{ $company->id }}"
                     data-name="{{ $company->name }}"
                     data-category="{{ $company->category }}"
                     data-lat="{{ $company->latitude ?? '' }}"
                     data-lng="{{ $company->longitude ?? '' }}">

                    <div class="company-village-card">
                        
                        <!-- Banner con portada o gradiente -->
                        <div class="company-banner-box">
                            @if($company->banner)
                                <img src="{{ asset('storage/'.$company->banner) }}" class="w-100 h-100 object-fit-cover" alt="{{ $company->name }} banner">
                            @endif
                            
                            <!-- Avatar / Logo solapado -->
                            <div class="company-avatar-box">
                                <div class="company-avatar-inner">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/'.$company->logo) }}" class="w-100 h-100 object-fit-cover" alt="{{ $company->name }}">
                                    @else
                                        <i class="bi bi-shop fs-4 text-primary"></i>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Info del Emprendimiento -->
                        <div class="p-4 pt-4 mt-2 d-flex flex-column flex-fill">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="fw-bold text-dark mb-0 text-truncate" title="{{ $company->name }}">{{ $company->name }}</h5>
                            </div>
                            
                            <div class="mt-1 mb-2">
                                <span class="badge badge-purple">{{ $company->category }}</span>
                            </div>
                            
                            <p class="text-muted small mb-3 flex-fill" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                {{ $company->description ?? 'Emprendimiento verificado de la red Spotlight.' }}
                            </p>

                            <!-- Social Links & Link to Profile -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light mt-auto">
                                <div class="d-flex gap-2 text-muted">
                                    @if($company->instagram)
                                        <a href="{{ $company->instagram }}" target="_blank" class="text-muted hover-opacity-100" title="Instagram"><i class="bi bi-instagram"></i></a>
                                    @endif
                                    @if($company->facebook)
                                        <a href="{{ $company->facebook }}" target="_blank" class="text-muted hover-opacity-100" title="Facebook"><i class="bi bi-facebook"></i></a>
                                    @endif
                                    @if($company->phone)
                                        <a href="tel:{{ $company->phone }}" class="text-muted hover-opacity-100" title="Teléfono"><i class="bi bi-telephone"></i></a>
                                    @endif
                                </div>

                                <a href="{{ route('public.company', $company) }}" class="fw-bold small text-primary text-decoration-none d-flex align-items-center gap-1">
                                    <span class="lang-en">Visit Shop</span><span class="lang-es">Ver Tienda</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            
                            {{-- Botón Ubicar en Mapa (Lógica intacta) --}}
                            @if($company->latitude && $company->longitude)
                                <button class="btn btn-sm btn-pill-light w-100 mt-3 fw-semibold text-muted"
                                        onclick="event.stopPropagation(); showMap({{ $company->latitude }}, {{ $company->longitude }}, '{{ addslashes($company->name) }}')">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <span class="lang-en">View on Map</span><span class="lang-es">Ubicar en Mapa</span>
                                </button>
                            @endif
                        </div>

                    </div>

                    {{-- Tooltip original (Intacto con sus clases e IDs de datos) --}}
                    <div class="company-tooltip" role="tooltip">
                        <div class="tooltip-header">
                            @if($company->logo)
                                <img src="{{ asset('storage/'.$company->logo) }}" class="tooltip-logo" alt="">
                            @else
                                <div class="tooltip-logo-placeholder"><i class="bi bi-shop"></i></div>
                            @endif
                            <div>
                                <div class="tooltip-name">{{ $company->name }}</div>
                                <span class="tooltip-badge">{{ $company->category }}</span>
                            </div>
                        </div>
                        @if($company->description)
                        <p class="tooltip-desc">{{ Str::limit($company->description, 80) }}</p>
                        @endif
                        <div class="tooltip-grid">
                            @if($company->phone)<div class="tooltip-info-item"><i class="bi bi-telephone-fill"></i><span>{{ $company->phone }}</span></div>@endif
                            @if($company->email)<div class="tooltip-info-item"><i class="bi bi-envelope-fill"></i><span>{{ $company->email }}</span></div>@endif
                            @if($company->latitude && $company->longitude)
                            <div class="tooltip-info-item tooltip-full">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span class="tooltip-geo-address" data-lat="{{ $company->latitude }}" data-lng="{{ $company->longitude }}">
                                    <span class="geo-loading lang-en">Loading location...</span>
                                    <span class="geo-loading lang-es">Cargando ubicación...</span>
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('public.company', $company) }}" class="tooltip-cta flex-fill">
                                <span class="lang-en">View Products</span><span class="lang-es">Ver Productos</span>
                            </a>
                        </div>
                        <div class="tooltip-arrow"></div>
                    </div>

                </div>
            </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded-4 border text-center">
                        <i class="bi bi-shop fs-1 text-muted mb-2 d-block"></i>
                        <p class="text-muted mb-0 lang-en">No entrepreneurs registered yet.</p>
                        <p class="text-muted mb-0 lang-es">No se encontraron emprendimientos registrados aún.</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</section>

{{-- Modal Mapa (Intacto) --}}
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold fs-4 text-dark" id="mapModalTitle">
                    <span class="lang-en">Company Location</span><span class="lang-es">Ubicación de la Empresa</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalMap" class="rounded-4 border" style="height: 400px; width: 100%; background: #f8fafc; overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts del Mapa / Geocoding Originales + Idioma (100% intactos) --}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // --- LANGUAGE TOGGLE SCRIPT ---
    document.addEventListener("DOMContentLoaded", () => {
        const btn = document.getElementById("btnSwitchLang");
        const html = document.documentElement;

        // Recuperar idioma preferido
        const savedLang = localStorage.getItem('spotlight-lang') || 'es';
        html.setAttribute("lang", savedLang);

        if (btn) {
            btn.addEventListener("click", () => {
                const current = html.getAttribute("lang");
                const nextLang = current === 'en' ? 'es' : 'en';
                html.setAttribute("lang", nextLang);
                localStorage.setItem('spotlight-lang', nextLang);
            });
        }
    });

    // --- FEATURED PRODUCTS CAROUSEL (UNO POR UNO, LOOP NATURAL Y RETORNO DIRECTO) ---
    const carouselTrack = document.getElementById('featuredProductsTrack');
    const btnCarouselPrev = document.getElementById('btnCarouselPrev');
    const btnCarouselNext = document.getElementById('btnCarouselNext');

    if (carouselTrack) {
        let autoPlayTimer = null;
        let isTransitioning = false;
        let resumeTimeout = null;
        const intervalTime = 3000; // Avanza automáticamente cada 3 segundos

        function getItemStep() {
            const firstItem = carouselTrack.querySelector('.featured-carousel-item');
            if (!firstItem) return 300;
            const style = window.getComputedStyle(carouselTrack);
            const gap = parseFloat(style.gap) || 24;
            return firstItem.offsetWidth + gap;
        }

        function scrollNextItem() {
            if (isTransitioning) return;
            isTransitioning = true;

            const step = getItemStep();
            const maxScrollLeft = carouselTrack.scrollWidth - carouselTrack.clientWidth;
            const currentScroll = carouselTrack.scrollLeft;

            // Si está en el final o le falta menos de medio ítem para llegar, vuelve suavemente al primer artículo (0)
            if (currentScroll >= (maxScrollLeft - 15)) {
                carouselTrack.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                // Avanza al siguiente índice exacto para que quede centrado uno por uno
                const nextTarget = Math.min(maxScrollLeft, Math.round((currentScroll + step) / step) * step);
                carouselTrack.scrollTo({ left: nextTarget, behavior: 'smooth' });
            }

            setTimeout(() => {
                isTransitioning = false;
            }, 500);
        }

        function scrollPrevItem() {
            if (isTransitioning) return;
            isTransitioning = true;

            const step = getItemStep();
            const maxScrollLeft = carouselTrack.scrollWidth - carouselTrack.clientWidth;
            const currentScroll = carouselTrack.scrollLeft;

            // Si ya está en la primera tarjeta (<= 15px) y pulsa atrás, va al final
            if (currentScroll <= 15) {
                carouselTrack.scrollTo({ left: maxScrollLeft, behavior: 'smooth' });
            } else {
                // Retrocede exactamente un artículo hacia la primera
                const prevTarget = Math.max(0, Math.round((currentScroll - step) / step) * step);
                carouselTrack.scrollTo({ left: prevTarget, behavior: 'smooth' });
            }

            setTimeout(() => {
                isTransitioning = false;
            }, 500);
        }

        function startAutoPlay() {
            stopAutoPlay();
            autoPlayTimer = setInterval(scrollNextItem, intervalTime);
        }

        function stopAutoPlay() {
            if (autoPlayTimer) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = null;
            }
        }

        function scheduleResume() {
            if (resumeTimeout) clearTimeout(resumeTimeout);
            resumeTimeout = setTimeout(() => {
                startAutoPlay();
            }, 2500);
        }

        if (btnCarouselPrev) {
            btnCarouselPrev.addEventListener('click', () => {
                stopAutoPlay();
                scrollPrevItem();
                scheduleResume();
            });
        }

        if (btnCarouselNext) {
            btnCarouselNext.addEventListener('click', () => {
                stopAutoPlay();
                scrollNextItem();
                scheduleResume();
            });
        }

        // Si el usuario hace scroll manual o toca una tarjeta, pausar temporalmente y reanudar solo
        carouselTrack.addEventListener('mouseenter', stopAutoPlay);
        carouselTrack.addEventListener('mouseleave', startAutoPlay);
        carouselTrack.addEventListener('touchstart', stopAutoPlay, { passive: true });
        carouselTrack.addEventListener('touchend', scheduleResume);

        // Iniciar pase automático inmediatamente
        startAutoPlay();
    }

    // --- MAP SCRIPTS ---
    let modalMap = null;
    let modalMarker = null;

    function showMap(lat, lng, name) {
        const modal = new bootstrap.Modal(document.getElementById('mapModal'));
        document.getElementById('mapModalTitle').innerText = name;
        modal.show();

        setTimeout(() => {
            if (modalMap) modalMap.remove();
            modalMap = L.map('modalMap', { zoomControl: true, scrollWheelZoom: true }).setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(modalMap);
            modalMarker = L.marker([lat, lng]).addTo(modalMap);
            modalMap.invalidateSize();
        }, 400);
    }

    const geoCache = {};
    async function reverseGeocode(lat, lng) {
        const key = `${lat},${lng}`;
        if (geoCache[key]) return geoCache[key];
        
        const lang = document.documentElement.getAttribute("lang") || 'es';
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=${lang}`);
            const data = await res.json();
            const addr = data.address;
            let label = '';
            if (addr.road) label += addr.road;
            if (addr.city || addr.town || addr.village) {
                const city = addr.city || addr.town || addr.village;
                label += (label ? ', ' : '') + city;
            }
            geoCache[key] = label || (lang === 'en' ? 'Map Location' : 'Ubicación en el mapa');
        } catch (e) {
            geoCache[key] = (lang === 'en' ? 'Map Location' : 'Ubicación en el mapa');
        }
        return geoCache[key];
    }

    document.querySelectorAll('.company-card-wrap').forEach(wrap => {
        const spans = wrap.querySelectorAll('.tooltip-geo-address');
        if (!spans.length) return;
        let fetched = false;
        wrap.addEventListener('mouseenter', async () => {
            if (fetched) return;
            fetched = true;
            for (const span of spans) {
                const lat = span.dataset.lat;
                const lng = span.dataset.lng;
                if (!lat || !lng) continue;
                const address = await reverseGeocode(lat, lng);
                span.innerHTML = address;
            }
        });
    });
</script>
@endpush

{{-- Modern International Footer --}}
<footer class="py-5 mt-5 bg-white border-top border-sl sl-footer">
    <div class="container">
        <div class="row g-4 justify-content-between pb-4">
            <div class="col-lg-5 col-md-12">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" class="sl-footer-logo">
                    <div>
                        <span class="brand-text fs-5 d-block">SPOTLIGHT</span>
                        <span class="brand-tagline">
                            <span class="lang-en">DIGITAL VILLAGE</span><span class="lang-es">ALDEA DIGITAL</span>
                        </span>
                    </div>
                </div>
                <p class="text-muted small mb-3 sl-footer-desc">
                    <span class="lang-en">The digital village connecting creators, products, and communities with secure commerce and local identity.</span>
                    <span class="lang-es">Aldea digital que conecta creadores, productos y comunidades con comercio seguro e identidad local.</span>
                </p>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill sl-footer-badge">
                    <i class="bi bi-shield-check"></i>
                    <span class="lang-en">100% Verified Local & Regional Commerce</span>
                    <span class="lang-es">Comercio Local y Regional 100% Verificado</span>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <h6 class="sl-footer-heading">
                    <span class="lang-en">EXPLORE</span><span class="lang-es">EXPLORAR</span>
                </h6>
                <ul class="sl-footer-list">
                    <li>
                        <a href="#productos" class="sl-footer-link">
                            <span class="lang-en">Marketplace</span><span class="lang-es">Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="#empresas" class="sl-footer-link">
                            <span class="lang-en">Shops</span><span class="lang-es">Tiendas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#comunidad" class="sl-footer-link">
                            <span class="lang-en">Community</span><span class="lang-es">Comunidad</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.tracking') }}" class="sl-footer-link">
                            <span class="lang-en">Order Tracking</span><span class="lang-es">Rastreo de Envíos</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-sm-6">
                <h6 class="sl-footer-heading">
                    <span class="lang-en">SUPPORT & LEGAL</span><span class="lang-es">SOPORTE & LEGAL</span>
                </h6>
                <ul class="sl-footer-list">
                    <li>
                        <a href="{{ route('public.legal', 'privacidad') }}" class="sl-footer-link">
                            <span class="lang-en">Privacy Policy</span><span class="lang-es">Política de Privacidad</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.legal', 'terminos') }}" class="sl-footer-link">
                            <span class="lang-en">Terms of Service</span><span class="lang-es">Términos del Servicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.legal', 'cookies') }}" class="sl-footer-link">
                            <span class="lang-en">Cookie Policy</span><span class="lang-es">Política de cookies</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.legal', 'devoluciones') }}" class="sl-footer-link">
                            <span class="lang-en">Returns & Refunds</span><span class="lang-es">Devoluciones y reembolsos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.legal', 'pqrs') }}" class="sl-footer-link">
                            <span class="lang-en">PQRS & Warranties</span><span class="lang-es">PQRS & Garantías</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-4 text-center text-muted small">
            <div>
                © {{ date('Y') }} <strong>Spotlight</strong> —
                <span class="lang-en">All rights reserved.</span>
                <span class="lang-es">Todos los derechos reservados.</span>
            </div>
        </div>
    </div>
</footer>
@endsection
