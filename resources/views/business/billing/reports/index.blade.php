@extends('layouts.dashboard')

@section('page-title', 'Reporte Fiscal de Facturación DIAN')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="vs-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Reportes Fiscales por Periodo</h4>
                <p class="text-muted small mb-0">Consulte la discriminación de bases gravables, IVA por tarifas (19%, 5%, 0%) y notas crédito aplicadas.</p>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar a CSV (Excel)
                </a>
            </div>
        </div>
    </div>

    {{-- Filtro de Periodo --}}
    <div class="vs-card p-3 mb-4">
        <form action="{{ route('business.billing.reports.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Año Gravable</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Mes</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="all" {{ $month === 'all' ? 'selected' : '' }}>Todo el Año</option>
                    @foreach([
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ] as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ (int)$month === $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill">
                    <i class="bi bi-funnel me-1"></i> Generar Reporte
                </button>
            </div>
        </form>
    </div>

    {{-- Resumen de Totales e Impuestos --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Facturado Bruto</span>
                <h4 class="fw-bold text-dark mb-0">${{ number_format($totalInvoicedGross, 0, ',', '.') }}</h4>
                <div class="text-muted" style="font-size: 0.7rem;">Antes de devoluciones</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Notas Crédito Aplicadas</span>
                <h4 class="fw-bold text-danger mb-0">-${{ number_format($totalCreditNotes, 0, ',', '.') }}</h4>
                <div class="text-muted" style="font-size: 0.7rem;">Anulaciones / Devoluciones</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Base Gravable Neta</span>
                <h4 class="fw-bold text-dark mb-0">${{ number_format($taxBaseTotal, 0, ',', '.') }}</h4>
                <div class="text-muted" style="font-size: 0.7rem;">Subtotal sin tributos</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4" style="border-left: 4px solid #10b981;">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Neto Facturado</span>
                <h4 class="fw-bold text-success mb-0">${{ number_format($totalNet, 0, ',', '.') }}</h4>
                <div class="text-muted" style="font-size: 0.7rem;">Ventas netas del periodo</div>
            </div>
        </div>
    </div>

    {{-- Desglose de Tarifas de IVA --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="vs-card p-3 bg-light border">
                <div class="fw-bold text-dark small mb-1">Tarifa General (IVA 19%)</div>
                <div class="fs-5 fw-bold text-primary">${{ number_format($iva19Total, 2, ',', '.') }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="vs-card p-3 bg-light border">
                <div class="fw-bold text-dark small mb-1">Tarifa Reducida (IVA 5%)</div>
                <div class="fs-5 fw-bold text-primary">${{ number_format($iva5Total, 2, ',', '.') }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="vs-card p-3 bg-light border">
                <div class="fw-bold text-dark small mb-1">Excluidos / Exentos (IVA 0%)</div>
                <div class="fs-5 fw-bold text-dark">${{ number_format($iva0Total, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Listado de Documentos del Periodo --}}
    <div class="vs-card p-4">
        <h6 class="fw-bold text-dark mb-3">Documentos Emitidos en el Periodo ({{ $documents->count() }})</h6>

        @if($documents->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No hay documentos validados en el periodo seleccionado.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Documento</th>
                            <th>Fecha</th>
                            <th>Adquirente</th>
                            <th class="text-end">Base Gravable</th>
                            <th class="text-end">IVA</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $doc->full_number }}</span>
                                    <span class="badge {{ $doc->isCreditNote() ? 'bg-danger' : 'bg-primary' }} text-white ms-1" style="font-size: 0.6rem;">
                                        {{ $doc->isCreditNote() ? 'NC' : 'FE' }}
                                    </span>
                                </td>
                                <td class="small">{{ $doc->issue_date->format('d/m/Y') }}</td>
                                <td class="small">{{ $doc->customer_name }} ({{ $doc->customer_id_number }})</td>
                                <td class="text-end small">${{ number_format($doc->tax_exclusive_amount, 2, ',', '.') }}</td>
                                <td class="text-end small text-primary">${{ number_format($doc->tax_total_amount, 2, ',', '.') }}</td>
                                <td class="text-end fw-bold small">${{ number_format($doc->payable_amount, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success" style="font-size: 0.65rem;">VALIDADO</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
