@extends('layouts.dashboard')

@section('page-title', 'Configuración de Resolución DIAN')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Tarjeta de Estado de Conectividad --}}
            <div class="vs-card p-4 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            @if($config->is_connected)
                                <span class="badge bg-success px-3 py-2 text-uppercase">
                                    <i class="bi bi-check-circle-fill me-1"></i> Servicio DIAN Conectado
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2 text-uppercase">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Configuración Pendiente
                                </span>
                            @endif
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Parámetros de Resolución DIAN</h4>
                        <p class="text-muted small mb-0">
                            Configure la numeración autorizada en el Formulario 1876 expedido por la DIAN para expedir facturas legales.
                        </p>
                    </div>

                    <div>
                        <form action="{{ route('business.billing.configuration.test') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-spotlight px-4 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-arrow-repeat me-2"></i> Probar Conexión y Activar
                            </button>
                        </form>
                    </div>
                </div>

                @if($config->last_connection_test_at)
                    <div class="mt-3 pt-3 border-top small text-muted d-flex justify-content-between align-items-center">
                        <span>Última prueba realizada: <strong>{{ $config->last_connection_test_at->format('d/m/Y H:i:s') }}</strong></span>
                        @if($config->last_connection_error)
                            <span class="text-danger"><i class="bi bi-x-circle me-1"></i> {{ $config->last_connection_error }}</span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Formulario de Configuración --}}
            <form action="{{ route('business.billing.configuration.update') }}" method="POST">
                @csrf

                {{-- Campos ocultos para mantener compatibilidad técnica con el motor de emisión --}}
                <input type="hidden" name="provider" value="{{ old('provider', $config->provider ?: 'sandbox') }}">
                <input type="hidden" name="environment" value="{{ old('environment', $config->environment ?: 'sandbox') }}">

                {{-- 1. Resolución de Facturación DIAN (Formulario 1876) --}}
                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>1. Resolución de Facturación DIAN (Formulario 1876)
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Número de Resolución DIAN <span class="text-danger">*</span></label>
                            <input type="text" name="resolution_number" class="form-control @error('resolution_number') is-invalid @enderror" 
                                   value="{{ old('resolution_number', $config->resolution_number) }}" 
                                   placeholder="Ej: 18760000001" required>
                            @error('resolution_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Prefijo Autorizado</label>
                            <input type="text" name="resolution_prefix" class="form-control @error('resolution_prefix') is-invalid @enderror" 
                                   value="{{ old('resolution_prefix', $config->resolution_prefix) }}" 
                                   placeholder="Ej: SETP o FE" maxlength="10">
                            @error('resolution_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Consecutivo Actual <span class="text-danger">*</span></label>
                            <input type="number" name="current_consecutive" class="form-control @error('current_consecutive') is-invalid @enderror" 
                                   value="{{ old('current_consecutive', $config->current_consecutive) }}" required min="1">
                            @error('current_consecutive') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Rango Desde <span class="text-danger">*</span></label>
                            <input type="number" name="range_from" class="form-control @error('range_from') is-invalid @enderror" 
                                   value="{{ old('range_from', $config->range_from) }}" required min="1">
                            @error('range_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Rango Hasta <span class="text-danger">*</span></label>
                            <input type="number" name="range_to" class="form-control @error('range_to') is-invalid @enderror" 
                                   value="{{ old('range_to', $config->range_to) }}" required min="1">
                            @error('range_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Vigencia Desde <span class="text-danger">*</span></label>
                            <input type="date" name="resolution_date_from" class="form-control @error('resolution_date_from') is-invalid @enderror" 
                                   value="{{ old('resolution_date_from', $config->resolution_date_from ? $config->resolution_date_from->format('Y-m-d') : now()->toDateString()) }}" required>
                            @error('resolution_date_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Vigencia Hasta <span class="text-danger">*</span></label>
                            <input type="date" name="resolution_date_to" class="form-control @error('resolution_date_to') is-invalid @enderror" 
                                   value="{{ old('resolution_date_to', $config->resolution_date_to ? $config->resolution_date_to->format('Y-m-d') : now()->addYear()->toDateString()) }}" required>
                            @error('resolution_date_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Clave Técnica DIAN <span class="text-danger">*</span></label>
                            <textarea name="technical_key" rows="2" class="form-control font-monospace @error('technical_key') is-invalid @enderror" 
                                      placeholder="Clave técnica alfanumérica entregada por la DIAN para cálculo del CUFE" required>{{ old('technical_key', $config->technical_key) }}</textarea>
                            <small class="text-muted">Clave técnica oficial asignada en el formulario 1876 de la DIAN.</small>
                            @error('technical_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Credenciales y Prefijos de Facturación --}}
                <div class="vs-card p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-key text-primary me-2"></i>2. Credenciales y Prefijos de Facturación (Cifradas)
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Software ID / Client ID</label>
                            <input type="text" name="software_id" class="form-control" 
                                   value="{{ old('software_id', $config->software_id) }}" 
                                   placeholder="Identificador del software ante la DIAN">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Software PIN</label>
                            <input type="password" name="software_pin" class="form-control" 
                                   value="{{ old('software_pin', $config->software_pin) }}" 
                                   placeholder="PIN de 5 dígitos">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">API Token / Clave de Acceso</label>
                            <input type="password" name="api_token" class="form-control" 
                                   value="{{ old('api_token', $config->api_token) }}" 
                                   placeholder="Token secreto de autenticación">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">TestSetID (Set de Pruebas DIAN)</label>
                            <input type="text" name="test_set_id" class="form-control" 
                                   value="{{ old('test_set_id', $config->test_set_id) }}" 
                                   placeholder="UUID del set de pruebas entregado por catálogo DIAN">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Prefijo Notas Crédito</label>
                            <input type="text" name="credit_note_prefix" class="form-control" 
                                   value="{{ old('credit_note_prefix', $config->credit_note_prefix ?: 'NC') }}" maxlength="10">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mb-5">
                    <a href="{{ route('business.billing.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Volver</a>
                    <button type="submit" class="btn btn-spotlight px-5 py-2 rounded-pill shadow-sm fw-semibold">
                        <i class="bi bi-save me-2"></i> Guardar Parámetros de Facturación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
