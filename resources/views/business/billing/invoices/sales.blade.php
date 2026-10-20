@extends('layouts.dashboard')

@section('page-title', 'Panel de Ventas y Pedidos')

@section('dashboard-content')
<div class="container-fluid px-0">

    {{-- Encabezado Principal --}}
    <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1e1135 0%, #2b0b3a 100%); color: white;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill bg-success text-white px-3 py-1.5 text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-patch-check-fill me-1"></i> Facturación Electrónica DIAN
                    </span>
                    <span class="badge rounded-pill bg-white bg-opacity-20 text-white px-3 py-1.5" style="font-size: 0.72rem;">
                        {{ $company->razon_social ?: $company->name }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-1" style="font-family: 'Outfit', sans-serif;">
                    Panel de Ventas y Pedidos
                </h3>
                <p class="text-white-50 small mb-0">
                    Supervisa todas las compras de tus clientes, el recaudo procesado a través de Wompi, el estado de liquidación bancaria neta y la emisión de Facturas Electrónicas de Venta validadas con CUFE ante la DIAN.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('business.billing.invoices.create') }}" class="btn btn-spotlight px-3 py-2 rounded-pill shadow-sm fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Factura Directa
                </a>
                <a href="{{ route('business.billing.sales', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-outline-light px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Exportar CSV
                </a>
                <a href="{{ route('business.billing.index') }}" class="btn btn-outline-light px-3 py-2 rounded-pill" style="font-size: 0.85rem;">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Panel
                </a>
            </div>
        </div>
    </div>

    {{-- Tarjetas de Métricas Clave --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Ventas Totales</span>
                    <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success"><i class="bi bi-currency-dollar fs-5"></i></div>
                </div>
                <h4 class="fw-bold text-dark mb-1">${{ number_format($stats['total_sales'] ?? 0, 0, ',', '.') }}</h4>
                <div class="text-muted small">{{ $stats['total_orders'] ?? 0 }} pedidos registrados</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Liquidación Neta</span>
                    <div class="rounded-circle p-2 text-white" style="background-color: #2A875D;"><i class="bi bi-wallet2 fs-5"></i></div>
                </div>
                <h4 class="fw-bold text-success mb-1">${{ number_format($stats['total_settled'] ?? 0, 0, ',', '.') }}</h4>
                <div class="text-muted small">Fondos liquidados Wompi</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Facturas Validadas</span>
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary"><i class="bi bi-patch-check fs-5"></i></div>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $stats['billed_count'] ?? 0 }}</h4>
                <div class="text-success small fw-semibold"><i class="bi bi-check-all"></i> Con código QR y CUFE</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Por Facturar</span>
                    <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history fs-5"></i></div>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $stats['pending_count'] ?? 0 }}</h4>
                <div class="small {{ ($stats['pending_count'] ?? 0) > 0 ? 'text-warning fw-semibold' : 'text-success' }}">
                    {{ ($stats['pending_count'] ?? 0) > 0 ? 'Listos para emitir a la DIAN' : 'Todas las ventas al día' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="vs-card p-3 p-md-4 mb-4">
        <form method="GET" action="{{ route('business.billing.sales') }}" id="salesFilterForm">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control bg-light border-start-0" placeholder="Buscar por # de pedido, referencia, cliente o documento...">
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <select name="billing_status" class="form-select text-dark" onchange="document.getElementById('salesFilterForm').submit()">
                        <option value="">Estado Fiscal: Todos</option>
                        <option value="not_billed" {{ $billingStatus === 'not_billed' ? 'selected' : '' }}>⏳ Pendientes de Facturar</option>
                        <option value="billed" {{ $billingStatus === 'billed' ? 'selected' : '' }}>✅ Facturados ante DIAN</option>
                    </select>
                </div>

                <div class="col-sm-6 col-md-2">
                    <select name="order_status" class="form-select text-dark" onchange="document.getElementById('salesFilterForm').submit()">
                        <option value="">Estado Pedido: Todos</option>
                        <option value="approved" {{ $orderStatus === 'approved' ? 'selected' : '' }}>Pagado / Aprobado</option>
                        <option value="pending" {{ $orderStatus === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="shipped" {{ $orderStatus === 'shipped' ? 'selected' : '' }}>Despachado</option>
                        <option value="delivered" {{ $orderStatus === 'delivered' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelled" {{ $orderStatus === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-spotlight w-100 rounded-pill shadow-sm fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    @if($search || $billingStatus || $orderStatus || $month)
                        <a href="{{ route('business.billing.sales') }}" class="btn btn-light border rounded-pill px-3 d-flex align-items-center justify-content-center" title="Limpiar filtros">
                            <i class="bi bi-x-lg text-muted"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Tabla de Ventas y Pedidos --}}
    <div class="vs-card p-4">
        @if($orders->isEmpty())
            <div class="text-center py-5">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #F6E7EB; color: #7F1D3F;">
                    <i class="bi bi-inbox fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No se encontraron ventas registradas</h5>
                <p class="text-muted small mb-3" style="max-width: 480px; margin: 0 auto;">
                    @if($search || $billingStatus || $orderStatus)
                        No hay pedidos que coincidan con los filtros aplicados. Intenta restablecer los filtros para ver todas tus ventas.
                    @else
                        Aún no tienes ventas en Spotlight. Cuando tus clientes compren tus productos o recibas pagos con Wompi, aparecerán aquí para liquidar y facturar ante la DIAN.
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-2">
                    @if($search || $billingStatus || $orderStatus)
                        <a href="{{ route('business.billing.sales') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer filtros
                        </a>
                    @endif
                    <a href="{{ route('business.billing.invoices.create') }}" class="btn btn-spotlight btn-sm px-3 rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Nueva Factura Directa
                    </a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th># Pedido / Ref</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total Venta</th>
                            <th>Liquidación Neta</th>
                            <th class="text-center">Estado Pedido</th>
                            <th class="text-center">Estado Fiscal DIAN</th>
                            <th class="text-end" style="min-width: 170px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $ordStatus = $order->order_status ?: $order->status;
                                $custName = $order->user ? $order->user->name : 'Consumidor Final';
                                $netAmount = $order->net_company_amount > 0 ? $order->net_company_amount : $order->total;
                            @endphp
                            <tr>
                                {{-- # Pedido y Referencia --}}
                                <td>
                                    <div class="fw-bold text-dark fs-6">#{{ $order->id }}</div>
                                    @if($order->reference)
                                        <div class="text-muted font-monospace" style="font-size: 0.68rem;" title="Referencia Spotlight / Wompi">
                                            {{ $order->reference }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Fecha --}}
                                <td>
                                    <div class="text-dark small fw-medium">{{ $order->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $order->created_at->format('H:i') }}
                                    </div>
                                </td>

                                {{-- Cliente --}}
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $custName }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        {{ $order->user && $order->user->email ? $order->user->email : 'Sin email registrado' }}
                                    </div>
                                </td>

                                {{-- Total Venta --}}
                                <td>
                                    <div class="fw-bold text-dark">${{ number_format($order->total, 0, ',', '.') }}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">
                                        {{ $order->details->count() }} producto(s) | {{ strtoupper($order->payment_method ?: 'Wompi') }}
                                    </div>
                                </td>

                                {{-- Liquidación Neta --}}
                                <td>
                                    <div class="fw-bold text-success">${{ number_format($netAmount, 0, ',', '.') }}</div>
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.65rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Liquidado
                                    </span>
                                </td>

                                {{-- Estado Pedido / Pago --}}
                                <td class="text-center">
                                    @if(in_array($ordStatus, ['paid', 'approved', 'confirmed']))
                                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill small fw-semibold">
                                            <i class="bi bi-check-circle me-1"></i>Pagado
                                        </span>
                                    @elseif(in_array($ordStatus, ['in_preparation', 'preparing']))
                                        <span class="badge bg-info text-white px-2.5 py-1 rounded-pill small fw-semibold">
                                            Preparación
                                        </span>
                                    @elseif($ordStatus === 'shipped')
                                        <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill small fw-semibold">
                                            Despachado
                                        </span>
                                    @elseif($ordStatus === 'cancelled')
                                        <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill small fw-semibold">
                                            Cancelado
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small fw-semibold">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>

                                {{-- Estado Fiscal DIAN --}}
                                <td class="text-center">
                                    @if($order->isBilled())
                                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill small fw-semibold">
                                            <i class="bi bi-check2-circle me-1"></i>Facturado
                                        </span>
                                        @if($order->electronicDocument)
                                            <div class="mt-1" style="font-size: 0.7rem;">
                                                <a href="{{ route('business.billing.documents.show', $order->electronicDocument) }}" class="text-decoration-none fw-bold" style="color: #7F1D3F;">
                                                    {{ $order->electronicDocument->full_number }} &rarr;
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small fw-semibold">
                                            <i class="bi bi-clock me-1"></i>Sin Facturar
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="text-end" style="white-space: nowrap;">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        {{-- Ver Detalle --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}" title="Ver resumen del pedido">
                                            <i class="bi bi-eye"></i> Detalle
                                        </button>

                                        {{-- Botón Facturar --}}
                                        @if(!$order->isBilled())
                                            <a href="{{ route('business.billing.invoices.create_from_order', $order) }}" class="btn btn-spotlight btn-sm px-3 py-1 rounded-pill shadow-sm fw-semibold" style="font-size: 0.8rem;">
                                                <i class="bi bi-receipt-cutoff me-1"></i> Facturar DIAN
                                            </a>
                                        @else
                                            @if($order->electronicDocument)
                                                <a href="{{ route('business.billing.documents.show', $order->electronicDocument) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" style="font-size: 0.8rem;">
                                                    <i class="bi bi-file-earmark-text me-1"></i> Factura
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <div class="text-muted small">
                    Mostrando {{ $orders->firstItem() ?? 0 }} a {{ $orders->lastItem() ?? 0 }} de {{ $orders->total() }} ventas registradas
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODALES DE RESUMEN DE PEDIDO (Fuera de la tabla para garantizar HTML válido y libre de saltos de diseño) --}}
    @foreach($orders as $order)
        @php
            $custName = $order->user ? $order->user->name : 'Consumidor Final';
        @endphp
        <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3 px-4 bg-light border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-dark mb-0" id="orderModalLabel{{ $order->id }}">
                                    Pedido #{{ $order->id }}
                                </h5>
                                @if($order->reference)
                                    <span class="badge bg-dark text-white font-monospace" style="font-size: 0.7rem;">{{ $order->reference }}</span>
                                @endif
                            </div>
                            <div class="text-muted small mt-1">Registrado el {{ $order->created_at->format('d/m/Y \a \l\a\s H:i') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border bg-light h-100">
                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">Datos del Comprador</h6>
                                    <div class="small">
                                        <div><strong>Nombre:</strong> {{ $custName }}</div>
                                        <div><strong>Email:</strong> {{ $order->user ? $order->user->email : 'N/A' }}</div>
                                        <div><strong>Teléfono:</strong> {{ $order->user && $order->user->phone ? $order->user->phone : 'No registrado' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border bg-light h-100">
                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">Información de Pago</h6>
                                    <div class="small">
                                        <div><strong>Método:</strong> {{ strtoupper($order->payment_method ?: 'Wompi') }}</div>
                                        <div><strong>Total Venta:</strong> ${{ number_format($order->total, 0, ',', '.') }} COP</div>
                                        <div><strong>Estado:</strong> <span class="badge bg-success text-white">Pagado</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-2 small text-uppercase">Productos Comprados</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered table-sm align-middle mb-0" style="font-size: 0.8rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center" style="width: 70px;">Cant.</th>
                                        <th class="text-end" style="width: 120px;">Precio Unit.</th>
                                        <th class="text-end" style="width: 120px;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->getEffectiveProductNameAttribute() }}</div>
                                                @if($item->product_sku)
                                                    <div class="text-muted font-monospace" style="font-size: 0.65rem;">SKU: {{ $item->product_sku }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                            <td class="text-end">${{ number_format($item->unit_price ?: ($item->price ?? 0), 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">${{ number_format($item->subtotal ?: (($item->unit_price ?: ($item->price ?? 0)) * $item->quantity), 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th class="text-end text-dark">${{ number_format($order->total, 0, ',', '.') }} COP</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer py-2 px-4 bg-light border-top">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                        @if(!$order->isBilled())
                            <a href="{{ route('business.billing.invoices.create_from_order', $order) }}" class="btn btn-sm btn-spotlight rounded-pill px-3 shadow-sm fw-semibold">
                                <i class="bi bi-receipt-cutoff me-1"></i> Facturar esta venta
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection
