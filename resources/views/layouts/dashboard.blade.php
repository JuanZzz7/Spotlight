@extends('layouts.app')
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
        <div class="p-4 border-bottom vs-sidebar-header">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Spotlight" class="vs-sidebar-logo-img">
                        <div class="text-start lh-1">
                            <div class="fw-bold fs-5 text-white vs-sidebar-brand-title">SPOTLIGHT</div>
                            <div class="vs-sidebar-brand-subtitle">
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
            <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-3 vs-sidebar-user-card">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold vs-sidebar-user-avatar">
                    {{ auth()->user()->display_initial }}
                </div>
                <div class="overflow-hidden">
                    <div class="fw-semibold text-white text-truncate lh-sm vs-sidebar-user-name">{{ auth()->user()->display_name }}</div>
                    <div class="vs-sidebar-user-role">
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

        <nav class="flex-fill p-3 d-flex flex-column gap-1 vs-sidebar-nav-scroll">
            @include('partials.sidebar')
        </nav>

        <div class="p-4 border-top vs-sidebar-header flex-shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm w-100 d-flex justify-content-center align-items-center gap-2 text-white border-0 vs-sidebar-logout-btn">
                    <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Main Content Area --}}
    <div class="flex-fill vs-main-content">
        @if($useDropdownNav)
        {{-- Barra de Menú Superior Equilibrada y Responsive para Usuario y Empresa --}}
        <nav class="vs-topbar vs-topbar-dropdown px-2 px-sm-3 px-md-4 py-2 d-flex align-items-center justify-content-between shadow-xs border-bottom bg-white sticky-top" style="min-height: 64px; max-height: 74px;">
            <div class="d-flex align-items-center gap-2 gap-md-3 min-w-0">
                <!-- Nombre de la página y Logo con dimensiones blindadas -->
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none flex-shrink-0">
                    <img src="{{ asset('images/logo-symbol-burgundy.png') }}"
                         alt="Spotlight"
                         width="36"
                         height="36"
                         class="vs-topbar-logo-symbol"
                         style="width: 36px !important; height: 36px !important; max-width: 36px !important; max-height: 36px !important; object-fit: contain; flex-shrink: 0;">
                    <span class="fw-bold fs-5 text-dark mb-0 d-none d-sm-inline vs-topbar-brand-title" style="letter-spacing: -0.5px;">SPOTLIGHT</span>
                </a>

                <div class="vr mx-1 d-none d-sm-block text-muted vs-topbar-vr"></div>

                <!-- Barra de menú como Menú Desplegable -->
                <div class="dropdown">
                    <button class="btn btn-sm rounded-pill d-flex align-items-center gap-1.5 px-2.5 px-sm-3 py-1.5 fw-bold shadow-xs border-0 dropdown-toggle text-decoration-none vs-dropdown-toggle-btn" type="button" id="dropdownNavMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-fill"></i>
                        <span>Menú</span>
                    </button>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-4 py-2 mt-2 vs-dropdown-menu" aria-labelledby="dropdownNavMenu">
                        @if($isBusiness)
                            <li><h6 class="dropdown-header text-uppercase fw-bold vs-dropdown-header">Panel de Empresa</h6></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.dashboard*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.dashboard') }}">
                                    <i class="bi bi-grid fs-5"></i>
                                    <span>Mi Panel</span>
                                </a>
                            </li>
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
                            @if(auth()->user()->company && auth()->user()->company->isColombian())
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.expenses*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.expenses.index') }}">
                                    <i class="bi bi-cash-stack fs-5"></i>
                                    <span>Ventas y egresos</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.billing*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.billing.index') }}">
                                    <i class="bi bi-receipt-cutoff fs-5"></i>
                                    <span>Facturación DIAN</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->company && auth()->user()->company->isInformal())
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.formalization*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.formalization.guide') }}">
                                    <i class="bi bi-mortarboard fs-5"></i>
                                    <span>Tutoriales de Formalización</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->company && auth()->user()->company->isColombian())
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('business.shipments*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.shipments.index') }}">
                                    <i class="bi bi-truck fs-5"></i>
                                    <span>Despachos y Envíos</span>
                                </a>
                            </li>
                            @endif
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->is('chat*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('chat.index') }}">
                                    <i class="bi bi-chat-dots fs-5"></i>
                                    <span>Mensajes</span>
                                </a>
                            </li>
                        @else
                            <li><h6 class="dropdown-header text-uppercase fw-bold vs-dropdown-header">Navegación</h6></li>
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
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.orders*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.orders.index') }}">
                                    <i class="bi bi-truck fs-5 text-muted"></i>
                                    <span>Mis Compras y Rastreo</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.cart*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.cart.index') }}">
                                    <i class="bi bi-cart3 fs-5 text-muted"></i>
                                    <span>Carrito de Compras</span>
                                    @if(session('cart') && count(session('cart')) > 0)
                                        <span class="badge rounded-pill ms-auto bg-sl-primary text-white">{{ count(session('cart')) }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->routeIs('user.assisted_purchases*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.assisted_purchases.index') }}">
                                    <i class="bi bi-globe-americas fs-5 text-muted"></i>
                                    <span>Mis Solicitudes Int.</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold {{ request()->is('chat*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('chat.index') }}">
                                    <i class="bi bi-chat-dots fs-5 text-muted"></i>
                                    <span>Mensajes</span>
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

            <!-- Derecha: Selector de Idioma, Menú de Cuenta y Carrito si es User -->
            <div class="d-flex align-items-center gap-1.5 gap-sm-2 flex-shrink-0">
                <button id="btnSwitchLang" type="button" class="btn btn-sm rounded-pill d-flex align-items-center gap-1 px-2.5 px-sm-3 py-1.5 shadow-xs fw-bold js-switch-lang vs-lang-switch-btn">
                    <i class="bi bi-translate"></i>
                    <span>ES / EN</span>
                </button>

                @if($isBusiness)
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white border rounded-pill d-flex align-items-center gap-2 px-2 px-sm-3 py-1.5 shadow-xs fw-semibold dropdown-toggle vs-account-toggle-btn" type="button" id="businessAccountMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(auth()->user()->display_logo)
                                <img src="{{ auth()->user()->display_logo }}" alt="{{ auth()->user()->display_name }}" width="26" height="26" class="rounded-circle shadow-xs flex-shrink-0" style="width: 26px !important; height: 26px !important; object-fit: cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs vs-account-avatar flex-shrink-0" style="width: 26px; height: 26px;">
                                    {{ auth()->user()->display_initial }}
                                </div>
                            @endif
                            <span class="d-none d-sm-inline text-truncate" style="max-width: 150px;">{{ auth()->user()->display_name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2 vs-account-dropdown-menu" aria-labelledby="businessAccountMenu">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark text-truncate">{{ auth()->user()->display_name }}</div>
                                <div class="text-muted small text-truncate">{{ auth()->user()->email }}</div>
                                <span class="badge rounded-pill mt-1 vs-role-badge">COMERCIO</span>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ request()->routeIs('business.dashboard*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('business.dashboard') }}">
                                    <i class="bi bi-grid"></i> Mi Panel
                                </a>
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
                        <button class="btn btn-sm btn-white border rounded-pill d-flex align-items-center gap-2 px-2 px-sm-3 py-1.5 shadow-xs fw-semibold dropdown-toggle vs-account-toggle-btn" type="button" id="userAccountMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs vs-account-avatar flex-shrink-0" style="width: 26px; height: 26px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-sm-inline text-truncate" style="max-width: 140px;">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2 vs-account-dropdown-menu" aria-labelledby="userAccountMenu">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark text-truncate">{{ auth()->user()->name }}</div>
                                <div class="text-muted small text-truncate">{{ auth()->user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ request()->routeIs('user.panel') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.panel') }}"><i class="bi bi-person-circle"></i> Mi Perfil</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ request()->routeIs('user.orders*') ? 'active-dropdown-item' : 'text-dark' }}" href="{{ route('user.orders.index') }}"><i class="bi bi-truck"></i> Mis Compras</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <div class="vr mx-1 d-none d-sm-block text-muted vs-topbar-vr"></div>

                    <!-- Botón del Carrito -->
                    <a href="{{ route('user.cart.index') }}" class="btn btn-sm rounded-pill d-flex align-items-center gap-1.5 px-2.5 px-sm-3 py-1.5 shadow-xs fw-bold text-decoration-none vs-cart-topbar-btn">
                        <i class="bi bi-cart3 fs-6 text-sl-primary"></i>
                        <span class="d-none d-md-inline">Carrito</span>
                        @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
                        <span class="badge rounded-pill cart-counter-badge vs-cart-counter-badge">
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
                <span class="text-muted fw-medium vs-admin-topbar-label">Spotlight Admin</span>
                <span class="text-muted">/</span>
                <h6 class="mb-0 fw-bold text-dark text-truncate vs-admin-topbar-title">@yield('page-title', 'Panel')</h6>
            </div>
            
            <div class="ms-auto d-flex align-items-center gap-2 gap-sm-3">
                <button type="button" class="btn btn-sm rounded-pill d-flex align-items-center gap-1 px-3 py-1 shadow-xs fw-bold js-switch-lang vs-lang-switch-btn">
                    <i class="bi bi-translate me-1"></i>
                    <span>ES / EN</span>
                </button>
                <span class="text-muted small d-none d-sm-inline fw-medium bg-white px-3 py-1 rounded-pill border">{{ auth()->user()->email }}</span>
            </div>
        </nav>
        @endif
        <div class="p-3 p-sm-4 p-lg-5">
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
