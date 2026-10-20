@extends('layouts.app')
@section('title', $product->name . ' - Spotlight')

@push('styles')
<style>
    .star-rating-select {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 6px;
    }
    .star-rating-select input {
        display: none;
    }
    .star-rating-select label {
        font-size: 2rem;
        color: #d1d5db;
        cursor: pointer;
        transition: color 0.15s ease, transform 0.15s ease;
    }
    .star-rating-select label:hover,
    .star-rating-select label:hover ~ label,
    .star-rating-select input:checked ~ label {
        color: #f59e0b;
        transform: scale(1.1);
    }
    .product-hero-card {
        border-radius: 24px;
        background: #ffffff;
        border: 1px solid var(--sl-border);
        box-shadow: var(--sl-shadow-sm);
    }
    .product-img-stage {
        background: linear-gradient(135deg, #FFFCF7 0%, #F6E7EB 100%);
        border-radius: 20px;
        min-height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(238, 221, 220, 0.4);
    }
    .product-img-stage img {
        max-height: 340px;
        width: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .product-img-stage:hover img {
        transform: scale(1.05);
    }
    .review-bubble {
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid var(--sl-border);
        box-shadow: var(--sl-shadow-xs);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .review-bubble:hover {
        transform: translateY(-2px);
        box-shadow: var(--sl-shadow-hover);
        border-color: var(--sl-accent);
    }
    .star-filled {
        color: #f59e0b;
    }
    .star-empty {
        color: #e2e8f0;
    }
    .nav-pill-custom {
        background: #ffffff;
        border: 1px solid var(--sl-border);
        color: var(--sl-text-body);
        border-radius: 50px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .nav-pill-custom:hover {
        background: var(--sl-primary-soft);
        color: var(--sl-primary);
        border-color: var(--sl-accent);
    }
    /* Estilos de botones para devolverse a seguir viendo productos */
    .btn-return-products {
        background-color: #ffffff;
        color: #7F1D3F;
        border: 1.5px solid #D98C9A;
        font-size: 0.88rem;
        transition: all 0.22s ease;
        text-decoration: none;
    }
    .btn-return-products:hover {
        background: #7F1D3F;
        color: #ffffff;
        border-color: #7F1D3F;
        transform: translateX(-4px);
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.25);
    }
    .btn-return-products i {
        transition: transform 0.2s ease;
    }
    .btn-return-products:hover i {
        transform: translateX(-3px);
    }
    .btn-return-secondary {
        background: #FFFCF7;
        color: #7F1D3F;
        border: 1.5px solid #D98C9A;
        font-size: 0.88rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-return-secondary:hover {
        background: #F6E7EB;
        color: #5E122C;
        border-color: #7F1D3F;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(127, 29, 63, 0.12);
    }
    /* Translation Logic */
    html[lang="es"] .lang-en { display: none !important; }
    html[lang="en"] .lang-es { display: none !important; }
</style>
@endpush

@section('content')
<!-- Top Navbar -->
<header class="bg-white border-bottom sticky-top shadow-xs" style="z-index: 100; backdrop-filter: blur(10px); background: rgba(255,252,247,0.94) !important;">
    <div class="container py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('images/logo.png') }}" alt="Spotlight" style="height: 38px; object-fit: contain;" class="me-2">
                    <span class="fw-bold fs-4" style="color: var(--sl-primary); font-family: 'Outfit', sans-serif;">SPOTLIGHT</span>
                    <span class="badge badge-purple ms-2 d-none d-sm-inline-block" style="font-size: 0.65rem;">LA ALDEA DIGITAL</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                @auth
                    @if(auth()->user()->isUser())
                        <a href="{{ route('user.dashboard') }}" class="nav-pill-custom d-none d-md-inline-flex align-items-center gap-1">
                            <i class="bi bi-grid"></i>
                            <span class="lang-en">Marketplace</span><span class="lang-es">Marketplace</span>
                        </a>
                        <a href="{{ route('user.cart.index') }}" class="nav-pill-custom position-relative d-inline-flex align-items-center gap-1">
                            <i class="bi bi-cart3"></i>
                            <span class="d-none d-sm-inline"><span class="lang-en">Cart</span><span class="lang-es">Carrito</span></span>
                            @if(count(session('cart', [])) > 0)
                                <span class="badge badge-coral rounded-pill" style="font-size: 0.65rem;">{{ count(session('cart', [])) }}</span>
                            @endif
                        </a>
                        <a href="{{ route('user.panel') }}" class="nav-pill-custom d-none d-sm-inline-flex align-items-center gap-1">
                            <i class="bi bi-person-circle"></i>
                            <span class="lang-en">My Panel</span><span class="lang-es">Mi Panel</span>
                        </a>
                    @elseif(auth()->user()->isBusiness())
                        <a href="{{ route('business.dashboard') }}" class="nav-pill-custom d-inline-flex align-items-center gap-1">
                            <i class="bi bi-shop"></i>
                            <span class="lang-en">Business Panel</span><span class="lang-es">Panel Negocio</span>
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-pill-custom d-inline-flex align-items-center gap-1">
                            <i class="bi bi-shield-check"></i> Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="nav-pill-custom">
                        <span class="lang-en">Log in</span><span class="lang-es">Iniciar Sesión</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-spotlight btn-sm fw-bold rounded-pill px-3 py-2">
                        <span class="lang-en">Sign up</span><span class="lang-es">Registrarse</span>
                    </a>
                @endauth
                
                <button id="btnSwitchLang" class="btn btn-sm btn-light fw-bold border shadow-sm rounded-pill px-3" style="color: var(--sl-text, #1C1929);">
                    EN / ES
                </button>
            </div>
        </div>
    </div>
</header>

<main class="py-4 py-lg-5" style="background-color: var(--sl-bg, #FAF8FE); min-height: 85vh;">
    <div class="container">
        
        @php
            $previousUrl = url()->previous();
            if (auth()->check() && auth()->user()->isUser()) {
                $defaultBackUrl = route('user.dashboard');
            } elseif (auth()->check() && auth()->user()->isBusiness()) {
                $defaultBackUrl = route('business.products.index');
            } else {
                $defaultBackUrl = route('home');
            }

            if ($previousUrl && $previousUrl !== url()->current() && !str_contains($previousUrl, 'login') && !str_contains($previousUrl, 'register') && !str_contains($previousUrl, 'checkout')) {
                $backUrl = $previousUrl;
            } else {
                $backUrl = $defaultBackUrl;
            }
        @endphp

        <!-- Botón con flecha para devolverse a seguir viendo productos y Breadcrumbs -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <a href="{{ $backUrl }}" class="btn-return-products d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill fw-bold shadow-xs">
                <i class="bi bi-arrow-left fs-5"></i>
                <span><span class="lang-en">Back to keep browsing products</span><span class="lang-es">Volver a seguir viendo productos</span></span>
            </a>

            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i><span class="lang-en">Home</span><span class="lang-es">Inicio</span></a></li>
                    @auth
                        @if(auth()->user()->isUser())
                            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none text-muted"><span class="lang-en">Marketplace</span><span class="lang-es">Tienda</span></a></li>
                        @endif
                    @endauth
                    @if($product->company)
                        <li class="breadcrumb-item"><a href="{{ route('public.company', $product->company) }}" class="text-decoration-none text-muted">{{ $product->company->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3" style="background: rgba(16, 185, 129, 0.12); color: #065f46;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ $backUrl }}" class="btn btn-sm rounded-pill px-3 py-1 bg-white text-dark border shadow-xs fw-bold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Seguir viendo productos
                    </a>
                    <a href="{{ route('user.cart.index') }}" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-xs fw-bold" style="background: #065f46;">
                        Ver Carrito <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 shadow-sm d-flex align-items-center gap-2 mb-4 p-3" style="background: rgba(239, 68, 68, 0.12); color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>
                    <strong>Ups, por favor revisa los siguientes errores:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Product Presentation Hero Card -->
        <div class="product-hero-card p-4 p-md-5 mb-5">
            <div class="row g-4 g-lg-5 align-items-center">
                <!-- Product Image Section -->
                <div class="col-lg-6">
                    <div class="product-img-stage shadow-xs">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="mainProductImg">
                        
                        <!-- Badges Overlay -->
                        <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-2">
                            @if($product->isMexican() || ($product->company && $product->company->isMexican()))
                                <span class="badge shadow-sm" style="background: #006847; color: #fff; font-size: 0.72rem; font-weight: 700;">
                                    🇲🇽 Hecho en México
                                </span>
                            @else
                                <span class="badge shadow-sm" style="background: #003893; color: #fff; font-weight: 700; font-size: 0.72rem;">
                                    🇨🇴 Hecho en Colombia
                                </span>
                            @endif

                            @if($product->stock > 0)
                                <span class="badge badge-cyan shadow-sm" style="font-size: 0.7rem; font-weight: 700;">
                                    <i class="bi bi-check2-circle me-1"></i> <span class="lang-en">In Stock</span><span class="lang-es">Disponible</span>
                                </span>
                            @else
                                <span class="badge bg-danger text-white shadow-sm" style="font-size: 0.7rem; font-weight: 700;">
                                    <i class="bi bi-x-circle me-1"></i> <span class="lang-en">Out of Stock</span><span class="lang-es">Agotado</span>
                                </span>
                            @endif
                            <span class="badge badge-purple shadow-sm" style="font-size: 0.7rem;">
                                <i class="bi bi-stars me-1"></i> ALDEA DIGITAL
                            </span>
                        </div>

                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-white text-dark shadow-sm border rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                <span class="lang-en">Stock:</span><span class="lang-es">Stock:</span> {{ $product->stock }} <span class="lang-en">units</span><span class="lang-es">uds</span>
                            </span>
                        </div>
                    </div>

                    <!-- Company Card Mini -->
                    @if($product->company)
                    <div class="mt-4 p-3 rounded-4 bg-light d-flex align-items-center justify-content-between border">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $product->company->logo ? asset('storage/'.$product->company->logo) : asset('images/logo.png') }}" alt="{{ $product->company->name }}" class="rounded-circle shadow-xs" style="width: 48px; height: 48px; object-fit: cover; background: #fff;">
                            <div>
                                <div class="d-flex align-items-center gap-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $product->company->name }}</h6>
                                    <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.85rem;" title="Comercio Verificado"></i>
                                    <span class="badge bg-white text-dark border ms-1" style="font-size: 0.7rem;">
                                        {{ $product->company->country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }}
                                    </span>
                                </div>
                                <span class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $product->company->city ?? 'Aldea Digital' }}{{ $product->company->state ? ', ' . $product->company->state : '' }} • {{ $product->company->category ?? 'Emprendimiento' }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('public.company', $product->company) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                            <span class="lang-en">View Store</span><span class="lang-es">Ver Tienda</span>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Product Details & Buy Section -->
                <div class="col-lg-6">
                    <div class="ps-lg-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge badge-coral text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                {{ $product->company->category ?? 'Creación Original' }}
                            </span>
                            @if($product->sku)
                                <span class="text-muted text-xs">SKU: {{ $product->sku }}</span>
                            @endif
                        </div>

                        <h1 class="fw-bold text-dark display-6 mb-3" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">
                            {{ $product->name }}
                        </h1>

                        <!-- Rating summary click-to-reviews -->
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-1">
                                @php
                                    $avg = $product->average_rating;
                                    $count = $product->reviews_count;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= round($avg) ? 'star-filled' : 'star-empty' }} fs-5"></i>
                                @endfor
                                <span class="fw-bold text-dark fs-5 ms-2">{{ $avg > 0 ? number_format($avg, 1) : '5.0' }}</span>
                            </div>
                            <span class="text-muted">•</span>
                            <a href="#reviews-section" class="text-decoration-none fw-semibold text-primary">
                                <span class="lang-en">{{ $count }} ratings & reviews</span>
                                <span class="lang-es">{{ $count }} calificaciones y reseñas</span>
                            </a>
                        </div>

                        <!-- Price Section -->
                        <div class="mb-4">
                            <span class="text-muted fw-semibold" style="font-size: 0.8rem;">
                                <span class="lang-en">Unit Price</span><span class="lang-es">Precio oficial</span>:
                            </span>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fw-black display-5" style="color: var(--sl-primary, #7F1D3F); font-family: 'Outfit', sans-serif;">
                                    ${{ number_format($product->price, ($product->currency === 'MXN' ? 2 : 0), ',', '.') }}
                                </span>
                                <span class="fw-bold text-muted fs-6">{{ $product->currency ?? 'COP' }}</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-shield-check me-1"></i> <span class="lang-en">Guaranteed direct price without hidden markups</span><span class="lang-es">Precio directo de creador sin comisiones ocultas</span>
                            </span>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif;">
                                <span class="lang-en">About this product</span><span class="lang-es">Descripción del producto</span>
                            </h6>
                            <p class="text-muted lh-base" style="font-size: 0.95rem;">
                                {{ $product->description ?: 'Producto auténtico ofrecido por la comunidad creativa de la Aldea Digital. Hecho con altos estándares de calidad, pasión y compromiso con el comercio local.' }}
                            </p>
                        </div>

                        <!-- Value Props -->
                        <div class="row g-2 mb-4">
                            <div class="col-sm-6">
                                <div class="p-2 rounded-3 bg-light border d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                                    <i class="bi bi-truck text-primary fs-5"></i>
                                    <span class="text-muted">Despacho seguro nacional e internacional</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 rounded-3 bg-light border d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                                    <i class="bi bi-shield-check text-success fs-5"></i>
                                    <span class="text-muted">Comercio 100% verificado por Spotlight</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add to Cart Form -->
                        @if($product->stock > 0)
                            <form action="{{ route('user.cart.add', $product) }}" method="POST" class="d-flex flex-wrap align-items-center gap-3">
                                @csrf
                                <div class="d-flex align-items-center bg-light border rounded-pill p-1 shadow-xs" style="width: 140px;">
                                    <button type="button" class="btn btn-sm btn-light border-0 rounded-circle text-muted fw-bold" style="width: 36px; height: 36px;" onclick="const input = this.parentNode.querySelector('input[type=number]'); input.stepDown();">-</button>
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control text-center border-0 bg-transparent fw-bold" style="font-size: 1rem;" readonly>
                                    <button type="button" class="btn btn-sm btn-light border-0 rounded-circle text-muted fw-bold" style="width: 36px; height: 36px;" onclick="const input = this.parentNode.querySelector('input[type=number]'); input.stepUp();">+</button>
                                </div>

                                <button type="submit" class="btn btn-spotlight btn-lg flex-fill rounded-pill fw-bold py-3 px-4 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-bag-plus-fill fs-5"></i>
                                    <span class="lang-en">Add to Cart</span><span class="lang-es">Añadir al Carrito</span>
                                </button>
                            </form>
                        @else
                            <div class="p-3 bg-light rounded-4 text-center border">
                                <span class="fw-bold text-muted"><i class="bi bi-exclamation-circle me-1"></i> Este producto no tiene inventario en este momento.</span>
                            </div>
                        @endif

                        {{-- Sección de Compra Internacional Asistida (Colombia ⇄ México) --}}
                        <div class="mt-4 pt-3 border-top">
                            <div class="p-3 rounded-4 border shadow-xs" style="background: linear-gradient(135deg, rgba(127,29,63,0.03) 0%, rgba(217,140,154,0.08) 100%); border-color: rgba(217,140,154,0.35) !important;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #7F1D3F; color: #fff; font-size: 0.72rem;">
                                        <i class="bi bi-globe2 me-1"></i> COMPRA INTERNACIONAL ASISTIDA
                                    </span>
                                    <span class="badge bg-white text-dark border fw-semibold" style="font-size: 0.7rem;">
                                        {{ $product->country === 'MX' ? '🇲🇽 México ➔ 🇨🇴 Colombia' : '🇨🇴 Colombia ➔ 🇲🇽 México' }}
                                    </span>
                                </div>
                                <p class="text-muted mb-3" style="font-size: 0.82rem; line-height: 1.4;">
                                    ¿Te encuentras en otro país? Solicita este producto para coordinar directamente con el emprendimiento el flete internacional, tiempos de entrega y condiciones de pago seguras.
                                </p>
                                <a href="{{ route('user.assisted_purchases.create', $product) }}" class="btn w-100 rounded-pill py-2 px-3 fw-bold text-white d-flex align-items-center justify-content-center gap-2 shadow-xs" style="background: linear-gradient(135deg, #29252A 0%, #7F1D3F 100%); font-size: 0.88rem;">
                                    <i class="bi bi-airplane-engines me-1"></i> Solicitar Compra Internacional Asistida
                                </a>
                            </div>
                        </div>

                        <!-- Botón secundario con flecha para devolverse a seguir viendo productos -->
                        <div class="mt-3">
                            <a href="{{ $backUrl }}" class="btn-return-secondary w-100 rounded-pill py-2 px-3 d-flex align-items-center justify-content-center gap-2 fw-semibold">
                                <i class="bi bi-arrow-left fs-5"></i>
                                <span><span class="lang-en">Keep browsing products</span><span class="lang-es">Seguir viendo productos</span></span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- =============================================================== -->
        <!-- BOTTOM SECTION: CALIFICACIONES Y RESEÑAS DE CLIENTES          -->
        <!-- =============================================================== -->
        <section id="reviews-section" class="pt-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                <div>
                    <span class="badge badge-purple px-3 py-1 fw-bold mb-2">EXPERIENCIAS REALES</span>
                    <h2 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">
                        <span class="lang-en">Customer Ratings & Reviews</span>
                        <span class="lang-es">Calificación y Reseñas de Clientes</span>
                    </h2>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                        <span class="lang-en">Honest feedback and ratings from customers who bought this product.</span>
                        <span class="lang-es">Opiniones transparentes de los compradores que ya adquirieron este producto.</span>
                    </p>
                </div>

                @auth
                    <a href="#form-review" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold shadow-xs">
                        <i class="bi bi-pencil-square me-1"></i> <span class="lang-en">Write a review</span><span class="lang-es">Dejar mi reseña</span>
                    </a>
                @endauth
            </div>

            <!-- Scorecard & Breakdown Card -->
            <div class="vs-card border-0 shadow-sm p-4 p-md-5 mb-5 rounded-4 bg-white">
                <div class="row g-4 align-items-center">
                    <!-- Average Big Score -->
                    <div class="col-md-4 text-center border-md-end pe-md-4">
                        <div class="display-3 fw-bold lh-1 mb-2" style="color: var(--sl-primary, #7F1D3F); font-family: 'Outfit', sans-serif;">
                            {{ $avg > 0 ? number_format($avg, 1) : '5.0' }}
                        </div>
                        <div class="d-flex justify-content-center gap-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill {{ $i <= round($avg > 0 ? $avg : 5) ? 'star-filled' : 'star-empty' }} fs-4"></i>
                            @endfor
                        </div>
                        <div class="text-muted fw-semibold" style="font-size: 0.85rem;">
                            <span class="lang-en">Based on {{ $count }} reviews</span>
                            <span class="lang-es">Basado en {{ $count }} calificaciones</span>
                        </div>
                        @if($userHasPurchased)
                            <div class="mt-3">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> <span class="lang-en">You purchased this product</span><span class="lang-es">Compraste este producto</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Star Breakdown Bars -->
                    <div class="col-md-5 px-md-4">
                        <div class="d-flex flex-column gap-2">
                            @php $breakdown = $product->rating_stars_array; @endphp
                            @foreach($breakdown as $star => $data)
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-semibold text-muted text-sm" style="width: 50px;">{{ $star }} <i class="bi bi-star-fill text-warning" style="font-size: 0.8rem;"></i></span>
                                <div class="progress flex-fill" style="height: 9px; border-radius: 20px; background-color: #f1f5f9;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $data['percentage'] }}%; background: linear-gradient(90deg, var(--sl-primary, #7F1D3F), var(--sl-accent, #D98C9A)); border-radius: 20px;" aria-valuenow="{{ $data['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="text-muted text-xs fw-bold" style="width: 35px; text-align: right;">{{ $data['count'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Trust / Verification Highlights -->
                    <div class="col-md-3 border-md-start ps-md-4 text-center text-md-start">
                        <div class="d-flex align-items-center gap-2 mb-2 justify-content-center justify-content-md-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 34px; height: 34px;">
                                <i class="bi bi-patch-check-fill fs-5"></i>
                            </div>
                            <span class="fw-bold text-dark fs-6"><span class="lang-en">Verified Purchase</span><span class="lang-es">Compra Verificada</span></span>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.8rem;">
                            <span class="lang-en">Our badge highlights customers whose purchase was directly fulfilled and verified in Spotlight.</span>
                            <span class="lang-es">La insignia destaca a los compradores cuya orden fue confirmada y entregada en Spotlight.</span>
                        </p>
                        <div class="p-2 rounded-3 bg-light border text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-lock-fill text-primary me-1"></i> <span class="lang-en">Authentic community feedback</span><span class="lang-es">Comunidad transparente y cercana</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form to Submit or Edit Review -->
            <div id="form-review" class="vs-card border-0 shadow-sm p-4 p-md-5 mb-5 rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, #FAF5F6 100%);">
                @auth
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--sl-primary, #7F1D3F), var(--sl-accent, #D98C9A));">
                            <i class="bi bi-chat-quote-fill fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
                                @if($userReview)
                                    <span class="lang-en">Update your review for this product</span>
                                    <span class="lang-es">Actualiza tu calificación y reseña</span>
                                @else
                                    <span class="lang-en">Rate and share your thoughts</span>
                                    <span class="lang-es">Califica y comparte tu opinión sobre este producto</span>
                                @endif
                            </h4>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                <span class="lang-en">Logged in as:</span><span class="lang-es">Conectado como:</span> <strong>{{ auth()->user()->name }}</strong>
                                @if($userHasPurchased)
                                    <span class="badge bg-success-subtle text-success ms-2"><i class="bi bi-patch-check-fill me-1"></i> Comprador Verificado</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('public.product.review', $product) }}" method="POST">
                        @csrf
                        
                        <!-- Interactive Star Rating Input -->
                        <div class="mb-4 p-3 bg-white rounded-4 border shadow-xs">
                            <label class="form-label fw-bold text-dark d-block mb-1">
                                <span class="lang-en">Your rating (1 to 5 stars)</span><span class="lang-es">Tu calificación (1 a 5 estrellas)</span>:
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="star-rating-select">
                                    @php $currentRating = old('rating', $userReview ? $userReview->rating : 5); @endphp
                                    <input type="radio" id="star5" name="rating" value="5" {{ $currentRating == 5 ? 'checked' : '' }} required>
                                    <label for="star5" title="5 estrellas - Excelente"><i class="bi bi-star-fill"></i></label>

                                    <input type="radio" id="star4" name="rating" value="4" {{ $currentRating == 4 ? 'checked' : '' }}>
                                    <label for="star4" title="4 estrellas - Muy bueno"><i class="bi bi-star-fill"></i></label>

                                    <input type="radio" id="star3" name="rating" value="3" {{ $currentRating == 3 ? 'checked' : '' }}>
                                    <label for="star3" title="3 estrellas - Bueno"><i class="bi bi-star-fill"></i></label>

                                    <input type="radio" id="star2" name="rating" value="2" {{ $currentRating == 2 ? 'checked' : '' }}>
                                    <label for="star2" title="2 estrellas - Regular"><i class="bi bi-star-fill"></i></label>

                                    <input type="radio" id="star1" name="rating" value="1" {{ $currentRating == 1 ? 'checked' : '' }}>
                                    <label for="star1" title="1 estrella - Malo"><i class="bi bi-star-fill"></i></label>
                                </div>
                                <span id="rating-label-text" class="fw-bold" style="color: var(--sl-primary, #7F1D3F); font-size: 0.95rem;">
                                    ¡Excelente! (5 estrellas)
                                </span>
                            </div>
                        </div>

                        <!-- Review Comment Input -->
                        <div class="mb-4">
                            <label for="review-comment" class="form-label fw-bold text-dark mb-1">
                                <span class="lang-en">Your review and comments</span><span class="lang-es">Comentario y experiencia con el producto</span>:
                            </label>
                            <textarea name="comment" id="review-comment" rows="4" class="form-control rounded-4 p-3 shadow-xs" placeholder="Describe los acabados, calidad, textura, tiempo de entrega o atención del creador. Tu comentario ayuda a la comunidad de emprendedores..." required>{{ old('comment', $userReview ? $userReview->comment : '') }}</textarea>
                            <div class="form-text text-muted mt-2" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Tu reseña será visible públicamente para otros compradores.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-spotlight rounded-pill px-5 py-3 fw-bold shadow-sm d-flex align-items-center gap-2">
                                <i class="bi bi-send-fill"></i>
                                <span class="lang-en">Publish Review</span>
                                <span class="lang-es">{{ $userReview ? 'Actualizar mi Reseña' : 'Publicar Calificación' }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 70px; height: 70px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F);">
                            <i class="bi bi-star-half fs-2"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif;">
                            <span class="lang-en">Did you buy or try this product?</span>
                            <span class="lang-es">¿Has probado o comprado este producto?</span>
                        </h4>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 520px; font-size: 0.9rem;">
                            <span class="lang-en">Log in with your customer account to rate this item and share your review with the creative community.</span>
                            <span class="lang-es">Inicia sesión con tu cuenta para calificar este producto con estrellas y dejar tu comentario sobre su calidad y entrega.</span>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('login') }}" class="btn btn-spotlight rounded-pill px-4 py-2 fw-bold shadow-sm">
                                <span class="lang-en">Log in to Review</span><span class="lang-es">Iniciar Sesión para Calificar</span>
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                                <span class="lang-en">Create Account</span><span class="lang-es">Crear Cuenta</span>
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- List of Reviews & Comments -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
                        <span class="lang-en">All Customer Comments ({{ $product->reviews_count }})</span>
                        <span class="lang-es">Comentarios de los Usuarios ({{ $product->reviews_count }})</span>
                    </h5>
                </div>

                @forelse($product->reviews as $review)
                    <div class="review-bubble p-4 p-md-4 mb-3">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <!-- User Avatar with dynamic gradient -->
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 44px; height: 44px; background: linear-gradient(135deg, #7F1D3F 0%, #D98C9A 100%); font-size: 1rem;">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $review->user->name ?? 'Usuario de Spotlight' }}</h6>
                                        @if($review->is_verified_purchase)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0" style="font-size: 0.65rem;">
                                                <i class="bi bi-patch-check-fill me-1"></i> <span class="lang-en">Verified Purchase</span><span class="lang-es">Compra Verificada</span>
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $review->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Star Rating -->
                            <div class="d-flex align-items-center gap-1 bg-light rounded-pill px-3 py-1 border shadow-2xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $review->rating ? 'star-filled' : 'star-empty' }}" style="font-size: 0.85rem;"></i>
                                @endfor
                                <span class="fw-bold text-dark text-xs ms-1">{{ $review->rating }}.0</span>
                            </div>
                        </div>

                        <!-- Review Text -->
                        <p class="text-dark mb-0 lh-base" style="font-size: 0.92rem;">
                            {{ $review->comment }}
                        </p>
                    </div>
                @empty
                    <div class="vs-card border-0 shadow-sm p-5 text-center rounded-4 bg-white">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 bg-light shadow-xs" style="width: 80px; height: 80px;">
                            <i class="bi bi-chat-heart text-muted fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">
                            <span class="lang-en">No reviews yet</span><span class="lang-es">Aún no hay reseñas para este producto</span>
                        </h5>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 460px; font-size: 0.88rem;">
                            <span class="lang-en">Be the first to rate this creation! Your feedback helps other shoppers and supports our creators.</span>
                            <span class="lang-es">¡Sé el primero en calificar esta creación! Tu opinión ayuda a otros compradores y respalda el trabajo de este emprendedor.</span>
                        </p>
                        @auth
                            <a href="#form-review" class="btn btn-spotlight rounded-pill px-4 py-2 fw-bold shadow-sm">
                                <span class="lang-en">Be the first to review</span><span class="lang-es">Sé el primero en calificar</span>
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>

            <!-- Related Products Section -->
            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
                <div class="pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="badge badge-coral mb-1">MÁS DEL MISMO CREADOR</span>
                            <h4 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
                                Otras creaciones de {{ $product->company->name }}
                            </h4>
                        </div>
                        <a href="{{ route('public.company', $product->company) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                            Ver catálogo completo
                        </a>
                    </div>

                    <div class="row g-4">
                        @foreach($relatedProducts as $rel)
                            <div class="col-sm-6 col-md-3">
                                <div class="vs-card h-100 border-0 bg-white rounded-4 shadow-sm hover-lift d-flex flex-column overflow-hidden">
                                    <div class="p-3 text-center" style="background: #FAF5F6;">
                                        <a href="{{ route('public.product', $rel) }}">
                                            <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="w-100" style="height: 140px; object-fit: contain;">
                                        </a>
                                    </div>
                                    <div class="p-3 d-flex flex-column flex-fill">
                                        <h6 class="fw-bold text-dark text-truncate mb-1">
                                            <a href="{{ route('public.product', $rel) }}" class="text-decoration-none text-dark">{{ $rel->name }}</a>
                                        </h6>
                                        <div class="d-flex align-items-center gap-1 mb-2">
                                            <i class="bi bi-star-fill star-filled text-xs"></i>
                                            <span class="fw-bold text-xs">{{ $rel->average_rating > 0 ? number_format($rel->average_rating, 1) : '5.0' }}</span>
                                            <span class="text-muted text-xs">({{ $rel->reviews_count }})</span>
                                        </div>
                                        <div class="fw-bold fs-6 mt-auto" style="color: var(--sl-primary, #7F1D3F);">
                                            ${{ number_format($rel->price, 0, ',', '.') }} <span class="text-muted text-xs">COP</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </section>

    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Language Toggle
    const btnLang = document.getElementById("btnSwitchLang");
    const html = document.documentElement;
    const savedLang = localStorage.getItem('spotlight-lang') || 'es';
    html.setAttribute("lang", savedLang);

    if (btnLang) {
        btnLang.addEventListener("click", () => {
            const current = html.getAttribute("lang");
            const nextLang = current === 'en' ? 'es' : 'en';
            html.setAttribute("lang", nextLang);
            localStorage.setItem('spotlight-lang', nextLang);
        });
    }

    // Rating Label dynamically updates when user clicks stars
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingLabel = document.getElementById('rating-label-text');
    const labelsMap = {
        '5': '¡Excelente! (5 estrellas)',
        '4': 'Muy bueno (4 estrellas)',
        '3': 'Bueno (3 estrellas)',
        '2': 'Regular (2 estrellas)',
        '1': 'Malo (1 estrella)'
    };

    ratingInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            if (ratingLabel && labelsMap[e.target.value]) {
                ratingLabel.textContent = labelsMap[e.target.value];
            }
        });
    });
});
</script>
@endpush
@endsection
