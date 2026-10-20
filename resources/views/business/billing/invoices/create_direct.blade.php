@extends('layouts.dashboard')

@section('page-title', 'Nueva Factura Electrónica Directa')

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
                                <i class="bi bi-receipt me-1"></i> Facturación Directa / Mostrador
                            </span>
                            <span class="badge rounded-pill bg-white bg-opacity-20 text-white px-2.5 py-1 small">
                                {{ $company->razon_social ?: $company->name }}
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-1">Nueva Factura Electrónica Directa</h4>
                        <p class="text-white-50 small mb-0">
                            Expide una factura legal ante la DIAN para ventas en punto físico, pedidos telefónicos o clientes directos.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('business.billing.sales') }}" class="btn btn-outline-light btn-sm px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-clock-history me-1"></i> Ver Ventas
                        </a>
                        <a href="{{ route('business.billing.index') }}" class="btn btn-outline-light btn-sm px-3 py-2 rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('business.billing.invoices.store') }}" method="POST" id="directInvoiceForm">
                @csrf

                {{-- 1. Datos Fiscales del Adquirente --}}
                <div class="vs-card p-4 mb-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-person-badge text-primary me-2"></i>1. Datos Fiscales del Adquirente (Comprador)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="btnConsumidorFinalDirect">
                            <i class="bi bi-person-fill me-1"></i> Usar Consumidor Final (222222222222)
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tipo de Documento <span class="text-danger">*</span></label>
                            <select name="customer_id_type" id="dir_customer_id_type" class="form-select form-select-sm" required>
                                <option value="13">13 - Cédula de Ciudadanía</option>
                                <option value="31">31 - NIT</option>
                                <option value="22">22 - Cédula de Extranjería</option>
                                <option value="41">41 - Pasaporte</option>
                                <option value="42">42 - Doc. Extranjero</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Número de Documento <span class="text-danger">*</span></label>
                            <input type="text" name="customer_id_number" id="dir_customer_id_number" class="form-control form-control-sm" placeholder="Ej: 1070987654" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Nombre o Razón Social <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="dir_customer_name" class="form-control form-control-sm" placeholder="Nombre completo del cliente" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Correo Electrónico (Opcional)</label>
                            <input type="email" name="customer_email" id="dir_customer_email" class="form-control form-control-sm" placeholder="cliente@correo.com">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Teléfono</label>
                            <input type="text" name="customer_phone" id="dir_customer_phone" class="form-control form-control-sm" placeholder="3101234567">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Dirección</label>
                            <input type="text" name="customer_address" id="dir_customer_address" class="form-control form-control-sm" value="Ubaté, Cundinamarca">
                        </div>
                    </div>
                </div>

                {{-- 2. Ítems y Productos a Facturar --}}
                <div class="vs-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-cart-check text-primary me-2"></i>2. Ítems y Productos a Facturar
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold" id="btnAddRow">
                            <i class="bi bi-plus-lg me-1"></i> Agregar Ítem
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="itemsTable">
                            <thead class="table-light small">
                                <tr>
                                    <th>Descripción del Producto / Servicio</th>
                                    <th style="width: 100px;" class="text-center">Cantidad</th>
                                    <th style="width: 150px;" class="text-end">Precio Unit. (COP)</th>
                                    <th style="width: 140px;" class="text-center">Tarifa IVA</th>
                                    <th style="width: 150px;" class="text-end">Subtotal con IVA</th>
                                    <th style="width: 60px;" class="text-center">Quitar</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr>
                                    <td>
                                        <input type="text" name="items[0][description]" class="form-control form-control-sm item-desc" placeholder="Descripción del producto o servicio" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][quantity]" class="form-control form-control-sm item-qty text-center" value="1" min="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][price_amount]" class="form-control form-control-sm item-price text-end" placeholder="0.00" min="0" required>
                                    </td>
                                    <td>
                                        <select name="items[0][tax_rate]" class="form-select form-select-sm item-tax">
                                            <option value="19">19% (General)</option>
                                            <option value="5">5% (Reducido)</option>
                                            <option value="0">0% (Exento/Excluido)</option>
                                        </select>
                                    </td>
                                    <td class="text-end fw-bold small text-dark row-total">
                                        $0
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Eliminar fila"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-semibold small">Subtotal sin IVA:</td>
                                    <td class="text-end fw-semibold small" id="liveSubtotal">$0</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-semibold small text-primary">IVA Total Liquidado:</td>
                                    <td class="text-end fw-semibold small text-primary" id="liveTax">$0</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-dark fs-6">TOTAL FACTURA ELECTRÓNICA:</td>
                                    <td class="text-end fw-bold text-success fs-6" id="liveTotal">$0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- 3. Medio de Pago y Emisión --}}
                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-credit-card text-primary me-2"></i>3. Medio de Pago Reglamentario DIAN
                    </h5>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Medio de Pago <span class="text-danger">*</span></label>
                            <select name="payment_method_code" class="form-select form-select-sm" required>
                                <option value="10">10 - Efectivo</option>
                                <option value="48">48 - Tarjeta de Crédito</option>
                                <option value="49">49 - Tarjeta Débito</option>
                                <option value="31">31 - Transferencia Débito Bancaria (PSE / Nequi / Daviplata)</option>
                                <option value="1">1 - Acuerdo comercial</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-end pt-3">
                            <button type="submit" class="btn btn-spotlight px-4 py-2 rounded-pill shadow-sm fw-semibold" onclick="return confirm('¿Confirma la emisión y validación de esta factura ante la DIAN?');">
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
    let rowIndex = 1;
    const itemsBody = document.getElementById('itemsBody');
    const btnAdd = document.getElementById('btnAddRow');

    function formatMoney(amount) {
        return '$' + Math.round(amount).toLocaleString('es-CO');
    }

    function calculateTotals() {
        let totalSub = 0;
        let totalTax = 0;
        let grandTotal = 0;

        itemsBody.querySelectorAll('tr').forEach(function(row) {
            const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
            const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
            const taxRate = parseFloat(row.querySelector('.item-tax')?.value) || 0;

            const lineSub = qty * price;
            const lineTax = lineSub * (taxRate / 100);
            const lineTotal = lineSub + lineTax;

            totalSub += lineSub;
            totalTax += lineTax;
            grandTotal += lineTotal;

            const rowTotalEl = row.querySelector('.row-total');
            if (rowTotalEl) {
                rowTotalEl.textContent = formatMoney(lineTotal);
            }
        });

        document.getElementById('liveSubtotal').textContent = formatMoney(totalSub);
        document.getElementById('liveTax').textContent = formatMoney(totalTax);
        document.getElementById('liveTotal').textContent = formatMoney(grandTotal);
    }

    itemsBody.addEventListener('input', calculateTotals);
    itemsBody.addEventListener('change', calculateTotals);

    btnAdd.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm item-desc" placeholder="Descripción del producto o servicio" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${rowIndex}][quantity]" class="form-control form-control-sm item-qty text-center" value="1" min="0.01" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${rowIndex}][price_amount]" class="form-control form-control-sm item-price text-end" placeholder="0.00" min="0" required>
            </td>
            <td>
                <select name="items[${rowIndex}][tax_rate]" class="form-select form-select-sm item-tax">
                    <option value="19">19% (General)</option>
                    <option value="5">5% (Reducido)</option>
                    <option value="0">0% (Exento/Excluido)</option>
                </select>
            </td>
            <td class="text-end fw-bold small text-dark row-total">
                $0
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Eliminar fila"><i class="bi bi-trash"></i></button>
            </td>
        `;
        itemsBody.appendChild(tr);
        rowIndex++;
        calculateTotals();
    });

    itemsBody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            if (itemsBody.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
                calculateTotals();
            } else {
                alert('Debe conservar al menos un ítem en la factura.');
            }
        }
    });

    // Consumidor Final
    const btnConsumidor = document.getElementById('btnConsumidorFinalDirect');
    if (btnConsumidor) {
        btnConsumidor.addEventListener('click', function() {
            document.getElementById('dir_customer_id_type').value = '31';
            document.getElementById('dir_customer_id_number').value = '222222222222';
            document.getElementById('dir_customer_name').value = 'Consumidor Final';
            document.getElementById('dir_customer_email').value = 'consumidor@spotlight.local';
            document.getElementById('dir_customer_address').value = 'Ubaté, Cundinamarca';
        });
    }
});
</script>
@endsection
