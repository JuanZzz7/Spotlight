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

    <!-- Campos Comunes: Ubicación Ubaté -->
    <div class="mb-4">
        <label class="form-label text-sm fw-semibold text-dark mb-2">Dirección Física (Ubaté) <span class="text-danger">*</span></label>
        <div class="input-group mb-2">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
            <input type="text" name="address" id="business_address_input" class="form-control form-control-lg border-start-0 text-sm" value="{{ old('address', $company->address ?? '') }}" placeholder="Ej. Carrera 7 # 8-20, Ubaté" required autocomplete="off">
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted" style="font-size: 0.75rem;">
                <i class="bi bi-info-circle me-1"></i>Escribe la dirección o haz clic en el mapa para fijar tu ubicación.
            </small>
            <span id="map-status-badge" class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.7rem;">
                <i class="bi bi-geo-fill me-1" style="color: #7F1D3F;"></i>Listo
            </span>
        </div>

        {{-- Mapa Interactivo de Ubicación --}}
        <div id="register-map-container" class="rounded-3 border overflow-hidden position-relative shadow-sm" style="height: 220px; background: #e2e8f0;">
            <div id="company_reg_map" style="width: 100%; height: 100%; min-height: 220px;"></div>
        </div>
        <input type="hidden" name="latitude" id="business_lat" value="{{ old('latitude', $company->latitude ?? 5.3086) }}">
        <input type="hidden" name="longitude" id="business_lng" value="{{ old('longitude', $company->longitude ?? -73.8149) }}">
        <input type="hidden" name="state" id="co_state" value="Cundinamarca">
        <input type="hidden" name="city" id="co_city" value="Ubaté">
    </div>

    <!-- Sub-contenedor: Empresa Registrada Formal (Colombia) -->
    <div id="container-formal" class="business-fields-container animate-fade-in">
        <h6 class="fw-bold mb-3" style="color: #7F1D3F;"><i class="bi bi-building-check me-1"></i> Datos de Empresa Registrada</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">Razón Social <span class="text-danger">*</span></label>
                <input type="text" name="razon_social" class="form-control text-sm" value="{{ old('razon_social', $company->razon_social ?? '') }}" placeholder="Nombre legal (Solo letras)" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '')">
                <small class="text-muted" style="font-size: 0.72rem;">Solo caracteres alfabéticos (A-Z, a-z)</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">NIT <span class="text-danger">*</span></label>
                <input type="text" name="nit" class="form-control text-sm" value="{{ old('nit', $company->nit ?? '') }}" placeholder="Ej. 900123456" inputmode="numeric" pattern="^[0-9]+$" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <small class="text-muted" style="font-size: 0.72rem;">Solo números de 0-9</small>
            </div>
        </div>

        <h6 class="fw-bold mb-3 mt-4" style="color: #7F1D3F;"><i class="bi bi-person-badge me-1"></i> Representante Legal</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label text-sm fw-medium text-dark mb-1">Nombre Completo <span class="text-danger">*</span></label>
                <input type="text" name="nombre_representante" class="form-control text-sm" value="{{ old('nombre_representante', $company->nombre_representante ?? '') }}" placeholder="Ej. Juan Pérez" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '')">
                <small class="text-muted" style="font-size: 0.72rem;">Solo letras (A-Z, a-z)</small>
            </div>
            <div class="col-md-4">
                <label class="form-label text-sm fw-medium text-dark mb-1">Cédula <span class="text-danger">*</span></label>
                <input type="text" name="cedula_propietario" class="form-control text-sm" value="{{ old('cedula_propietario', $company->cedula_propietario ?? '') }}" placeholder="Cédula de ciudadanía" inputmode="numeric" pattern="^[0-9]+$" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <small class="text-muted" style="font-size: 0.72rem;">Solo números de 0-9</small>
            </div>
            <div class="col-md-4">
                <label class="form-label text-sm fw-medium text-dark mb-1">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email" name="email_representante" class="form-control text-sm" value="{{ old('email_representante', $company->email_representante ?? '') }}" placeholder="correo@ejemplo.com">
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
                <label class="form-label text-sm fw-medium text-dark mb-1">Nombre Comercial <span class="text-danger">*</span></label>
                <input type="text" name="nombre_comercial" class="form-control text-sm" value="{{ old('nombre_comercial', $company->nombre_comercial ?? '') }}" placeholder="¿Cómo conocen tu negocio?" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '')">
                <small class="text-muted" style="font-size: 0.72rem;">Solo letras (A-Z, a-z)</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm fw-medium text-dark mb-1">Cédula del Propietario <span class="text-danger">*</span></label>
                <input type="text" name="cedula_propietario" class="form-control text-sm" value="{{ old('cedula_propietario', $company->cedula_propietario ?? '') }}" placeholder="Número de identificación" inputmode="numeric" pattern="^[0-9]+$" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <small class="text-muted" style="font-size: 0.72rem;">Solo números de 0-9</small>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label text-sm fw-medium text-dark mb-1">RUT Personal (PDF opcional)</label>
            <input type="file" name="rut_personal_file" class="form-control text-sm" accept="application/pdf,.pdf" {{ $docsDisabled ? 'disabled data-locked="true"' : '' }}>
            <small class="text-muted" style="font-size: 0.75rem;">Si posees RUT personal, adjúntalo en formato PDF (Máx. 5MB).</small>
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
    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background: linear-gradient(135deg, rgba(127, 29, 63, 0.08), rgba(217, 140, 154, 0.15)); border: 1px solid rgba(127, 29, 63, 0.15);">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-2">🇲🇽</div>
            <div>
                <h6 class="fw-bold mb-0" style="color: #7F1D3F;">Emprendimiento en Puebla, México</h6>
                <small class="text-muted">Registro exclusivo para emprendedores, artesanos y negocios de Puebla sin trámites colombianos ni RFC obligatorio.</small>
            </div>
        </div>
        <span class="badge rounded-pill px-3 py-1" style="background: #7F1D3F; color: #fff; font-size: 0.72rem;">Spotlight Puebla</span>
    </div>

    <!-- 1. Datos Básicos del Emprendimiento -->
    <h6 class="fw-bold mb-3" style="color: #7F1D3F;"><i class="bi bi-shop me-1"></i> Información General del Emprendimiento</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-7">
            <label class="form-label text-sm fw-medium text-dark mb-1">Nombre Comercial o de la Marca <span class="text-danger">*</span></label>
            <input type="text" name="nombre_comercial" id="mx_nombre_comercial" class="form-control text-sm" value="{{ old('nombre_comercial', $company->name ?? '') }}" placeholder="Ej. Talavera y Cerámica Tradicional de Puebla" required>
            <small class="text-muted" style="font-size: 0.72rem;">Nombre visible con el que tus clientes te conocerán en el marketplace.</small>
        </div>
        <div class="col-md-5">
            <label class="form-label text-sm fw-medium text-dark mb-1">Categoría del Emprendimiento <span class="text-danger">*</span></label>
            <select name="category" id="mx_category" class="form-select text-sm" required>
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
        </div>
    </div>

    <!-- 2. Responsable y Contacto -->
    <h6 class="fw-bold mb-3 mt-4" style="color: #7F1D3F;"><i class="bi bi-person-badge me-1"></i> Responsable y Medios de Contacto</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label text-sm fw-medium text-dark mb-1">Nombre del Responsable / Administrador</label>
            <input type="text" name="nombre_representante" id="mx_nombre_representante" class="form-control text-sm" value="{{ old('nombre_representante', $company->nombre_representante ?? '') }}" placeholder="Ej. Valentina Ramos Flores">
        </div>
        <div class="col-md-4">
            <label class="form-label text-sm fw-medium text-dark mb-1">Correo Electrónico de Atención</label>
            <input type="email" name="email_representante" id="mx_email_representante" class="form-control text-sm" value="{{ old('email_representante', $company->email ?? '') }}" placeholder="contacto@mitienda.mx">
        </div>
        <div class="col-md-4">
            <label class="form-label text-sm fw-medium text-dark mb-1">Teléfono Móvil o WhatsApp (+52)</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-sm fw-bold border-end-0">+52</span>
                <input type="tel" name="phone" id="mx_phone" class="form-control text-sm border-start-0" value="{{ old('phone', $company->phone ?? '') }}" placeholder="222 123 4567">
            </div>
            <small class="text-muted" style="font-size: 0.72rem;">Clave Lada de Puebla (222) o tu número de WhatsApp.</small>
        </div>
    </div>

    <!-- 3. Ubicación Local en Puebla -->
    <h6 class="fw-bold mb-3 mt-4" style="color: #7F1D3F;"><i class="bi bi-geo-alt me-1"></i> Ubicación Local en Puebla</h6>
    <input type="hidden" name="state" id="mx_state" value="Puebla">
    <input type="hidden" name="city" id="mx_city" value="Puebla">
    <div class="row g-3 mb-3">
        <div class="col-md-8">
            <label class="form-label text-sm fw-medium text-dark mb-1">Dirección Física o Comercial (Puebla) <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                <input type="text" name="address" id="mx_address" class="form-control text-sm border-start-0" value="{{ old('address', $company->address ?? '') }}" placeholder="Ej. Av. Juan de Palafox y Mendoza #204, Centro Histórico" required>
            </div>
            <small class="text-muted" style="font-size: 0.72rem;">Dirección local en Puebla donde elaboras, exhibes o despachas tus productos.</small>
        </div>
        <div class="col-md-4">
            <label class="form-label text-sm fw-medium text-dark mb-1">Código Postal (C.P.)</label>
            <input type="text" name="postal_code" id="mx_postal_code" class="form-control text-sm" value="{{ old('postal_code', $company->postal_code ?? '') }}" placeholder="Ej. 72000" maxlength="10">
        </div>
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

