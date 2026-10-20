@extends('layouts.dashboard')

@section('page-title', 'Emitir Factura Electrónica - Pedido #' . $order->id)

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            {{-- Encabezado --}}
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1e1135 0%, #2b0b3a 100%); color: white;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill bg-success text-white px-3 py-1 fw-semibold small">
                                <i class="bi bi-receipt me-1"></i> Pedido #{{ $order->id }}
                            </span>
                            @if($order->reference)
                                <span class="badge rounded-pill bg-white bg-opacity-20 text-white px-2.5 py-1 small font-monospace">
                                    Ref: {{ $order->reference }}
                                </span>
                            @endif
                        </div>
                        <h4 class="fw-bold text-white mb-1">Emisión de Factura Electrónica de Venta</h4>
                        <p class="text-white-50 small mb-0">
                            Revisa los datos fiscales del cliente y los productos antes de transmitir la factura a la DIAN.
                        </p>
                    </div>
                    <a href="{{ route('business.billing.sales') }}" class="btn btn-outline-light btn-sm px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Volver a Ventas
                    </a>
                </div>
            </div>

            <form action="{{ route('business.billing.invoices.store_from_order', $order) }}" method="POST">
                @csrf

                {{-- 1. Datos del Adquirente / Cliente --}}
                <div class="vs-card p-4 mb-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-person-badge text-primary me-2"></i>1. Datos Fiscales del Adquirente (Comprador)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="btnSetConsumidorFinal">
                            <i class="bi bi-person-fill me-1"></i> Usar Consumidor Final (222222222222)
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tipo de Documento <span class="text-danger">*</span></label>
                            <select name="customer_id_type" id="customer_id_type" class="form-select form-select-sm" required>
                                <option value="13" {{ ($user && $user->document ? '13' : '31') === '13' ? 'selected' : '' }}>13 - Cédula de Ciudadanía</option>
                                <option value="31">31 - NIT</option>
                                <option value="22">22 - Cédula de Extranjería</option>
                                <option value="41">41 - Pasaporte</option>
                                <option value="42">42 - Doc. Extranjero</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Número de Documento <span class="text-danger">*</span></label>
                            <input type="text" name="customer_id_number" id="customer_id_number" class="form-control form-control-sm" 
                                   value="{{ old('customer_id_number', $user?->document ?: '222222222222') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Nombre o Razón Social <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control form-control-sm" 
                                   value="{{ old('customer_name', $user?->name ?: 'Consumidor Final') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Correo Electrónico (Para envío)</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control form-control-sm" 
                                   value="{{ old('customer_email', $user?->email) }}" placeholder="cliente@correo.com">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Teléfono</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control form-control-sm" 
                                   value="{{ old('customer_phone', $user?->phone) }}" placeholder="3101234567">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Dirección</label>
                            <input type="text" name="customer_address" id="customer_address" class="form-control form-control-sm" 
                                   value="{{ old('customer_address', $user?->address ?: 'Ubaté, Cundinamarca') }}">
                        </div>
                    </div>
                </div>

                {{-- 2. Detalle de Productos a Facturar --}}
                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-box-seam text-primary me-2"></i>2. Detalle de Ítems e Impuestos a Liquidar
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Ítem / Descripción</th>
                                    <th class="text-center" style="width: 80px;">Cant.</th>
                                    <th class="text-end" style="width: 130px;">Precio Unit.</th>
                                    <th class="text-center" style="width: 100px;">Tarifa IVA</th>
                                    <th class="text-end" style="width: 120px;">IVA</th>
                                    <th class="text-end" style="width: 130px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $calcSubtotal = 0;
                                    $calcTax = 0;
                                    $calcTotal = 0;
                                @endphp
                                @foreach($details as $d)
                                    @php
                                        $qty = (float) $d->quantity;
                                        $unitPrice = (float) $d->unit_price;
                                        $lineSub = round($qty * $unitPrice, 2);
                                        $taxRate = (float) ($d->tax_rate ?? ($d->product?->tax_rate ?? 19.00));
                                        $lineTax = round($lineSub * ($taxRate / 100), 2);
                                        $lineTotal = $lineSub + $lineTax;

                                        $calcSubtotal += $lineSub;
                                        $calcTax += $lineTax;
                                        $calcTotal += $lineTotal;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark small">{{ $d->effective_product_name }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">SKU: {{ $d->product_sku ?: ($d->product?->sku ?: 'PROD-' . $d->product_id) }}</div>
                                        </td>
                                        <td class="text-center fw-semibold small">{{ $qty }}</td>
                                        <td class="text-end small">${{ number_format($unitPrice, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $taxRate }}%</span>
                                        </td>
                                        <td class="text-end small text-muted">${{ number_format($lineTax, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-dark small">${{ number_format($lineTotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-semibold small">Subtotal sin IVA:</td>
                                    <td class="text-end fw-semibold small">${{ number_format($calcSubtotal, 0, ',', '.') }} COP</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end fw-semibold small text-primary">IVA Total:</td>
                                    <td class="text-end fw-semibold small text-primary">${{ number_format($calcTax, 0, ',', '.') }} COP</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold text-dark fs-6">TOTAL A FACTURAR:</td>
                                    <td class="text-end fw-bold text-success fs-6">${{ number_format($calcTotal, 0, ',', '.') }} COP</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- 3. Método de Pago y Emisión --}}
                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-credit-card text-primary me-2"></i>3. Medio de Pago Reglamentario DIAN
                    </h5>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Medio de Pago <span class="text-danger">*</span></label>
                            <select name="payment_method_code" class="form-select form-select-sm" required>
                                <option value="10" {{ $order->payment_method_code === '10' ? 'selected' : '' }}>10 - Efectivo / Pago Contra Entrega</option>
                                <option value="48" {{ $order->payment_method_code === '48' ? 'selected' : '' }}>48 - Tarjeta de Crédito</option>
                                <option value="49" {{ $order->payment_method_code === '49' ? 'selected' : '' }}>49 - Tarjeta Débito</option>
                                <option value="31" {{ $order->payment_method_code === '31' ? 'selected' : '' }}>31 - Transferencia Débito Bancaria (PSE / Nequi / Daviplata)</option>
                                <option value="1">1 - Instrumento no definido / Acuerdo comercial</option>
                            </select>
                        </div>

                        <div class="col-md-6 text-end pt-3">
                            <button type="submit" class="btn btn-spotlight px-4 py-2 rounded-pill shadow-sm fw-semibold" onclick="return confirm('¿Confirma la emisión y validación de esta Factura Electrónica ante la DIAN?');">
                                <i class="bi bi-send-check me-2"></i> Emitir Factura DIAN
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnConsumidor = document.getElementById('btnSetConsumidorFinal');
    if (btnConsumidor) {
        btnConsumidor.addEventListener('click', function() {
            document.getElementById('customer_id_type').value = '31';
            document.getElementById('customer_id_number').value = '222222222222';
            document.getElementById('customer_name').value = 'Consumidor Final';
            document.getElementById('customer_email').value = 'consumidor@spotlight.local';
            document.getElementById('customer_address').value = 'Ubaté, Cundinamarca';
        });
    }
});
</script>
@endsection
