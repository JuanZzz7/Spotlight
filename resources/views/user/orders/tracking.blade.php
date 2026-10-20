@extends('layouts.dashboard')
@section('title', 'Rastreo en Tiempo Real - Orden #' . $order->id . ' - Spotlight')
@section('page-title', 'Rastreo de Envío en Tiempo Real')

@push('styles')
<style>
    .pulse-live {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
        animation: pulseAnimation 1.6s infinite;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .stepper-container {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 30px 0 20px;
    }
    .stepper-progress-bg {
        position: absolute;
        top: 24px;
        left: 30px;
        right: 30px;
        height: 6px;
        background-color: #f1f5f9;
        z-index: 1;
        border-radius: 10px;
    }
    .stepper-progress-bar {
        position: absolute;
        top: 24px;
        left: 30px;
        height: 6px;
        background: linear-gradient(90deg, var(--sl-primary, #7F1D3F), var(--sl-accent, #D98C9A));
        z-index: 2;
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    .step-item {
        position: relative;
        z-index: 3;
        text-align: center;
        flex: 1;
    }
    .step-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid #e2e8f0;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        margin-bottom: 8px;
    }
    .step-item.active .step-icon {
        background: var(--sl-primary, #7F1D3F);
        border-color: #F6E7EB;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.25);
    }
    .step-item.completed .step-icon {
        background: #10b981;
        border-color: #d1fae5;
        color: #ffffff;
    }
    .step-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
    }
    .step-item.active .step-label {
        color: var(--sl-primary, #7F1D3F);
        font-weight: 700;
    }
    .step-item.completed .step-label {
        color: #0f172a;
    }
    .timeline-log {
        position: relative;
        padding-left: 32px;
    }
    .timeline-log::before {
        content: '';
        position: absolute;
        top: 10px;
        bottom: 10px;
        left: 11px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 24px;
    }
    .timeline-dot {
        position: absolute;
        left: -32px;
        top: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid var(--sl-primary, #7F1D3F);
        color: var(--sl-primary, #7F1D3F);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }
    .timeline-item:first-child .timeline-dot {
        background: var(--sl-primary, #7F1D3F);
        color: #ffffff;
        box-shadow: 0 0 10px rgba(127, 29, 63, 0.35);
    }
</style>
@endpush

@section('dashboard-content')
<div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <a href="{{ route('user.orders.index') }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 mb-2 border text-muted fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Volver a Mis Compras
        </a>
        <h3 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
            Rastreo de Envío: <span class="text-primary">{{ $shipment->tracking_number }}</span>
        </h3>
        <p class="text-muted text-sm mb-0">Orden #{{ $order->id }} • Compra realizada el {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button id="btnRefreshTracking" class="btn btn-white btn-sm border rounded-pill px-3 py-2 fw-semibold shadow-xs d-flex align-items-center gap-2">
            <i class="bi bi-arrow-clockwise" id="refreshSpinner"></i> Actualizar en tiempo real
        </button>

        <!-- Botón de Simulación para Demostración -->
        <button id="btnSimulateAdvance" class="btn btn-spotlight btn-sm rounded-pill px-3 py-2 fw-bold shadow-xs d-flex align-items-center gap-2" title="Avanza el estado del envío para probar la API y el rastreo en vivo">
            <i class="bi bi-fast-forward-fill"></i> Simular Avance En Vivo
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Main Tracking Stage -->
    <div class="col-lg-8">
        <!-- Live Status Hero Card -->
        <div class="vs-card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 24px; background: #ffffff;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 border-bottom mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 d-flex align-items-center justify-content-center text-white" style="width: 54px; height: 54px; background: linear-gradient(135deg, var(--sl-primary, #7F1D3F), var(--sl-accent, #D98C9A));">
                        <i class="bi bi-truck fs-3"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge {{ $shipment->status_badge_class }} rounded-pill px-3 py-1 fw-bold" id="badgeStatus" style="font-size: 0.8rem;">
                                {{ $shipment->status_label }}
                            </span>
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1 text-xs d-flex align-items-center gap-1">
                                <span class="pulse-live"></span> Conectado a API Logística
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-0" id="currentLocationText">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $shipment->current_location ?: $shipment->origin_city }}
                        </h5>
                    </div>
                </div>

                <div class="text-md-end">
                    <div class="text-muted text-xs text-uppercase fw-bold">Entrega estimada</div>
                    <div class="fw-bold text-dark fs-6" id="estimatedDeliveryText">
                        {{ $shipment->estimated_delivery_at ? $shipment->estimated_delivery_at->format('d/m/Y') . ' (Antes de las 6:00 PM)' : '1 a 2 días hábiles' }}
                    </div>
                </div>
            </div>

            <!-- Stepper Progress in Real Time -->
            <div class="position-relative my-4">
                <div class="stepper-progress-bg"></div>
                <div class="stepper-progress-bar" id="stepperProgressBar" style="width: {{ $shipment->progress_percentage }}%;"></div>

                @php
                    $steps = [
                        ['key' => 'preparing', 'label' => 'En Preparación', 'icon' => 'bi-box-seam'],
                        ['key' => 'dispatched', 'label' => 'Despachado', 'icon' => 'bi-truck'],
                        ['key' => 'in_transit', 'label' => 'En Tránsito', 'icon' => 'bi-arrow-left-right'],
                        ['key' => 'out_for_delivery', 'label' => 'En Reparto', 'icon' => 'bi-bicycle'],
                        ['key' => 'delivered', 'label' => 'Entregado', 'icon' => 'bi-check-circle-fill'],
                    ];
                    $statusesOrder = ['pending' => 0, 'preparing' => 1, 'dispatched' => 2, 'in_transit' => 3, 'out_for_delivery' => 4, 'delivered' => 5];
                    $currentIdx = $statusesOrder[$shipment->status] ?? 1;
                @endphp

                <div class="stepper-container" id="stepperNodes">
                    @foreach($steps as $idx => $st)
                        @php
                            $stepIdx = $idx + 1;
                            $stateClass = '';
                            if ($stepIdx < $currentIdx) {
                                $stateClass = 'completed';
                            } elseif ($stepIdx == $currentIdx) {
                                $stateClass = 'active';
                            }
                        @endphp
                        <div class="step-item {{ $stateClass }}" data-step="{{ $st['key'] }}">
                            <div class="step-icon">
                                <i class="bi {{ $st['icon'] }}"></i>
                            </div>
                            <div class="step-label d-none d-sm-block">{{ $st['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-3 bg-light rounded-4 d-flex align-items-center justify-content-between border mt-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <span class="text-sm text-dark">
                        Rastreo sincronizado en tiempo real. Cobertura nacional asegurada por Spotlight.
                    </span>
                </div>
                <span class="badge bg-white text-muted border rounded-pill px-3 py-1 text-xs" id="apiStatusTag">
                    API Status: 200 OK
                </span>
            </div>
        </div>

        <!-- Real-Time Event Timeline Log -->
        <div class="vs-card border-0 shadow-sm p-4 p-md-5" style="border-radius: 24px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h5 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-clock-history text-primary me-2"></i> Historial y Eventos en Tiempo Real
                </h5>
                <span class="badge badge-purple text-xs" id="eventsCountBadge">{{ $shipment->events->count() }} actualizaciones</span>
            </div>

            <div class="timeline-log" id="timelineContainer">
                @forelse($shipment->events as $event)
                    <div class="timeline-item">
                        <div class="timeline-dot">
                            <i class="bi {{ $event->icon }}"></i>
                        </div>
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-1">
                            <h6 class="fw-bold text-dark mb-0">{{ $event->title }}</h6>
                            <span class="text-muted text-xs">{{ $event->occurred_at ? $event->occurred_at->format('d/m/Y h:i A') : '' }}</span>
                        </div>
                        <p class="text-muted text-sm mb-1">{{ $event->description }}</p>
                        @if($event->location)
                            <span class="badge bg-light text-muted border text-xs">
                                <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $event->location }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="text-muted text-center py-4">No se han registrado eventos para este envío todavía.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Carrier Info, Address, Products -->
    <div class="col-lg-4">
        <!-- Transportadora Card -->
        <div class="vs-card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #ffffff;">
            <div class="text-muted text-xs text-uppercase fw-bold mb-2">Transportadora Oficial</div>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: var(--sl-primary, #7F1D3F);">
                    <i class="bi bi-truck fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0" id="carrierNameText">{{ $shipment->carrier_name }}</h6>
                    <span class="text-muted text-xs">Logística Aliada Spotlight</span>
                </div>
            </div>

            <div class="p-3 bg-light rounded-3 mb-3">
                <div class="text-muted text-xs mb-1">Número de Guía:</div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="fw-bold font-monospace text-dark fs-6" id="trackingGuideNumber">{{ $shipment->tracking_number }}</span>
                    <button class="btn btn-sm btn-white border rounded-pill px-2 py-1 text-xs fw-semibold" onclick="navigator.clipboard.writeText('{{ $shipment->tracking_number }}'); alert('¡Número de guía copiado al portapapeles!');">
                        <i class="bi bi-copy me-1"></i> Copiar
                    </button>
                </div>
            </div>

            <div class="d-flex flex-column gap-2 text-xs text-muted">
                <div class="d-flex justify-content-between">
                    <span>Atención transportadora:</span>
                    <span class="fw-bold text-dark">01 8000 915 525</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Vendedor:</span>
                    <span class="fw-bold text-dark">{{ $shipment->company->name ?? 'Emprendedor Spotlight' }}</span>
                </div>
            </div>
        </div>

        <!-- Dirección de Destino -->
        <div class="vs-card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #ffffff;">
            <div class="text-muted text-xs text-uppercase fw-bold mb-3">Destino de Entrega</div>
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-pin-map-fill text-danger fs-4 mt-1"></i>
                <div>
                    <div class="fw-bold text-dark mb-1">{{ $shipment->recipient_name }}</div>
                    <p class="text-muted text-sm mb-2 lh-sm">{{ $shipment->destination_address }}</p>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1 text-xs">
                        <i class="bi bi-building me-1"></i> {{ $shipment->destination_city }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Resumen de Productos del Paquete -->
        <div class="vs-card border-0 shadow-sm p-4" style="border-radius: 20px; background: #ffffff;">
            <div class="text-muted text-xs text-uppercase fw-bold mb-3">Contenido del Paquete</div>
            <div class="d-flex flex-column gap-3">
                @foreach($order->details as $item)
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">{{ $item->quantity }}</span>
                            <span class="text-dark fw-semibold text-sm">{{ $item->effective_product_name }}</span>
                        </div>
                        <span class="text-muted text-xs fw-bold">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark text-sm">Total Orden:</span>
                <span class="fw-black fs-5" style="color: var(--sl-primary, #7F1D3F); font-family: 'Outfit', sans-serif;">${{ number_format($order->total, 0, ',', '.') }} COP</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderId = {{ $order->id }};
        const shipmentId = {{ $shipment->id }};
        const btnRefresh = document.getElementById('btnRefreshTracking');
        const btnSimulate = document.getElementById('btnSimulateAdvance');
        const spinner = document.getElementById('refreshSpinner');

        async function fetchLiveTracking() {
            spinner.classList.add('spin-animation');
            try {
                const response = await fetch(`/api/v1/orders/${orderId}/tracking`);
                if (!response.ok) throw new Error('Error al consultar API de rastreo');
                const data = await response.json();
                updateUIWithTrackingData(data);
            } catch (err) {
                console.error('Error fetching live tracking:', err);
            } finally {
                setTimeout(() => spinner.classList.remove('spin-animation'), 500);
            }
        }

        async function simulateAdvance() {
            btnSimulate.disabled = true;
            btnSimulate.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Avanzando...';
            try {
                const response = await fetch(`/api/v1/shipments/${shipmentId}/simulate-advance`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error('Error al simular avance');
                const data = await response.json();
                updateUIWithTrackingData(data);
                if (data.simulation_message) {
                    alert(data.simulation_message);
                }
            } catch (err) {
                console.error(err);
                alert('Hubo un error simulando el avance.');
            } finally {
                btnSimulate.disabled = false;
                btnSimulate.innerHTML = '<i class="bi bi-fast-forward-fill"></i> Simular Avance En Vivo';
            }
        }

        function updateUIWithTrackingData(data) {
            if (!data.success) return;

            // Status label & color
            const badge = document.getElementById('badgeStatus');
            badge.innerText = data.status.label;
            badge.style.backgroundColor = data.status.color;

            // Current location & Estimated delivery
            document.getElementById('currentLocationText').innerHTML = `<i class="bi bi-geo-alt-fill text-danger me-1"></i> ${data.current_location || data.origin_city}`;
            if (data.estimated_delivery_at) {
                document.getElementById('estimatedDeliveryText').innerText = data.estimated_delivery_at;
            }

            // Progress bar
            document.getElementById('stepperProgressBar').style.width = data.status.progress_percent + '%';

            // Stepper nodes
            const stepsMap = {
                'preparing': 1,
                'dispatched': 2,
                'in_transit': 3,
                'out_for_delivery': 4,
                'delivered': 5
            };
            const currentIdx = stepsMap[data.status.code] || 1;

            document.querySelectorAll('#stepperNodes .step-item').forEach((node, idx) => {
                const stepNum = idx + 1;
                node.classList.remove('active', 'completed');
                if (stepNum < currentIdx) {
                    node.classList.add('completed');
                } else if (stepNum === currentIdx) {
                    node.classList.add('active');
                }
            });

            // Update Timeline events
            const timeline = document.getElementById('timelineContainer');
            if (data.timeline && data.timeline.length > 0) {
                let html = '';
                data.timeline.forEach(item => {
                    html += `
                        <div class="timeline-item">
                            <div class="timeline-dot"><i class="bi ${item.icon || 'bi-geo-alt'}"></i></div>
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-1">
                                <h6 class="fw-bold text-dark mb-0">${item.title}</h6>
                                <span class="text-muted text-xs">${item.occurred_at || ''}</span>
                            </div>
                            <p class="text-muted text-sm mb-1">${item.description || ''}</p>
                            ${item.location ? `<span class="badge bg-light text-muted border text-xs"><i class="bi bi-geo-alt me-1 text-danger"></i>${item.location}</span>` : ''}
                        </div>
                    `;
                });
                timeline.innerHTML = html;
                document.getElementById('eventsCountBadge').innerText = data.timeline.length + ' actualizaciones';
            }
        }

        btnRefresh?.addEventListener('click', fetchLiveTracking);
        btnSimulate?.addEventListener('click', simulateAdvance);

        // Auto polling cada 15 segundos si la página está activa y el paquete no ha sido entregado
        @if(!$shipment->isDelivered())
            setInterval(fetchLiveTracking, 15000);
        @endif
    });
</script>
<style>
    .spin-animation {
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endpush
@endsection
