@php
  $isRegistration = !isset($company) || !$company->id;
  $docsDisabled = !$isRegistration && $company->status === 'active' && $company->document_update_status !== 'granted';
  $selectedCountry = old('country', isset($company) ? ($company->country ?? 'CO') : 'CO');
@endphp

{{-- ══════════════════════════════════════════════════════════════
     CONTENEDOR 1: COLOMBIA 🇨🇴 (Conservado 100% Sin Modificaciones)
     ══════════════════════════════════════════════════════════════ --}}
<div id="container-colombia" class="country-container animate-fade-in" style="{{ $selectedCountry === 'MX' ? 'display: none;' : 'display: block;' }}">
    
    <!-- Selector de Tipo de Negocio -->
    <div class="mb-4">
        <label class="form-label fw-semibold text-dark mb-2">
            <span class="lang-en">What type of business do you have?</span>
            <span class="lang-es">¿Qué tipo de negocio tienes?</span>
        </label>
        <div class="row g-3">
            <div class="col-sm-6">
                <input type="radio" class="btn-check" name="tipo_negocio" id="tipo_formal" value="formal" autocomplete="off" {{ old('tipo_negocio', $company->tipo_negocio ?? 'formal') === 'formal' ? 'checked' : '' }}>
                <label class="business-type-card h-100 w-100" for="tipo_formal">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="type-icon-box">
                            <i class="bi bi-building"></i>
                        </div>
                        <span class="fw-bold fs-6 text-heading">Empresa registrada</span>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.76rem; line-height: 1.4;">
                        Persona Jurídica o Natural con registro mercantil formal ante Cámara de Comercio.
                    </small>
                </label>
            </div>
            <div class="col-sm-6">
                <input type="radio" class="btn-check" name="tipo_negocio" id="tipo_informal" value="informal" autocomplete="off" {{ old('tipo_negocio', $company->tipo_negocio ?? '') === 'informal' ? 'checked' : '' }}>
                <label class="business-type-card h-100 w-100 position-relative" for="tipo_informal">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="type-icon-box">
                            <i class="bi bi-shop"></i>
                        </div>
                        <span class="fw-bold fs-6 text-heading">Emprendimiento local</span>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.76rem; line-height: 1.4;">
                        Negocio o taller independiente local en crecimiento no registrado formalmente.
                    </small>
                    <span class="position-absolute top-0 end-0 translate-middle-y badge rounded-pill shadow-xs px-2 py-1" style="font-size: 0.68rem; right: 12px !important; z-index: 2; background: linear-gradient(135deg, #7F1D3F, #D98C9A); color: white;">
                        <i class="bi bi-stars"></i> Popular
                    </span>
                </label>
            </div>
        </div>
    </div>

    <!-- Campos Comunes: Ubicación Geográfica Precisa -->
    <div class="mb-4">
        <label class="form-label text-sm fw-semibold text-dark mb-2 d-flex align-items-center justify-content-between">
            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Ubicación Física del Negocio <span class="text-danger">*</span></span>
            <span class="badge bg-light text-primary border rounded-pill px-2.5 py-0.5 text-2xs fw-semibold">
                Selecciona tu ciudad
            </span>
        </label>

        {{-- Selector de Departamento y Ciudad --}}
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <label class="form-label text-2xs fw-bold text-dark mb-1">Departamento / Estado <span class="text-danger">*</span></label>
                <select name="state" id="co_state" class="form-select form-select-sm fw-semibold">
                    {{-- Dinámico con colombia-geo.js --}}
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label text-2xs fw-bold text-dark mb-1">Ciudad / Municipio <span class="text-danger">*</span></label>
                <select name="city" id="co_city" class="form-select form-select-sm fw-semibold">
                    {{-- Dinámico con colombia-geo.js --}}
                </select>
            </div>
        </div>

        {{-- Dirección con buscador acotado --}}
        <div class="input-group mb-2">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-signpost text-muted"></i></span>
            <input type="text" name="address" id="business_address_input" class="form-control form-control-sm border-start-0 text-sm"
                   value="{{ old('address', $company->address ?? '') }}" placeholder="Ej. Carrera 7 # 8-20, Calle 10 con 5..." required autocomplete="off">
            <button type="button" class="btn btn-sm btn-primary px-3 fw-semibold shadow-2xs" id="btn_search_reg_map">
                <i class="bi bi-search me-1"></i> Ubicar
            </button>
        </div>

        <div class="map-helper-status-box rounded-3 p-2.5 mb-2 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
            <div id="map-status-badge" class="fw-medium mb-1" style="font-size: 0.78rem; line-height: 1.4; white-space: normal; word-break: break-word;">
                <span class="text-dark"><i class="bi bi-geo-fill me-1" style="color: #7F1D3F;"></i>Mapa listo para ubicar tu negocio</span>
            </div>
            <div class="text-muted" style="font-size: 0.72rem; line-height: 1.35;">
                <i class="bi bi-info-circle me-1 text-primary"></i>Escribe la calle y pulsa <strong>"Ubicar"</strong>, o arrastra el marcador azul al punto exacto de tu local.
            </div>
        </div>

        {{-- Mapa Interactivo de Ubicación --}}
        <div id="register-map-container" class="rounded-3 border overflow-hidden position-relative shadow-sm" style="height: 230px; background: #e2e8f0;">
            <div id="company_reg_map" style="width: 100%; height: 100%; min-height: 230px;"></div>
        </div>
        <input type="hidden" name="latitude" id="business_lat" value="{{ old('latitude', $company->latitude ?? 5.3086) }}">
        <input type="hidden" name="longitude" id="business_lng" value="{{ old('longitude', $company->longitude ?? -73.8149) }}">
    </div>

    <!-- Sub-contenedor: Empresa Registrada Formal (Colombia) -->
    <div id="container-formal" class="business-fields-container animate-fade-in">
        <h6 class="fw-bold mb-3" style="color: #7F1D3F;"><i class="bi bi-building-check me-1"></i> Datos de Empresa Registrada</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">Razón Social <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-building"></i></span>
                    <input type="text" name="razon_social" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('razon_social', $company->razon_social ?? '') }}" placeholder="Ej. Distribuciones Alfa 24/7 S.A.S." oninput="this.value = this.value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\,\&\'\-\#\/]/g, '')">
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">Letras, números y signos comerciales (&, -, ., #)</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">NIT <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hash"></i></span>
                    <input type="text" name="nit" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('nit', $company->nit ?? '') }}" placeholder="Ej. 900123456 o 900123456-1" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9\-]/g, '')">
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">Solo números y guion opcional con dígito</small>
            </div>
        </div>

        <h6 class="fw-bold mb-3 mt-4" style="color: #7F1D3F;"><i class="bi bi-person-badge me-1"></i> Representante Legal</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">Nombre Completo <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="nombre_representante" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('nombre_representante', $company->nombre_representante ?? '') }}" placeholder="Ej. Juan Pérez" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\.\'\-]+$" oninput="this.value = this.value.replace(/[0-9]/g, '').replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\.\'\-]/g, '')">
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">Solo letras y espacios (sin números)</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">Cédula <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-vcard"></i></span>
                    <input type="text" name="cedula_propietario" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('cedula_propietario', $company->cedula_propietario ?? '') }}" placeholder="Cédula de ciudadanía" inputmode="numeric" pattern="^[0-9]+$" minlength="5" maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">Solo números de 0-9 (mín. 5 dígitos)</small>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label text-sm fw-semibold text-dark mb-1">
                    Fecha de Nacimiento <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar3"></i></span>
                    <input type="date" name="birthdate" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('birthdate', (isset($user) && $user->birthdate ? $user->birthdate->format('Y-m-d') : (auth()->check() && auth()->user()->birthdate ? auth()->user()->birthdate->format('Y-m-d') : ''))) }}" max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                </div>
                <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-shield-lock-fill text-danger me-1"></i>Obligatorio mayor de 18 años para el representante.
                </small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-semibold text-dark mb-1">
                    Correo Electrónico <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email_representante" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('email_representante', $company->email_representante ?? '') }}" placeholder="correo@ejemplo.com">
                </div>
                <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-bell text-muted me-1"></i>Para notificaciones legales y comerciales.
                </small>
            </div>
        </div>

        <h6 class="fw-bold mb-3 mt-4" style="color: #7F1D3F;"><i class="bi bi-files me-1"></i> Documentos Adjuntos</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">Cámara de Comercio (PDF) <span class="text-danger">*</span></label>
                <input type="file" name="camara_comercio_file" class="form-control text-sm" accept="application/pdf,.pdf" {{ $docsDisabled ? 'disabled data-locked="true"' : '' }}>
                <small class="text-danger fw-semibold d-block mt-1" style="font-size: 0.72rem;"><i class="bi bi-file-earmark-pdf me-1"></i>Obligatorio en formato PDF (Máx. 5MB).</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">RUT (PDF) <span class="text-danger">*</span></label>
                <input type="file" name="rut_file" class="form-control text-sm" accept="application/pdf,.pdf" {{ $docsDisabled ? 'disabled data-locked="true"' : '' }}>
                <small class="text-danger fw-semibold d-block mt-1" style="font-size: 0.72rem;"><i class="bi bi-file-earmark-pdf me-1"></i>Obligatorio en formato PDF (Máx. 5MB).</small>
            </div>
        </div>
    </div>

    <!-- Sub-contenedor: Emprendimiento Local Informal (Colombia) -->
    <div id="container-informal" class="business-fields-container animate-fade-in" style="display: none;">
        <h6 class="fw-bold mb-3" style="color: #7F1D3F;"><i class="bi bi-shop me-1"></i> Datos de Emprendimiento Local</h6>
        
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1 d-flex align-items-center justify-content-between">
                    <span>Nombre Comercial <span class="text-danger">*</span></span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-shop"></i></span>
                    <input type="text" name="nombre_comercial" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('nombre_comercial', $company->nombre_comercial ?? '') }}" placeholder="Ej. Studio 54, Tienda Don Pedro" oninput="this.value = this.value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\,\&\'\-\#\/]/g, '')">
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">Nombre visible con el que te reconocen tus clientes.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1 d-flex align-items-center justify-content-between">
                    <span>Cédula del Propietario <span class="text-danger">*</span></span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-vcard"></i></span>
                    <input type="text" name="cedula_propietario" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('cedula_propietario', $company->cedula_propietario ?? '') }}" placeholder="Número de documento de identidad" inputmode="numeric" pattern="^[0-9]+$" minlength="5" maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">Solo dígitos numéricos (0-9, mín. 5 dígitos).</small>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-sm fw-semibold text-dark mb-1">
                    Fecha de Nacimiento <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar3"></i></span>
                    <input type="date" name="birthdate" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('birthdate', (isset($user) && $user->birthdate ? $user->birthdate->format('Y-m-d') : (auth()->check() && auth()->user()->birthdate ? auth()->user()->birthdate->format('Y-m-d') : ''))) }}" max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                </div>
                <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-shield-lock-fill text-danger me-1"></i>Obligatorio mayor de 18 años para ser titular.
                </small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-semibold text-dark mb-1">
                    RUT Personal <span class="text-muted fw-normal text-xs">(Opcional)</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-file-earmark-pdf"></i></span>
                    <input type="file" name="rut_personal_file" class="form-control form-control-sm border-start-0 text-sm" accept="application/pdf,.pdf" {{ $docsDisabled ? 'disabled data-locked="true"' : '' }}>
                </div>
                <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-file-earmark-pdf text-muted me-1"></i>Si posees RUT personal en PDF (Máx. 5MB).
                </small>
            </div>
        </div>

        {{-- Apartado de Tutoriales de Formalización para Empresas Informales --}}
        <div class="card border rounded-4 overflow-hidden mb-4 shadow-xs" style="background: linear-gradient(135deg, #fff9fa 0%, #ffffff 100%); border-color: rgba(127, 29, 63, 0.18) !important;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2" style="background: #F6E7EB; color: #7F1D3F;">
                            <i class="bi bi-mortarboard-fill fs-5"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                                ¿Cómo es el proceso para volverte formal? (Tutoriales)
                            </h6>
                            <span class="text-muted" style="font-size: 0.75rem;">Acompañamiento educativo para emprendedores en crecimiento</span>
                        </div>
                    </div>
                    @if(Route::has('business.formalization.guide'))
                        <a href="{{ route('business.formalization.guide') }}" class="btn btn-sm btn-outline-spotlight rounded-pill px-3" target="_blank" style="font-size: 0.75rem;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Guía Completa
                        </a>
                    @endif
                </div>

                {{-- Aviso de No Intermediación --}}
                <div class="alert alert-warning border-0 rounded-3 p-3 mb-3 d-flex align-items-start gap-2 shadow-xs" style="background-color: #fffbeb; color: #92400e;">
                    <i class="bi bi-shield-exclamation text-warning fs-5 flex-shrink-0 mt-1" style="color: #b45309 !important;"></i>
                    <div style="font-size: 0.78rem; line-height: 1.45;">
                        <strong class="d-block mb-1" style="color: #92400e;">Aviso Importante: Spotlight NO realiza este trámite</strong>
                        Nuestra plataforma es exclusivamente un canal tecnológico de comercio. <strong>Nosotros no tramitamos, no cobramos ni gestionamos registros mercantiles ni tributarios</strong> ante la Cámara de Comercio ni la DIAN. Esta guía reúne los tutoriales oficiales para que lo gestiones de forma directa y autónoma.
                    </div>
                </div>

                {{-- Pasos esenciales resumidos --}}
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <div class="p-2 px-3 bg-white border rounded-3 d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">1</span>
                            <div class="overflow-hidden">
                                <strong class="d-block text-dark small text-truncate" style="font-size: 0.78rem;">Consultar Homonimia</strong>
                                <span class="text-muted d-block text-truncate" style="font-size: 0.7rem;">Verificar nombre en rues.org.co</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 px-3 bg-white border rounded-3 d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">2</span>
                            <div class="overflow-hidden">
                                <strong class="d-block text-dark small text-truncate" style="font-size: 0.78rem;">Elegir Figura Legal</strong>
                                <span class="text-muted d-block text-truncate" style="font-size: 0.7rem;">Persona Natural o Empresa S.A.S.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 px-3 bg-white border rounded-3 d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">3</span>
                            <div class="overflow-hidden">
                                <strong class="d-block text-dark small text-truncate" style="font-size: 0.78rem;">Matrícula en Cámara CCB</strong>
                                <span class="text-muted d-block text-truncate" style="font-size: 0.7rem;">ccb.org.co o sede Ubaté</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 px-3 bg-white border rounded-3 d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">4</span>
                            <div class="overflow-hidden">
                                <strong class="d-block text-dark small text-truncate" style="font-size: 0.78rem;">RUT y NIT en la DIAN</strong>
                                <span class="text-muted d-block text-truncate" style="font-size: 0.7rem;">CIIU y responsabilidades fiscales</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enlaces a tutoriales en video --}}
                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    <a href="https://www.youtube.com/results?search_query=como+inscribir+el+rut+por+primera+vez+dian+virtual" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger rounded-pill px-3" style="font-size: 0.75rem;">
                        <i class="bi bi-youtube me-1"></i> Tutorial RUT DIAN
                    </a>
                    <a href="https://www.youtube.com/results?search_query=como+matricular+empresa+camara+de+comercio+de+bogota+en+linea" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.75rem;">
                        <i class="bi bi-youtube me-1"></i> Tutorial Matrícula CCB
                    </a>
                    <a href="https://www.ccb.org.co" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.75rem;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Portal CCB Ubaté
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkbox Habeas Data Colombia (Ley 1581) -->
    <div class="form-check mb-4 bg-light p-3 rounded border" id="habeas_data_colombia_wrapper">
        <input class="form-check-input ms-1 me-2 mt-1" type="checkbox" name="habeas_data_accepted" id="habeasDataCheckCO" value="1" {{ old('habeas_data_accepted', $company->habeas_data_accepted ?? false) ? 'checked' : '' }} required>
        <label class="form-check-label text-sm text-dark" for="habeasDataCheckCO">
            He leído y acepto la <a href="#" class="text-primary text-decoration-none fw-medium">Política de Tratamiento de Datos Personales</a> (Ley 1581 de 2012 - Habeas Data Colombia). Autorizo el uso de mi información para fines del servicio.
        </label>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     CONTENEDOR 2: MÉXICO 🇲🇽 (Adaptado a la Realidad Mexicana)
     ══════════════════════════════════════════════════════════════ --}}
