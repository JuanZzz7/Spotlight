@extends('layouts.dashboard')

@section('page-title', 'Facturación Electrónica DIAN')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="vs-card p-4 p-md-5 text-center shadow-sm mb-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                     style="width: 80px; height: 80px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F);">
                    <i class="bi bi-shield-lock-fill fs-1"></i>
                </div>

                <h3 class="fw-bold text-dark mb-2">Módulo de Facturación Electrónica DIAN</h3>
                <p class="text-muted mb-4 fs-6">
                    Estimado comerciante de <strong class="text-dark">{{ $company->name }}</strong>, actualmente su negocio está registrado bajo la modalidad 
                    <span class="badge bg-secondary px-3 py-2 text-uppercase">Comercio Informal / Emprendimiento</span>.
                </p>

                <div class="alert alert-info text-start rounded-4 p-4 mb-4 border-0" style="background-color: #f0f7ff; color: #1e3a8a;">
                    <h6 class="fw-bold d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-info-circle-fill fs-5 text-primary"></i> 
                        Marco Normativo Colombiano (Estatuto Tributario & Resolución DIAN 000165 de 2023)
                    </h6>
                    <p class="small mb-0 leading-relaxed">
                        En Colombia, la expedición de factura electrónica de venta con validación previa exige personería jurídica o inscripción mercantil formal con Registro Único Tributario (RUT) y responsabilidades fiscales activas. Spotlight no asume la emisión a nombre propio de ventas pertenecientes a terceros independientes.
                    </p>
                </div>

                <div class="text-start mb-4 p-4 rounded-4 bg-light border">
                    <h6 class="fw-bold text-dark mb-3">Ruta para habilitar su facturación electrónica en Spotlight:</h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted small">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check2-circle text-success fs-5 flex-shrink-0"></i>
                            <span>Inscribir su matrícula mercantil en la Cámara de Comercio de Bogotá (Sede Regional Ubaté).</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check2-circle text-success fs-5 flex-shrink-0"></i>
                            <span>Obtener o actualizar su RUT ante la DIAN con responsabilidades tributarias y código CIIU.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check2-circle text-success fs-5 flex-shrink-0"></i>
                            <span>Actualizar el perfil de su empresa en Spotlight a <strong>Comercio Formal</strong> adjuntando los certificados correspondientes.</span>
                        </li>
                    </ul>
                </div>

                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="{{ route('business.formalization.guide') }}" class="btn btn-warning px-4 py-2 text-dark fw-bold rounded-pill shadow-xs">
                        <i class="bi bi-mortarboard-fill me-1"></i> Ver Tutoriales de Formalización
                    </a>
                    <a href="{{ route('business.company.edit') }}" class="btn btn-spotlight px-4 py-2">
                        <i class="bi bi-pencil-square me-2"></i> Actualizar Perfil a Comercio Formal
                    </a>
                    <a href="{{ route('business.dashboard') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                        Volver al Panel Principal
                    </a>
                </div>
            </div>

            {{-- Centro de Tutoriales incrustado --}}
            <div class="mb-4">
                @include('business.partials.formalization-tutorials')
            </div>
        </div>
    </div>
</div>
@endsection
