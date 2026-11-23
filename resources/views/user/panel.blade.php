@extends('layouts.dashboard')
@section('title', 'Mi Perfil - Spotlight')
@section('page-title', 'Mi Perfil')

@push('styles')
<link href="{{ asset('css/user-panel.css') }}?v=2.5" rel="stylesheet">
@endpush

@section('dashboard-content')
@php
    $user = auth()->user();
    $firstName = explode(' ', $user->name)[0];
    $orders = $orders ?? collect();
    $latestOrder = $latestOrder ?? $orders->first();

    $activeOrders = $orders->filter(function ($o) {
        $st = $o->order_status ?: $o->status;
        $shipSt = $o->shipment?->status;
        if ($shipSt === 'delivered' || in_array($st, ['delivered', 'cancelled', 'failed', 'refunded'])) {
            return false;
        }
        return true;
    });
    $activeOrdersCount = $activeOrders->count();
    $latestActiveOrder = $activeOrders->first();

    $completedOrders = $orders->filter(function ($o) {
        $st = $o->order_status ?: $o->status;
        return !in_array($st, ['cancelled', 'failed', 'refunded']);
    });
    $monthOrders = $completedOrders->filter(fn($o) => $o->created_at && $o->created_at->isCurrentMonth());
    $monthTotal = (float) $monthOrders->sum('total');
    $totalProductsBought = (int) $completedOrders->sum(fn($o) => $o->details ? $o->details->sum('quantity') : 0);
    $pointsAvailable = (int) floor($completedOrders->sum('total') / 1000);
@endphp

