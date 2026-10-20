@extends('layouts.dashboard')
@section('title', 'Gestión de Envíos y Despachos - Spotlight')
@section('page-title', 'Gestión de Envíos y Despachos')

@section('dashboard-content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">Centro Logístico de Despachos</h4>
        <p class="text-muted text-sm mb-0">Controla la preparación, generación de guías y despacho con transportadoras en tiempo real.</p>
    </div>
    <div>
        <a href="{{ route('business.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 shadow-sm d-flex align-items-center gap-2 mb-4 p-3" style="background: rgba(16, 185, 129, 0.12); color: #065f46;">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-semibold">{{ session('success') }}</div>
    </div>
@endif

<!-- Quick Metrics -->
<div class="row g-3 mb-4">
    @php
        $pendingCount = $shipments->whereIn('status', ['pending', 'preparing'])->count();
        $inTransitCount = $shipments->whereIn('status', ['dispatched', 'in_transit', 'out_for_delivery'])->count();
        $deliveredCount = $shipments->where('status', 'delivered')->count();
    @endphp
    <div class="col-sm-4">
        <div class="vs-card border-0 shadow-sm p-4 bg-white rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Por Despachar</span>
                <span class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 36px; height: 36px;">
                    <i class="bi bi-box-seam fs-5"></i>
                </span>
            </div>
            <div class="display-6 fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">{{ $pendingCount }}</div>
            <div class="text-muted text-xs mt-1">Paquetes en preparación</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="vs-card border-0 shadow-sm p-4 bg-white rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">En Tránsito / Reparto</span>
                <span class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                    <i class="bi bi-truck fs-5"></i>
                </span>
            </div>
            <div class="display-6 fw-bold text-primary mb-0" style="font-family: 'Outfit', sans-serif;">{{ $inTransitCount }}</div>
            <div class="text-muted text-xs mt-1">Con transportadora en camino</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="vs-card border-0 shadow-sm p-4 bg-white rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted text-xs text-uppercase fw-bold">Entregados</span>
                <span class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 36px; height: 36px;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </span>
            </div>
            <div class="display-6 fw-bold text-success mb-0" style="font-family: 'Outfit', sans-serif;">{{ $deliveredCount }}</div>
            <div class="text-muted text-xs mt-1">Entregas confirmadas</div>
        </div>
    </div>
</div>

<!-- Shipments Table -->
<div class="vs-card border-0 shadow-sm p-4 rounded-4 bg-white">
    <div class="table-responsive">
        <table class="table align-middle border-light mb-0">
            <thead class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-3 py-3 border-0">Orden / Guía</th>
                    <th class="py-3 border-0">Destinatario</th>
                    <th class="py-3 border-0">Transportadora</th>
                    <th class="py-3 border-0 text-center">Estado Logístico</th>
                    <th class="py-3 border-0">Ubicación Actual</th>
                    <th class="pe-3 py-3 border-0 text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($shipments as $shipment)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td class="ps-3 py-3">
                        <div class="fw-bold text-dark">Orden #{{ $shipment->order_id }}</div>
                        <span class="font-monospace text-xs text-primary fw-semibold">{{ $shipment->tracking_number }}</span>
                        <div class="mt-1">
                            @if($shipment->order && $shipment->order->isPaid())
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    <i class="bi bi-shield-check"></i> Pago Verificado
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-15 text-dark border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    <i class="bi bi-clock-history text-warning"></i> Pago No Verificado
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="py-3">
                        <div class="fw-semibold text-dark text-sm">{{ $shipment->recipient_name }}</div>
                        <div class="text-muted text-xs text-truncate" style="max-width: 220px;" title="{{ $shipment->destination_address }}">
                            {{ $shipment->destination_address }} ({{ $shipment->destination_city }})
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 text-xs fw-semibold">
                            <i class="bi bi-truck text-primary me-1"></i> {{ $shipment->carrier_name }}
                        </span>
                    </td>
                    <td class="py-3 text-center">
                        <span class="badge {{ $shipment->status_badge_class }} rounded-pill px-3 py-1 fw-bold text-xs">
                            {{ $shipment->status_label }}
                        </span>
                    </td>
                    <td class="py-3 text-sm text-muted">
                        <i class="bi bi-geo-alt text-danger me-1"></i> {{ $shipment->current_location ?: $shipment->origin_city }}
                    </td>
                    <td class="pe-3 py-3 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <!-- Botón Despachar si aún está en preparación -->
                            @if(in_array($shipment->status, ['pending', 'preparing']))
                                @if($shipment->order && $shipment->order->isPaid())
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-2xs" data-bs-toggle="modal" data-bs-target="#modalDispatch-{{ $shipment->id }}">
                                        <i class="bi bi-send-fill me-1"></i> Despachar
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-light border text-muted rounded-pill px-3 fw-semibold" disabled title="No se puede despachar: la transacción de pago no ha sido verificada como exitosa">
                                        <i class="bi bi-lock-fill me-1"></i> Pago Pendiente
                                    </button>
                                @endif
                            @else
                                <!-- Botón Actualizar Estado -->
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#modalUpdate-{{ $shipment->id }}">
                                    <i class="bi bi-arrow-repeat me-1"></i> Estado
                                </button>
                            @endif

                            <!-- Enlace al rastreo en vivo -->
                            <a href="{{ route('public.tracking', ['guia' => $shipment->tracking_number]) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2" title="Ver rastreo público">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>

                        <!-- Modal Despachar -->
                        <div class="modal fade" id="modalDispatch-{{ $shipment->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('business.shipments.dispatch', $shipment) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Despachar Pedido #{{ $shipment->order_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body py-3">
                                            @if(!$shipment->order || !$shipment->order->isPaid())
                                                <div class="alert alert-danger rounded-3 text-xs mb-3">
                                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                                    <strong>Pago No Verificado:</strong> Esta orden aún no tiene una transacción bancaria confirmada como exitosa. El despacho permanece bloqueado.
                                                </div>
                                            @endif
                                            <p class="text-muted text-sm mb-3">Asigna la transportadora oficial y el número de guía para notificar al cliente en tiempo real.</p>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark text-xs">Empresa de Transporte / Courier:</label>
                                                <select name="carrier" class="form-select rounded-3">
                                                    @foreach($carriers as $key => $c)
                                                        <option value="{{ $key }}" {{ $shipment->carrier === $key ? 'selected' : '' }}>
                                                            {{ $c['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark text-xs">Número de Guía Oficial:</label>
                                                <input type="text" name="tracking_number" value="{{ $shipment->tracking_number }}" class="form-control rounded-3 font-monospace" placeholder="Deja en blanco para auto-generar">
                                                <span class="text-muted text-xs">Puedes ingresar el código de guía física de la transportadora o dejar el sugerido.</span>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark text-xs">Notas de despacho (opcional):</label>
                                                <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Ej: Paquete sellado con cinta de seguridad, peso 1.2 kg."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-sm btn-spotlight rounded-pill px-4 fw-bold">Confirmar Despacho</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actualizar Estado -->
                        <div class="modal fade" id="modalUpdate-{{ $shipment->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('business.shipments.status', $shipment) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Actualizar Estado: {{ $shipment->tracking_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body py-3">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark text-xs">Nuevo Estado:</label>
                                                <select name="status" class="form-select rounded-3">
                                                    <option value="in_transit" {{ $shipment->status === 'in_transit' ? 'selected' : '' }}>En Tránsito hacia Destino</option>
                                                    <option value="out_for_delivery" {{ $shipment->status === 'out_for_delivery' ? 'selected' : '' }}>En Ruta de Entrega (Reparto Local)</option>
                                                    <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Entregado al Destinatario</option>
                                                    <option value="failed" {{ $shipment->status === 'failed' ? 'selected' : '' }}>Novedad / Retraso en Envío</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark text-xs">Ubicación Actual / Hub:</label>
                                                <input type="text" name="location" value="{{ $shipment->current_location }}" class="form-control rounded-3" placeholder="Ej: Centro de Distribución Siberia, Bogotá">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark text-xs">Detalle del Evento:</label>
                                                <input type="text" name="description" class="form-control rounded-3" placeholder="Ej: Arribo a terminal central para asignación a repartidor local">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-sm btn-spotlight rounded-pill px-4 fw-bold">Guardar Actualización</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-box2 fs-1 text-muted d-block mb-2"></i>
                        No tienes envíos registrados en este momento.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $shipments->links() }}
    </div>
</div>
@endsection
