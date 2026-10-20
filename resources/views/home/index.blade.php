@extends('layouts.app')
@section('title', 'Spotlight — La Aldea Digital | Descubre Emprendimientos y Productos')

@push('styles')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* ==========================================================================
       SPOTLIGHT - LA ALDEA DIGITAL
       Identidad Visual Oficial: Borgoña, Rosa Empolvado, Rosa Suave, Blanco Marfil y Carbón
       ========================================================================== */

    :root {
        --ald-burgundy: #7F1D3F;
        --ald-burgundy-dark: #5E122C;
        --ald-rose-dust: #D98C9A;
        --ald-rose-soft: #F6E7EB;
        --ald-ivory: #FFFCF7;
        --ald-charcoal: #29252A;
        --ald-card: #FFFFFF;
        --ald-border: #EEDDDC;
        --ald-text: #29252A;
        --ald-muted: #736B75;

        /* Alias retrocompatibles mapeados a la paleta oficial */
        --ald-purple: #7F1D3F;
        --ald-purple-dark: #5E122C;
        --ald-purple-soft: #F6E7EB;
        --ald-coral: #D98C9A;
        --ald-coral-soft: #FAF0F2;
        --ald-cyan: #7F1D3F;
        --ald-cyan-soft: #F6E7EB;
        --ald-amber: #D98C9A;
        --ald-bg: #FFFCF7;
    }

    body {
        background-color: var(--sl-bg);
        color: var(--sl-text-body);
        overflow-x: hidden;
    }

    /* Ambient Brand Accents (Sutiles y elegantes) */
    .ambient-glow-1 {
        position: absolute;
        top: -120px;
        left: 50%;
        transform: translateX(-50%);
        width: 850px;
        height: 480px;
        background: radial-gradient(ellipse, rgba(127, 29, 63, 0.08) 0%, rgba(217, 140, 154, 0.04) 45%, rgba(255, 252, 247, 0) 70%);
        z-index: -1;
        pointer-events: none;
    }

    .ambient-glow-2 {
        position: absolute;
        top: 45%;
        right: -100px;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(246, 231, 235, 0.5) 0%, rgba(127, 29, 63, 0.03) 50%, rgba(255, 252, 247, 0) 70%);
        z-index: -1;
        pointer-events: none;
    }

    /* Navbar */
    .spotlight-nav {
        background: rgba(255, 252, 247, 0.92);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid var(--sl-border);
        transition: all 0.25s ease;
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .brand-mark {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .brand-logo-img {
        width: 42px;
        height: 42px;
        object-fit: contain;
        display: block;
        transition: transform 0.22s ease;
    }

    .brand-mark:hover .brand-logo-img {
        transform: rotate(-6deg) scale(1.06);
    }

    .brand-logo-badge {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #7F1D3F 0%, #D98C9A 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 900;
        font-size: 1.25rem;
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.25);
    }

    .brand-text {
        font-family: var(--sl-font-display);
        font-weight: 800;
        font-size: 1.35rem;
        letter-spacing: -0.03em;
        color: var(--sl-text-heading);
        line-height: 1;
    }

    .brand-tagline {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--sl-primary);
    }

    .nav-pill-link {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--sl-text-body);
        padding: 0.5rem 1rem;
        border-radius: var(--sl-radius-pill);
        transition: all 0.18s ease;
        text-decoration: none;
    }

    .nav-pill-link:hover {
        background-color: var(--sl-primary-soft);
        color: var(--sl-primary);
    }

    /* Hero Section */
    .hero-container {
        padding: 4.5rem 0 3.5rem;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 1.15rem;
        background: var(--sl-primary-soft);
        border: 1px solid rgba(127, 29, 63, 0.2);
        border-radius: var(--sl-radius-pill);
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--sl-primary);
        margin-bottom: 1.5rem;
    }

    .hero-badge .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--sl-primary);
        box-shadow: 0 0 0 3px rgba(217, 140, 154, 0.35);
    }

    .hero-title {
        font-size: clamp(2.5rem, 5.5vw, 4.5rem);
        font-weight: 900;
        line-height: 1.08;
        letter-spacing: -0.035em;
        color: var(--sl-text-heading);
        margin-bottom: 1.25rem;
    }

    .hero-title .highlight-text {
        background: linear-gradient(135deg, #7F1D3F 0%, #D98C9A 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-lead {
        font-size: 1.15rem;
        line-height: 1.6;
        color: var(--sl-text-body);
        max-width: 620px;
        margin: 0 auto 2.25rem;
        font-weight: 500;
    }

    /* Product Cards */
    .product-card {
        background: var(--sl-surface);
        border: 1px solid var(--sl-border);
        border-radius: 20px;
        padding: 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.22s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--sl-shadow-hover);
        border-color: var(--sl-accent);
    }

    .product-img-wrapper {
        height: 210px;
        border-radius: 14px;
        overflow: hidden;
        background: #FDF9FA;
        position: relative;
        margin-bottom: 1rem;
        border: 1px solid rgba(238, 221, 220, 0.5);
    }

    .product-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .product-card:hover .product-img-wrapper img {
        transform: scale(1.05);
    }

    .product-price-tag {
        font-family: var(--sl-font-display);
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--sl-primary);
        letter-spacing: -0.02em;
    }

    .btn-cart-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background-color: var(--sl-primary-soft);
        color: var(--sl-primary);
        border: 1px solid rgba(127, 29, 63, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-cart-circle:hover {
        background-color: var(--sl-primary);
        color: #ffffff;
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(127, 29, 63, 0.28);
    }

    /* Business / Company Cards */
    .company-card-wrap {
        position: relative;
    }

    .company-village-card {
        background: var(--sl-surface);
        border: 1px solid var(--sl-border);
        border-radius: 20px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .company-card-wrap:hover .company-village-card {
        transform: translateY(-4px);
        box-shadow: var(--sl-shadow-hover);
        border-color: var(--sl-accent);
    }

    .company-banner-box {
        height: 145px;
        position: relative;
        background: linear-gradient(135deg, #7F1D3F 0%, #D98C9A 100%);
    }

    .company-avatar-box {
        position: absolute;
        bottom: -24px;
        left: 20px;
        width: 62px;
        height: 62px;
        border-radius: 18px;
        background: #ffffff;
        padding: 3px;
        box-shadow: 0 4px 14px rgba(41, 37, 42, 0.12);
        z-index: 2;
    }

    .company-avatar-inner {
        width: 100%;
        height: 100%;
        border-radius: 15px;
        overflow: hidden;
        background: var(--sl-primary-soft);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Tooltip */
    .company-tooltip {
        position: absolute;
        bottom: calc(100% + 12px);
        left: 50%;
        transform: translateX(-50%) translateY(8px);
        width: 310px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 20px 50px rgba(41, 37, 42, 0.15), 0 4px 16px rgba(41, 37, 42, 0.05);
        padding: 18px;
        z-index: 1000;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.22s ease, transform 0.22s ease;
        border: 1px solid var(--sl-border);
    }

    .tooltip-arrow {
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%) rotate(-135deg);
        width: 16px;
        height: 16px;
        background: #ffffff;
        border-right: 1px solid var(--sl-border);
        border-bottom: 1px solid var(--sl-border);
    }

    .company-card-wrap:hover .company-tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
        pointer-events: auto;
    }

    .company-card-wrap:nth-child(3n) .company-tooltip,
    .company-card-wrap:last-child .company-tooltip {
        bottom: auto;
        top: calc(100% + 12px);
        transform: translateX(-50%) translateY(-8px);
    }

    .company-card-wrap:nth-child(3n):hover .company-tooltip,
    .company-card-wrap:last-child:hover .company-tooltip {
        transform: translateX(-50%) translateY(0);
    }

    .tooltip-header { display: flex; gap: 12px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--sl-border-subtle); }
    .tooltip-logo, .tooltip-logo-placeholder { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
    .tooltip-logo-placeholder { background: linear-gradient(135deg, #7F1D3F, #D98C9A); display: flex; align-items: center; justify-content: center; color: #fff; }
    .tooltip-name { font-weight: 700; font-size: 0.95rem; color: var(--sl-text-heading); }
    .tooltip-badge { background: var(--sl-primary-soft); color: var(--sl-primary); font-size: 0.72rem; font-weight: 700; padding: 2px 10px; border-radius: 20px; }
    .tooltip-desc { font-size: 0.8rem; color: var(--sl-text-muted); margin-bottom: 10px; }
    .tooltip-grid { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }
    .tooltip-info-item { display: flex; gap: 8px; font-size: 0.78rem; color: var(--sl-text-body); }
    .tooltip-info-item i { color: var(--sl-primary); }
    .tooltip-cta { display: block; text-align: center; background: linear-gradient(135deg, #7F1D3F, #D98C9A); color: #fff; border-radius: 12px; padding: 9px; font-size: 0.84rem; font-weight: 700; text-decoration: none; transition: opacity 0.2s; }
    .tooltip-cta:hover { opacity: 0.92; color: #fff; }

    /* Highlights Ribbon */
    .village-feature-box {
        background: #ffffff;
        border: 1px solid var(--sl-border);
        border-radius: 18px;
        padding: 1.5rem;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .village-feature-box:hover {
        transform: translateY(-3px);
        box-shadow: var(--sl-shadow-md);
    }

    .feature-icon-circle {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
</style>
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
                <div class="brand-tagline">LA ALDEA DIGITAL</div>
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

{{-- Hero Principal: Spotlight, la Aldea Digital --}}
<section class="hero-container">
    <div class="container text-center">
        
        <div class="hero-badge animate-fade-in">
            <span class="pulse-dot"></span>
            <span class="lang-en">SPOTLIGHT · DIGITAL VILLAGE OF ENTREPRENEURS</span>
            <span class="lang-es">SPOTLIGHT · LA ALDEA DIGITAL DE EMPRENDIMIENTOS</span>
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

{{-- Beneficios / Pilares de la Aldea Digital --}}
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
            
            <a href="{{ auth()->check() && auth()->user()->role === 'user' ? route('user.dashboard') : route('login') }}" class="btn btn-pill-light fw-bold text-primary d-inline-flex align-items-center gap-2">
                <span class="lang-en">View more products</span><span class="lang-es">Ver más productos</span>
                <i class="bi bi-arrow-right fs-5"></i>
            </a>
        </div>
        
        {{-- Grid de Productos Reales (Solo 4 productos) --}}
        <div class="row g-4 mb-4">
            @forelse($products->take(4) as $product)
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    
                    <div class="product-img-wrapper">
                        <a href="{{ route('public.product', $product) }}">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
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
                            <a href="{{ route('public.product', $product) }}#reviews-section" class="text-xs text-primary text-decoration-none ms-1">Reseñas</a>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                            <div class="product-price-tag">
                                ${{ number_format($product->price, ($product->currency === 'MXN' ? 2 : 0), ',', '.') }} <span class="text-muted" style="font-size: 0.7rem;">{{ $product->currency ?? 'COP' }}</span>
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
                            <div class="fw-bold text-dark small">Comunidad Viva</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Creatividad sin fronteras</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <span class="badge badge-purple mb-2">
                    <span class="lang-en">THE DIGITAL VILLAGE</span><span class="lang-es">LA ALDEA DIGITAL</span>
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
<footer class="py-5 mt-5 bg-white border-top border-sl">
    <div class="container">
        <div class="row g-4 align-items-center justify-content-between">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight Logo" style="width: 32px; height: 32px; object-fit: contain;">
                    <span class="brand-text fs-5">SPOTLIGHT</span>
                </div>
                <p class="text-muted small mb-0" style="max-width: 420px; line-height: 1.5;">
                    <span class="lang-en">The digital village connecting creators, products, and communities.</span>
                    <span class="lang-es">La aldea digital que conecta creadores, productos y comunidades.</span>
                </p>
            </div>
            <div class="col-md-7 d-flex flex-wrap justify-content-md-end gap-4 text-muted small fw-semibold">
                <a href="#productos" class="text-muted text-decoration-none hover-opacity-100">
                    <span class="lang-en">Marketplace</span><span class="lang-es">Productos</span>
                </a>
                <a href="#empresas" class="text-muted text-decoration-none hover-opacity-100">
                    <span class="lang-en">Shops</span><span class="lang-es">Tiendas</span>
                </a>
                <a href="#comunidad" class="text-muted text-decoration-none hover-opacity-100">
                    <span class="lang-en">Community</span><span class="lang-es">Comunidad</span>
                </a>
                <span class="text-muted opacity-50">|</span>
                <span class="text-muted">© {{ date('Y') }} Spotlight</span>
            </div>
        </div>
    </div>
</footer>
@endsection