<div class="profile-dashboard-wrapper">

    {{-- 1. Saludo y Acciones Principales --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="font-outfit fw-bold mb-1 up-greeting-title" id="greetingHeader">
                ¡Hola, <span class="user-first-name-span">{{ $firstName }}</span>!
            </h1>
            <p class="mb-0 text-muted up-fs-14">
                Gestiona tus compras, pedidos en curso, comercios favoritos y beneficios de la comunidad Spotlight.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('user.orders.index') }}" class="btn btn-sl-outlined">
                <i class="bi bi-truck me-1"></i> Mis Compras y Rastreo
            </a>
            <a href="{{ route('user.dashboard') }}" class="btn btn-sl-primary">
                <i class="bi bi-shop"></i> Explorar Marketplace
            </a>
        </div>
    </div>

    {{-- 2. Cabecera de Perfil Ligera y Editable (Sin bloque oscuro ni cliente nivel plata) --}}
    <div class="sl-card p-4 p-md-4 mb-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <!-- Avatar interactivo -->
                <div class="position-relative">
                    <div id="mainAvatarDisplay" class="user-avatar-circle overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover" id="mainAvatarImg">
                        @else
                            <span id="mainAvatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <button type="button" class="avatar-edit-trigger-badge" title="Cambiar foto de perfil" onclick="openProfileDrawer(true)" aria-label="Cambiar foto">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                </div>

                <!-- Datos del usuario (Nombre y correo, sin nivel plata) -->
                <div>
                    <h4 class="font-outfit fw-bold mb-1 up-user-name-title" id="mainUserNameDisplay">
                        {{ $user->name }}
                    </h4>
                    <div class="text-muted up-text-13">
                        <i class="bi bi-envelope me-1"></i> <span id="mainUserEmailDisplay">{{ $user->email }}</span>
                    </div>
                </div>
            </div>

            <!-- Botón para abrir el panel de edición -->
            <div>
                <button type="button" class="btn btn-sl-secondary w-100 w-sm-auto" onclick="openProfileDrawer()">
                    <i class="bi bi-pencil-square"></i> Editar perfil
                </button>
            </div>
        </div>
    </div>

    {{-- 3. Pedido en Camino (Foco Principal Destacado) --}}
    <div class="sl-card p-3 p-sm-4 mb-4" id="recentOrderCard">
        @if($latestActiveOrder)
            @php
                $firstDetail = $latestActiveOrder->details?->first();
                $orderProductName = $firstDetail?->effective_product_name ?? $firstDetail?->product?->name ?? ('Pedido #' . $latestActiveOrder->id);
                $orderPrice = '$' . number_format($latestActiveOrder->total, 0, ',', '.') . ' ' . ($latestActiveOrder->currency ?? 'COP');
                $totalUnits = $latestActiveOrder->details ? $latestActiveOrder->details->sum('quantity') : 1;
                $orderQty = $totalUnits . ($totalUnits == 1 ? ' unidad' : ' unidades');
                $orderCompanyName = $latestActiveOrder->company?->name ?? ($firstDetail?->product?->company?->name ?? 'Comercio Local');
                $shipStatus = $latestActiveOrder->shipment?->status ?? ($latestActiveOrder->order_status ?: $latestActiveOrder->status);
                $stepIndex = match($shipStatus) {
                    'pending', 'paid', 'approved', 'confirmed' => 1,
                    'preparing', 'in_preparation', 'processing' => 2,
                    'dispatched', 'shipped', 'in_transit', 'out_for_delivery' => 3,
                    'delivered' => 4,
                    default => 1,
                };
            @endphp
            <!-- Encabezado del pedido -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom up-border-soft">
                <div class="d-flex align-items-center gap-2">
                    <span class="sl-badge-subtle up-badge-live">
                        <i class="bi bi-broadcast text-sl-primary"></i> Seguimiento en vivo
                    </span>
                    <span class="fw-bold up-meta-13">
                        Orden #{{ $latestActiveOrder->reference ?? ('SP-' . str_pad($latestActiveOrder->id, 4, '0', STR_PAD_LEFT)) }}
                    </span>
                </div>
                <span class="text-muted up-text-12">Despacho directo aliado</span>
            </div>

            <h3 class="font-outfit fw-bold mb-3 up-order-title">
                Tu pedido más reciente está en curso
            </h3>

            <!-- Ficha compacta de producto y comercio -->
            <div class="p-3 rounded-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 up-order-summary-box">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-white d-flex align-items-center justify-content-center shadow-xs up-order-icon-box flex-shrink-0">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <h6 class="fw-bold mb-1 up-text-14 text-truncate">
                            {{ $orderProductName }}
                        </h6>
                        <div class="d-flex flex-wrap align-items-center gap-2 up-meta-13">
                            <span class="fw-bold text-sl-primary">{{ $orderPrice }}</span>
                            <span>•</span>
                            <span>{{ $orderQty }}</span>
                            <span>•</span>
                            <span class="fw-semibold">{{ $orderCompanyName }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción del pedido -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('user.orders.tracking', $latestActiveOrder) }}" class="btn btn-sl-primary btn-sm">
                        <i class="bi bi-truck me-1"></i> Rastrear en tiempo real
                    </a>
                    <a href="{{ route('user.orders.index') }}" class="btn btn-sl-outlined btn-sm">
                        <i class="bi bi-receipt me-1"></i> Mis compras ({{ $orders->count() }})
                    </a>
                    <a href="{{ route('chat.index') }}" class="btn btn-sl-outlined btn-sm">
                        <i class="bi bi-chat-dots"></i> Mensaje a tienda
                    </a>
                </div>
            </div>

            @if($latestActiveOrder->shipment)
                <!-- Barra de progreso en vivo del envío -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between flex-wrap gap-1 text-xs fw-bold text-muted mb-2">
                        <span><i class="bi bi-box-seam me-1"></i>Preparación</span>
                        <span><i class="bi bi-truck me-1"></i>Despacho</span>
                        <span><i class="bi bi-arrow-left-right me-1"></i>En Tránsito</span>
                        <span><i class="bi bi-bicycle me-1"></i>En Reparto</span>
                        <span><i class="bi bi-check-circle-fill me-1"></i>Entregado</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 8px; background-color: #f1f5f9;">
                        <div class="progress-bar" style="width: {{ $latestActiveOrder->shipment->progress_percentage ?? 20 }}%; background: linear-gradient(90deg, var(--sl-primary), var(--sl-secondary)); border-radius: 8px;"></div>
                    </div>
                    @if($latestActiveOrder->shipment->tracking_number)
                        <div class="d-flex justify-content-between align-items-center pt-2 flex-wrap gap-2" style="font-size: 12px; color: var(--sl-text-muted);">
                            <span><i class="bi bi-shield-check text-success me-1"></i> Envío asegurado y verificado por Spotlight.</span>
                            <a href="{{ route('public.tracking', ['guia' => $latestActiveOrder->shipment->tracking_number]) }}" target="_blank" class="text-decoration-none fw-semibold text-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Ver en portal público de rastreo
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Barra de progreso simple de 4 pasos -->
            <div class="position-relative pt-2 pb-2 mb-3">
                <div class="order-stepper-track">
                    <div class="order-stepper-progress" style="width: {{ ($stepIndex - 1) * 33 }}%;"></div>
                </div>

                <div class="d-flex justify-content-between position-relative z-1">
                    <!-- Paso 1 -->
                    <div class="stepper-node">
                        <div class="stepper-circle {{ $stepIndex > 1 ? 'completed' : ($stepIndex === 1 ? 'active' : 'pending') }}">
                            <i class="bi {{ $stepIndex > 1 ? 'bi-check-lg' : 'bi-receipt' }} fs-5"></i>
                        </div>
                        <div class="fw-bold up-label-13">Confirmado</div>
                        <div class="text-muted up-text-11">{{ $latestActiveOrder->created_at?->format('d/m H:i') }}</div>
                    </div>

                    <!-- Paso 2 -->
                    <div class="stepper-node">
                        <div class="stepper-circle {{ $stepIndex > 2 ? 'completed' : ($stepIndex === 2 ? 'active' : 'pending') }}">
                            <i class="bi {{ $stepIndex > 2 ? 'bi-check-lg' : 'bi-box-seam' }} fs-5"></i>
                        </div>
                        <div class="fw-bold up-label-13">Preparando</div>
                        <div class="text-muted up-text-11">{{ $stepIndex >= 2 ? 'En comercio' : 'Pendiente' }}</div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="stepper-node">
                        <div class="stepper-circle {{ $stepIndex > 3 ? 'completed' : ($stepIndex === 3 ? 'active' : 'pending') }}">
                            <i class="bi bi-truck fs-5"></i>
                        </div>
                        <div class="fw-bold {{ $stepIndex === 3 ? 'up-step-active' : 'up-label-13' }}">En ruta</div>
                        @if($stepIndex === 3)
                            <span class="sl-badge-subtle py-0 px-2 up-text-10">Ahora</span>
                        @else
                            <div class="text-muted up-text-11">{{ $stepIndex > 3 ? 'Completado' : 'Por despachar' }}</div>
                        @endif
                    </div>

                    <!-- Paso 4 -->
                    <div class="stepper-node">
                        <div class="stepper-circle {{ $stepIndex === 4 ? 'completed' : 'pending' }}">
                            <i class="bi bi-check2-circle fs-6"></i>
                        </div>
                        <div class="fw-semibold text-muted up-text-13">Entrega</div>
                        <div class="text-muted up-text-11">Destino final</div>
                    </div>
                </div>
            </div>

            <div class="pt-2 text-center text-md-start up-note">
                <i class="bi bi-info-circle me-1 up-icon-secondary"></i> Servicio de mensajería directa aliado con seguimiento desde tu cuenta.
            </div>
        @else
            <!-- Estado limpio en 0 para cuentas recién registradas o sin envíos activos -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom up-border-soft">
                <div class="d-flex align-items-center gap-2">
                    <span class="sl-badge-subtle">
                        <i class="bi bi-truck text-sl-primary me-1"></i> 0 envíos en curso
                    </span>
                </div>
                <span class="text-muted up-text-12">Estado logístico al día</span>
            </div>

            <div class="p-3 p-md-4 rounded-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 up-order-summary-box">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-white d-flex align-items-center justify-content-center shadow-xs up-order-icon-box flex-shrink-0">
                        <i class="bi bi-box2-heart fs-4 text-sl-primary"></i>
                    </div>
                    <div>
                        <h5 class="font-outfit fw-bold mb-1 text-dark" style="font-size: 1.05rem;">
                            No tienes envíos ni pedidos activos en este momento
                        </h5>
                        <p class="mb-0 text-muted up-text-13">
                            Tus contadores inician en 0. Cuando realices una compra en Spotlight, aquí podrás rastrear el despacho, la transportadora y la guía en tiempo real.
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sl-primary btn-sm px-3 py-2">
                        <i class="bi bi-shop me-1"></i> Explorar productos
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- 4. Fila de Métricas (4 tarjetas unificadas con datos reales en 0 para cuentas nuevas) --}}
    <div class="row g-3 mb-4">
        <!-- 1. Pedidos en curso -->
        <div class="col-sm-6 col-lg-3">
            <div class="sl-card p-3 p-md-3 h-100 d-flex flex-column">
                <div class="text-muted mb-2 up-metric-label">Pedidos en curso</div>
                <div class="metric-value-num mb-2">{{ $activeOrdersCount }} <span class="up-metric-unit">en curso</span></div>
                <div class="mt-auto pt-2 border-top up-metric-foot">
                    @if($latestActiveOrder)
                        Último de <strong>{{ $latestActiveOrder->company?->name ?? 'Comercio Local' }}</strong>
                    @else
                        Sin envíos activos
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Compras del mes -->
        <div class="col-sm-6 col-lg-3">
            <div class="sl-card p-3 p-md-3 h-100 d-flex flex-column">
                <div class="text-muted mb-2 up-metric-label">Compras del mes</div>
                <div class="metric-value-num mb-2">${{ number_format($monthTotal, 0, ',', '.') }} <span class="up-metric-unit">COP</span></div>
                <div class="mt-auto pt-2 border-top up-metric-foot">
                    {{ $monthOrders->count() }} {{ $monthOrders->count() === 1 ? 'orden registrada este mes' : 'órdenes registradas este mes' }}
                </div>
            </div>
        </div>

        <!-- 3. Garantías activas -->
        <div class="col-sm-6 col-lg-3">
            <div class="sl-card p-3 p-md-3 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted up-metric-label">Garantías activas</span>
                    <span class="sl-badge-subtle">{{ $totalProductsBought > 0 ? '100% protegido' : '0 activos' }}</span>
                </div>
                <div class="metric-value-num mb-2">{{ $totalProductsBought }} <span class="up-metric-unit">{{ $totalProductsBought === 1 ? 'producto' : 'productos' }}</span></div>
                <div class="mt-auto pt-2 border-top up-metric-foot">
                    {{ $totalProductsBought > 0 ? 'Respaldados por comercios locales' : '0 productos adquiridos' }}
                </div>
            </div>
        </div>

        <!-- 4. Puntos disponibles -->
        <div class="col-sm-6 col-lg-3">
            <div class="sl-card p-3 p-md-3 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted up-metric-label">Puntos disponibles</span>
                    <span class="sl-badge-subtle">Canjeable</span>
                </div>
                <div class="metric-value-num mb-2">{{ number_format($pointsAvailable, 0, ',', '.') }} <span class="up-metric-unit">pts</span></div>
                <div class="mt-auto pt-2 border-top up-metric-foot">
                    ≈ ${{ number_format($pointsAvailable * 10, 0, ',', '.') }} COP en canjes
                </div>
            </div>
        </div>
    </div>

    {{-- 5. Parte Inferior en Dos Columnas --}}
    <div class="row g-4">
        <!-- Columna Izquierda: Historial de Compras Recientes -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-outfit fw-bold mb-0 up-section-title">
                    Historial de compras recientes
                </h5>
                <span class="text-muted up-text-13">{{ $orders->count() }} {{ $orders->count() === 1 ? 'compra total' : 'compras totales' }}</span>
            </div>

            <div class="sl-card p-3 mb-3">
                <div class="d-flex flex-column gap-2">
                    @forelse($orders->take(5) as $ord)
                        @php
                            $ordFirstDetail = $ord->details?->first();
                            $ordTitle = $ordFirstDetail?->effective_product_name ?? $ordFirstDetail?->product?->name ?? ('Orden #' . $ord->id);
                            $extraItems = $ord->details ? max(0, $ord->details->count() - 1) : 0;
                            if ($extraItems > 0) {
                                $ordTitle .= ' (+' . $extraItems . ' más)';
                            }
                            $ordCompany = $ord->company?->name ?? ($ordFirstDetail?->product?->company?->name ?? 'Comercio Aliado');
                            $ordStatus = $ord->shipment?->status ?? ($ord->order_status ?: $ord->status);
                            $isDelivered = $ordStatus === 'delivered';
                        @endphp
                        <div class="p-3 rounded-3 up-history-item">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                <div>
                                    <h6 class="fw-bold mb-1 up-text-14">
                                        {{ $ordTitle }}
                                    </h6>
                                    <div class="text-muted up-text-12">{{ $ordCompany }} • {{ $ord->created_at?->format('d/m/Y') }}</div>
                                </div>
                                @if($isDelivered)
                                    <span class="sl-badge-success">
                                        <i class="bi bi-check2"></i> Entregado
                                    </span>
                                @else
                                    <span class="sl-badge-subtle">
                                        <i class="bi bi-truck me-1"></i> {{ $ord->shipment?->status_label ?? 'En curso' }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top up-border-divider flex-wrap gap-2">
                                <div class="fw-bold up-text-14">
                                    ${{ number_format($ord->total, 0, ',', '.') }} <span class="text-muted fw-normal up-text-11">{{ $ord->currency ?? 'COP' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <a href="{{ route('user.orders.tracking', $ord) }}" class="text-muted text-decoration-none up-text-12">
                                        <i class="bi bi-geo-alt me-1"></i>Rastreo
                                    </a>
                                    <a href="{{ route('user.orders.index') }}" class="btn btn-sl-secondary btn-sm py-1 px-3">
                                        <i class="bi bi-eye"></i> Ver orden
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 48px; height: 48px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F);">
                                <i class="bi bi-bag-check fs-5"></i>
                            </div>
                            <h6 class="font-outfit fw-bold text-dark mb-1">0 compras registradas</h6>
                            <p class="text-muted up-text-12 mb-0">
                                Tu historial está en 0. Las órdenes que realices aparecerán aquí con su estado y factura.
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Mensaje de apoyo -->
                <div class="mt-3 pt-2 text-center text-muted up-text-13">
                    <i class="bi bi-heart me-1 text-sl-primary"></i> Tus compras apoyan directamente a talleres, productores y creadores independientes.
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Tiendas Frecuentes y Ayuda Ligera -->
        <div class="col-lg-5">
            @php
                $frequentCompanies = $orders->map(function ($o) {
                    return $o->company ?? $o->details?->first()?->product?->company;
                })->filter()->unique('id')->take(3);
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-outfit fw-bold mb-0 up-section-title">
                    Tus tiendas frecuentes
                </h5>
                <a href="{{ route('user.map') }}" class="text-decoration-none up-link-13">
                    Ver en mapa <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="d-flex flex-column gap-3 mb-4">
                @forelse($frequentCompanies as $favCompany)
                    <div class="sl-card p-3 d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center up-fav-store-icon">
                                <i class="bi bi-shop fs-5"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold mb-0 up-text-14">{{ $favCompany->name }}</h6>
                                    <span class="sl-badge-subtle py-0 px-2 up-text-11">Verificado</span>
                                </div>
                                <div class="text-muted up-text-12">{{ $favCompany->category ?: 'Comercio Local' }} • {{ $favCompany->city ?: 'Ubaté' }}</div>
                            </div>
                        </div>
                        <a href="{{ route('public.company', $favCompany) }}" class="btn btn-sl-outlined btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center up-fav-store-btn" title="Ver tienda">
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="sl-card p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 44px; height: 44px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F);">
                            <i class="bi bi-shop-window fs-5"></i>
                        </div>
                        <h6 class="font-outfit fw-bold text-dark mb-1">0 tiendas frecuentes</h6>
                        <p class="text-muted up-text-12 mb-3">
                            A medida que compres en los comercios de Spotlight, tus tiendas frecuentes aparecerán aquí para acceso rápido.
                        </p>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-sl-outlined btn-sm">
                            <i class="bi bi-compass me-1"></i> Descubrir tiendas
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Ayuda: Tarjeta Ligera en Fondo #F6E7EB -->
            <div class="sl-card p-4 up-help-card">
                <h5 class="font-outfit fw-bold mb-2 up-help-title">
                    ¿Tienes dudas sobre un producto?
                </h5>
                <p class="mb-3 up-help-desc">
                    Chatea directamente con los comerciantes desde tu Centro de Mensajes sin intermediarios.
                </p>
                <a href="{{ route('chat.index') }}" class="btn btn-sl-primary btn-sm">
                    Abrir Mensajes <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- PANEL LATERAL (DRAWER) DE EDICIÓN DE PERFIL                              --}}
{{-- ========================================================================= --}}
<div class="profile-drawer-backdrop" id="profileDrawerBackdrop" onclick="handleDrawerBackdropClick()"></div>

