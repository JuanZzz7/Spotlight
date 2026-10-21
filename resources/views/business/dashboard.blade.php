@extends('layouts.dashboard')
@section('title','Empresario - Spotlight')
@section('page-title','Panel Empresario')

@section('dashboard-content')
@if(!$company)
    <div class="alert alert-warning border-0" style="background-color: rgba(217, 119, 6, 0.1); color: #d97706;">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Aún no has configurado tu empresa. <a href="{{ route('business.company.edit') }}" class="fw-semibold text-decoration-underline" style="color: #d97706;">Completa tu perfil empresarial</a> para empezar a vender.
    </div>
@else
    @if($company->status === 'pending')
    <div class="alert alert-warning border-0 d-flex align-items-center mb-4 shadow-sm" style="background-color: #fffbeb; color: #92400e;">
        <i class="bi bi-clock-history fs-4 me-3 text-warning"></i>
        <div>
            <h6 class="mb-1 fw-bold">Cuenta pendiente de validación</h6>
            <p class="mb-0 text-sm">Tus documentos están siendo revisados por nuestro equipo. Durante este tiempo, tus productos y tu perfil de empresa no serán visibles al público.</p>
        </div>
    </div>
    @elseif($company->status === 'inactive')
    <div class="alert alert-danger border-0 d-flex align-items-center mb-4 shadow-sm" style="background-color: #fef2f2; color: #991b1b;">
        <i class="bi bi-x-circle fs-4 me-3 text-danger"></i>
        <div>
            <h6 class="mb-1 fw-bold">Cuenta no aprobada o suspendida</h6>
            <p class="mb-0 text-sm">Hubo un problema con la validación de tus documentos. Por favor, revisa tu correo electrónico para más detalles o actualiza tus datos en "Mi empresa".</p>
        </div>
    </div>
    @endif
    {{-- Branding Header --}}
    <div class="vs-card overflow-hidden mb-4 p-0 shadow-sm border-0" style="border-radius: 20px;">
        <div style="height: 140px; position: relative; background: linear-gradient(135deg, #29252A 0%, #3D1222 50%, #7F1D3F 100%);">
            @if($company->banner)
                <img src="{{ asset('storage/'.$company->banner) }}" class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover; opacity: 0.5;">
            @endif
            <!-- Tienda pública activa -->
            <div class="position-absolute" style="top: 20px; left: 20px; z-index: 2;">
                <span class="badge rounded-pill text-white d-flex align-items-center gap-2 px-3 py-2 fw-normal" style="background: rgba(0,0,0,0.5) !important; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="rounded-circle" style="width: 8px; height: 8px; background-color: #a3e635;"></div>
                    Tienda pública activa
                </span>
            </div>
            <!-- Cambiar portada -->
            <div class="position-absolute" style="top: 20px; right: 20px; z-index: 2;">
                <button class="btn btn-sm text-white rounded-pill px-3 py-2 d-flex align-items-center gap-2 fw-semibold shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="bi bi-camera"></i> Cambiar portada
                </button>
            </div>
            
            <div class="position-absolute" style="bottom: -30px; left: 30px; z-index: 3;">
                <div class="vs-card p-1 bg-white shadow-sm" style="width: 85px; height: 85px; border-radius: 18px; border: 1px solid #f1f5f9;">
                    <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center" style="border-radius: 14px; overflow: hidden;">
                        @if($company->logo)
                            <img src="{{ asset('storage/'.$company->logo) }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <i class="bi bi-shop fs-2" style="color: var(--sl-primary, #7F1D3F);"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 pt-5 pb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 ms-2">
                <div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <h4 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">{{ $company->name }}</h4>
                        <i class="bi bi-patch-check-fill fs-5" style="color: var(--sl-primary, #7F1D3F);"></i>
                        <span class="badge badge-purple ms-1 fw-bold" style="font-size: 0.65rem;">
                            {{ mb_strtoupper($company->category) }}
                        </span>
                        @if($company->isFormal())
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold" style="font-size: 0.68rem;">
                                <i class="bi bi-shield-check me-1"></i>Empresa Formal • NIT {{ $company->nit }}
                            </span>
                            @if($company->canIssueElectronicInvoices())
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: rgba(127, 29, 63, 0.1); color: var(--sl-primary, #7F1D3F); border: 1px solid rgba(127, 29, 63, 0.2); font-size: 0.68rem;">
                                    <i class="bi bi-receipt me-1"></i>Facturación DIAN Activa
                                </span>
                            @endif
                        @else
                            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold" style="font-size: 0.68rem;">
                                <i class="bi bi-shop me-1"></i>Emprendimiento Local
                            </span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center text-muted gap-3 mt-2 fw-medium" style="font-size: 0.8rem;">
                        <span><i class="bi bi-geo-alt me-1" style="color: #94a3b8;"></i>Aldea Digital</span>
                        <span><i class="bi bi-star-fill text-warning me-1"></i>4.9 <span class="opacity-75">(42 calificaciones)</span></span>
                        <span style="color: var(--sl-primary, #7F1D3F);"><i class="bi bi-truck me-1"></i>Envíos internacionales</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('business.company.edit') }}" class="btn bg-white btn-sm rounded-pill px-4 py-2 fw-semibold d-flex align-items-center gap-2 border shadow-sm text-dark">
                        <i class="bi bi-pencil" style="color: var(--sl-primary, #7F1D3F);"></i> Personalizar Perfil
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-spotlight btn-sm rounded-pill px-4 py-2 fw-semibold d-flex align-items-center gap-2 text-white shadow-sm border-0">
                        <i class="bi bi-eye"></i> Ver Tienda
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

