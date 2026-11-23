@extends('layouts.dashboard')
@section('title','Centro Analítico Admin - Spotlight')
@section('page-title','Panel Administrador')

@section('sidebar-menu')
<a href="{{ route('admin.dashboard') }}" class="vs-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid fs-5"></i> Panel principal
</a>
<a href="{{ route('admin.users.index') }}" class="vs-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
    <i class="bi bi-people fs-5"></i> Gestión de usuarios
</a>
<a href="{{ route('admin.companies.index') }}" class="vs-nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}">
    <i class="bi bi-building fs-5"></i> Gestión de empresas
</a>
<a href="{{ route('admin.fiscal_verifications.index') }}" class="vs-nav-link {{ request()->routeIs('admin.fiscal_verifications*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-check fs-5"></i> Verificación DIAN
</a>
<a href="{{ route('admin.assisted_purchases.index') }}" class="vs-nav-link {{ request()->routeIs('admin.assisted_purchases*') ? 'active' : '' }}">
    <i class="bi bi-globe-americas fs-5"></i> Solicitudes Int.
</a>
<a href="{{ route('chat.index') }}" class="vs-nav-link {{ request()->is('chat*') ? 'active' : '' }}">
    <i class="bi bi-chat-dots fs-5"></i> Mensajes
</a>
@endsection

