@extends('layouts.dashboard')
@section('title','Ventas y Egresos')
@section('page-title','Ventas e Información Financiera')

@section('dashboard-content')
{{-- Flash Alerts --}}
@if(session('success'))
    <div class="alert alert-success border-0 d-flex align-items-center mb-4 rounded-4 shadow-sm" style="background-color: #ecfdf5; color: #065f46;">
        <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
        <div class="fw-semibold text-sm">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 d-flex align-items-center mb-4 rounded-4 shadow-sm" style="background-color: #fef2f2; color: #991b1b;">
        <i class="bi bi-exclamation-octagon-fill fs-5 me-3 text-danger"></i>
        <div class="fw-semibold text-sm">{{ session('error') }}</div>
    </div>
@endif

{{-- Header Area --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge rounded-pill fw-bold d-flex align-items-center gap-2 px-3 py-2" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.65rem; border: 1px solid var(--sl-border); letter-spacing: 0.5px;">
                <span class="rounded-circle" style="width: 6px; height: 6px; background-color: var(--sl-primary, #7F1D3F);"></span>
                FINANZAS & CONTROL
            </span>
        </div>
        <h3 class="fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">Ventas e Información Financiera</h3>
        <p class="text-muted mb-0" style="font-size: 0.85rem; max-width: 650px; line-height: 1.5;">Control integral de flujo de caja, balance operativo y registro detallado de egresos para tu negocio en tiempo real.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
        <a href="{{ route('business.expenses.export_pdf', request()->query()) }}" target="_blank" class="btn bg-white border text-dark shadow-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 fw-semibold" style="font-size: 0.85rem; transition: all 0.2s;">
            <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Exportar PDF
        </a>
        <button onclick="exportExpensesCSV()" class="btn bg-white border text-dark shadow-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 fw-semibold" style="font-size: 0.85rem; transition: all 0.2s;">
            <i class="bi bi-file-earmark-spreadsheet text-success"></i> Exportar CSV
        </button>
        <a href="{{ route('business.expenses.create') }}" class="btn text-white rounded-pill px-4 py-2 d-flex align-items-center gap-2 fw-semibold border-0 shadow-sm" style="background: linear-gradient(135deg, #7F1D3F, #D98C9A); font-size: 0.85rem; transition: all 0.2s;">
            <i class="bi bi-plus-lg"></i> Registrar egreso
        </a>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-4 mb-4">
    <!-- Card 1: Ingresos -->
    <div class="col-sm-6 col-lg-3">
        <div class="vs-card h-100 border rounded-4 p-4 shadow-sm" style="background: white; border-color: #f1f5f9;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">INGRESOS TOTALES</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #dcfce7;">
                    <i class="bi bi-arrow-up-short fs-4 text-success"></i>
                </div>
            </div>
            <div class="fw-bold text-dark mb-2" style="font-size: 1.6rem; letter-spacing: -0.5px;">${{ number_format($incomes->sum('amount') ?: 0, 0, ',', '.') }} <span class="text-muted fw-bold" style="font-size: 0.65rem;">COP</span></div>
            <div class="d-flex align-items-center gap-1 mt-auto" style="font-size: 0.75rem;">
                <span class="text-success fw-bold" style="background: #dcfce7; padding: 2px 6px; border-radius: 4px;">{{ $incomes->count() }} ventas</span> <span class="text-muted ms-1">registradas</span>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Egresos -->
    <div class="col-sm-6 col-lg-3">
        <div class="vs-card h-100 border rounded-4 p-4 shadow-sm" style="background: white; border-color: #f1f5f9;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">EGRESOS TOTALES</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #fee2e2;">
                    <i class="bi bi-arrow-down-short fs-4 text-danger"></i>
                </div>
            </div>
            <div class="fw-bold text-danger mb-2" style="font-size: 1.6rem; letter-spacing: -0.5px;">${{ number_format($expenses->sum('amount') ?: 0, 0, ',', '.') }} <span class="text-muted fw-bold" style="font-size: 0.65rem;">COP</span></div>
            <div class="d-flex align-items-center gap-1 mt-auto" style="font-size: 0.75rem;">
                <span class="fw-bold text-dark">{{ $expenses->count() }} registros</span> <span class="text-muted">acumulados</span>
            </div>
        </div>
    </div>
    
    <!-- Card 3: Balance Neto -->
    <div class="col-sm-6 col-lg-3">
        <div class="vs-card h-100 border rounded-4 p-4 shadow-sm" style="background: white; border-color: #f1f5f9;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">BALANCE NETO</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: var(--sl-primary-soft, #F6E7EB);">
                    <i class="bi bi-currency-dollar fs-5" style="color: var(--sl-primary, #7F1D3F);"></i>
                </div>
            </div>
            @php $balance = $incomes->sum('amount') - $expenses->sum('amount'); @endphp
            <div class="fw-bold text-dark mb-2" style="font-size: 1.6rem; letter-spacing: -0.5px;">${{ number_format($balance, 0, ',', '.') }} <span class="text-muted fw-bold" style="font-size: 0.65rem;">COP</span></div>
            <div class="d-flex align-items-center gap-2 mt-auto" style="font-size: 0.75rem;">
                <span class="badge fw-bold text-dark px-2 py-1 shadow-sm" style="background: #fef08a; border: 1px solid #fde047;">Margen Neto</span>
                <span class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold ms-1">{{ $balance >= 0 ? 'Positivo' : 'Déficit' }}</span>
            </div>
        </div>
    </div>
    
    <!-- Card 4: Soportes y Facturas -->
    <div class="col-sm-6 col-lg-3">
        <div class="vs-card h-100 border rounded-4 p-4 shadow-sm" style="background: white; border-color: #f1f5f9;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">FACTURAS Y SOPORTES</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #ede9fe;">
                    <i class="bi bi-paperclip fs-5" style="color: #6366f1;"></i>
                </div>
            </div>
            <div class="fw-bold text-dark mb-2" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                {{ $expenses->whereNotNull('invoice_path')->count() }}
                <span class="text-muted fw-normal" style="font-size: 0.7rem;">adjuntos</span>
            </div>
            <div class="d-flex align-items-center gap-1 mt-auto" style="font-size: 0.75rem;">
                <span class="text-muted fw-medium">Listos para visualización flotante</span>
            </div>
        </div>
    </div>
</div>

{{-- Main Container --}}
<div class="vs-card border-0 shadow-sm rounded-4 bg-white mb-5 p-4">
    <!-- Tabs & Filters -->
    <form action="{{ route('business.expenses.index') }}" method="GET" id="filter-form">
        <input type="hidden" name="tab" id="tab-input" value="{{ $tab }}">
        
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-4 mb-4">
            <!-- Tabs -->
            <div class="d-flex align-items-center gap-4 border-bottom w-100 w-xl-auto pb-0" style="border-color: #f1f5f9 !important;">
                <div onclick="setTab('egresos')" class="position-relative pb-3 cursor-pointer d-flex align-items-center">
                    <span class="fw-bold {{ $tab === 'egresos' ? 'text-dark' : 'text-muted' }}" style="font-size: 0.85rem;">Egresos registrados</span>
                    <span class="badge rounded-pill ms-2 fw-bold" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.65rem;">{{ $expenses->count() }}</span>
                    @if($tab === 'egresos') <div class="position-absolute bottom-0 start-0 w-100" style="height: 3px; background: var(--sl-primary, #7F1D3F); border-radius: 3px 3px 0 0;"></div> @endif
                </div>
                <div onclick="setTab('ingresos')" class="position-relative pb-3 cursor-pointer d-flex align-items-center">
                    <span class="fw-bold {{ $tab === 'ingresos' ? 'text-dark' : 'text-muted' }}" style="font-size: 0.85rem;">Historial de ingresos</span>
                    <span class="badge rounded-pill ms-2 fw-bold" style="background: #f0fdf4; color: #166534; font-size: 0.65rem;">{{ $incomes->count() }}</span>
                    @if($tab === 'ingresos') <div class="position-absolute bottom-0 start-0 w-100" style="height: 3px; background: #166534; border-radius: 3px 3px 0 0;"></div> @endif
                </div>
                <div onclick="setTab('balance')" class="position-relative pb-3 cursor-pointer d-flex align-items-center">
                    <span class="fw-bold {{ $tab === 'balance' ? 'text-dark' : 'text-muted' }}" style="font-size: 0.85rem;">Balance general</span>
                    @if($tab === 'balance') <div class="position-absolute bottom-0 start-0 w-100" style="height: 3px; background: #4f46e5; border-radius: 3px 3px 0 0;"></div> @endif
                </div>
            </div>

            <!-- Filters -->
            <div class="d-flex flex-wrap align-items-center gap-3 w-100 w-xl-auto">
                <div class="position-relative flex-grow-1 flex-md-grow-0" style="min-width: 240px;">
                    <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.8rem;"></i>
                    <input type="text" name="search" value="{{ $search }}" onchange="document.getElementById('filter-form').submit()" class="form-control rounded-pill text-sm shadow-sm" placeholder="Buscar por concepto..." style="padding-left: 2.5rem; font-size: 0.8rem; height: 38px; border: 1px solid #e2e8f0; background: #fdfcfc;">
                </div>
                <div class="d-flex gap-2">
                    <select name="category" onchange="document.getElementById('filter-form').submit()" class="form-select rounded-pill shadow-sm text-dark px-3" style="font-size: 0.8rem; height: 38px; border-color: #e2e8f0 !important; cursor: pointer;">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select name="month" onchange="document.getElementById('filter-form').submit()" class="form-select rounded-pill shadow-sm text-dark px-3 fw-bold" style="font-size: 0.8rem; height: 38px; border-color: #e2e8f0 !important; cursor: pointer;">
                        <option value="">Todos los meses</option>
                        <option value="{{ date('Y-m') }}" {{ $month === date('Y-m') ? 'selected' : '' }}>Este mes ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</option>
                        <option value="{{ date('Y-m', strtotime('-1 month')) }}" {{ $month === date('Y-m', strtotime('-1 month')) ? 'selected' : '' }}>Mes anterior</option>
                    </select>
                    @if($search || $category || $month)
                        <a href="{{ route('business.expenses.index', ['tab' => $tab]) }}" class="btn btn-sm btn-light border rounded-pill px-3 d-flex align-items-center justify-content-center" title="Limpiar filtros" style="height: 38px;">
                            <i class="bi bi-x-lg text-muted"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0 10px;">
            <thead>
                <tr class="text-uppercase text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    <th class="fw-bold border-0 pb-2 px-3">CONCEPTO</th>
                    <th class="fw-bold border-0 pb-2">MONTO</th>
                    <th class="fw-bold border-0 pb-2">FECHA</th>
                    <th class="fw-bold border-0 pb-2">DESCRIPCIÓN & DETALLE</th>
                    <th class="fw-bold border-0 pb-2 text-center">FACTURA / SOPORTE</th>
                    <th class="fw-bold border-0 pb-2 text-center px-3">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $i => $expense)
            @php
                $isExpense = ($expense->type === 'expense');
                if (!$isExpense) {
                    $c = ['icon'=>'bi-cash', 'bg'=>'#dcfce7', 'color'=>'#15803d', 'tagBg'=>'#f0fdf4', 'tagText'=>'Ingreso de Venta', 'tagColor'=>'#166534'];
                } else {
                    $cat = $expense->category ?: 'Operación General';
                    $catColors = [
                        'Marketing Digital'        => ['icon'=>'bi-instagram', 'bg'=>'#F6E7EB', 'color'=>'#7F1D3F', 'tagBg'=>'#FAF5F6', 'tagColor'=>'#7F1D3F'],
                        'Insumos de Producción'   => ['icon'=>'bi-box-seam', 'bg'=>'#fef3c7', 'color'=>'#d97706', 'tagBg'=>'#fef3c7', 'tagColor'=>'#b45309'],
                        'Distribución & Logística' => ['icon'=>'bi-truck', 'bg'=>'#e0e7ff', 'color'=>'#4f46e5', 'tagBg'=>'#eff6ff', 'tagColor'=>'#4338ca'],
                        'Servicios Públicos'      => ['icon'=>'bi-lightning-charge', 'bg'=>'#fef9c3', 'color'=>'#ca8a04', 'tagBg'=>'#fef9c3', 'tagColor'=>'#a16207'],
                        'Arrendamiento'           => ['icon'=>'bi-building', 'bg'=>'#f1f5f9', 'color'=>'#334155', 'tagBg'=>'#f8fafc', 'tagColor'=>'#334155'],
                        'Nómina y Honorarios'     => ['icon'=>'bi-people', 'bg'=>'#dbeafe', 'color'=>'#2563eb', 'tagBg'=>'#eff6ff', 'tagColor'=>'#1d4ed8'],
                    ];
                    $c = $catColors[$cat] ?? ['icon'=>'bi-cash-coin', 'bg'=>'#f1f5f9', 'color'=>'#475569', 'tagBg'=>'#f8fafc', 'tagColor'=>'#334155'];
                    $c['tagText'] = $cat;
                }
            @endphp
            <tr style="background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.04); border-radius: 12px; transition: transform 0.2s;">
                <td class="border-start border-top border-bottom rounded-start-4 p-3 ps-4" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: {{ $c['bg'] }};">
                            <i class="bi {{ $c['icon'] }} fs-5" style="color: {{ $c['color'] }};"></i>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $expense->concept }}</div>
                            <span class="badge px-2 py-1 fw-semibold text-start w-auto d-inline-block" style="background: {{ $c['tagBg'] }}; color: {{ $c['tagColor'] }}; font-size: 0.65rem; border: 1px solid rgba(0,0,0,0.05);">{{ $c['tagText'] }}</span>
                        </div>
                    </div>
                </td>
                <td class="border-top border-bottom p-3" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle" style="width: 6px; height: 6px; background: {{ $expense->type === 'income' ? '#10b981' : '#ef4444' }};"></span>
                        <span class="fw-bold {{ $expense->type === 'income' ? 'text-success' : 'text-danger' }}" style="font-size: 0.95rem;">${{ number_format($expense->amount,0,',','.') }} <span style="font-size: 0.65rem;">COP</span></span>
                    </div>
                </td>
                <td class="border-top border-bottom p-3 text-muted" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                        <i class="bi bi-clock text-muted"></i> {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                    </div>
                </td>
                <td class="border-top border-bottom p-3" style="border-color: #f1f5f9 !important;">
                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ Str::limit($expense->description ?: 'Registro sin detalle adicional', 45) }}</div>
                    <div class="text-muted mt-1" style="font-size: 0.68rem;">{{ $expense->type === 'income' ? 'Venta acreditada en tienda' : 'Gasto operativo de la empresa' }}</div>
                </td>
                {{-- FACTURA / SOPORTE CON VENTANA FLOTANTE --}}
                <td class="border-top border-bottom p-3 text-center" style="border-color: #f1f5f9 !important;">
                    @if($isExpense)
                        @if($expense->invoice_path)
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-2 border fw-semibold" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.72rem; border-color: var(--sl-border, #EEDDDC);" data-bs-toggle="modal" data-bs-target="#invoiceModal{{ $expense->id }}" title="Ver factura en ventana flotante">
                                <i class="bi {{ $expense->isPdf() ? 'bi-file-earmark-pdf-fill text-danger' : 'bi-file-earmark-image-fill text-success' }} fs-6"></i>
                                <span>{{ $expense->isPdf() ? 'Ver Factura PDF' : 'Ver Soporte' }}</span>
                            </button>
                        @else
                            <a href="{{ route('business.expenses.edit', $expense->id) }}" class="badge bg-light text-muted border text-decoration-none px-2 py-1" style="font-size: 0.68rem;" title="Clic para adjuntar factura">
                                <i class="bi bi-paperclip me-1"></i> Adjuntar soporte
                            </a>
                        @endif
                    @else
                        <span class="badge bg-light text-success border px-2 py-1" style="font-size: 0.68rem;">
                            <i class="bi bi-cart-check me-1"></i> Pedido Pagado
                        </span>
                    @endif
                </td>
                {{-- ACCIONES --}}
                <td class="border-end border-top border-bottom rounded-end-4 p-3 text-center pe-4" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        @if($isExpense)
                            @if($expense->invoice_path)
                                <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center rounded" style="width: 32px; height: 32px; background: #f1f5f9; color: #475569; border: none;" data-bs-toggle="modal" data-bs-target="#invoiceModal{{ $expense->id }}" title="Ver factura flotante">
                                    <i class="bi bi-eye" style="font-size: 0.9rem;"></i>
                                </button>
                            @endif
                            <a href="{{ route('business.expenses.edit', $expense->id) }}" class="btn btn-sm d-flex align-items-center justify-content-center rounded text-primary" style="width: 32px; height: 32px; background: #eff6ff; border: none;" title="Editar egreso">
                                <i class="bi bi-pencil" style="font-size: 0.9rem;"></i>
                            </a>
                            <form action="{{ route('business.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este egreso y su soporte?')" class="m-0">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm text-danger d-flex align-items-center justify-content-center rounded" style="width: 32px; height: 32px; background: #fee2e2; border: none; transition: background 0.2s;" title="Eliminar egreso">
                                    <i class="bi bi-trash" style="font-size: 0.9rem;"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>

            {{-- VENTANA FLOTANTE (MODAL) PARA VISUALIZAR LA FACTURA --}}
            @if($isExpense && $expense->invoice_path)
            <div class="modal fade" id="invoiceModal{{ $expense->id }}" tabindex="-1" aria-labelledby="invoiceModalLabel{{ $expense->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header py-3 px-4 border-bottom" style="background: linear-gradient(135deg, #FAF5F6 0%, #FFFFFF 100%);">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="modal-title fw-bold text-dark mb-0" id="invoiceModalLabel{{ $expense->id }}">
                                        Factura / Soporte de Egreso #{{ $expense->id }}
                                    </h5>
                                    <span class="badge rounded-pill fw-bold" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.65rem;">
                                        {{ $expense->category ?: 'Operación General' }}
                                    </span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <strong>{{ $expense->concept }}</strong> • Monto: ${{ number_format($expense->amount, 0, ',', '.') }} COP • Fecha: {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 text-center bg-light">
                            @if($expense->isPdf())
                                <div class="rounded-4 overflow-hidden shadow-sm border bg-white" style="height: 520px;">
                                    <iframe src="{{ $expense->invoice_url }}" class="w-100 h-100" style="border: none;"></iframe>
                                </div>
                            @else
                                <div class="rounded-4 p-3 bg-white border shadow-sm d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                    <img src="{{ $expense->invoice_url }}" alt="Soporte Factura" class="img-fluid rounded-3" style="max-height: 520px; object-fit: contain;">
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-3 text-muted small px-1">
                                <div>
                                    <i class="bi bi-paperclip me-1"></i> Archivo: <strong>{{ $expense->invoice_filename ?: 'factura_egreso_'.$expense->id }}</strong>
                                </div>
                                <div>
                                    <span>Comprobante digital para auditoría interna</span>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-top py-2 px-4 bg-white d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <a href="{{ $expense->invoice_url }}" download="{{ $expense->invoice_filename ?: 'factura_'.$expense->id }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="bi bi-download me-1"></i> Descargar
                                </a>
                                <a href="{{ $expense->invoice_url }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Abrir en Pestaña
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('business.expenses.edit', $expense->id) }}" class="btn btn-sm btn-spotlight rounded-pill px-3">
                                    <i class="bi bi-pencil me-1"></i> Editar Egreso
                                </a>
                                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @empty
            <tr><td colspan="6" class="text-center text-muted py-5">No hay transacciones en esta vista.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination/Footer -->
    <div class="d-flex justify-content-between align-items-center mt-3 pt-4 border-top" style="border-color: #f1f5f9 !important;">
        <span class="text-muted fw-medium" style="font-size: 0.8rem;">Mostrando {{ $transactions->count() }} transacciones registradas</span>
    </div>
</div>

<script>
    function setTab(tab) {
        document.getElementById('tab-input').value = tab;
        document.getElementById('filter-form').submit();
    }

    function exportExpensesCSV() {
        let csv = "ID,Concepto,Categoria,Monto,Tipo,Fecha,Descripcion,Tiene_Factura\n";
        @foreach($transactions as $e)
            @php 
                $hasInv = ($e->type === 'expense' && !empty($e->invoice_path)) ? 'SI' : 'NO';
                $catVal = $e->category ?? ($e->type === 'income' ? 'Ingreso' : 'Operación General');
            @endphp
            csv += `{{$e->id}},"{{ str_replace('"', '""', $e->concept) }}","{{ str_replace('"', '""', $catVal) }}",{{$e->amount}},{{$e->type}},{{ \Carbon\Carbon::parse($e->date)->format('Y-m-d') }},"{{ str_replace('"', '""', $e->description ?? '') }}",{{$hasInv}}\n`;
        @endforeach
        let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'transacciones_{{ date("Ymd_His") }}.csv';
        a.click();
    }
</script>
<style>
    .cursor-pointer { cursor: pointer; }
    tr:hover { transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important; }
</style>
@endsection