@if(isset($company) && $company && $company->isFormal() && $company->canIssueElectronicInvoices())
<div class="card border-0 rounded-4 mb-4 shadow-sm overflow-hidden" style="background: #ffffff; border: 1px solid #bbf7d0 !important; border-left: 6px solid #10b981 !important;">
    <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center p-3 flex-shrink-0" style="background: #dcfce7; width: 56px; height: 56px; border: 1px solid #bbf7d0;">
                <i class="bi bi-shield-check fs-3" style="color: #15803d;"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill px-2 py-1 fw-bold text-dark" style="background: #dcfce7; border: 1px solid #bbf7d0; font-size: 0.72rem; color: #15803d !important;">
                        <i class="bi bi-patch-check-fill me-1 text-success"></i>Empresa Formal Certificada DIAN
                    </span>
                    <span class="badge rounded-pill px-2 py-1 fw-semibold" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; border: 1px solid #e2e8f0;">
                        NIT: {{ $company->nit }}
                    </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: #0f172a !important;">
                    Facturación Electrónica DIAN Operativa
                </h5>
                <p class="mb-0 small" style="max-width: 680px; line-height: 1.5; color: #334155 !important;">
                    Tu empresa formal tiene habilitada la emisión de facturas y notas crédito con resolución DIAN, generación UBL 2.1 y firma digital CUFE/CUDE. Puedes emitir facturas legales en 1 clic para tus ventas.
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('business.billing.index') }}" class="btn rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center gap-2 text-white" style="background: var(--sl-primary, #7F1D3F); border: none; font-size: 0.85rem;">
                <i class="bi bi-receipt-cutoff"></i> Módulo DIAN
            </a>
            <a href="{{ route('business.billing.sales') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.85rem;">
                Panel de Ventas
            </a>
        </div>
    </div>
</div>
@endif

@if(isset($company) && $company && $company->isInformal())
<div class="card border-0 rounded-4 mb-4 shadow-sm overflow-hidden" style="background: #ffffff; border: 1px solid #fed7aa !important; border-left: 6px solid #f59e0b !important;">
    <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center p-3 flex-shrink-0" style="background: #fef3c7; width: 56px; height: 56px; border: 1px solid #fde68a;">
                <i class="bi bi-mortarboard-fill fs-3" style="color: #b45309;"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill px-2 py-1 fw-bold text-dark" style="background: #fef3c7; border: 1px solid #fde68a; font-size: 0.72rem; color: #92400e !important;">
                        <i class="bi bi-stars me-1 text-warning"></i>Guía para Emprendimientos
                    </span>
                    <span class="badge rounded-pill px-2 py-1 fw-semibold" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; border: 1px solid #e2e8f0;">
                        Comercio Informal
                    </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: #0f172a !important;">
                    ¿Deseas conocer el proceso para formalizar tu negocio?
                </h5>
                <p class="mb-0 small" style="max-width: 680px; line-height: 1.5; color: #334155 !important;">
                    Accede a nuestros tutoriales en video y la ruta paso a paso (Cámara de Comercio y DIAN). 
                    <strong style="color: #0f172a;">Aviso:</strong> Spotlight no realiza trámites mercantiles ni tributarios; te brindamos los recursos oficiales para que lo gestiones de forma directa y autónoma.
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('business.formalization.guide') }}" class="btn rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2" style="background: #f59e0b; color: #000000; border: none; font-size: 0.9rem;">
                <i class="bi bi-play-circle-fill fs-5"></i> Ver Tutoriales
            </a>
        </div>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    @php
    $cards = [
        [
            'label'=>'INGRESOS TOTALES',
            'value'=>'$'.number_format($income,0,',','.'),
            'icon'=>'bi-arrow-up',
            'color'=>'#7F1D3F',
            'subtext'=>'<span style="color:#7F1D3F; font-weight:600;"><i class="bi bi-graph-up me-1"></i>+14.5%</span> <span class="ms-1">este mes</span>'
        ],
        [
            'label'=>'EGRESOS TOTALES',
            'value'=>'$'.number_format($expenses,0,',','.'),
            'icon'=>'bi-arrow-down',
            'color'=>'#64748b',
            'subtext'=>'Gastos operativos <span class="float-end fw-semibold">21.2% total</span>'
        ],
        [
            'label'=>'GANANCIA NETA',
            'value'=>'$'.number_format($net,0,',','.'),
            'icon'=>'bi-graph-up',
            'color'=>'#10b981',
            'subtext'=>'<span class="badge text-success me-2 rounded-pill px-2" style="background:#d1fae5;">Margen 78.7%</span> <span class="float-end">Saludable</span>',
            'valueColor' => '#10b981'
        ],
        [
            'label'=>'PRODUCTOS',
            'value'=>$company?$company->products->count():0,
            'icon'=>'bi-box-seam',
            'color'=>'#D98C9A',
            'subtext'=>'<span style="color:#7F1D3F; font-weight:600;">100% con stock</span> <a href="'.route('business.products.create').'" class="float-end text-decoration-none fw-bold" style="color:#7F1D3F;">+ Agregar</a>'
        ],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-sm-6 col-xl-3">
        <div class="vs-card h-100 border-0 shadow-sm" style="border-top: 4px solid {{ $c['color'] }} !important; border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-bold" style="font-size: 0.75rem; color: {{ $c['color'] }}; letter-spacing: 0.5px;">{{ $c['label'] }}</div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:{{ $c['color'] }}15">
                        <i class="bi {{ $c['icon'] }}" style="color:{{ $c['color'] }}; font-size: 1rem;"></i>
                    </div>
                </div>
                <div class="fw-bold mb-3" style="color: {{ $c['valueColor'] ?? '#0f172a' }}; font-size: 1.8rem; letter-spacing: -0.5px;">{{ $c['value'] }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">
                    {!! $c['subtext'] !!}
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-lg-7">
        <div class="vs-card h-100 border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="fw-bold text-dark mb-1">Actividad de Ventas</h6>
                    <div class="text-muted" style="font-size: 0.75rem;">Flujo comercial consolidado de {{ $company->name ?? 'tu tienda' }}</div>
                </div>
                <div class="bg-light rounded-pill p-1 d-flex">
                    <button class="btn btn-sm rounded-pill text-muted px-3 border-0 fw-medium" style="font-size: 0.75rem;">Hoy</button>
                    <button class="btn btn-sm rounded-pill bg-white text-dark shadow-sm px-3 fw-bold" style="font-size: 0.75rem; color: var(--sl-primary, #7F1D3F) !important;">Esta semana</button>
                    <button class="btn btn-sm rounded-pill text-muted px-3 border-0 fw-medium" style="font-size: 0.75rem;">Este mes</button>
                </div>
            </div>
            <!-- Dynamic Chart Bars -->
            <div class="d-flex align-items-end justify-content-between pt-4" style="height: 180px;">
                @if(!empty($weeklySales))
                    @foreach($weeklySales as $w)
                    <div class="d-flex flex-column align-items-center" style="width: 12%;">
                        @if($w['is_today'] || $w['amount'] > 0)
                            <div class="badge bg-dark mb-2 rounded-pill px-2 py-1 shadow-sm" style="font-size: 0.65rem;">${{ number_format($w['amount'], 0, ',', '.') }}</div>
                            <div class="w-100 rounded" style="height: {{ $w['percentage'] }}%; background: linear-gradient(to bottom, var(--sl-accent, #D98C9A), var(--sl-primary, #7F1D3F)); transition: height 0.5s;"></div>
                        @else
                            <div class="w-100 rounded" style="height: {{ $w['percentage'] }}%; background: #f1f5f9;"></div>
                        @endif
                        <span class="mt-2 fw-semibold {{ $w['is_today'] ? 'text-coral' : 'text-muted' }}" style="font-size: 0.7rem; {{ $w['is_today'] ? 'color: var(--sl-primary, #7F1D3F); font-weight: 700;' : '' }}">{{ $w['day'] }}</span>
                    </div>
                    @endforeach
                @else
                    @php $days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']; $heights = [30, 45, 40, 20, 50, 85, 25]; @endphp
                    @foreach($days as $i => $day)
                    <div class="d-flex flex-column align-items-center" style="width: 12%;">
                        <div class="w-100 rounded" style="height: {{ $heights[$i] }}%; background: #f1f5f9;"></div>
                        <span class="mt-2 fw-semibold text-muted" style="font-size: 0.7rem;">{{ $day }}</span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-5">
        <div class="vs-card h-100 border-0 shadow-sm p-4">
            <h6 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">Acciones Rápidas</h6>
            <div class="text-muted mb-4" style="font-size: 0.75rem;">Herramientas ágiles para tu negocio</div>
            
            <div class="d-flex flex-column gap-3 mb-4">
                <a href="{{ route('business.products.create') ?? '#' }}" class="text-decoration-none">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 border bg-white shadow-sm" style="transition: all 0.2s;">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: var(--sl-primary-soft, #F6E7EB);">
                            <i class="bi bi-plus-square fs-5" style="color: var(--sl-primary, #7F1D3F);"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark text-sm">Publicar nuevo producto</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Subir fotos y fijar precios</div>
                        </div>
                        <i class="bi bi-arrow-right text-muted ms-auto"></i>
                    </div>
                </a>
                <a href="{{ route('business.shipments.index') }}" class="text-decoration-none">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 border bg-white shadow-sm" style="transition: all 0.2s;">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: var(--sl-primary-soft, #F6E7EB);">
                            <i class="bi bi-truck fs-5" style="color: var(--sl-primary, #7F1D3F);"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark text-sm">Gestionar despachos</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Control logístico y guías de envío</div>
                        </div>
                        <i class="bi bi-arrow-right text-muted ms-auto"></i>
                    </div>
                </a>
                <a href="{{ route('business.billing.reports.index') }}" class="text-decoration-none">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 border bg-white shadow-sm" style="transition: all 0.2s;">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #f1f5f9;">
                            <i class="bi bi-file-earmark-spreadsheet text-secondary fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark text-sm">Descargar extracto contable <i class="bi bi-download ms-1"></i></div>
                            <div class="text-muted" style="font-size: 0.7rem;">Reporte DIAN y balance comercial</div>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mt-auto" style="background-color: var(--sl-primary-soft, #F6E7EB); border: 1px dashed rgba(127,29,63,0.3);">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #7F1D3F;">
                    <i class="bi bi-shield-lock-fill text-white"></i>
                </div>
                <div class="text-dark" style="font-size: 0.75rem; line-height: 1.4;">
                    Tus fondos están respaldados por la pasarela segura de <span class="fw-bold" style="color: #7F1D3F;">SPOTLIGHT Ubaté</span>.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="vs-card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: var(--sl-primary-soft, #F6E7EB);">
                <i class="bi bi-clock-history fs-5" style="color: #7F1D3F;"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Últimas ventas</h6>
                <div class="text-muted" style="font-size: 0.75rem;">Transacciones registradas en tiempo real</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('business.billing.sales') }}" class="btn btn-sm text-white rounded-pill px-4 py-2 fw-medium shadow-sm d-flex align-items-center gap-2 border-0" style="background: var(--sl-primary, #7F1D3F);">
                <i class="bi bi-eye"></i> Ver todas las ventas ({{ $totalOrdersCount ?? $sales->count() }})
            </a>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
            <thead>
                <tr class="text-uppercase text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    <th class="fw-bold border-0 pb-2 px-3">PRODUCTO</th>
                    <th class="fw-bold border-0 pb-2">COMPRADOR</th>
                    <th class="fw-bold border-0 pb-2 text-center">CANT.</th>
                    <th class="fw-bold border-0 pb-2 text-center">ESTADO</th>
                    <th class="fw-bold border-0 pb-2 text-start">TOTAL</th>
                    <th class="fw-bold border-0 pb-2 text-start">FECHA</th>
                    <th class="fw-bold border-0 pb-2 text-end px-3">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sales as $i => $sale)
            @php 
                $orderStatus = $sale->order ? ($sale->order->order_status ?: $sale->order->status) : 'paid';
                $isPaid = $sale->order ? $sale->order->isPaid() : true;
                $st = match($orderStatus) {
                    'paid', 'approved', 'confirmed' => ['label'=>'Pagado', 'color'=>'#10b981', 'bg'=>'#d1fae5'],
                    'in_preparation' => ['label'=>'En preparación', 'color'=>'#8b5cf6', 'bg'=>'#ede9fe'],
                    'shipped' => ['label'=>'Despachado', 'color'=>'#6366f1', 'bg'=>'#e0e7ff'],
                    'delivered' => ['label'=>'Entregado', 'color'=>'#0f172a', 'bg'=>'#f1f5f9'],
                    default => ['label'=>'Pagado', 'color'=>'#10b981', 'bg'=>'#d1fae5']
                };
                $buyerName = $sale->order->user->name ?? 'Consumidor';
                $buyerInitials = strtoupper(substr($buyerName, 0, 1) . (str_contains($buyerName, ' ') ? substr(explode(' ', $buyerName)[1] ?? '', 0, 1) : ''));
            @endphp
            <tr style="background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.04); border-radius: 12px;">
                <td class="border-start border-top border-bottom rounded-start-4 p-3 ps-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #FAF5F6; border: 1px solid var(--sl-border);">
                            <i class="bi bi-box-seam fs-5" style="color: #7F1D3F;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark text-sm">{{ $sale->getEffectiveProductNameAttribute() }}</div>
                            <div class="text-muted mt-1" style="font-size: 0.65rem;">
                                @if($sale->product_sku) SKU: {{ $sale->product_sku }} • @endif {{ $company->category ?? 'Artesanal' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="border-top border-bottom p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.65rem; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F);">
                            {{ $buyerInitials }}
                        </div>
                        <div class="fw-semibold text-dark" style="font-size: 0.8rem;">{{ $buyerName }}</div>
                    </div>
                </td>
                <td class="border-top border-bottom p-3 text-center fw-semibold text-dark">{{ $sale->quantity }}</td>
                <td class="border-top border-bottom p-3 text-center">
                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1 py-2 px-3" style="background-color: {{ $st['bg'] }}; color: {{ $st['color'] }}; font-weight: 600; font-size: 0.7rem;">
                        <span class="rounded-circle" style="width: 6px; height: 6px; background-color: {{ $st['color'] }};"></span>
                        {{ $st['label'] }}
                    </span>
                </td>
                <td class="border-top border-bottom p-3 text-start fw-bold" style="color: #7F1D3F;">${{ number_format($sale->subtotal ?: ($sale->price * $sale->quantity),0,',','.') }}</td>
                <td class="border-top border-bottom p-3 text-start text-muted text-sm">{{ $sale->created_at->format('d/m/Y') }}</td>
                <td class="border-end border-top border-bottom rounded-end-4 p-3 text-end pe-4">
                    <a href="{{ route('business.billing.sales', ['search' => $sale->order_id]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                        Ver en Ventas &rarr;
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-5">No hay ventas registradas aún.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <span class="text-muted fw-medium" style="font-size: 0.75rem;">Mostrando {{ $sales->count() }} ventas recientes</span>
        <a href="{{ route('business.billing.sales') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
            Ir al Panel Completo de Ventas &rarr;
        </a>
    </div>
</div>
@endsection
