@extends('layouts.dashboard')

@section('page-title', 'Facturación Electrónica DIAN')

@section('dashboard-content')
<div class="container-fluid px-0">
    {{-- Banner de Estado de Habilitación Fiscal --}}
    <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1e1135 0%, #2b0b3a 100%); color: white;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill {{ $profile ? $profile->status_badge_class : 'bg-secondary' }} px-3 py-2 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-shield-check me-1"></i> Estado Fiscal: {{ $profile ? $profile->status_label : 'No Solicitado' }}
                    </span>
                    @if($config && $config->is_connected)
                        <span class="badge rounded-pill bg-success px-3 py-2 text-uppercase fw-bold" style="font-size: 0.75rem;">
                            <i class="bi bi-patch-check-fill me-1"></i> Servicio DIAN Conectado
                        </span>
                    @else
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2 text-uppercase fw-bold" style="font-size: 0.75rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i> Configuración Pendiente
                        </span>
                    @endif
                </div>
                <h4 class="fw-bold mb-1 text-white">{{ $company->razon_social ?: $company->name }}</h4>
                <p class="text-white-50 mb-0 small">
                    NIT: <strong>{{ $profile ? $profile->identification_number . '-' . $profile->dv : ($company->nit ?: 'Sin registrar') }}</strong> | 
                    Municipio: <strong>Ubaté, Cundinamarca (25843)</strong> | 
                    Régimen: <strong>{{ $profile ? ucfirst(str_replace('_', ' ', $profile->tax_regime)) : 'Responsable de IVA' }}</strong>
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($company->canIssueElectronicInvoices())
                    <a href="{{ route('business.billing.sales') }}" class="btn btn-spotlight px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Emitir Factura
                    </a>
                @else
                    <a href="{{ route('business.billing.profile') }}" class="btn btn-light text-dark px-3 py-2 rounded-pill shadow-sm fw-semibold">
                        <i class="bi bi-arrow-right-circle me-1"></i> Continuar Habilitación
                    </a>
                @endif
                <a href="{{ route('business.billing.documents.index') }}" class="btn btn-outline-light px-3 py-2 rounded-pill">
                    <i class="bi bi-folder2-open me-1"></i> Historial de Facturas
                </a>
            </div>
        </div>
    </div>

    {{-- Flujo de Habilitación en 4 Pasos --}}
    @php
        $step1Done = $profile && in_array($profile->fiscal_status, ['in_review', 'verified', 'pending_technical_setup', 'enabled'], true);
        $step2Done = $profile && in_array($profile->fiscal_status, ['verified', 'pending_technical_setup', 'enabled'], true);
        $step3Done = $config && $config->is_connected;
        $step4Done = $company->canIssueElectronicInvoices();
    @endphp
    <div class="vs-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-diagram-3 me-2 text-primary"></i>Ruta de Habilitación y Cumplimiento DIAN
            </h6>
            @if($step1Done && $step2Done && $step3Done && $step4Done)
                <span class="badge bg-success text-white px-3 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i>Habilitado para Facturar
                </span>
            @else
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold">
                    En Proceso de Habilitación
                </span>
            @endif
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="p-3 rounded-3 border h-100 {{ $step1Done ? 'bg-light border-success' : 'bg-white' }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-circle {{ $step1Done ? 'bg-success text-white' : 'bg-secondary text-white' }}" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        @if($step1Done)<i class="bi bi-check-circle-fill text-success"></i>@endif
                    </div>
                    <div class="fw-bold text-dark small mb-1">Perfil Fiscal & RUT</div>
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">Registro de responsabilidades, CIIU y documentos.</p>
                    <a href="{{ route('business.billing.profile') }}" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 0.75rem;">Gestionar Perfil &rarr;</a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 rounded-3 border h-100 {{ $step2Done ? 'bg-light border-success' : 'bg-white' }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-circle {{ $step2Done ? 'bg-success text-white' : 'bg-secondary text-white' }}" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        @if($step2Done)<i class="bi bi-check-circle-fill text-success"></i>@endif
                    </div>
                    <div class="fw-bold text-dark small mb-1">Verificación Legal</div>
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">Revisión de certificados mercantiles por Spotlight.</p>
                    <span class="badge {{ $step2Done ? 'bg-success text-white' : 'bg-secondary text-white' }}" style="font-size: 0.65rem;">
                        {{ $step2Done ? 'Aprobado' : ($profile && $profile->fiscal_status === 'in_review' ? 'En Revisión' : 'Pendiente') }}
                    </span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 rounded-3 border h-100 {{ $step3Done ? 'bg-light border-success' : 'bg-white' }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-circle {{ $step3Done ? 'bg-success text-white' : 'bg-secondary text-white' }}" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                        @if($step3Done)<i class="bi bi-check-circle-fill text-success"></i>@endif
                    </div>
                    <div class="fw-bold text-dark small mb-1">Resolución y Rangos DIAN (Formulario 1876)</div>
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">Vigencia de resolución, prefijos y numeración autorizada.</p>
                    <a href="{{ route('business.billing.configuration') }}" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 0.75rem;">Configurar Parámetros &rarr;</a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 rounded-3 border h-100 {{ $step4Done ? 'bg-light border-success' : 'bg-white' }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-circle {{ $step4Done ? 'bg-success text-white' : 'bg-secondary text-white' }}" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">4</span>
                        @if($step4Done)<i class="bi bi-check-circle-fill text-success"></i>@endif
                    </div>
                    <div class="fw-bold text-dark small mb-1">Emisión Habilitada</div>
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">Expedición de facturas con validación DIAN.</p>
                    <span class="badge {{ $step4Done ? 'bg-success text-white' : 'bg-danger text-white' }}" style="font-size: 0.65rem;">
                        {{ $step4Done ? 'Listo para Emitir' : 'Bloqueado' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Métricas Clave --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Facturado</span>
                    <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success"><i class="bi bi-currency-dollar fs-5"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-1">${{ number_format($totalInvoiced, 0, ',', '.') }}</h3>
                <span class="text-success small fw-medium"><i class="bi bi-check-all"></i> Facturas validadas DIAN</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">IVA Liquidado</span>
                    <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info"><i class="bi bi-pie-chart fs-5"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-1">${{ number_format($totalTax, 0, ',', '.') }}</h3>
                <span class="text-muted small">Tributos generados</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Documentos DIAN</span>
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary"><i class="bi bi-file-earmark-medical fs-5"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $totalDocuments }}</h3>
                <span class="text-muted small">Facturas y notas</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="vs-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Ventas Pendientes</span>
                    <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history fs-5"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $pendingSalesCount }}</h3>
                <a href="{{ route('business.billing.sales') }}" class="text-decoration-none small fw-semibold text-warning">Ver ventas por facturar &rarr;</a>
            </div>
        </div>
    </div>

    {{-- Accesos Rápidos y Documentos Recientes --}}
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="vs-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2"></i>Facturas Electrónicas Recientes</h5>
                    <a href="{{ route('business.billing.documents.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Ver Todo el Historial</a>
                </div>

                @if($recentDocuments->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-receipt fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-3">Aún no se han emitido facturas electrónicas para este comercio.</p>
                        @if($company->canIssueElectronicInvoices())
                            <a href="{{ route('business.billing.sales') }}" class="btn btn-spotlight btn-sm px-3 rounded-pill">
                                Emitir Factura
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total COP</th>
                                    <th>Estado DIAN</th>
                                    <th class="text-end" style="min-width: 130px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDocuments as $doc)
                                    <tr>
                                        <td>
                                            <a href="{{ route('business.billing.documents.show', $doc) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $doc->full_number }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.65rem;">
                                                {{ $doc->type_label }}
                                                @if($doc->parentDocument)
                                                    <span class="text-info">(Ref: {{ $doc->parentDocument->full_number }})</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="small">{{ $doc->issue_date->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark small">{{ $doc->customer_name }}</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">{{ $doc->customer_id_number }}</div>
                                        </td>
                                        <td class="fw-bold text-dark small">${{ number_format($doc->payable_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $doc->status_badge_class }}" style="font-size: 0.7rem;">
                                                {{ $doc->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-end" style="white-space: nowrap;">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('business.billing.documents.show', $doc) }}" class="btn btn-outline-secondary" title="Ver Detalle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('business.billing.documents.download_pdf', $doc) }}" target="_blank" class="btn btn-outline-secondary" title="Representación Gráfica PDF">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                                @if($doc->xml_path)
                                                    <a href="{{ route('business.billing.documents.download_xml', $doc) }}" class="btn btn-outline-secondary" title="Descargar XML">
                                                        <i class="bi bi-file-earmark-code"></i>
                                                    </a>
                                                @endif
                                                @if($doc->customer_email)
                                                    <a href="mailto:{{ $doc->customer_email }}?subject={{ rawurlencode('Factura Electrónica ' . $doc->full_number . ' - ' . ($company->razon_social ?: $company->name)) }}&body={{ rawurlencode("Estimado(a) {$doc->customer_name},\n\nLe compartimos la factura electrónica {$doc->full_number}.\n\nAtentamente,\n" . ($company->razon_social ?: $company->name)) }}" class="btn btn-outline-secondary" title="Enviar por correo">
                                                        <i class="bi bi-envelope"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="vs-card p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-gear-wide-connected me-2"></i>Herramientas de Facturación</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('business.billing.sales') }}" class="btn btn-light text-start p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small">Ventas Elegibles</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Generar facturas a partir de pedidos pagados</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>

                    <a href="{{ route('business.billing.invoices.create') }}" class="btn btn-light text-start p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small">Factura Directa</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Expedir factura libre sin pedido web</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>

                    <a href="{{ route('business.billing.reports.index') }}" class="btn btn-light text-start p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small">Reportes Fiscales</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Informes mensuales, IVA discriminado y CSV</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>

                    <a href="{{ route('business.billing.configuration') }}" class="btn btn-light text-start p-3 rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small">Resolución de Facturación</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Configuración de rangos y prefijos (Form. 1876)</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>

            <div class="vs-card p-4" style="background-color: #fdfafc; border-color: rgba(217, 0, 119, 0.2);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <h6 class="fw-bold text-dark mb-0">Facturación Certificada</h6>
                </div>
                <p class="text-muted small mb-2 leading-relaxed" style="font-size: 0.75rem;">
                    Tus facturas y notas electrónicas cuentan con validación previa oficial ante la DIAN y código QR de autenticidad.
                </p>
                <a href="https://www.dian.gov.co/impuestos/factura-electronica/Paginas/default.aspx" target="_blank" rel="noopener noreferrer" class="text-decoration-none small fw-semibold" style="color: var(--sl-primary, #7F1D3F); font-size: 0.75rem;">
                    <i class="bi bi-question-circle me-1"></i>¿Tienes dudas con tu resolución? Consulta la guía aquí &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