<div class="profile-drawer-container" id="profileDrawerContainer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
    <!-- Cabecera del Drawer -->
    <div class="p-4 border-bottom d-flex align-items-center justify-content-between up-border-soft">
        <div>
            <h4 class="font-outfit fw-bold mb-0 up-drawer-title" id="drawerTitle">
                Editar perfil
            </h4>
            <span class="text-muted up-text-12">Configura tu foto y datos personales</span>
        </div>
        <button type="button" class="btn btn-sm btn-link text-muted p-1 text-decoration-none" onclick="attemptCloseDrawer()" aria-label="Cerrar">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <!-- Formulario de Edición (Scrollable) -->
    <div class="p-4 flex-fill overflow-y-auto">
        <form id="profileEditForm" autocomplete="off" novalidate onsubmit="submitProfileForm(event)">
            @csrf
            @method('PUT')
            <input type="hidden" name="remove_avatar" id="inputRemoveAvatar" value="0">

            <!-- Sección de Foto de Perfil -->
            <div class="text-center pb-4 mb-4 border-bottom up-border-soft">
                <div class="position-relative d-inline-block mb-3">
                    <div id="drawerAvatarPreviewWrap" class="user-avatar-circle up-drawer-avatar-lg">
                        @if($user->avatar)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover rounded-circle" id="drawerAvatarImg">
                        @else
                            <span id="drawerAvatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Input oculto para archivo de imagen -->
                <input type="file" id="avatarFileInput" name="avatar" accept="image/jpeg,image/png,image/webp" class="up-hidden-file" onchange="handleAvatarFileSelect(event)">

                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sl-secondary btn-sm" onclick="document.getElementById('avatarFileInput').click()">
                        <i class="bi bi-upload"></i> <span id="btnUploadPhotoText">{{ $user->avatar ? 'Cambiar foto' : 'Subir foto' }}</span>
                    </button>
                    <button type="button" class="btn btn-sl-outlined btn-sm text-danger {{ $user->avatar ? '' : 'up-hidden' }}" id="btnRemoveAvatar" onclick="removeUserAvatar()">
                        <i class="bi bi-trash3"></i> Quitar
                    </button>
                </div>
                <div id="avatarErrorMsg" class="text-danger mt-2 up-avatar-err"></div>
                <div class="text-muted mt-1 up-text-11">Formatos JPG, PNG o WebP. Máximo 2 MB.</div>
            </div>

            <!-- Campos de Datos Personales con tipografía consistente -->
            <div class="d-flex flex-column gap-3">
                <!-- Nombre y Apellidos -->
                <div>
                    <label class="form-label fw-bold mb-1 up-label-13" for="inputName">
                        Nombre y apellidos <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control drawer-form-control" id="inputName" name="name" value="{{ $user->name }}" placeholder="Ej: Ana María García" required oninput="handleFormInputChange()">
                    <div class="invalid-feedback up-text-11" id="valErrorName">El nombre y apellidos son obligatorios.</div>
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label class="form-label fw-bold mb-1 up-label-13" for="inputEmail">
                        Correo electrónico <span class="text-danger">*</span>
                    </label>
                    <input type="email" class="form-control drawer-form-control" id="inputEmail" name="email" value="{{ $user->email }}" placeholder="ana@ejemplo.com" required oninput="handleFormInputChange()">
                    <div class="invalid-feedback up-text-11" id="valErrorEmail">Ingresa un correo electrónico válido.</div>
                    <div class="text-muted mt-1 up-text-11">Se utiliza para acceder a tu cuenta y notificaciones de compras.</div>
                </div>

                <!-- Teléfono / WhatsApp (10 dígitos exactos) -->
                <div>
                    <label class="form-label fw-bold mb-1 up-label-13" for="inputPhone">
                        Teléfono / WhatsApp (10 dígitos)
                    </label>
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text bg-white border-end-0 drawer-form-control up-phone-prefix">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <input type="tel"
                               inputmode="numeric"
                               maxlength="10"
                               pattern="[0-9]{10}"
                               class="form-control drawer-form-control border-start-0 up-phone-input"
                               id="inputPhone"
                               name="phone"
                               value="{{ $user->phone }}"
                               placeholder="Ej: 3101234567"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10); handleFormInputChange();">
                    </div>
                    <div class="invalid-feedback up-text-11" id="valErrorPhone">Ingresa un número de teléfono válido de 10 dígitos.</div>
                </div>

                <!-- País y Ciudad -->
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold mb-1 up-label-13" for="inputCountry">
                            País
                        </label>
                        <input type="text" class="form-control drawer-form-control" id="inputCountry" name="country" value="{{ $user->country ?? 'Colombia' }}" placeholder="Ej: Colombia" oninput="handleFormInputChange()">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold mb-1 up-label-13" for="inputCity">
                            Ciudad
                        </label>
                        <input type="text" class="form-control drawer-form-control" id="inputCity" name="city" value="{{ $user->city ?? 'Ubaté' }}" placeholder="Ej: Ubaté" oninput="handleFormInputChange()">
                    </div>
                </div>

                <!-- Dirección de entrega principal -->
                <div>
                    <label class="form-label fw-bold mb-1 up-label-13" for="inputAddress">
                        Dirección de entrega principal
                    </label>
                    <input type="text" class="form-control drawer-form-control" id="inputAddress" name="address" value="{{ $user->address }}" placeholder="Ej: Cra. 7 # 10-25, Apto 301" oninput="handleFormInputChange()">
                    <div class="text-muted mt-1 up-text-11">Dirección predeterminada para envíos de pedidos.</div>
                </div>

                <!-- Fecha de Nacimiento (Opcional) -->
                <div>
                    <label class="form-label fw-bold mb-1 up-label-13" for="inputBirthdate">
                        Fecha de nacimiento <span class="text-muted fw-normal up-text-11">(opcional)</span>
                    </label>
                    <input type="date" class="form-control drawer-form-control" id="inputBirthdate" name="birthdate" value="{{ $user->birthdate ? $user->birthdate->format('Y-m-d') : '' }}" oninput="handleFormInputChange()">
                </div>
            </div>
        </form>
    </div>

    <!-- Botones de Acción al Final del Drawer -->
    <div class="p-4 border-top bg-white d-flex align-items-center justify-content-end gap-2 up-border-soft">
        <button type="button" class="btn btn-sl-outlined" onclick="attemptCloseDrawer()">
            Cancelar
        </button>
        <button type="submit" form="profileEditForm" id="btnSaveProfile" class="btn btn-sl-primary" disabled>
            <span id="btnSaveText">Guardar cambios</span>
            <span id="btnSaveSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>
