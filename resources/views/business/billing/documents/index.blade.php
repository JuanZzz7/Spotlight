@extends('layouts.dashboard')

@section('page-title', 'Historial de Documentos Electrónicos DIAN')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="vs-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Historial y Trazabilidad DIAN</h4>
                <p class="text-muted small mb-0">Consulte facturas electrónicas de venta, notas crédito y notas débito emitidas con su estado de validación.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('business.billing.sales') }}" class="btn btn-spotlight btn-sm px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Facturar Venta
                </a>
                <a href="{{ route('business.billing.reports.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-pill">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i> Reportes
                </a>
            </div>
        </div>
    </div>

    {{-- Filtros de Búsqueda --}}
    <div class="vs-card p-3 mb-4">
        <form action="{{ route('business.billing.documents.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Buscar por Número / Cliente / CUFE</label>
                <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Ej: FE-101, Juan Perez, 901...">
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Tipo de Documento</label>
                <select name="document_type" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    <option value="01" {{ request('document_type') === '01' ? 'selected' : '' }}>01 - Factura de Venta</option>
                    <option value="91" {{ request('document_type') === '91' ? 'selected' : '' }}>91 - Nota Crédito</option>
                    <option value="92" {{ request('document_type') === '92' ? 'selected' : '' }}>92 - Nota Débito</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Estado DIAN</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Validado</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Enviado</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazado</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Anulado</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Desde</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Hasta</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill"><i class="bi bi-filter"></i></button>
            </div>
        </form>
    </div>

    {{-- Tabla de Documentos --}}
    <div class="vs-card p-4">
        @if($documents->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <h6 class="fw-bold text-dark mt-2 mb-1">No se encontraron documentos electrónicos</h6>
                <p class="text-muted small mb-0">Ajuste los filtros o emita una factura desde sus ventas.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Número / Tipo</th>
                            <th>Fecha</th>
                            <th>Adquirente</th>
                            <th class="text-end">Base Gravable</th>
                            <th class="text-end">IVA</th>
                            <th class="text-end">Total COP</th>
                            <th class="text-center">Estado DIAN</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                            <tr>
                                <td>
                                    <a href="{{ route('business.billing.documents.show', $doc) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $doc->full_number }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.65rem;">
                                        {{ $doc->type_label }}
                                        @if($doc->parentDocument)
                                            (Ref: {{ $doc->parentDocument->full_number }})
                                        @endif
                                    </div>
                                </td>
                                <td class="small">{{ $doc->issue_date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $doc->customer_name }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Doc: {{ $doc->customer_id_number }}</div>
                                </td>
                                <td class="text-end small">${{ number_format($doc->tax_exclusive_amount, 2, ',', '.') }}</td>
                                <td class="text-end small text-primary">${{ number_format($doc->tax_total_amount, 2, ',', '.') }}</td>
                                <td class="text-end fw-bold text-dark small">${{ number_format($doc->payable_amount, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $doc->status_badge_class }}" style="font-size: 0.7rem;">
                                        {{ $doc->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('business.billing.documents.show', $doc) }}" class="btn btn-outline-secondary" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('business.billing.documents.download_pdf', $doc) }}" target="_blank" class="btn btn-outline-secondary" title="Representación Gráfica">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        @if($doc->xml_path)
                                            <a href="{{ route('business.billing.documents.download_xml', $doc) }}" class="btn btn-outline-secondary" title="Descargar XML UBL">
                                                <i class="bi bi-file-earmark-code"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
