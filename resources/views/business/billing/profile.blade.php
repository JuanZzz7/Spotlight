@extends('layouts.dashboard')

@section('page-title', 'Perfil Fiscal y Verificación DIAN')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Tarjeta de Estado Actual --}}
            <div class="vs-card p-4 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="badge {{ $profile->status_badge_class }} px-3 py-2 text-uppercase mb-2">
                            {{ $profile->status_label }}
                        </span>
                        <h4 class="fw-bold text-dark mb-1">Perfil Fiscal del Comercio</h4>
                        <p class="text-muted small mb-0">
                            Configure los datos tributarios reglamentarios registrados en el RUT para solicitar la habilitación de facturación electrónica.
                        </p>
                    </div>

                    @if($profile->fiscal_status === 'rejected' && $profile->status_notes)
                        <div class="alert alert-danger mb-0 p-3 rounded-3" style="max-width: 450px;">
                            <strong class="d-block small"><i class="bi bi-exclamation-octagon-fill me-1"></i> Motivo del Rechazo:</strong>
                            <span class="small">{{ $profile->status_notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Formulario de Perfil Fiscal --}}
            <form action="{{ route('business.billing.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-person-vcard text-primary me-2"></i>1. Identificación Tributaria del Contribuyente
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tipo de Persona <span class="text-danger">*</span></label>
                            <select name="person_type" class="form-select @error('person_type') is-invalid @enderror" required>
                                <option value="persona_juridica" {{ old('person_type', $profile->person_type) === 'persona_juridica' ? 'selected' : '' }}>Persona Jurídica (Sociedad / SAS / Ltda)</option>
                                <option value="persona_natural" {{ old('person_type', $profile->person_type) === 'persona_natural' ? 'selected' : '' }}>Persona Natural (Comerciante Individual)</option>
                            </select>
                            @error('person_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tipo de Documento <span class="text-danger">*</span></label>
                            <select name="id_type" class="form-select @error('id_type') is-invalid @enderror" required>
                                <option value="31" {{ old('id_type', $profile->id_type) === '31' ? 'selected' : '' }}>31 - NIT (Número de Identificación Tributaria)</option>
                                <option value="13" {{ old('id_type', $profile->id_type) === '13' ? 'selected' : '' }}>13 - Cédula de Ciudadanía</option>
                                <option value="22" {{ old('id_type', $profile->id_type) === '22' ? 'selected' : '' }}>22 - Cédula de Extranjería</option>
                                <option value="41" {{ old('id_type', $profile->id_type) === '41' ? 'selected' : '' }}>41 - Pasaporte</option>
                            </select>
                            @error('id_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Número de Identificación (sin DV) <span class="text-danger">*</span></label>
                            <input type="text" name="identification_number" class="form-control @error('identification_number') is-invalid @enderror" 
                                   value="{{ old('identification_number', $profile->identification_number) }}" 
                                   placeholder="Ej: 901234567" required>
                            <small class="text-muted">Ingrese únicamente números sin guion ni dígito de verificación.</small>
                            @error('identification_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Dígito de Verificación (DV)</label>
                            <input type="text" class="form-control bg-light" value="{{ $profile->dv ?: 'Calculado auto (Módulo 11)' }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Razón Social Registrada en RUT <span class="text-danger">*</span></label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name', $profile->business_name) }}" 
                                   placeholder="Nombre o Razón Social exacta según RUT" required>
                            @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nombre Comercial</label>
                            <input type="text" name="commercial_name" class="form-control @error('commercial_name') is-invalid @enderror" 
                                   value="{{ old('commercial_name', $profile->commercial_name) }}" 
                                   placeholder="Nombre comercial del establecimiento">
                            @error('commercial_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-file-earmark-ruled text-primary me-2"></i>2. Régimen y Responsabilidades Tributarias DIAN
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Régimen Fiscal <span class="text-danger">*</span></label>
                            <select name="tax_regime" class="form-select @error('tax_regime') is-invalid @enderror" required>
                                <option value="responsable_iva" {{ old('tax_regime', $profile->tax_regime) === 'responsable_iva' ? 'selected' : '' }}>Responsable de IVA (Código DIAN 48)</option>
                                <option value="no_responsable_iva" {{ old('tax_regime', $profile->tax_regime) === 'no_responsable_iva' ? 'selected' : '' }}>No Responsable de IVA (Código DIAN 49)</option>
                                <option value="regimen_simple" {{ old('tax_regime', $profile->tax_regime) === 'regimen_simple' ? 'selected' : '' }}>Régimen Simple de Tributación (RST)</option>
                                <option value="gran_contribuyente" {{ old('tax_regime', $profile->tax_regime) === 'gran_contribuyente' ? 'selected' : '' }}>Gran Contribuyente (Código DIAN 13)</option>
                            </select>
                            @error('tax_regime') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Código CIIU Actividad Económica Principal <span class="text-danger">*</span></label>
                            <input type="text" name="ciiu_code" class="form-control @error('ciiu_code') is-invalid @enderror" 
                                   value="{{ old('ciiu_code', $profile->ciiu_code) }}" 
                                   placeholder="Ej: 4719, 4771, 5611" maxlength="4" required>
                            <small class="text-muted">4 dígitos según casilla 46 de su RUT.</small>
                            @error('ciiu_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Responsabilidades Fiscales Registradas en Casilla 53 del RUT <span class="text-danger">*</span></label>
                            @php
                                $selectedResp = old('tax_responsibilities', $profile->tax_responsibilities ?: []);
                            @endphp
                            <div class="row g-2 p-3 bg-light rounded-3 border">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tax_responsibilities[]" value="O-13" id="resp_o13" {{ in_array('O-13', $selectedResp) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="resp_o13"><strong>O-13:</strong> Gran Contribuyente</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tax_responsibilities[]" value="O-15" id="resp_o15" {{ in_array('O-15', $selectedResp) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="resp_o15"><strong>O-15:</strong> Autorretenedor</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tax_responsibilities[]" value="O-23" id="resp_o23" {{ in_array('O-23', $selectedResp) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="resp_o23"><strong>O-23:</strong> Agente de Retención en Ventas (IVA)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tax_responsibilities[]" value="O-47" id="resp_o47" {{ in_array('O-47', $selectedResp) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="resp_o47"><strong>O-47:</strong> Régimen Simple de Tributación</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tax_responsibilities[]" value="R-99-PN" id="resp_r99" {{ in_array('R-99-PN', $selectedResp) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="resp_r99"><strong>R-99-PN:</strong> No responsable de IVA</label>
                                    </div>
                                </div>
                            </div>
                            @error('tax_responsibilities') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-geo-alt text-primary me-2"></i>3. Domicilio Fiscal y Notificaciones
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Dirección Fiscal <span class="text-danger">*</span></label>
                            <input type="text" name="fiscal_address" class="form-control @error('fiscal_address') is-invalid @enderror" 
                                   value="{{ old('fiscal_address', $profile->fiscal_address ?: $company->address) }}" 
                                   placeholder="Ej: Carrera 7 # 8-45" required>
                            @error('fiscal_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Municipio DANE <span class="text-danger">*</span></label>
                            <input type="text" name="municipality_code" class="form-control" value="25843" readonly>
                            <small class="text-muted">25843 - Ubaté</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Departamento DANE <span class="text-danger">*</span></label>
                            <input type="text" name="department_code" class="form-control" value="25" readonly>
                            <small class="text-muted">25 - Cundinamarca</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Código Postal</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $profile->postal_code ?: '250420') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Correo Electrónico para Facturación <span class="text-danger">*</span></label>
                            <input type="email" name="fiscal_email" class="form-control @error('fiscal_email') is-invalid @enderror" 
                                   value="{{ old('fiscal_email', $profile->fiscal_email ?: $company->email) }}" required>
                            @error('fiscal_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Teléfono Fiscal <span class="text-danger">*</span></label>
                            <input type="text" name="fiscal_phone" class="form-control @error('fiscal_phone') is-invalid @enderror" 
                                   value="{{ old('fiscal_phone', $profile->fiscal_phone ?: $company->phone) }}" required>
                            @error('fiscal_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-file-earmark-pdf text-primary me-2"></i>4. Documentación de Respaldo (PDF)
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">RUT Completo (PDF)</label>
                            <input type="file" name="rut_file" class="form-control @error('rut_file') is-invalid @enderror" accept=".pdf">
                            <small class="text-muted d-block mt-1">
                                Estado: {{ $profile->rut_document_path || $company->rut_file ? 'Archivo cargado ✓' : 'Pendiente' }}
                            </small>
                            @error('rut_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Cámara de Comercio (PDF)</label>
                            <input type="file" name="camara_comercio_file" class="form-control @error('camara_comercio_file') is-invalid @enderror" accept=".pdf">
                            <small class="text-muted d-block mt-1">
                                Estado: {{ $profile->camara_comercio_document_path || $company->camara_comercio_file ? 'Archivo cargado ✓' : 'Pendiente' }}
                            </small>
                            @error('camara_comercio_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Cédula Representante Legal (PDF)</label>
                            <input type="file" name="legal_rep_id_file" class="form-control @error('legal_rep_id_file') is-invalid @enderror" accept=".pdf">
                            <small class="text-muted d-block mt-1">
                                Estado: {{ $profile->legal_rep_id_document_path ? 'Archivo cargado ✓' : 'Opcional si figura en RUT' }}
                            </small>
                            @error('legal_rep_id_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mb-5">
                    <a href="{{ route('business.billing.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Cancelar</a>
                    <button type="submit" class="btn btn-spotlight px-5 py-2 shadow-sm">
                        <i class="bi bi-send-check me-2"></i> Guardar y Enviar a Revisión
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
