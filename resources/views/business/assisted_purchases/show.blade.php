@extends('layouts.dashboard')
@section('title', 'Solicitud ' . $assistedPurchase->request_code . ' - ' . $company->name)
@section('page-title', 'Gestión de Solicitud Internacional')

@section('dashboard-content')
<div class="mb-4">
    <a href="{{ route('business.assisted_purchases.index') }}" class="text-decoration-none text-muted fw-semibold d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Volver al listado de solicitudes
    </a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold text-dark mb-0 font-monospace">{{ $assistedPurchase->request_code }}</h3>
                <span class="badge {{ $assistedPurchase->status_badge }} rounded-pill px-3 py-1">
                    {{ $assistedPurchase->status_label }}
                </span>
            </div>
            <div class="text-muted mt-1" style="font-size: 0.85rem;">
                Registrada el {{ $assistedPurchase->created_at->format('d/m/Y \a \l\a\s H:i') }} • 
                Ruta: {{ $assistedPurchase->origin_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }} 
                <i class="bi bi-arrow-right mx-1"></i> 
                {{ $assistedPurchase->destination_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }}
            </div>
        </div>

        @if(in_array($assistedPurchase->status, [\App\Models\AssistedPurchase::STATUS_PAYMENT_IN_PROCESS, \App\Models\AssistedPurchase::STATUS_PENDING_PAYMENT]))
            <form action="{{ route('business.assisted_purchases.confirm_payment', $assistedPurchase) }}" method="POST" onsubmit="return confirm('¿Confirmas que recibiste el pago completo acordado para esta solicitud?')">
                @csrf
                <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-xs d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> Confirmar Recepción de Pago
                </button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 shadow-xs mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 rounded-4 shadow-xs mb-4">
        <strong class="d-block mb-1">Por favor verifica los campos:</strong>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    {{-- Columna Izquierda: Información de Producto y Comprador --}}
    <div class="col-lg-5">
        {{-- Card de Producto --}}
        <div class="bg-white rounded-4 border p-4 shadow-xs mb-4">
            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-box-seam me-1"></i> Producto Solicitado
            </h6>
            <div class="d-flex align-items-center gap-3 pb-3 border-bottom">
                <img src="{{ $assistedPurchase->product->image_url ?? asset('images/logo.png') }}" alt="" class="rounded-3 border" style="width: 70px; height: 70px; object-fit: cover;">
                <div>
                    <h6 class="fw-bold text-dark mb-1">{{ $assistedPurchase->product_name }}</h6>
                    <div class="text-muted text-xs">SKU: {{ $assistedPurchase->product_sku ?: 'N/A' }}</div>
                    <div class="fw-semibold text-dark mt-1">
                        Cantidad solicitada: <span class="badge bg-dark text-white rounded-pill px-2">{{ $assistedPurchase->quantity }} ud(s)</span>
                    </div>
                </div>
            </div>

            <div class="pt-3">
                <div class="d-flex justify-content-between text-muted text-sm mb-1">
                    <span>Precio unitario congelado:</span>
                    <strong class="text-dark">{{ $assistedPurchase->formatted_original_price }}</strong>
                </div>
                <div class="d-flex justify-content-between text-muted text-sm mb-1">
                    <span>Subtotal base ({{ $assistedPurchase->quantity }} uds):</span>
                    <strong class="text-dark">{{ $assistedPurchase->original_currency }} ${{ number_format($assistedPurchase->original_price * $assistedPurchase->quantity, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between text-muted text-sm mb-1">
                    <span>Costo de envío acordado:</span>
                    <strong class="text-primary">{{ $assistedPurchase->payment_currency }} ${{ number_format($assistedPurchase->shipping_cost_agreed, 2) }}</strong>
                </div>
                @if($assistedPurchase->other_fees_agreed > 0)
                    <div class="d-flex justify-content-between text-muted text-sm mb-1">
                        <span>Cargos adicionales / aranceles:</span>
                        <strong class="text-dark">{{ $assistedPurchase->payment_currency }} ${{ number_format($assistedPurchase->other_fees_agreed, 2) }}</strong>
                    </div>
                @endif
                <div class="d-flex justify-content-between align-items-baseline pt-2 border-top mt-2">
                    <span class="fw-bold text-dark">Total acordado:</span>
                    <span class="fw-black fs-4" style="color: var(--sl-primary, #7F1D3F);">
                        {{ $assistedPurchase->formatted_total_agreed }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Card de Destinatario y Comprador --}}
        <div class="bg-white rounded-4 border p-4 shadow-xs mb-4">
            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #7F1D3F;">
                <i class="bi bi-geo-alt-fill me-1"></i> Datos de Entrega y Comprador
            </h6>
            <div class="mb-2">
                <div class="text-muted text-xs text-uppercase fw-bold">Destinatario</div>
                <div class="fw-bold text-dark">{{ $assistedPurchase->recipient_name }}</div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <div class="text-muted text-xs text-uppercase fw-bold">Teléfono</div>
                    <div class="fw-semibold text-dark">{{ $assistedPurchase->recipient_phone }}</div>
                </div>
                <div class="col-6">
                    <div class="text-muted text-xs text-uppercase fw-bold">Documento / Identificación</div>
                    <div class="fw-semibold text-dark">{{ $assistedPurchase->recipient_tax_id ?: 'No especificado' }}</div>
                </div>
            </div>
            <div class="mb-2">
                <div class="text-muted text-xs text-uppercase fw-bold">País de Destino</div>
                <div class="fw-semibold text-dark">
                    {{ $assistedPurchase->destination_country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }}
                </div>
            </div>
            <div class="mb-2">
                <div class="text-muted text-xs text-uppercase fw-bold">Estado / Departamento y Ciudad</div>
                <div class="fw-semibold text-dark">{{ $assistedPurchase->destination_city }}, {{ $assistedPurchase->destination_state }} (CP: {{ $assistedPurchase->destination_postal_code ?: 'N/A' }})</div>
            </div>
            <div class="mb-2">
                <div class="text-muted text-xs text-uppercase fw-bold">Dirección Exacta</div>
                <div class="fw-semibold text-dark">{{ $assistedPurchase->destination_address }}</div>
            </div>
            @if($assistedPurchase->delivery_notes)
                <div class="p-2 bg-light rounded-3 mt-3 border">
                    <div class="text-muted text-xs fw-bold"><i class="bi bi-chat-left-quote me-1"></i> Indicaciones del Comprador:</div>
                    <div class="text-dark text-sm mt-1 fst-italic">"{{ $assistedPurchase->delivery_notes }}"</div>
                </div>
            @endif
        </div>

        {{-- Card de Notificación de Pago del Comprador (Si existe) --}}
        @if($assistedPurchase->payment_reference || $assistedPurchase->payment_notes)
            <div class="bg-white rounded-4 border p-4 shadow-xs border-success mb-4" style="background: rgba(16, 185, 129, 0.03);">
                <div class="d-flex align-items-center gap-2 mb-2 text-success">
                    <i class="bi bi-wallet2 fs-5"></i>
                    <h6 class="fw-bold mb-0">Referencia de Pago Reportada por el Comprador</h6>
                </div>
                <div class="mb-2">
                    <span class="text-muted text-xs text-uppercase fw-bold">Comprobante / Referencia:</span>
                    <div class="fw-bold font-monospace text-dark">{{ $assistedPurchase->payment_reference }}</div>
                </div>
                @if($assistedPurchase->paid_at)
                    <div class="mb-2">
                        <span class="text-muted text-xs text-uppercase fw-bold">Fecha de Confirmación:</span>
                        <div class="fw-semibold text-success">{{ $assistedPurchase->paid_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
                @if($assistedPurchase->payment_notes)
                    <div class="text-muted text-xs text-uppercase fw-bold">Detalles / Nota:</div>
                    <p class="text-dark text-sm mb-0">{{ $assistedPurchase->payment_notes }}</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Columna Derecha: Formulario de Negociación, Cotización y Envío --}}
    <div class="col-lg-7">
        <div class="bg-white rounded-4 border p-4 shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Coordinación de Envío y Cotización</h5>
                    <div class="text-muted text-xs">Actualiza los costos acordados y el estado del despacho internacional</div>
                </div>
                <span class="badge bg-light text-dark border">Paso a paso asistido</span>
            </div>

            <form action="{{ route('business.assisted_purchases.update', $assistedPurchase) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Estado de la Solicitud --}}
                <div class="mb-4">
                    <label class="form-label text-xs fw-bold text-uppercase text-muted">Estado Actual de la Solicitud</label>
                    <select name="status" class="form-select form-select-lg rounded-3 border fw-bold text-dark" style="font-size: 0.95rem;">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $assistedPurchase->status) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-xs">El comprador verá este estado actualizado inmediatamente en su panel de seguimiento.</div>
                </div>

                <div class="row g-3 mb-3">
                    {{-- Moneda de Cobro --}}
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">Moneda de Pago</label>
                        <select name="payment_currency" class="form-select rounded-3 border fw-semibold">
                            <option value="COP" {{ old('payment_currency', $assistedPurchase->payment_currency) === 'COP' ? 'selected' : '' }}>COP (Pesos Col.)</option>
                            <option value="MXN" {{ old('payment_currency', $assistedPurchase->payment_currency) === 'MXN' ? 'selected' : '' }}>MXN (Pesos Mex.)</option>
                            <option value="USD" {{ old('payment_currency', $assistedPurchase->payment_currency) === 'USD' ? 'selected' : '' }}>USD (Dólares)</option>
                        </select>
                    </div>

                    {{-- Flete Internacional --}}
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">Flete de Envío Acordado</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">$</span>
                            <input type="number" step="0.01" min="0" name="shipping_cost_agreed" class="form-control rounded-end-3" value="{{ old('shipping_cost_agreed', $assistedPurchase->shipping_cost_agreed) }}" placeholder="0.00">
                        </div>
                    </div>

                    {{-- Otros Gastos --}}
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">Otros Cargos / Embalaje</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">$</span>
                            <input type="number" step="0.01" min="0" name="other_fees_agreed" class="form-control rounded-end-3" value="{{ old('other_fees_agreed', $assistedPurchase->other_fees_agreed) }}" placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- Método de Pago Acordado --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">Mecanismo de Pago Previsto</label>
                        <input type="text" name="payment_method_agreed" class="form-control rounded-3" value="{{ old('payment_method_agreed', $assistedPurchase->payment_method_agreed) }}" placeholder="Ej: Transferencia SPEI / Wompi / Mercado Pago">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">Tiempo Estimado de Entrega</label>
                        <input type="text" name="estimated_delivery_days" class="form-control rounded-3" value="{{ old('estimated_delivery_days', $assistedPurchase->estimated_delivery_days) }}" placeholder="Ej: 5 a 10 días hábiles">
                    </div>
                </div>

                {{-- Guía de Despacho --}}
                <div class="p-3 bg-light rounded-4 border mb-3">
                    <h6 class="fw-bold text-dark text-xs text-uppercase mb-2" style="letter-spacing: 0.5px;">
                        <i class="bi bi-truck me-1"></i> Información de Despacho Internacional
                    </h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Transportadora o Empresa Logística</label>
                            <input type="text" name="carrier_name" class="form-control rounded-3 bg-white" value="{{ old('carrier_name', $assistedPurchase->carrier_name) }}" placeholder="Ej: FedEx / DHL / Servientrega / Estafeta">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Número de Guía o Rastreo</label>
                            <input type="text" name="tracking_number" class="form-control rounded-3 bg-white font-monospace" value="{{ old('tracking_number', $assistedPurchase->tracking_number) }}" placeholder="Ej: FX-98374291MX">
                        </div>
                    </div>
                </div>

                {{-- Notas y Condiciones para el Comprador --}}
                <div class="mb-4">
                    <label class="form-label text-xs fw-bold text-uppercase text-muted">Condiciones y Notas para el Comprador</label>
                    <textarea name="seller_notes" rows="4" class="form-control rounded-3" placeholder="Indica aquí los datos para la transferencia o enlace de pago acordado, requisitos de aduana o advertencias de entrega...">{{ old('seller_notes', $assistedPurchase->seller_notes) }}</textarea>
                    <div class="form-text text-xs">Esta información se mostrará en el panel del comprador de forma transparente y segura.</div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('business.assisted_purchases.index') }}" class="btn btn-light rounded-pill px-4 fw-semibold border">
                        Cancelar
                    </a>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-xs" style="background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Cambios y Notificar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
