@extends('layouts.dashboard')

@section('page-title', $document->type_label . ' ' . $document->full_number)

@section('dashboard-content')
<div class="container-fluid px-0">
    {{-- Encabezado del Documento --}}
    <div class="vs-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge {{ $document->status_badge_class }} px-3 py-1 text-uppercase">
                        {{ $document->status_label }}
                    </span>
                    <span class="badge bg-light text-dark border">
                        Ambiente: {{ strtoupper($document->environment) }}
                    </span>
                    @if($document->parentDocument)
                        <span class="badge bg-info text-white">
                            Afecta a: {{ $document->parentDocument->full_number }}
                        </span>
                    @endif
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $document->full_number }}</h3>
                <p class="text-muted small mb-0">
                    {{ $document->type_label }} | Fecha de Expedición: <strong>{{ $document->issue_date->format('d/m/Y') }}</strong> {{ $document->issue_time }}
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('business.billing.documents.download_pdf', $document) }}" target="_blank" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Representación Gráfica
                </a>

                @if($document->xml_path)
                    <a href="{{ route('business.billing.documents.download_xml', $document) }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill">
                        <i class="bi bi-file-earmark-code me-1"></i> Descargar XML UBL 2.1
                    </a>
                @endif

                <form action="{{ route('business.billing.documents.sync', $document) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" title="Sincronizar estado con la DIAN">
                        <i class="bi bi-arrow-repeat me-1"></i> Sincronizar DIAN
                    </button>
                </form>

                @if($document->isInvoice() && $document->isValidated() && $document->status !== 'cancelled')
                    <button type="button" class="btn btn-danger btn-sm px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#creditNoteModal">
                        <i class="bi bi-slash-circle me-1"></i> Emitir Nota Crédito
                    </button>
                @endif

                <a href="{{ route('business.billing.documents.index') }}" class="btn btn-light btn-sm px-3 rounded-pill border">
                    Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Emisor y Adquirente --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="vs-card p-4 h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-building text-primary me-2"></i>Emisor (Comercio)
                </h6>
                <div class="small text-muted mb-1">Razón Social: <strong class="text-dark">{{ $document->issuer_business_name }}</strong></div>
                <div class="small text-muted mb-1">NIT: <strong class="text-dark">{{ $document->issuer_nit }}-{{ $document->issuer_dv }}</strong></div>
                <div class="small text-muted mb-1">Régimen: <span class="text-dark">{{ ucfirst(str_replace('_', ' ', $document->issuer_tax_regime)) }}</span></div>
                <div class="small text-muted mb-1">Dirección: <span class="text-dark">{{ $document->issuer_address }}</span></div>
                <div class="small text-muted mb-1">Municipio: <span class="text-dark">Ubaté, Cundinamarca ({{ $document->issuer_municipality_code }})</span></div>
                <div class="small text-muted">Correo: <span class="text-dark">{{ $document->issuer_email }}</span></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="vs-card p-4 h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-person text-primary me-2"></i>Adquirente (Cliente)
                </h6>
                <div class="small text-muted mb-1">Nombre: <strong class="text-dark">{{ $document->customer_name }}</strong></div>
                <div class="small text-muted mb-1">Identificación: <strong class="text-dark">{{ $document->customer_id_number }} (Tipo {{ $document->customer_id_type }})</strong></div>
                <div class="small text-muted mb-1">Dirección: <span class="text-dark">{{ $document->customer_address ?: 'No especificada' }}</span></div>
                <div class="small text-muted mb-1">Municipio: <span class="text-dark">Ubaté ({{ $document->customer_municipality_code }})</span></div>
                <div class="small text-muted mb-1">Correo: <span class="text-dark">{{ $document->customer_email ?: 'Sin registrar' }}</span></div>
                <div class="small text-muted">Teléfono: <span class="text-dark">{{ $document->customer_phone ?: 'Sin registrar' }}</span></div>
            </div>
        </div>
    </div>

    {{-- Ítems y Liquidación de Impuestos --}}
    <div class="vs-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
            <i class="bi bi-list-check text-primary me-2"></i>Detalle de Ítems e Impuestos
        </h5>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Descripción</th>
                        <th class="text-center" style="width: 80px;">Unidad</th>
                        <th class="text-center" style="width: 90px;">Cantidad</th>
                        <th class="text-end" style="width: 140px;">Precio Unit.</th>
                        <th class="text-end" style="width: 140px;">Base Gravable</th>
                        <th class="text-center" style="width: 90px;">IVA %</th>
                        <th class="text-end" style="width: 130px;">Valor IVA</th>
                        <th class="text-end" style="width: 140px;">Total Ítem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($document->items as $it)
                        <tr>
                            <td class="text-center small">{{ $it->line_number }}</td>
                            <td>
                                <div class="fw-semibold text-dark small">{{ $it->description }}</div>
                                <div class="text-muted" style="font-size: 0.65rem;">Cód: {{ $it->product_code }}</div>
                            </td>
                            <td class="text-center small">{{ $it->unit_measure }}</td>
                            <td class="text-center fw-bold small">{{ $it->quantity }}</td>
                            <td class="text-end small">${{ number_format($it->price_amount, 2, ',', '.') }}</td>
                            <td class="text-end small">${{ number_format($it->tax_base_amount, 2, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $it->tax_rate }}%</span>
                            </td>
                            <td class="text-end small text-primary">${{ number_format($it->tax_amount, 2, ',', '.') }}</td>
                            <td class="text-end fw-bold text-dark small">${{ number_format($it->total_item_amount, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="8" class="text-end fw-bold">Total Base Gravable:</td>
                        <td class="text-end fw-bold">${{ number_format($document->tax_exclusive_amount, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-end fw-bold text-primary">Total IVA:</td>
                        <td class="text-end fw-bold text-primary">${{ number_format($document->tax_total_amount, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="fs-6">
                        <td colspan="8" class="text-end fw-bold text-dark">TOTAL PAGADERO (COP):</td>
                        <td class="text-end fw-bold text-success">${{ number_format($document->payable_amount, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Bitácora de Transmisión y Trazabilidad DIAN --}}
    <div class="vs-card p-4">
        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
            <i class="bi bi-clock-history text-primary me-2"></i>Bitácora de Auditoría y Trazabilidad de Peticiones
        </h6>

        @if($document->logs->isEmpty())
            <p class="text-muted small mb-0">No hay registros de bitácora disponibles.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0 font-monospace" style="font-size: 0.75rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha / Hora</th>
                            <th>Acción</th>
                            <th>Proveedor</th>
                            <th>Endpoint</th>
                            <th>HTTP Status</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($document->logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="fw-bold">{{ $log->action }}</td>
                                <td>{{ $log->provider }}</td>
                                <td class="text-muted">{{ $log->endpoint }}</td>
                                <td>
                                    <span class="badge {{ $log->http_status === 200 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $log->http_status }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->is_success)
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Exitoso</span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> {{ $log->error_message }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Modal para Emisión de Nota Crédito --}}
@if($document->isInvoice())
<div class="modal fade" id="creditNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('business.billing.documents.credit_note', $document) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-slash-circle text-danger me-2"></i>Emitir Nota Crédito Electrónica
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Se emitirá una Nota Crédito Electrónica (Tipo 91) asociada a la factura <strong>{{ $document->full_number }}</strong> ante la DIAN.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Concepto de Corrección DIAN <span class="text-danger">*</span></label>
                        <select name="correction_concept_code" class="form-select" required>
                            <option value="2">2 - Anulación de factura electrónica</option>
                            <option value="1">1 - Devolución de parte de los bienes</option>
                            <option value="3">3 - Rebaja o descuento total o parcial aplicado</option>
                            <option value="4">4 - Ajuste de precio</option>
                            <option value="5">5 - Otros</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Motivo / Justificación Legal <span class="text-danger">*</span></label>
                        <textarea name="correction_reason" rows="3" class="form-control" placeholder="Explique el motivo de la anulación o corrección..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4" onclick="return confirm('¿Confirma la emisión de la Nota Crédito ante la DIAN? Esta acción es irreversible.');">
                        Emitir Nota Crédito
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
