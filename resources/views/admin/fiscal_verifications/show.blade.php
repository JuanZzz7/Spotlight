@extends('layouts.dashboard')

@section('page-title', 'Auditoría Fiscal: ' . $profile->business_name)

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="vs-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge {{ $profile->status_badge_class }} px-3 py-1 text-uppercase mb-2">
                    {{ $profile->status_label }}
                </span>
                <h4 class="fw-bold text-dark mb-1">{{ $profile->business_name }}</h4>
                <p class="text-muted small mb-0">
                    NIT: <strong>{{ $profile->identification_number }}-{{ $profile->dv }}</strong> | Comercio: <strong>{{ $profile->company->name }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.fiscal_verifications.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    &larr; Volver al Listado
                </a>
            </div>
        </div>
    </div>

    @if($profile->status_notes)
        <div class="alert alert-warning mb-4 rounded-4 p-3 border-0">
            <h6 class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Notas del Estado Actual:</h6>
            <p class="small mb-0">{{ $profile->status_notes }}</p>
        </div>
    @endif

    <div class="row g-4">
        {{-- Ficha Técnica del Comercio --}}
        <div class="col-lg-8">
            <div class="vs-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-card-checklist text-primary me-2"></i>Información Tributaria y Legal
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Tipo de Persona:</span>
                        <strong class="text-dark small">{{ $profile->person_type === 'persona_juridica' ? 'Persona Jurídica' : 'Persona Natural' }}</strong>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Régimen Fiscal:</span>
                        <strong class="text-dark small">{{ ucfirst(str_replace('_', ' ', $profile->tax_regime)) }}</strong>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Actividad Económica (CIIU):</span>
                        <strong class="text-dark small">{{ $profile->ciiu_code }}</strong>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Dirección Fiscal:</span>
                        <strong class="text-dark small">{{ $profile->fiscal_address }} (Ubaté, Cundinamarca)</strong>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Correo de Facturación:</span>
                        <strong class="text-dark small">{{ $profile->fiscal_email }}</strong>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Teléfono:</span>
                        <strong class="text-dark small">{{ $profile->fiscal_phone }}</strong>
                    </div>

                    <div class="col-12">
                        <span class="text-muted small d-block mb-1">Responsabilidades Fiscales Registradas:</span>
                        <div class="d-flex flex-wrap gap-2">
                            @if(is_array($profile->tax_responsibilities))
                                @foreach($profile->tax_responsibilities as $resp)
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $resp }}</span>
                                @endforeach
                            @else
                                <span class="text-muted small">No especificadas</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documentos Adjuntos para Descarga/Revisión --}}
            <div class="vs-card p-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-file-earmark-pdf text-primary me-2"></i>Documentos de Soporte
                </h5>

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <div>
                            <div class="fw-semibold text-dark small"><i class="bi bi-file-earmark-check text-success me-2"></i>Registro Único Tributario (RUT)</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Documento obligatorio para verificación tributaria</div>
                        </div>
                        @if($profile->rut_document_path || $profile->company->rut_file)
                            <a href="{{ route('admin.fiscal_verifications.download', [$profile, 'rut']) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-download me-1"></i> Descargar RUT
                            </a>
                        @else
                            <span class="badge bg-secondary">No adjunto</span>
                        @endif
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <div>
                            <div class="fw-semibold text-dark small"><i class="bi bi-file-earmark-check text-success me-2"></i>Certificado de Existencia y Representación (Cámara de Comercio)</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Certificado expedido por Cámara de Comercio sede Ubaté</div>
                        </div>
                        @if($profile->camara_comercio_document_path || $profile->company->camara_comercio_file)
                            <a href="{{ route('admin.fiscal_verifications.download', [$profile, 'camara']) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-download me-1"></i> Descargar Cámara
                            </a>
                        @else
                            <span class="badge bg-secondary">No adjunto</span>
                        @endif
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <div>
                            <div class="fw-semibold text-dark small"><i class="bi bi-person-bounding-box text-primary me-2"></i>Documento del Representante Legal</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Cédula de ciudadanía o extranjería</div>
                        </div>
                        @if($profile->legal_rep_id_document_path)
                            <a href="{{ route('admin.fiscal_verifications.download', [$profile, 'rep_id']) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-download me-1"></i> Descargar Documento
                            </a>
                        @else
                            <span class="badge bg-light text-muted border">No requerido</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel de Acciones Administrativas --}}
        <div class="col-lg-4">
            {{-- Aprobar Verificación --}}
            <div class="vs-card p-4 mb-4 border-success">
                <h6 class="fw-bold text-success mb-2"><i class="bi bi-check2-circle me-1"></i>Aprobar Verificación Legal</h6>
                <p class="text-muted small mb-3">
                    Certifica que la existencia legal y responsabilidades tributarias coinciden con el RUT. El comercio podrá proceder con la configuración técnica.
                </p>
                <form action="{{ route('admin.fiscal_verifications.verify', $profile) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small text-muted">Observaciones de Aprobación (Opcional):</label>
                        <input type="text" name="notes" class="form-control form-control-sm" value="Documentación legal revisada y conforme al RUT.">
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill fw-semibold" onclick="return confirm('¿Aprobar la verificación legal de este comercio?');">
                        <i class="bi bi-check-lg me-1"></i> Aprobar Verificación Legal
                    </button>
                </form>
            </div>

            {{-- Rechazar Verificación --}}
            <div class="vs-card p-4 mb-4 border-danger">
                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-x-circle me-1"></i>Rechazar Solicitud</h6>
                <p class="text-muted small mb-3">
                    Rechaza la solicitud indicando los motivos (ej. RUT desactualizado, matrícula mercantil no coincide).
                </p>
                <form action="{{ route('admin.fiscal_verifications.reject', $profile) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small text-muted">Motivo del Rechazo <span class="text-danger">*</span>:</label>
                        <textarea name="reason" rows="3" class="form-control form-control-sm" placeholder="Indique claramente qué documento debe corregir el comerciante..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-semibold" onclick="return confirm('¿Rechazar esta solicitud de habilitación?');">
                        <i class="bi bi-slash-circle me-1"></i> Rechazar con Observaciones
                    </button>
                </form>
            </div>

            {{-- Suspender Habilitación --}}
            @if($profile->fiscal_status === 'enabled')
                <div class="vs-card p-4 border-warning">
                    <h6 class="fw-bold text-warning mb-2"><i class="bi bi-pause-circle me-1"></i>Suspender Emisión</h6>
                    <p class="text-muted small mb-3">
                        Suspende temporalmente la facultad de emitir facturas electrónicas a este comercio.
                    </p>
                    <form action="{{ route('admin.fiscal_verifications.suspend', $profile) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted">Motivo de la Suspensión <span class="text-danger">*</span>:</label>
                            <textarea name="reason" rows="2" class="form-control form-control-sm" placeholder="Motivo de la suspensión..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill fw-semibold text-dark" onclick="return confirm('¿Suspender la habilitación fiscal de este comercio?');">
                            Suspender Habilitación
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