<style>
.animate-fade-in {
    animation: fadeIn 0.35s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
/* Estilo moderno para selector de tipo de negocio */
.business-type-card {
    display: block;
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 0.95rem 1.15rem;
    cursor: pointer;
    transition: all 0.22s ease;
}

.business-type-card:hover {
    border-color: #D98C9A;
    background: #FFFCF7;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(127, 29, 63, 0.08);
}

.type-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #F6E7EB;
    color: #7F1D3F;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.22s ease;
}

.btn-check:checked + .business-type-card {
    border-color: #7F1D3F !important;
    background-color: #FFFCF7 !important;
    box-shadow: 0 6px 18px rgba(127, 29, 63, 0.15) !important;
}

.btn-check:checked + .business-type-card .type-icon-box {
    background: #7F1D3F !important;
    color: #ffffff !important;
}

.btn-check:checked + .business-type-card .text-heading {
    color: #7F1D3F !important;
}
</style>

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
            }
        } else {
            if (containerMX) {
                containerMX.style.display = 'none';
                setInputsDisabled(containerMX, true);
            }
            if (containerCO) {
                containerCO.style.display = 'block';
                // Habilitar campos comunes de Colombia
                const commonInputs = containerCO.querySelectorAll('#business_address_input, #business_lat, #business_lng, #co_state, #co_city, #habeasDataCheckCO, input[name="tipo_negocio"]');
                commonInputs.forEach(i => i.disabled = false);
                toggleFormalInformal();
                // Invalidate leaflet map size if exists
                if (window._regMapInstance) {
                    setTimeout(() => window._regMapInstance.invalidateSize(), 200);
                }
            }
        }
    };

    // Estado inicial al cargar la página
    const initialCountry = '{{ $selectedCountry }}';
    window.switchRegistrationCountry(initialCountry);

    // ─── Mapa Interactivo para la Dirección (Colombia / Ubaté) ───
    function initRegisterMap() {
        const mapEl = document.getElementById('company_reg_map');
        if (!mapEl || typeof L === 'undefined') return;
        if (window._regMapInstance) {
            setTimeout(() => {
                window._regMapInstance.invalidateSize();
            }, 100);
            return;
        }

        const latInput = document.getElementById('business_lat');
        const lngInput = document.getElementById('business_lng');
        const addrInput = document.getElementById('business_address_input');
        const statusBadge = document.getElementById('map-status-badge');

        let defaultLat = parseFloat(latInput ? latInput.value : 5.3086) || 5.3086;
        let defaultLng = parseFloat(lngInput ? lngInput.value : -73.8149) || -73.8149;

        try {
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
                iconUrl:       'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
                shadowUrl:     'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            });

            const regMap = L.map(mapEl, {
                scrollWheelZoom: false
            }).setView([defaultLat, defaultLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19
            }).addTo(regMap);

            let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(regMap);
            window._regMapInstance = regMap;
            window._regMarkerInstance = marker;

            function setCoords(lat, lng) {
                if (latInput) latInput.value = lat.toFixed(8);
                if (lngInput) lngInput.value = lng.toFixed(8);
                marker.setLatLng([lat, lng]);
            }

            function handleMapPick(lat, lng) {
                setCoords(lat, lng);
                if (statusBadge) {
                    statusBadge.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span>Buscando dirección...';
                }
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=es`)
                    .then(r => r.json())
                    .then(data => {
                        const a = data.address || {};
                        let str = '';
                        if (a.road) str += a.road;
                        if (a.house_number) str += ' #' + a.house_number;
                        const town = a.town || a.city || a.municipality || 'Ubaté';
                        if (town) str += (str ? ', ' : '') + town;
                        if (!str && data.display_name) str = data.display_name.split(',').slice(0, 3).join(', ');
                        if (str && addrInput) {
                            addrInput.value = str;
                        }
                        if (statusBadge) {
                            statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-2 py-1';
                            statusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Ubicado en mapa';
                        }
                    })
                    .catch(() => {
                        if (statusBadge) {
                            statusBadge.className = 'badge bg-light text-muted border px-2 py-1';
                            statusBadge.innerHTML = '<i class="bi bi-geo-alt me-1"></i>Punto seleccionado';
                        }
                    });
            }

            regMap.on('click', e => handleMapPick(e.latlng.lat, e.latlng.lng));
            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                handleMapPick(pos.lat, pos.lng);
            });

            let debounceTimer = null;
            if (addrInput) {
                addrInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    if (query.length < 4) return;

                    if (statusBadge) {
                        statusBadge.className = 'badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1';
                        statusBadge.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span>Buscando en mapa...';
                    }

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const fullQuery = query.toLowerCase().includes('ubat') ? query : `${query}, Ubaté, Cundinamarca, Colombia`;
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullQuery)}&accept-language=es&limit=1`)
                            .then(r => r.json())
                            .then(results => {
                                if (results && results.length > 0) {
                                    const lat = parseFloat(results[0].lat);
                                    const lon = parseFloat(results[0].lon);
                                    setCoords(lat, lon);
                                    regMap.setView([lat, lon], 15);
                                    if (statusBadge) {
                                        statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-2 py-1';
                                        statusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Reflejado en mapa';
                                    }
                                } else {
                                    if (statusBadge) {
                                        statusBadge.className = 'badge bg-light text-muted border px-2 py-1';
                                        statusBadge.innerHTML = '<i class="bi bi-pin-map me-1"></i>Fija el punto en el mapa';
                                    }
                                }
                            })
                            .catch(() => {});
                    }, 600);
                });
            }

            setTimeout(() => regMap.invalidateSize(), 400);

        } catch (e) {
            console.error('Error al inicializar mapa en registro:', e);
        }
    }

    window.initCompanyRegMap = initRegisterMap;
    const step2El = document.getElementById('step-2');
    if (document.getElementById('company_reg_map') && (!step2El || step2El.style.display !== 'none')) {
        setTimeout(initRegisterMap, 200);
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