<div id="container-mexico" class="country-container animate-fade-in" style="{{ $selectedCountry === 'MX' ? 'display: block;' : 'display: none;' }}">
    
    <!-- Banner de Encabezado México (Puebla) -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 p-3 rounded-3 mb-4" style="background: linear-gradient(135deg, rgba(127, 29, 63, 0.08), rgba(217, 140, 154, 0.15)); border: 1px solid rgba(127, 29, 63, 0.15);">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 42px; height: 42px; background: #ffffff; border: 1px solid rgba(127, 29, 63, 0.18);">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="18" viewBox="0 0 36 24" class="rounded-1">
                    <rect width="12" height="24" x="0" fill="#006847"/>
                    <rect width="12" height="24" x="12" fill="#ffffff"/>
                    <rect width="12" height="24" x="24" fill="#ce1126"/>
                    <circle cx="18" cy="12" r="2.8" fill="#8c6239"/>
                </svg>
            </div>
            <div>
                <h6 class="fw-bold mb-0.5" style="color: #7F1D3F; font-size: 0.94rem;">Emprendimiento en Puebla, México</h6>
                <small class="text-muted d-block" style="font-size: 0.75rem; line-height: 1.35;">Registro exclusivo para emprendedores, artesanos y negocios de México sin trámites colombianos ni RFC obligatorio.</small>
            </div>
        </div>
        <span class="badge rounded-pill px-3 py-1.5 align-self-start align-self-sm-center flex-shrink-0" style="background: #7F1D3F; color: #fff; font-size: 0.72rem;">Spotlight Puebla</span>
    </div>

    <!-- 1. Datos Básicos del Emprendimiento -->
    <h6 class="fw-bold mb-3" style="color: #7F1D3F;"><i class="bi bi-shop me-1"></i> Información General del Emprendimiento</h6>
    <div class="row g-3 mb-3 align-items-start">
        <div class="col-md-6">
            <label class="form-label text-sm fw-semibold text-dark mb-1 biz-uniform-label">Nombre Comercial o Marca <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-shop"></i></span>
                <input type="text" name="nombre_comercial" id="mx_nombre_comercial" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('nombre_comercial', $company->name ?? '') }}" placeholder="Ej. Creta Accesorios, Talavera Puebla" required>
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Nombre visible para tus clientes en el marketplace.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label text-sm fw-semibold text-dark mb-1 biz-uniform-label">Categoría del Negocio <span class="text-danger">*</span></label>
            <select name="category" id="mx_category" class="form-select form-select-sm text-sm" required>
                <option value="">Selecciona una categoría...</option>
                @php
                    $categories = [
                        'Alimentos y Bebidas Típicas',
                        'Artesanías, Folklore y Arte',
                        'Moda, Ropa y Calzado',
                        'Joyería y Accesorios',
                        'Belleza y Cuidado Personal',
                        'Hogar, Cerámica y Decoración',
                        'Tecnología y Accesorios',
                        'Salud y Bienestar',
                        'Servicios Creativos',
                        'Otro Emprendimiento'
                    ];
                    $currentCat = old('category', $company->category ?? '');
                @endphp
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $currentCat === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Sector principal donde se exhibirán tus productos.</small>
        </div>
    </div>

    <!-- 2. Responsable y Contacto -->
    <h6 class="fw-bold mb-3 mt-4" style="color: #7F1D3F;"><i class="bi bi-person-badge me-1"></i> Responsable y Medios de Contacto</h6>
    <div class="row g-3 mb-3 align-items-start">
        <div class="col-md-6">
            <label class="form-label text-sm fw-semibold text-dark mb-1 biz-uniform-label">Nombre del Responsable</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                <input type="text" name="nombre_representante" id="mx_nombre_representante" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('nombre_representante', $company->nombre_representante ?? '') }}" placeholder="Ej. Valentina Ramos Flores" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\.\'\-]+$" oninput="this.value = this.value.replace(/[0-9]/g, '').replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\.\'\-]/g, '')">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Solo letras y espacios (sin números).</small>
        </div>
        <div class="col-md-6">
            <label class="form-label text-sm fw-semibold text-dark mb-1 biz-uniform-label">
                Fecha de Nacimiento
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar3"></i></span>
                <input type="date" name="birthdate" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('birthdate', (isset($user) && $user->birthdate ? $user->birthdate->format('Y-m-d') : (auth()->check() && auth()->user()->birthdate ? auth()->user()->birthdate->format('Y-m-d') : ''))) }}" max="{{ now()->subYears(18)->format('Y-m-d') }}">
            </div>
            <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">
                <i class="bi bi-shield-lock-fill text-danger me-1"></i>Requisito legal mayor de 18 años para titulares.
            </small>
        </div>
    </div>
    <div class="row g-3 mb-4 align-items-start">
        <div class="col-md-6">
            <label class="form-label text-sm fw-semibold text-dark mb-1 biz-uniform-label">Correo Electrónico de Atención</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email_representante" id="mx_email_representante" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('email_representante', $company->email ?? '') }}" placeholder="contacto@mitienda.mx">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Para notificaciones y contacto directo.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label text-sm fw-semibold text-dark mb-1 biz-uniform-label">Teléfono Móvil o WhatsApp (+52)</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-sm fw-bold border-end-0"><i class="bi bi-whatsapp text-success me-1"></i> +52</span>
                <input type="tel" name="phone" id="mx_phone" class="form-control form-control-sm border-start-0 text-sm" value="{{ old('phone', $company->phone ?? '') }}" placeholder="222 123 4567">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Clave Lada de Puebla (222) o tu número de WhatsApp.</small>
        </div>
    </div>

    <!-- 3. Ubicación Local en México (Puebla u otros Estados) -->
    <div class="mb-4">
        <label class="form-label text-sm fw-semibold text-dark mb-2 d-flex align-items-center justify-content-between flex-wrap gap-1">
            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Ubicación Física del Emprendimiento (México) <span class="text-danger">*</span></span>
            <span class="badge bg-light text-primary border rounded-pill px-2.5 py-0.5 text-2xs fw-semibold">
                Puebla o cualquier Estado
            </span>
        </label>

        {{-- Selector de Estado y Municipio para México --}}
        <div class="row g-2 mb-2 align-items-start">
            <div class="col-md-6">
                <label class="form-label text-2xs fw-bold text-dark mb-1 biz-uniform-label">Estado en México <span class="text-danger">*</span></label>
                <select name="state" id="mx_state" class="form-select form-select-sm fw-semibold">
                    {{-- Dinámico con colombia-geo.js (Puebla por defecto) --}}
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label text-2xs fw-bold text-dark mb-1 biz-uniform-label">Municipio / Alcaldía <span class="text-danger">*</span></label>
                <select name="city" id="mx_city" class="form-select form-select-sm fw-semibold">
                    {{-- Dinámico con colombia-geo.js --}}
                </select>
            </div>
        </div>

        {{-- Dirección con buscador acotado y Código Postal --}}
        <div class="row g-2 mb-2 align-items-start">
            <div class="col-md-8">
                <label class="form-label text-2xs fw-bold text-dark mb-1 biz-uniform-label">Dirección o Calle <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-signpost"></i></span>
                    <input type="text" name="address" id="mx_address" class="form-control form-control-sm border-start-0 text-sm"
                           value="{{ old('address', $company->address ?? '') }}" placeholder="Ej. Trópico 3 Int 104, Av. Palafox #204" required autocomplete="off">
                    <button type="button" class="btn btn-sm btn-primary px-3 fw-semibold shadow-2xs" id="btn_search_mx_map">
                        <i class="bi bi-search me-1"></i> Ubicar
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label text-2xs fw-bold text-dark mb-1 biz-uniform-label">Código Postal (C.P.)</label>
                <input type="text" name="postal_code" id="mx_postal_code" class="form-control form-control-sm text-sm"
                       value="{{ old('postal_code', $company->postal_code ?? '') }}" placeholder="Ej. 72640" maxlength="10" inputmode="numeric">
            </div>
        </div>

        <div class="map-helper-status-box rounded-3 p-2.5 mb-2 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
            <div id="mx-map-status-badge" class="fw-medium mb-1" style="font-size: 0.78rem; line-height: 1.4; white-space: normal; word-break: break-word;">
                <span class="text-dark"><i class="bi bi-geo-fill me-1" style="color: #7F1D3F;"></i>Ubicado en Puebla por defecto</span>
            </div>
            <div class="text-muted" style="font-size: 0.72rem; line-height: 1.35;">
                <i class="bi bi-info-circle me-1 text-primary"></i>Selecciona tu municipio, escribe tu calle y C.P. y pulsa <strong>"Ubicar"</strong>, o arrastra el pin azul al punto exacto.
            </div>
        </div>

        {{-- Mapa Interactivo para México --}}
        <div id="mx-register-map-container" class="rounded-3 border overflow-hidden position-relative shadow-sm" style="height: 230px; background: #e2e8f0;">
            <div id="mx_reg_map" style="width: 100%; height: 100%; min-height: 230px;"></div>
        </div>
        <input type="hidden" name="latitude" id="mx_lat" value="{{ old('latitude', $company->latitude ?? 19.0414) }}">
        <input type="hidden" name="longitude" id="mx_lng" value="{{ old('longitude', $company->longitude ?? -98.2063) }}">
    </div>

    <!-- 4. Descripción y Logotipo -->
    <h6 class="fw-bold mb-3 mt-4" style="color: #7F1D3F;"><i class="bi bi-image me-1"></i> Perfil Público y Presentación</h6>
    <div class="row g-3 mb-3">
        <div class="col-12">
            <label class="form-label text-sm fw-medium text-dark mb-1">Descripción del Emprendimiento</label>
            <textarea name="description" id="mx_description" rows="3" class="form-control text-sm" placeholder="Cuéntanos la historia de tu marca, técnicas que utilizas o qué hace únicos a tus productos...">{{ old('description', $company->description ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label text-sm fw-medium text-dark mb-1">Logotipo o Imagen de la Tienda (Opcional)</label>
            <input type="file" name="logo" id="mx_logo" class="form-control text-sm" accept="image/jpeg,image/png,image/webp">
            <small class="text-muted" style="font-size: 0.72rem;">Formatos recomendados: JPG, PNG, WEBP (Cuadrado, Máx. 2MB).</small>
        </div>
    </div>

    <!-- 5. Datos Fiscales Mexicanos (RFC - Totalmente Opcional) -->
    <div class="card border rounded-3 p-3 mb-4" style="background-color: #fafbfc; border-color: #e2e8f0;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check me-1" style="color: #7F1D3F;"></i> Datos Fiscales en México (Opcional)</h6>
            <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.7rem;">Sin retenciones ni CFDI</span>
        </div>
        <p class="text-muted small mb-3" style="font-size: 0.77rem; line-height: 1.45;">
            En Spotlight <strong>el RFC es 100% opcional</strong> para crear tu cuenta y publicar tus productos. No exigimos CURP ni documentos tributarios colombianos (RUT, NIT o Cámara de Comercio). Si cuentas con RFC y deseas registrarlo como referencia informativa para tu tienda, puedes ingresarlo a continuación:
        </p>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">RFC (Opcional)</label>
                <input type="text" name="tax_id" id="mx_tax_id" class="form-control text-sm" value="{{ old('tax_id', $company->tax_id ?? '') }}" placeholder="Ej. XAXX010101000" maxlength="15" style="text-transform: uppercase;">
                <small class="text-muted" style="font-size: 0.70rem;">Si no dispones de RFC, deja este campo en blanco. No afecta tu registro.</small>
            </div>
        </div>
    </div>

    <!-- Checkbox Términos y Aviso de Privacidad México -->
    <div class="form-check mb-4 bg-light p-3 rounded border" id="habeas_data_mexico_wrapper">
        <input class="form-check-input ms-1 me-2 mt-1" type="checkbox" name="habeas_data_accepted" id="habeasDataCheckMX" value="1" {{ old('habeas_data_accepted', $company->habeas_data_accepted ?? false) ? 'checked' : '' }} required>
        <label class="form-check-label text-sm text-dark" for="habeasDataCheckMX">
            He leído y acepto los <a href="#" class="text-primary text-decoration-none fw-medium">Términos y Condiciones</a> y el <a href="#" class="text-primary text-decoration-none fw-medium">Aviso de Privacidad</a> de Spotlight para emprendimientos en México.
        </label>
    </div>
</div>

@if(!$isRegistration && $company->status === 'active')
    <div class="alert {{ $company->document_update_status === 'granted' ? 'alert-success' : 'alert-info' }} mb-4 border-0 shadow-sm d-flex align-items-center">
        @if($company->document_update_status === 'granted')
            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
            <div>
                <h6 class="mb-1 fw-bold">Permiso concedido</h6>
                <p class="mb-0 text-sm">Ya puedes subir los nuevos documentos. Al guardar, tu cuenta volverá a estar en revisión.</p>
            </div>
        @elseif($company->document_update_status === 'requested')
            <i class="bi bi-hourglass-split fs-4 me-3 text-info"></i>
            <div>
                <h6 class="mb-1 fw-bold">Solicitud enviada</h6>
                <p class="mb-0 text-sm">Has solicitado cambiar tus documentos. Espera a que el administrador habilite la edición.</p>
            </div>
        @else
            <i class="bi bi-shield-lock-fill fs-4 me-3 text-info"></i>
            <div>
                <h6 class="mb-1 fw-bold">Documentos bloqueados</h6>
                <p class="mb-0 text-sm">Tus documentos actuales ya fueron aprobados. <button type="button" class="btn btn-sm btn-outline-spotlight-alt ms-2 mt-1 mt-md-0" data-bs-toggle="modal" data-bs-target="#requestUpdateModal">Solicitar modificación</button></p>
            </div>
        @endif
    </div>
@endif

@once
@push('styles')
<link href="{{ asset('css/business.css') }}?v=2.9" rel="stylesheet">
@endpush
@endonce

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Referencias de Contenedores de País
    const containerCO = document.getElementById('container-colombia');
    const containerMX = document.getElementById('container-mexico');

    // Referencias Colombia Formal / Informal
    const radioFormal = document.getElementById('tipo_formal');
    const radioInformal = document.getElementById('tipo_informal');
    const containerFormal = document.getElementById('container-formal');
    const containerInformal = document.getElementById('container-informal');

    // Función auxiliar para habilitar/deshabilitar elementos de un contenedor
    function setInputsDisabled(container, disabledState) {
        if (!container) return;
        const elements = container.querySelectorAll('input, select, textarea');
        elements.forEach(el => {
            if (el.hasAttribute('data-locked')) {
                el.disabled = true;
            } else {
                el.disabled = disabledState;
            }
        });
    }

    // Lógica para alternar formal/informal en Colombia
    function toggleFormalInformal() {
        if (!radioFormal || !radioInformal || !containerFormal || !containerInformal) return;
        // Solo alternar si el contenedor de Colombia está activo
        if (containerCO && containerCO.style.display !== 'none') {
            if (radioFormal.checked) {
                containerFormal.style.display = 'block';
                containerInformal.style.display = 'none';
                setInputsDisabled(containerInformal, true);
                setInputsDisabled(containerFormal, false);
            } else {
                containerFormal.style.display = 'none';
                containerInformal.style.display = 'block';
                setInputsDisabled(containerFormal, true);
                setInputsDisabled(containerInformal, false);
            }
        }
    }

    if (radioFormal && radioInformal) {
        radioFormal.addEventListener('change', toggleFormalInformal);
        radioInformal.addEventListener('change', toggleFormalInformal);
    }

    // ─── Mapas Interactivos con Precisión Geográfica (Colombia y México) ───
    function initRegisterMapCO() {
        const mapEl = document.getElementById('company_reg_map');
        if (!mapEl || typeof L === 'undefined') return;
        if (window._regMapInstanceCO) {
            setTimeout(() => window._regMapInstanceCO.invalidateSize(), 100);
            return;
        }

        try {
            if (typeof window.initSpotlightPreciseLocation === 'function') {
                const picker = window.initSpotlightPreciseLocation({
                    country: 'CO',
                    stateSelectId: 'co_state',
                    citySelectId: 'co_city',
                    addressInputId: 'business_address_input',
                    latInputId: 'business_lat',
                    lngInputId: 'business_lng',
                    searchBtnId: 'btn_search_reg_map',
                    statusDisplayId: 'map-status-badge',
                    mapId: 'company_reg_map',
                    initialState: "{{ old('state', $company->state ?? 'Cundinamarca') }}",
                    initialCity: "{{ old('city', $company->city ?? 'Ubaté') }}",
                    initialLat: "{{ old('latitude', $company->latitude ?? 5.3086) }}",
                    initialLng: "{{ old('longitude', $company->longitude ?? -73.8149) }}"
                });
                if (picker) {
                    window._regMapInstanceCO = picker.map;
                    window._regMapInstance = picker.map;
                }
            }
        } catch (e) {
            console.error('Error al inicializar mapa Colombia:', e);
        }
    }

    function initRegisterMapMX() {
        const mapEl = document.getElementById('mx_reg_map');
        if (!mapEl || typeof L === 'undefined') return;
        if (window._regMapInstanceMX) {
            setTimeout(() => window._regMapInstanceMX.invalidateSize(), 100);
            return;
        }

        try {
            if (typeof window.initSpotlightPreciseLocation === 'function') {
                const picker = window.initSpotlightPreciseLocation({
                    country: 'MX',
                    stateSelectId: 'mx_state',
                    citySelectId: 'mx_city',
                    addressInputId: 'mx_address',
                    postalCodeInputId: 'mx_postal_code',
                    latInputId: 'mx_lat',
                    lngInputId: 'mx_lng',
                    searchBtnId: 'btn_search_mx_map',
                    statusDisplayId: 'mx-map-status-badge',
                    mapId: 'mx_reg_map',
                    initialState: "{{ old('state', $company->state ?? 'Puebla') }}",
                    initialCity: "{{ old('city', $company->city ?? 'Puebla') }}",
                    initialLat: "{{ old('latitude', $company->latitude ?? 19.0414) }}",
                    initialLng: "{{ old('longitude', $company->longitude ?? -98.2063) }}"
                });
                if (picker) {
                    window._regMapInstanceMX = picker.map;
                }
            }
        } catch (e) {
            console.error('Error al inicializar mapa México:', e);
        }
    }

    // ─── Función Global para Alternar entre Colombia y México ───
    window.switchRegistrationCountry = function(countryCode) {
        const isMexico = (countryCode === 'MX');

        if (isMexico) {
            if (containerCO) {
                containerCO.style.display = 'none';
                setInputsDisabled(containerCO, true);
            }
            if (containerMX) {
                containerMX.style.display = 'block';
                setInputsDisabled(containerMX, false);
                setTimeout(initRegisterMapMX, 150);
                if (window._regMapInstanceMX) {
                    setTimeout(() => window._regMapInstanceMX.invalidateSize(), 200);
                }
            }
        } else {
            if (containerMX) {
                containerMX.style.display = 'none';
                setInputsDisabled(containerMX, true);
            }
            if (containerCO) {
                containerCO.style.display = 'block';
                const commonInputs = containerCO.querySelectorAll('#business_address_input, #business_lat, #business_lng, #co_state, #co_city, #habeasDataCheckCO, input[name="tipo_negocio"]');
                commonInputs.forEach(i => i.disabled = false);
                toggleFormalInformal();
                setTimeout(initRegisterMapCO, 150);
                if (window._regMapInstanceCO) {
                    setTimeout(() => window._regMapInstanceCO.invalidateSize(), 200);
                }
            }
        }
    };

    // Estado inicial al cargar la página
    const initialCountry = '{{ $selectedCountry }}';
    window.switchRegistrationCountry(initialCountry);

    window.initCompanyRegMap = function() {
        initRegisterMapCO();
        initRegisterMapMX();
    };

    const step2El = document.getElementById('step-2');
    if ((document.getElementById('company_reg_map') || document.getElementById('mx_reg_map')) && (!step2El || step2El.style.display !== 'none')) {
        setTimeout(window.initCompanyRegMap, 200);
    }
});
</script>

@if(!$isRegistration && $company->status === 'active' && $company->document_update_status !== 'granted' && $company->document_update_status !== 'requested')
<!-- Modal para solicitar actualización de documentos -->
<div class="modal fade" id="requestUpdateModal" tabindex="-1" aria-labelledby="requestUpdateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <form action="{{ route('business.company.request_document_update') }}" method="POST">
        @csrf
        <div class="modal-header bg-light border-0">
          <h5 class="modal-title fw-bold" id="requestUpdateModalLabel">Solicitar Cambio de Documentos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-medium">Motivo del cambio <span class="text-danger">*</span></label>
            <textarea name="document_update_reason" class="form-control" rows="3" required placeholder="Explica brevemente por qué necesitas actualizar tus documentos legales..."></textarea>
          </div>
          <div class="alert alert-warning border-0 p-3 mb-0 text-sm" style="background-color: #fffbeb; color: #92400e;">
            <i class="bi bi-exclamation-triangle me-2"></i> Al enviar esta solicitud, el administrador revisará tu caso y habilitará temporalmente la carga de nuevos archivos. Una vez los subas, tu tienda volverá a revisión.
          </div>
        </div>
        <div class="modal-footer border-0 bg-light">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-spotlight fw-semibold text-white" style="background: linear-gradient(135deg, #7F1D3F, #D98C9A); border: none;">Enviar Solicitud</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
