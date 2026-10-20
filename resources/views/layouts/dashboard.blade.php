@extends('layouts.app')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: var(--sl-font-body) !important;
        background-color: var(--sl-bg) !important;
        color: var(--sl-text-body) !important;
    }
    .active-dropdown-item {
        background-color: #F6E7EB !important;
        color: #7F1D3F !important;
        font-weight: 700 !important;
        border-radius: 8px;
    }
</style>
@endpush
@section('content')
@php
    $isUser = auth()->check() && auth()->user()->role === 'user';
    $isBusiness = auth()->check() && auth()->user()->role === 'business';
    $useDropdownNav = $isUser || $isBusiness;
@endphp
<div class="d-flex position-relative">
    @if(!$useDropdownNav)
    {{-- Sidebar Overlay for Mobile (Solo Admin) --}}
    <div class="vs-sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar (Azul Noche) --}}
    <div class="vs-sidebar d-flex flex-column p-0" id="sidebar">
        <div class="p-4 border-bottom" style="border-color: rgba(255, 255, 255, 0.08) !important;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Spotlight" style="height: 32px; object-fit: contain;">
                        <div class="text-start lh-1">
                            <div class="fw-bold fs-5 text-white" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">SPOTLIGHT</div>
                            <div style="font-size: 0.6rem; color: var(--sl-accent); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                                @if(auth()->user()->role === 'business')
                                    EMPRESAS & COMERCIO
                                @elseif(auth()->user()->role === 'admin')
                                    ADMINISTRACIÓN
                                @else
                                    ALDEA DIGITAL
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                <button class="btn btn-link text-white d-lg-none p-0 ms-2" id="sidebarClose">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            
            {{-- User card inside navy sidebar --}}
            <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-3" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.08);">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 36px; height: 36px; background-color: var(--sl-primary); font-size: 0.9rem; flex-shrink: 0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="fw-semibold text-white text-truncate lh-sm" style="font-size: 0.85rem;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 0.7rem; color: var(--sl-text-on-dark-muted);">
                        @if(auth()->user()->role === 'business')
                            Comercio Verificado
                        @elseif(auth()->user()->role === 'admin')
                            Administrador
                        @else
                            Comunidad Spotlight
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex-fill p-3 d-flex flex-column gap-1">
            @include('partials.sidebar')
        </nav>

        <div class="p-4 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm w-100 d-flex justify-content-center align-items-center gap-2 text-white border-0" style="background: rgba(255, 255, 255, 0.08); font-size: 0.85rem;">
                    <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Main Content Area --}}
    <div class="flex-fill" style="min-height: 100vh; width: 100%; overflow-y: auto; background-color: var(--sl-bg);">
        @if($useDropdownNav)
        {{-- Barra de Menú Desplegable para todos los paneles de Usuario y Empresa --}}
        <nav class="vs-topbar px-3 px-md-4 py-3 d-flex align-items-center justify-content-between shadow-xs border-bottom bg-white sticky-top" style="z-index: 1000; min-height: 70px;">
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <!-- Nombre de la página y Logo -->
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight" style="height: 38px; width: 38px; object-fit: contain;">
                    <span class="fw-bold fs-4 text-dark mb-0 d-none d-sm-inline" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">SPOTLIGHT</span>
                </a>

                <div class="vr mx-1 d-none d-sm-block text-muted" style="height: 24px; opacity: 0.25;"></div>

                <!-- Barra de menú como Menú Desplegable -->
                <div class="dropdown">
                    <button class="btn btn-sm rounded-pill d-flex align-items-center gap-2 px-3 py-2 fw-bold shadow-xs border-0 dropdown-toggle text-decoration-none" type="button" id="dropdownNavMenu" data-bs-toggle="dropdown" aria-expanded="false" style="background: #F6E7EB; color: #7F1D3F; font-size: 0.85rem; transition: all 0.2s ease;">
                        <i class="bi bi-grid-fill"></i>
                        <span>Menú</span>
                    </button>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-4 py-2 mt-2" aria-labelledby="dropdownNavMenu" style="min-width: 250px; box-shadow: 0 10px 30px rgba(127,29,63,0.12) !important;">
                        @if($isBusiness)
                            <li><h6 class="dropdown-header text-uppercase fw-bold" style="color: #7F1D3F; font-size: 0.68rem; letter-spacing: 0.5px;">Panel de Empresa</h6></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.company*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.company.edit') }}">
                                    <i class="bi bi-building fs-5"></i>
                                    <span>Mi empresa</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.products*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.products.index') }}">
                                    <i class="bi bi-box-seam fs-5"></i>
                                    <span>Mis productos</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.expenses*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.expenses.index') }}">
                                    <i class="bi bi-cash-stack fs-5"></i>
                                    <span>Ventas y egresos</span>
                                </a>
                            </li>
                            @if(auth()->user()->company && auth()->user()->company->isColombian())
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.billing*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.billing.index') }}">
                                    <i class="bi bi-receipt-cutoff fs-5"></i>
                                    <span>Facturación DIAN</span>
                                </a>
                            </li>
                            @endif
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.shipments*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.shipments.index') }}">
                                    <i class="bi bi-truck fs-5"></i>
                                    <span>Despachos y Envíos</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->is('chat*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('chat.index') }}">
                                    <i class="bi bi-chat-dots fs-5"></i>
                                    <span>Mensajes</span>
                                </a>
                            </li>
                        @else
                            <li><h6 class="dropdown-header text-uppercase fw-bold" style="color: #7F1D3F; font-size: 0.68rem; letter-spacing: 0.5px;">Navegación</h6></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.dashboard') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.dashboard') }}">
                                    <i class="bi bi-shop fs-5"></i>
                                    <span>Tienda / Mercado</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.panel') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.panel') }}">
                                    <i class="bi bi-person-badge fs-5 text-muted"></i>
                                    <span>Mi Perfil</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.map') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.map') }}">
                                    <i class="bi bi-geo-alt fs-5 text-muted"></i>
                                    <span>Mapa de Empresas</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.cart*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.cart.index') }}">
                                    <i class="bi bi-cart3 fs-5 text-muted"></i>
                                    <span>Carrito de Compras</span>
                                    @if(session('cart') && count(session('cart')) > 0)
                                        <span class="badge rounded-pill ms-auto" style="background: #7F1D3F; color: #fff;">{{ count(session('cart')) }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.assisted_purchases*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.assisted_purchases.index') }}">
                                    <i class="bi bi-globe-americas fs-5 text-muted"></i>
                                    <span>Mis Solicitudes Int.</span>
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                    <i class="bi bi-box-arrow-left fs-5"></i>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Derecha: Menú de Cuenta y Carrito si es User -->
            <div class="d-flex align-items-center gap-2 gap-sm-3">
                @if($isBusiness)
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white border rounded-pill d-flex align-items-center gap-2 px-3 py-2 shadow-xs fw-semibold dropdown-toggle" type="button" id="businessAccountMenu" data-bs-toggle="dropdown" aria-expanded="false" style="color: #29252A; font-size: 0.85rem; border-color: #e2e8f0;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 28px; height: 28px; background: #7F1D3F; font-size: 0.78rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2" aria-labelledby="businessAccountMenu" style="min-width: 220px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark text-truncate">{{ auth()->user()->name }}</div>
                                <div class="text-muted small text-truncate">{{ auth()->user()->email }}</div>
                                <span class="badge rounded-pill mt-1" style="background: #F6E7EB; color: #7F1D3F; font-size: 0.65rem;">COMERCIO</span>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ request()->routeIs('business.company*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.company.edit') }}">
                                    <i class="bi bi-building"></i> Mi Empresa
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ request()->routeIs('business.products*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.products.index') }}">
                                    <i class="bi bi-box-seam"></i> Mis Productos
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                        <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white border rounded-pill d-flex align-items-center gap-2 px-3 py-2 shadow-xs fw-semibold dropdown-toggle" type="button" id="userAccountMenu" data-bs-toggle="dropdown" aria-expanded="false" style="color: #29252A; font-size: 0.85rem; border-color: #e2e8f0;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 28px; height: 28px; background: #7F1D3F; font-size: 0.78rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2" aria-labelledby="userAccountMenu" style="min-width: 200px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark text-truncate">{{ auth()->user()->name }}</div>
                                <div class="text-muted small text-truncate">{{ auth()->user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ request()->routeIs('user.panel') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.panel') }}"><i class="bi bi-person-circle"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <!-- Botón del Carrito -->
                    <a href="{{ route('user.cart.index') }}" class="btn btn-sm rounded-pill d-flex align-items-center gap-2 px-3 py-2 shadow-xs fw-bold text-decoration-none" style="background: #ffffff; border: 1.5px solid #D98C9A; color: #7F1D3F; font-size: 0.85rem; transition: all 0.2s ease;">
                        <i class="bi bi-cart3 fs-5" style="color: #7F1D3F;"></i>
                        <span class="d-none d-md-inline">Carrito</span>
                        @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
                        <span class="badge rounded-pill cart-counter-badge" style="background: #7F1D3F; color: #ffffff; font-size: 0.72rem; padding: 4px 7px;">
                            {{ $cartCount }}
                        </span>
                    </a>
                @endif
            </div>
        </nav>
        @else
        {{-- Barra para Administrador (mantiene sidebar) --}}
        <nav class="vs-topbar d-flex align-items-center gap-3">
            <button class="btn btn-link text-dark d-lg-none p-0" id="sidebarToggle">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="text-muted fw-medium" style="font-size: 0.85rem;">Spotlight Admin</span>
                <span class="text-muted">/</span>
                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.95rem;">@yield('page-title', 'Panel')</h6>
            </div>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted small d-none d-sm-inline fw-medium bg-white px-3 py-1 rounded-pill border">{{ auth()->user()->email }}</span>
            </div>
        </nav>
        @endif
        <div class="p-4 p-lg-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 rounded-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3"><i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-warning alert-dismissible fade show border-0 rounded-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error al guardar:</strong> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('dashboard-content')
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        }

        if(sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if(sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
        if(sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
    });
</script>
@endpush
@endsection