@push('styles')
<style>
    .adm-hero-banner {
        background: linear-gradient(135deg, #7F1D3F 0%, #5E122C 60%, #3D0B1C 100%);
        border-radius: 22px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(127, 29, 63, 0.22);
    }
    .adm-hero-banner::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(217, 140, 154, 0.28) 0%, transparent 70%);
        pointer-events: none;
    }
    .adm-hero-chip {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.24);
        color: #ffffff;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        backdrop-filter: blur(6px);
    }
    .adm-kpi-card {
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid rgba(217, 140, 154, 0.22);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .adm-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(41, 37, 42, 0.08) !important;
    }
    .adm-chart-card {
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid rgba(217, 140, 154, 0.24);
        box-shadow: 0 6px 20px rgba(41, 37, 42, 0.04);
    }
    .adm-seg-group {
        background: #F6E7EB;
        padding: 4px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 2px;
        flex-wrap: wrap;
    }
    .adm-seg-btn {
        border: none;
        background: transparent;
        color: #5C545F;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 13px;
        border-radius: 999px;
        cursor: pointer;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .adm-seg-btn.active {
        background: #7F1D3F;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(127, 29, 63, 0.25);
    }
    .adm-series-pill {
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        border-radius: 12px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.18s ease;
        user-select: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .adm-series-pill:hover {
        border-color: #D98C9A;
        background: #FFFCF7;
    }
    .adm-series-pill.muted-series {
        opacity: 0.45;
        background: #f8fafc;
    }
    .adm-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .adm-doughnut-wrap {
        position: relative;
        height: 230px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .adm-doughnut-center {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        text-align: center;
    }
    .adm-legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 10px;
        border-radius: 10px;
        font-size: 0.78rem;
        transition: background 0.15s ease;
    }
    .adm-legend-item:hover {
        background: #FAF5F6;
    }
    .adm-funnel-row {
        padding: 10px 12px;
        border-radius: 12px;
        background: #FFFCF7;
        border: 1px solid rgba(217, 140, 154, 0.22);
    }
    .adm-search-mini {
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        padding: 4px 12px 4px 30px;
        font-size: 0.76rem;
        background: #f8fafc;
        outline: none;
        width: 175px;
        transition: border-color 0.2s, background 0.2s;
    }
    .adm-search-mini:focus {
        border-color: #7F1D3F;
        background: #ffffff;
    }
    @media (max-width: 575.98px) {
        .adm-search-mini {
            width: 100%;
        }
    }
</style>
@endpush

@section('dashboard-content')
@php
    $healthAverage = count($analytics['radar']['scores']) > 0
        ? (int) round(array_sum($analytics['radar']['scores']) / count($analytics['radar']['scores']))
        : 0;
@endphp

{{-- 1. Cabecera Ejecutiva con Pulso en Tiempo Real --}}
<div class="adm-hero-banner p-4 p-lg-4 mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 position-relative" style="z-index: 1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="adm-hero-chip">
                    <i class="bi bi-activity"></i> Telemetría en Tiempo Real
                </span>
                <span class="adm-hero-chip">
                    <i class="bi bi-shield-check"></i> Índice de Salud: <strong>{{ $healthAverage }}%</strong>
                </span>
            </div>
            <h3 class="fw-bold mb-1 font-outfit text-white" style="letter-spacing: -0.5px;">
                Centro de Inteligencia y Gobernanza Spotlight
            </h3>
            <p class="mb-0 text-white opacity-75" style="font-size: 0.88rem; max-width: 680px;">
                Monitorea el crecimiento de usuarios, activación de empresas, formalización DIAN y tracción comercial del ecosistema con gráficas interactivas.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <div class="adm-hero-chip">
                <i class="bi bi-box-seam"></i> {{ $stats['active_products'] }} Productos activos
            </div>
            <div class="adm-hero-chip">
                <i class="bi bi-bag-check"></i> {{ $stats['total_orders'] }} Órdenes
            </div>
            <div class="adm-hero-chip">
                <i class="bi bi-truck"></i> {{ $stats['total_shipments'] }} Envíos
            </div>
            <div class="adm-hero-chip">
                <i class="bi bi-star-fill text-warning"></i> {{ $stats['total_reviews'] }} Reseñas
            </div>
        </div>
    </div>
</div>

{{-- 2. Tarjetas KPI Ejecutivas con Indicadores de Progreso --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        [
            'icon' => 'bi-people-fill',
            'label' => 'Usuarios Compradores',
            'value' => $stats['users'],
            'color' => '#7F1D3F',
            'badge' => $stats['verified_pct'] . '% verificados',
            'pct'   => $stats['verified_pct'],
            'foot'  => 'Cuentas con correo validado en plataforma',
            'route' => route('admin.users.index'),
        ],
        [
            'icon' => 'bi-briefcase-fill',
            'label' => 'Emprendedores / Comercios',
            'value' => $stats['businesses'],
            'color' => '#D98C9A',
            'badge' => $stats['companies'] . ' tiendas creadas',
            'pct'   => $stats['businesses'] > 0 ? min(100, round(($stats['companies'] / $stats['businesses']) * 100)) : 0,
            'foot'  => 'Cuentas empresariales registradas',
            'route' => route('admin.users.index'),
        ],
        [
            'icon' => 'bi-building-check',
            'label' => 'Empresas en Plataforma',
            'value' => $stats['companies'],
            'color' => '#5E122C',
            'badge' => $stats['active_companies_pct'] . '% activas',
            'pct'   => $stats['active_companies_pct'],
            'foot'  => $stats['active_companies'] . ' comercios operando en vitrina pública',
            'route' => route('admin.companies.index'),
        ],
        [
            'icon' => 'bi-clock-history',
            'label' => 'Empresas por Aprobar',
            'value' => $stats['pending_companies'],
            'color' => '#D97706',
            'badge' => $stats['pending_companies'] > 0 ? 'Requiere atención' : 'Al día',
            'pct'   => $stats['companies'] > 0 ? round(($stats['pending_companies'] / $stats['companies']) * 100) : 0,
            'foot'  => 'Solicitudes de apertura pendientes de moderación',
            'route' => route('admin.companies.index'),
        ],
        [
            'icon' => 'bi-file-earmark-check-fill',
            'label' => 'Verificaciones DIAN',
            'value' => $stats['pending_verifications'],
            'color' => '#2563EB',
            'badge' => $stats['dian_enabled'] . ' habilitadas',
            'pct'   => $stats['companies'] > 0 ? min(100, round(($stats['dian_enabled'] / $stats['companies']) * 100)) : 0,
            'foot'  => 'Perfiles fiscales en revisión vs facturadores activos',
            'route' => route('admin.fiscal_verifications.index'),
        ],
        [
            'icon' => 'bi-globe-americas',
            'label' => 'Comercio Internacional',
            'value' => $stats['assisted_purchases'],
            'color' => '#059669',
            'badge' => 'CO: ' . $analytics['markets']['co'] . ' • MX: ' . $analytics['markets']['mx'],
            'pct'   => $stats['companies'] > 0 ? min(100, round(($analytics['markets']['co'] / max(1, $stats['companies'])) * 100)) : 100,
            'foot'  => 'Solicitudes asistidas activas entre Colombia y México',
            'route' => route('admin.assisted_purchases.index'),
        ],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="col-sm-6 col-xl-4">
        <a href="{{ $card['route'] }}" class="text-decoration-none">
            <div class="adm-kpi-card h-100 p-3 shadow-xs" style="border-left: 4px solid {{ $card['color'] }} !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: {{ $card['color'] }}15;">
                            <i class="bi {{ $card['icon'] }} fs-5" style="color: {{ $card['color'] }};"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold" style="font-size: 0.78rem;">{{ $card['label'] }}</div>
                            <div class="fw-bold fs-4 text-dark lh-1 mt-1" style="font-family: 'Outfit', sans-serif;">{{ $card['value'] }}</div>
                        </div>
                    </div>
                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: {{ $card['color'] }}15; color: {{ $card['color'] }}; font-size: 0.68rem;">
                        {{ $card['badge'] }}
                    </span>
                </div>
                <div class="progress my-2" style="height: 5px; border-radius: 999px; background: #f1f5f9;">
                    <div class="progress-bar" style="width: {{ max(4, $card['pct']) }}%; background: {{ $card['color'] }}; border-radius: 999px;"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.72rem;">
                    <span>{{ $card['foot'] }}</span>
                    <i class="bi bi-arrow-up-right" style="color: {{ $card['color'] }};"></i>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- 3. Fila Principal de Gráficas Interactivas: Pulso Temporal (8 cols) + Radiografía Empresarial (4 cols) --}}
<div class="row g-4 mb-4">
    <!-- Gráfica 1: Pulso y Crecimiento del Ecosistema -->
    <div class="col-lg-8">
        <div class="adm-chart-card p-3 p-sm-4 h-100 d-flex flex-column">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background: #7F1D3F;"></span>
                        <h6 class="fw-bold text-dark mb-0 font-outfit" style="font-size: 1.05rem;">
                            Pulso de Crecimiento y Actividad del Ecosistema
                        </h6>
                    </div>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.76rem;">
                        Haz clic en los rangos de tiempo o en las tarjetas inferiores para filtrar cada serie en vivo.
                    </p>
                </div>

                <!-- Controles interactivos: Rango + Tipo de Gráfico -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="adm-seg-group" id="timelineRangeGroup" role="group" aria-label="Rango temporal">
                        <button type="button" class="adm-seg-btn active" data-range="7d">7 Días</button>
                        <button type="button" class="adm-seg-btn" data-range="30d">30 Días</button>
                        <button type="button" class="adm-seg-btn" data-range="6m">6 Meses</button>
                    </div>
                    <div class="adm-seg-group" id="timelineTypeGroup" role="group" aria-label="Tipo de gráfica">
                        <button type="button" class="adm-seg-btn active" data-type="line" title="Vista de ondas suaves">
                            <i class="bi bi-graph-up"></i> Área
                        </button>
                        <button type="button" class="adm-seg-btn" data-type="bar" title="Vista de barras comparativas">
                            <i class="bi bi-bar-chart-fill"></i> Barras
                        </button>
                    </div>
                </div>
            </div>

            <!-- Píldoras interactivas para activar/ocultar series y ver totales del rango -->
            <div class="row g-2 mb-3">
                <div class="col-sm-4">
                    <div class="adm-series-pill" data-dataset-index="0" onclick="toggleTimelineDataset(0, this)">
                        <span class="adm-dot" style="background: #7F1D3F;"></span>
                        <div class="flex-grow-1">
                            <div class="text-muted fw-semibold" style="font-size: 0.7rem;">Nuevos Usuarios</div>
                            <div class="fw-bold text-dark font-outfit" style="font-size: 1rem;" id="sumUsersRange">0</div>
                        </div>
                        <i class="bi bi-eye text-muted small series-eye-icon"></i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="adm-series-pill" data-dataset-index="1" onclick="toggleTimelineDataset(1, this)">
                        <span class="adm-dot" style="background: #D98C9A;"></span>
                        <div class="flex-grow-1">
                            <div class="text-muted fw-semibold" style="font-size: 0.7rem;">Nuevas Empresas</div>
                            <div class="fw-bold text-dark font-outfit" style="font-size: 1rem;" id="sumCompaniesRange">0</div>
                        </div>
                        <i class="bi bi-eye text-muted small series-eye-icon"></i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="adm-series-pill" data-dataset-index="2" onclick="toggleTimelineDataset(2, this)">
                        <span class="adm-dot" style="background: #059669;"></span>
                        <div class="flex-grow-1">
                            <div class="text-muted fw-semibold" style="font-size: 0.7rem;">Interacciones / Catálogo</div>
                            <div class="fw-bold text-dark font-outfit" style="font-size: 1rem;" id="sumActivityRange">0</div>
                        </div>
                        <i class="bi bi-eye text-muted small series-eye-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Lienzo Chart.js -->
            <div class="flex-grow-1 position-relative" style="min-height: 270px;">
                <canvas id="ecosystemTimelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfica 2: Radiografía Empresarial Multi-Dimensión (Doughnut Interactivo) -->
    <div class="col-lg-4">
        <div class="adm-chart-card p-3 p-sm-4 h-100 d-flex flex-column">
            <div class="mb-3">
                <h6 class="fw-bold text-dark mb-1 font-outfit" style="font-size: 1.05rem;">
                    Radiografía Empresarial
                </h6>
                <p class="text-muted mb-2" style="font-size: 0.75rem;">
                    Explora la composición de comercios por dimensión:
                </p>
                <div class="adm-seg-group w-100 justify-content-between" id="doughnutDimGroup">
                    <button type="button" class="adm-seg-btn active flex-fill text-center" data-dim="categories">Sectores</button>
                    <button type="button" class="adm-seg-btn flex-fill text-center" data-dim="formality">Formalidad DIAN</button>
                    <button type="button" class="adm-seg-btn flex-fill text-center" data-dim="status_market">Estado / País</button>
                </div>
            </div>

            <div class="adm-doughnut-wrap my-2">
                <canvas id="companiesDoughnutChart"></canvas>
                <div class="adm-doughnut-center">
                    <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;" id="doughnutCenterLabel">Total Empresas</span>
                    <span class="fw-bold text-dark font-outfit" style="font-size: 1.75rem; line-height: 1.1;" id="doughnutCenterValue">{{ $stats['companies'] }}</span>
                </div>
            </div>

            <!-- Leyenda dinámica interactiva -->
            <div class="mt-auto pt-2 border-top d-flex flex-column gap-1" id="doughnutCustomLegend"></div>
        </div>
    </div>
</div>

{{-- 4. Segunda Fila Analítica: Radar de Salud del Ecosistema (5 cols) + Composición y Embudo Operativo (7 cols) --}}
<div class="row g-4 mb-4">
    <!-- Gráfica 3: Radar de Gobernanza y Madurez Digital -->
    <div class="col-lg-5">
        <div class="adm-chart-card p-3 p-sm-4 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="fw-bold text-dark mb-1 font-outfit" style="font-size: 1.05rem;">
                        <i class="bi bi-diagram-3-fill me-1" style="color: #7F1D3F;"></i> Radar de Salud del Ecosistema
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">
                        Evaluación porcentual (0–100%) de los 6 pilares operativos de Spotlight.
                    </p>
                </div>
                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #F6E7EB; color: #7F1D3F; font-size: 0.75rem;">
                    Promedio: {{ $healthAverage }}%
                </span>
            </div>

            <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative" style="min-height: 265px;">
                <canvas id="governanceRadarChart"></canvas>
            </div>

            <div class="row g-2 pt-2 border-top text-center">
                <div class="col-4">
                    <div class="text-muted" style="font-size: 0.68rem;">Verificación</div>
                    <div class="fw-bold text-dark font-outfit" style="font-size: 0.9rem;">{{ $analytics['radar']['scores'][0] }}%</div>
                </div>
                <div class="col-4 border-start border-end">
                    <div class="text-muted" style="font-size: 0.68rem;">Con Catálogo</div>
                    <div class="fw-bold text-dark font-outfit" style="font-size: 0.9rem;">{{ $analytics['radar']['scores'][2] }}%</div>
                </div>
                <div class="col-4">
                    <div class="text-muted" style="font-size: 0.68rem;">Geolocalizadas</div>
                    <div class="fw-bold text-dark font-outfit" style="font-size: 0.9rem;">{{ $analytics['radar']['scores'][4] }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 4: Composición de la Comunidad y Embudo de Conversión -->
    <div class="col-lg-7">
        <div class="adm-chart-card p-3 p-sm-4 h-100 d-flex flex-column">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-1 font-outfit" style="font-size: 1.05rem;">
                        <i class="bi bi-layers-half me-1" style="color: #7F1D3F;"></i> Distribución de Comunidad y Embudo Operativo
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">
                        Segmentación por tipo de cuenta, verificación y madurez comercial.
                    </p>
                </div>
                <div class="adm-seg-group" id="communityViewGroup">
                    <button type="button" class="adm-seg-btn active" data-view="bars">
                        <i class="bi bi-bar-chart-steps me-1"></i> Gráfica Roles
                    </button>
                    <button type="button" class="adm-seg-btn" data-view="funnel">
                        <i class="bi bi-funnel-fill me-1"></i> Embudo
                    </button>
                </div>
            </div>

            <!-- Vista A: Gráfico de barras horizontales de roles -->
            <div id="communityBarsPane" class="flex-grow-1 position-relative" style="min-height: 250px;">
                <canvas id="communityRolesChart"></canvas>
            </div>

            <!-- Vista B: Embudo interactivo de madurez -->
            @php
                $totalAccounts = max(1, $stats['users'] + $stats['businesses']);
                $funnelSteps = [
                    ['label' => '1. Cuentas Totales Registradas (Compradores + Comercios)', 'count' => $stats['users'] + $stats['businesses'], 'pct' => 100, 'color' => '#7F1D3F'],
                    ['label' => '2. Usuarios con Identidad / Correo Verificado', 'count' => $analytics['roles_breakdown']['buyers_verified'] + $stats['businesses'], 'pct' => $stats['verified_pct'], 'color' => '#9E2A53'],
                    ['label' => '3. Empresas Creadas y Activas en Vitrina', 'count' => $stats['active_companies'], 'pct' => $stats['active_companies_pct'], 'color' => '#D98C9A'],
                    ['label' => '4. Comercios con Catálogo de Productos Publicado', 'count' => $stats['active_products'] . ' prods.', 'pct' => $analytics['radar']['scores'][2], 'color' => '#059669'],
                    ['label' => '5. Empresas Formales con Perfil Fiscal / DIAN', 'count' => $analytics['roles_breakdown']['merchants_formal'], 'pct' => $analytics['radar']['scores'][3], 'color' => '#2563EB'],
                ];
            @endphp
            <div id="communityFunnelPane" class="d-none flex-grow-1 d-flex flex-column justify-content-center gap-2">
                @foreach($funnelSteps as $step)
                <div class="adm-funnel-row">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $step['label'] }}</span>
                        <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: {{ $step['color'] }}18; color: {{ $step['color'] }}; font-size: 0.72rem;">
                            {{ $step['count'] }} ({{ $step['pct'] }}%)
                        </span>
                    </div>
                    <div class="progress" style="height: 7px; border-radius: 999px; background: #e2e8f0;">
                        <div class="progress-bar" style="width: {{ max(6, $step['pct']) }}%; background: {{ $step['color'] }}; border-radius: 999px;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- 5. Tablas de Supervisión en Vivo con Buscador Instantáneo --}}
<div class="row g-4 mb-4">
    <!-- Últimos Usuarios Registrados -->
    <div class="col-lg-6">
        <div class="vs-card border-0 shadow-sm h-100 p-3 p-sm-4" style="border-radius: 20px;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0 font-outfit">
                    <i class="bi bi-people-fill me-2" style="color: #7F1D3F;"></i>Últimos Usuarios Registrados
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.72rem;"></i>
                        <input type="text" class="adm-search-mini" placeholder="Filtrar usuario..." oninput="filterAdminTable('recentUsersTable', this.value)">
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 text-xs fw-semibold">Ver todos</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" id="recentUsersTable">
                    <thead>
                        <tr class="text-muted text-xs">
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Verificado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $ru)
                        <tr>
                            <td class="fw-semibold text-dark text-sm">{{ $ru->name }}</td>
                            <td class="text-muted text-xs">{{ $ru->email }}</td>
                            <td>
                                <span class="badge badge-soft-primary rounded-pill px-2 py-1 text-xs">{{ ucfirst($ru->role) }}</span>
                            </td>
                            <td>
                                @if($ru->email_verified_at)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0 text-xs"><i class="bi bi-check"></i> Sí</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-0 text-xs">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No hay registros aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Últimas Empresas Registradas -->
    <div class="col-lg-6">
        <div class="vs-card border-0 shadow-sm h-100 p-3 p-sm-4" style="border-radius: 20px;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0 font-outfit">
                    <i class="bi bi-building-fill me-2" style="color: #7F1D3F;"></i>Últimas Empresas Registradas
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.72rem;"></i>
                        <input type="text" class="adm-search-mini" placeholder="Filtrar empresa..." oninput="filterAdminTable('recentCompaniesTable', this.value)">
                    </div>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 text-xs fw-semibold">Ver todas</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" id="recentCompaniesTable">
                    <thead>
                        <tr class="text-muted text-xs">
                            <th>Empresa</th>
                            <th>País</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCompanies as $rc)
                        <tr>
                            <td class="fw-semibold text-dark text-sm">{{ $rc->name }}</td>
                            <td>
                                <span class="badge bg-light border text-dark rounded-pill px-2 py-0 text-xs">
                                    {{ $rc->country === 'MX' ? 'MX • México' : 'CO • Colombia' }}
                                </span>
                            </td>
                            <td class="text-muted text-xs">{{ $rc->category ?: 'General' }}</td>
                            <td>
                                <span class="badge {{ $rc->status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-2 py-0 text-xs">
                                    {{ ucfirst($rc->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No hay empresas registradas aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 6. Acceso Rápido a Gobernanza --}}
<div class="vs-card p-3 border-0 shadow-sm" style="border-radius: 18px;">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h6 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">Acceso rápido a gobernanza y administración</h6>
            <p class="text-muted mb-0" style="font-size: 0.76rem;">Acciones operativas directas sobre cuentas, comercios y cumplimiento fiscal.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-people me-1"></i>Gestionar usuarios
            </a>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-building me-1"></i>Aprobación de empresas
            </a>
            <a href="{{ route('admin.fiscal_verifications.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-file-earmark-check me-1"></i>Verificaciones DIAN
            </a>
            <a href="{{ route('admin.assisted_purchases.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-globe-americas me-1"></i>Solicitudes Internacionales
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const analyticsData = @json($analytics);
    const PALETTE = ['#7F1D3F', '#D98C9A', '#059669', '#2563EB', '#D97706', '#5E122C', '#8B5CF6'];

    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // =========================================================================
    // 1. GRÁFICA PRINCIPAL: PULSO TEMPORAL DEL ECOSISTEMA
    // =========================================================================
    let currentRange = '7d';
    let currentChartType = 'line';
    const ctxTimeline = document.getElementById('ecosystemTimelineChart');

    function sumArray(arr) {
        return (arr || []).reduce((acc, v) => acc + (Number(v) || 0), 0);
    }

    function updateRangeTotals(rangeKey) {
        const d = analyticsData.timeline[rangeKey];
        if (!d) return;
        document.getElementById('sumUsersRange').textContent = '+' + sumArray(d.users);
        document.getElementById('sumCompaniesRange').textContent = '+' + sumArray(d.companies);
        document.getElementById('sumActivityRange').textContent = '+' + sumArray(d.activity);
    }

    function buildTimelineDatasets(rangeKey, type) {
        const d = analyticsData.timeline[rangeKey];
        const isLine = type === 'line';
        return [
            {
                label: 'Nuevos Usuarios',
                data: d.users,
                borderColor: '#7F1D3F',
                backgroundColor: isLine ? 'rgba(127, 29, 63, 0.14)' : '#7F1D3F',
                borderWidth: 2.5,
                tension: 0.38,
                fill: isLine,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#7F1D3F',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderRadius: isLine ? 0 : 6,
            },
            {
                label: 'Nuevas Empresas',
                data: d.companies,
                borderColor: '#D98C9A',
                backgroundColor: isLine ? 'rgba(217, 140, 154, 0.18)' : '#D98C9A',
                borderWidth: 2.5,
                tension: 0.38,
                fill: isLine,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#D98C9A',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderRadius: isLine ? 0 : 6,
            },
            {
                label: 'Interacciones / Catálogo',
                data: d.activity,
                borderColor: '#059669',
                backgroundColor: isLine ? 'rgba(5, 150, 105, 0.12)' : '#059669',
                borderWidth: 2.5,
                tension: 0.38,
                fill: isLine,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderRadius: isLine ? 0 : 6,
            }
        ];
    }

    let timelineChart = null;
    if (ctxTimeline) {
        updateRangeTotals(currentRange);
        timelineChart = new Chart(ctxTimeline, {
            type: currentChartType,
            data: {
                labels: analyticsData.timeline[currentRange].labels,
                datasets: buildTimelineDatasets(currentRange, currentChartType)
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#29252A',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        usePointStyle: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { color: 'rgba(226, 232, 240, 0.7)', drawBorder: false }
                    },
                    x: {
                        ticks: { font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Cambiar rango temporal (7d / 30d / 6m)
    document.querySelectorAll('#timelineRangeGroup .adm-seg-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#timelineRangeGroup .adm-seg-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentRange = this.dataset.range;
            updateRangeTotals(currentRange);

            if (timelineChart) {
                const hiddenStates = timelineChart.data.datasets.map((_, idx) => !timelineChart.isDatasetVisible(idx));
                timelineChart.data.labels = analyticsData.timeline[currentRange].labels;
                timelineChart.data.datasets = buildTimelineDatasets(currentRange, currentChartType);
                hiddenStates.forEach((isHidden, idx) => {
                    timelineChart.setDatasetVisibility(idx, !isHidden);
                });
                timelineChart.update();
            }
        });
    });

    // Cambiar tipo de gráfico (Área vs Barras)
    document.querySelectorAll('#timelineTypeGroup .adm-seg-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#timelineTypeGroup .adm-seg-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentChartType = this.dataset.type;

            if (timelineChart) {
                const hiddenStates = timelineChart.data.datasets.map((_, idx) => !timelineChart.isDatasetVisible(idx));
                timelineChart.config.type = currentChartType;
                timelineChart.data.datasets = buildTimelineDatasets(currentRange, currentChartType);
                hiddenStates.forEach((isHidden, idx) => {
                    timelineChart.setDatasetVisibility(idx, !isHidden);
                });
                timelineChart.update();
            }
        });
    });

    // Activar/Ocultar series haciendo clic en las tarjetas superiores
    window.toggleTimelineDataset = function (datasetIdx, pillEl) {
        if (!timelineChart) return;
        const visible = timelineChart.isDatasetVisible(datasetIdx);
        timelineChart.setDatasetVisibility(datasetIdx, !visible);
        timelineChart.update();

        pillEl.classList.toggle('muted-series', visible);
        const icon = pillEl.querySelector('.series-eye-icon');
        if (icon) {
            icon.className = visible ? 'bi bi-eye-slash text-muted small series-eye-icon' : 'bi bi-eye text-muted small series-eye-icon';
        }
    };

    // =========================================================================
    // 2. GRÁFICA DOUGHNUT MULTI-DIMENSIÓN: RADIOGRAFÍA EMPRESARIAL
    // =========================================================================
    const ctxDoughnut = document.getElementById('companiesDoughnutChart');
    let doughnutChart = null;

    function renderDoughnutLegend(labels, values, colors) {
        const container = document.getElementById('doughnutCustomLegend');
        if (!container) return;
        const total = Math.max(1, sumArray(values));
        container.innerHTML = labels.map((lbl, i) => {
            const val = values[i] || 0;
            const pct = Math.round((val / total) * 100);
            const color = colors[i % colors.length];
            return `
                <div class="adm-legend-item">
                    <div class="d-flex align-items-center gap-2 text-truncate">
                        <span class="adm-dot" style="background: ${color};"></span>
                        <span class="fw-semibold text-dark text-truncate">${lbl}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="fw-bold text-dark">${val}</span>
                        <span class="badge rounded-pill bg-light text-muted border" style="font-size: 0.68rem;">${pct}%</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function updateDoughnutDimension(dimKey) {
        const dist = analyticsData.distributions[dimKey];
        if (!dist || !doughnutChart) return;

        const total = sumArray(dist.values);
        const dimTitles = {
            categories: 'Por Sector',
            formality: 'Formalidad',
            status_market: 'Estado / País'
        };
        document.getElementById('doughnutCenterLabel').textContent = dimTitles[dimKey] || 'Empresas';
        document.getElementById('doughnutCenterValue').textContent = total;

        doughnutChart.data.labels = dist.labels;
        doughnutChart.data.datasets[0].data = dist.values;
        doughnutChart.data.datasets[0].backgroundColor = PALETTE.slice(0, dist.labels.length);
        doughnutChart.update();

        renderDoughnutLegend(dist.labels, dist.values, PALETTE);
    }

    if (ctxDoughnut) {
        const initDist = analyticsData.distributions.categories;
        doughnutChart = new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: initDist.labels,
                datasets: [{
                    data: initDist.values,
                    backgroundColor: PALETTE.slice(0, initDist.labels.length),
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#29252A',
                        padding: 10,
                        cornerRadius: 10
                    }
                }
            }
        });
        renderDoughnutLegend(initDist.labels, initDist.values, PALETTE);
    }

    document.querySelectorAll('#doughnutDimGroup .adm-seg-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#doughnutDimGroup .adm-seg-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateDoughnutDimension(this.dataset.dim);
        });
    });

    // =========================================================================
    // 3. GRÁFICA RADAR: SALUD Y GOBERNANZA DEL ECOSISTEMA
    // =========================================================================
    const ctxRadar = document.getElementById('governanceRadarChart');
    if (ctxRadar) {
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: analyticsData.radar.labels,
                datasets: [{
                    label: 'Nivel Actual (%)',
                    data: analyticsData.radar.scores,
                    backgroundColor: 'rgba(127, 29, 63, 0.18)',
                    borderColor: '#7F1D3F',
                    pointBackgroundColor: '#7F1D3F',
                    pointBorderColor: '#ffffff',
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: '#7F1D3F',
                    borderWidth: 2.5,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#29252A',
                        callbacks: {
                            label: ctx => ` ${ctx.raw}% de cumplimiento`
                        }
                    }
                },
                scales: {
                    r: {
                        angleLines: { color: 'rgba(217, 140, 154, 0.25)' },
                        grid: { color: 'rgba(217, 140, 154, 0.25)' },
                        pointLabels: {
                            font: { size: 11, weight: '600' },
                            color: '#29252A'
                        },
                        suggestedMin: 0,
                        suggestedMax: 100,
                        ticks: {
                            stepSize: 25,
                            display: false
                        }
                    }
                }
            }
        });
    }

    // =========================================================================
    // 4. GRÁFICA HORIZONTAL: COMPOSICIÓN DE LA COMUNIDAD POR ROL Y ESTADO
    // =========================================================================
    const ctxRoles = document.getElementById('communityRolesChart');
    if (ctxRoles) {
        const rb = analyticsData.roles_breakdown;
        new Chart(ctxRoles, {
            type: 'bar',
            data: {
                labels: [
                    'Compradores Verificados',
                    'Compradores por Verificar',
                    'Comercios Formales (NIT)',
                    'Emprendimientos Locales',
                    'Administradores'
                ],
                datasets: [{
                    label: 'Registros',
                    data: [
                        rb.buyers_verified,
                        rb.buyers_unverified,
                        rb.merchants_formal,
                        rb.merchants_local,
                        rb.admins
                    ],
                    backgroundColor: ['#7F1D3F', '#D98C9A', '#2563EB', '#059669', '#5E122C'],
                    borderRadius: 8,
                    barThickness: 22
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#29252A',
                        padding: 10,
                        cornerRadius: 10
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    y: {
                        ticks: { font: { size: 11, weight: '600' }, color: '#29252A' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Alternar entre Gráfica de Roles y Embudo Operativo
    document.querySelectorAll('#communityViewGroup .adm-seg-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#communityViewGroup .adm-seg-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const view = this.dataset.view;
            document.getElementById('communityBarsPane').classList.toggle('d-none', view !== 'bars');
            document.getElementById('communityFunnelPane').classList.toggle('d-none', view !== 'funnel');
        });
    });

    // =========================================================================
    // 5. FILTRO INSTANTÁNEO EN TABLAS DE USUARIOS Y EMPRESAS RECIENTES
    // =========================================================================
    window.filterAdminTable = function (tableId, query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    };
});
</script>
@endpush