</div>

{{-- Diálogo de confirmación para descartar cambios --}}
<div class="discard-confirm-dialog" id="discardConfirmModal">
    <div class="sl-card p-4 mx-3 up-discard-card">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center up-discard-icon">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </div>
            <div>
                <h5 class="font-outfit fw-bold mb-1 up-dialog-title">¿Descartar cambios?</h5>
                <p class="text-muted mb-0 up-text-13">Tienes modificaciones sin guardar que se perderán.</p>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-sl-outlined btn-sm" onclick="closeDiscardModal()">Seguir editando</button>
            <button type="button" class="btn btn-sl-primary btn-sm" onclick="forceCloseDrawer()">Descartar</button>
        </div>
    </div>
</div>

{{-- Toast flotante de notificación positiva --}}
<div class="profile-toast-alert" id="profileToast">
    <i class="bi bi-check-circle-fill fs-5 text-white"></i>
    <span id="profileToastMessage">Cambios guardados</span>
</div>

@push('scripts')
<script>
(function() {
    // Estado interno del formulario para detectar modificaciones
    let originalData = {
        name: @json($user->name),
        email: @json($user->email),
        phone: @json($user->phone ?? ''),
        country: @json($user->country ?? 'Colombia'),
        city: @json($user->city ?? 'Ubaté'),
        address: @json($user->address ?? ''),
        birthdate: @json($user->birthdate ? $user->birthdate->format('Y-m-d') : ''),
        hasAvatar: @json(!empty($user->avatar)),
        avatarUrl: @json($user->avatar_url)
    };

    let selectedAvatarFile = null;
    let pendingRemoveAvatar = false;
    let isSubmitting = false;

    // Abrir Drawer
    window.openProfileDrawer = function(focusAvatar = false) {
        document.getElementById('profileDrawerBackdrop').classList.add('show');
        document.getElementById('profileDrawerContainer').classList.add('show');
        document.body.style.overflow = 'hidden';

        if (focusAvatar) {
            setTimeout(() => {
                document.getElementById('avatarFileInput')?.click();
            }, 300);
        } else {
            setTimeout(() => {
                document.getElementById('inputName')?.focus();
            }, 300);
        }
        checkFormChanges();
    };

    // Intentar cerrar el Drawer con verificación de cambios pendientes
    window.attemptCloseDrawer = function() {
        if (hasUnsavedChanges()) {
            document.getElementById('discardConfirmModal').classList.add('show');
        } else {
            forceCloseDrawer();
        }
    };

    window.closeDiscardModal = function() {
        document.getElementById('discardConfirmModal').classList.remove('show');
    };

    window.forceCloseDrawer = function() {
        closeDiscardModal();
        document.getElementById('profileDrawerBackdrop').classList.remove('show');
        document.getElementById('profileDrawerContainer').classList.remove('show');
        document.body.style.overflow = '';
        resetFormToOriginal();
    };

    window.handleDrawerBackdropClick = function() {
        attemptCloseDrawer();
    };

    // Comprobar si hay cambios no guardados
    function hasUnsavedChanges() {
        if (selectedAvatarFile !== null) return true;
        if (pendingRemoveAvatar) return true;

        const curName = (document.getElementById('inputName')?.value || '').trim();
        const curEmail = (document.getElementById('inputEmail')?.value || '').trim();
        const curPhone = (document.getElementById('inputPhone')?.value || '').trim();
        const curCountry = (document.getElementById('inputCountry')?.value || '').trim();
        const curCity = (document.getElementById('inputCity')?.value || '').trim();
        const curAddress = (document.getElementById('inputAddress')?.value || '').trim();
        const curBirthdate = (document.getElementById('inputBirthdate')?.value || '').trim();

        return (
            curName !== originalData.name ||
            curEmail !== originalData.email ||
            curPhone !== originalData.phone ||
            curCountry !== originalData.country ||
            curCity !== originalData.city ||
            curAddress !== originalData.address ||
            curBirthdate !== originalData.birthdate
        );
    }

    // Validación en tiempo real y habilitación del botón guardar
    window.handleFormInputChange = function() {
        validateInputs();
        checkFormChanges();
    };

    function validateInputs() {
        let valid = true;
        const nameEl = document.getElementById('inputName');
        const emailEl = document.getElementById('inputEmail');
        const phoneEl = document.getElementById('inputPhone');

        // Nombre obligatorio
        if (!nameEl.value.trim()) {
            nameEl.classList.add('is-invalid');
            valid = false;
        } else {
            nameEl.classList.remove('is-invalid');
        }

        // Email válido
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailEl.value.trim())) {
            emailEl.classList.add('is-invalid');
            valid = false;
        } else {
            emailEl.classList.remove('is-invalid');
        }

        // Teléfono formato opcional
        if (phoneEl.value.trim() && phoneEl.value.trim().length < 7) {
            phoneEl.classList.add('is-invalid');
            valid = false;
        } else {
            phoneEl.classList.remove('is-invalid');
        }

        return valid;
    }

    function checkFormChanges() {
        const btnSave = document.getElementById('btnSaveProfile');
        if (!btnSave) return;
        const hasChanges = hasUnsavedChanges();
        const isValid = validateInputs();

        btnSave.disabled = !hasChanges || !isValid || isSubmitting;
    }

    // Manejo de Selección de Archivo de Avatar con recorte/previsualización
    window.handleAvatarFileSelect = function(e) {
        const file = e.target.files && e.target.files[0];
        const errorEl = document.getElementById('avatarErrorMsg');
        errorEl.style.display = 'none';

        if (!file) return;

        // Validar formato permitido: JPG, PNG, WebP
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            errorEl.innerText = 'Formato no permitido. Solo se aceptan imágenes JPG, PNG o WebP.';
            errorEl.style.display = 'block';
            e.target.value = '';
            return;
        }

        // Validar tamaño máximo: 2 MB (2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            errorEl.innerText = 'La imagen excede el tamaño máximo permitido de 2 MB.';
            errorEl.style.display = 'block';
            e.target.value = '';
            return;
        }

        selectedAvatarFile = file;
        pendingRemoveAvatar = false;
        document.getElementById('inputRemoveAvatar').value = '0';

        // Previsualización inmediata en máscara circular
        const reader = new FileReader();
        reader.onload = function(evt) {
            const wrap = document.getElementById('drawerAvatarPreviewWrap');
            wrap.innerHTML = `<img src="${evt.target.result}" class="w-100 h-100 object-fit-cover rounded-circle" alt="Vista previa">`;
            document.getElementById('btnUploadPhotoText').innerText = 'Cambiar foto';
            document.getElementById('btnRemoveAvatar').style.display = 'inline-flex';
            checkFormChanges();
        };
        reader.readAsDataURL(file);
    };

    // Quitar foto
    window.removeUserAvatar = function() {
        selectedAvatarFile = null;
        pendingRemoveAvatar = true;
        document.getElementById('inputRemoveAvatar').value = '1';
        document.getElementById('avatarFileInput').value = '';

        const currentName = document.getElementById('inputName')?.value.trim() || originalData.name;
        const initial = (currentName.charAt(0) || 'U').toUpperCase();

        const wrap = document.getElementById('drawerAvatarPreviewWrap');
        wrap.innerHTML = `<span>${initial}</span>`;
        document.getElementById('btnUploadPhotoText').innerText = 'Subir foto';
        document.getElementById('btnRemoveAvatar').style.display = 'none';
        checkFormChanges();
    };

    function resetFormToOriginal() {
        selectedAvatarFile = null;
        pendingRemoveAvatar = false;
        document.getElementById('inputRemoveAvatar').value = '0';
        document.getElementById('avatarFileInput').value = '';
        document.getElementById('avatarErrorMsg').style.display = 'none';

        document.getElementById('inputName').value = originalData.name;
        document.getElementById('inputEmail').value = originalData.email;
        document.getElementById('inputPhone').value = originalData.phone;
        document.getElementById('inputCountry').value = originalData.country;
        document.getElementById('inputCity').value = originalData.city;
        document.getElementById('inputAddress').value = originalData.address;
        document.getElementById('inputBirthdate').value = originalData.birthdate;

        // Restaurar avatar en el drawer
        const wrap = document.getElementById('drawerAvatarPreviewWrap');
        if (originalData.hasAvatar && originalData.avatarUrl) {
            wrap.innerHTML = `<img src="${originalData.avatarUrl}" class="w-100 h-100 object-fit-cover rounded-circle" alt="${originalData.name}">`;
            document.getElementById('btnUploadPhotoText').innerText = 'Cambiar foto';
            document.getElementById('btnRemoveAvatar').style.display = 'inline-flex';
        } else {
            wrap.innerHTML = `<span>${originalData.name.charAt(0).toUpperCase()}</span>`;
            document.getElementById('btnUploadPhotoText').innerText = 'Subir foto';
            document.getElementById('btnRemoveAvatar').style.display = 'none';
        }

        validateInputs();
        checkFormChanges();
    }

    // Envío del formulario por AJAX sin recargar la página
    window.submitProfileForm = function(e) {
        e.preventDefault();
        if (!validateInputs()) return;

        isSubmitting = true;
        const btnSave = document.getElementById('btnSaveProfile');
        const btnText = document.getElementById('btnSaveText');
        const btnSpinner = document.getElementById('btnSaveSpinner');

        btnSave.disabled = true;
        btnText.innerText = 'Guardando...';
        btnSpinner.classList.remove('d-none');

        const formData = new FormData(document.getElementById('profileEditForm'));

        // Realizar petición AJAX hacia route('user.profile.update')
        fetch('{{ route("user.profile.update") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => { throw errData; });
            }
            return response.json();
        })
        .then(data => {
            isSubmitting = false;
            btnText.innerText = 'Guardar cambios';
            btnSpinner.classList.add('d-none');

            if (data.success && data.user) {
                // Actualizar estado original
                originalData.name = data.user.name;
                originalData.email = data.user.email;
                originalData.phone = data.user.phone || '';
                originalData.country = data.user.country || '';
                originalData.city = data.user.city || '';
                originalData.address = data.user.address || '';
                originalData.birthdate = data.user.birthdate || '';
                originalData.hasAvatar = !!data.user.avatar;
                originalData.avatarUrl = data.user.avatar;

                // Actualizar la interfaz al instante sin recarga
                updateLiveUI(data.user);

                // Cerrar Drawer
                forceCloseDrawer();

                // Mostrar notificación toast positiva
                showToast(data.message || 'Cambios guardados');
            }
        })
        .catch(err => {
            isSubmitting = false;
            btnSave.disabled = false;
            btnText.innerText = 'Guardar cambios';
            btnSpinner.classList.add('d-none');

            if (err && err.errors) {
                if (err.errors.name) {
                    const el = document.getElementById('inputName');
                    el.classList.add('is-invalid');
                    document.getElementById('valErrorName').innerText = err.errors.name[0];
                }
                if (err.errors.email) {
                    const el = document.getElementById('inputEmail');
                    el.classList.add('is-invalid');
                    document.getElementById('valErrorEmail').innerText = err.errors.email[0];
                }
                if (err.errors.phone) {
                    const el = document.getElementById('inputPhone');
                    el.classList.add('is-invalid');
                    document.getElementById('valErrorPhone').innerText = err.errors.phone[0];
                }
                if (err.errors.avatar) {
                    const errorEl = document.getElementById('avatarErrorMsg');
                    errorEl.innerText = err.errors.avatar[0];
                    errorEl.style.display = 'block';
                }
            } else {
                alert('Ocurrió un error al guardar los cambios. Por favor intenta de nuevo.');
            }
        });
    };

    // Actualización inmediata en caliente de todos los elementos visuales
    function updateLiveUI(userData) {
        // 1. Saludo superior
        document.querySelectorAll('.user-first-name-span').forEach(el => {
            el.innerText = userData.first_name || userData.name.split(' ')[0];
        });

        // 2. Tarjeta principal de perfil
        const nameDisplay = document.getElementById('mainUserNameDisplay');
        if (nameDisplay) nameDisplay.innerText = userData.name;

        const emailDisplay = document.getElementById('mainUserEmailDisplay');
        if (emailDisplay) emailDisplay.innerText = userData.email;

        // 3. Dirección en el modal de la orden
        const modalAddress = document.getElementById('modalOrderAddress');
        if (modalAddress && userData.address) {
            modalAddress.innerText = userData.address + (userData.city ? ', ' + userData.city : '');
        }

        // 4. Avatar principal
        const avatarWrap = document.getElementById('mainAvatarDisplay');
        if (avatarWrap) {
            if (userData.avatar) {
                avatarWrap.innerHTML = `<img src="${userData.avatar}" alt="${userData.name}" class="w-100 h-100 object-fit-cover" id="mainAvatarImg">`;
            } else {
                avatarWrap.innerHTML = `<span id="mainAvatarInitial">${userData.initial || userData.name.charAt(0).toUpperCase()}</span>`;
            }
        }

        // 5. Actualizar avatar y nombre en la barra superior unificada
        const topbarName = document.querySelector('#userAccountMenu span.d-none');
        if (topbarName) topbarName.innerText = userData.name;

        const topbarInitial = document.querySelector('#userAccountMenu .rounded-circle');
        if (topbarInitial) {
            topbarInitial.innerText = userData.initial || userData.name.charAt(0).toUpperCase();
        }
    }

    function showToast(msg) {
        const toast = document.getElementById('profileToast');
        const msgEl = document.getElementById('profileToastMessage');
        if (toast && msgEl) {
            msgEl.innerText = msg;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3500);
        }
    }
})();
</script>
@endpush
@endsection
