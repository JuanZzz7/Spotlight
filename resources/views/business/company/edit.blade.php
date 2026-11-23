@extends('layouts.dashboard')
@section('title','Mi Empresa - Spotlight')
@section('page-title','Personalización de Empresa')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<link href="{{ asset('css/business.css') }}" rel="stylesheet">
@endpush

@section('dashboard-content')
<div style="max-width: 960px; margin: 0 auto;">

    {{-- Encabezado estilizado --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-black mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-gear-wide-connected text-primary"></i> Ajustes de la Empresa
            </h4>
            <p class="text-muted text-sm mb-0">Gestiona y personaliza la identidad, legalidad, contacto y cobros de tu negocio de forma ordenada.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill text-xs fw-semibold">
                <i class="bi bi-shop me-1 text-primary"></i> {{ $company->name ?: 'Mi Empresa' }}
            </span>
        </div>
    </div>

    {{-- Barra de Botones Desplegables / Selectores de Sección --}}
    <div class="row g-2 g-md-3 mb-4" id="settings-button-group">
        <div class="col-6 col-md-3">
            <button type="button" class="settings-nav-btn active" data-target="panel-general">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="btn-icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-palette"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="d-block text-xs text-uppercase opacity-75">Básico</span>
                        <span class="d-block text-sm text-truncate">General</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right chevron-icon d-none d-md-inline text-xs"></i>
            </button>
        </div>
        <div class="col-6 col-md-3">
            <button type="button" class="settings-nav-btn" data-target="panel-legal">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="btn-icon-box bg-success-subtle text-success">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="d-block text-xs text-uppercase opacity-75">Documentos</span>
                        <span class="d-block text-sm text-truncate">Legal</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right chevron-icon d-none d-md-inline text-xs"></i>
            </button>
        </div>
        <div class="col-6 col-md-3">
            <button type="button" class="settings-nav-btn" data-target="panel-contact">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="btn-icon-box bg-info-subtle text-info">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="d-block text-xs text-uppercase opacity-75">Ubicación</span>
                        <span class="d-block text-sm text-truncate">Contacto</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right chevron-icon d-none d-md-inline text-xs"></i>
            </button>
        </div>
        <div class="col-6 col-md-3">
            <button type="button" class="settings-nav-btn" data-target="panel-banking">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="btn-icon-box bg-warning-subtle text-warning">
                        <i class="bi bi-bank"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="d-block text-xs text-uppercase opacity-75">Finanzas</span>
                        <span class="d-block text-sm text-truncate">Bancarios</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right chevron-icon d-none d-md-inline text-xs"></i>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════
         SECCIÓN 1: VISTA GENERAL
    ═══════════════════════════════════ --}}
    <div id="panel-general" class="settings-panel">
        <form action="{{ route('business.company.update') }}" method="POST" enctype="multipart/form-data" id="form-general">
            @csrf
            <input type="hidden" name="section" value="general">

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-primary text-white p-3 rounded-top-4 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shop me-2"></i>Vista general</h5>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                        <i class="bi bi-palette me-2 text-primary"></i>Identidad Visual
                    </h6>

                {{-- Branding --}}
                <div class="position-relative mb-5">
                    {{-- Banner --}}
                    <div class="rounded-3 overflow-hidden bg-light position-relative" style="height: 180px; border: 2px dashed #dee2e6;">
                        @if($company->banner)
                            <img src="{{ asset('storage/'.$company->banner) }}" class="w-100 h-100" style="object-fit: cover;" id="bannerPreview">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted" id="bannerPlaceholder">
                                <div class="text-center">
                                    <i class="bi bi-image fs-1 d-block mb-1"></i>
                                    <span class="small">Banner de la empresa (Recomendado: 1200x400 &bull; Todo tipo de imágenes)</span>
                                </div>
                            </div>
                        @endif

                        {{-- Boton cambiar banner superpuesto --}}
                        <div class="position-absolute bottom-0 end-0 p-2 d-flex flex-column align-items-end gap-1">
                            <label for="bannerInput" class="btn btn-dark btn-sm rounded-pill px-3 shadow mb-0 cursor-pointer">
                                <i class="bi bi-camera me-1"></i> Cambiar Banner
                            </label>
                            <input type="file" name="banner" id="bannerInput" class="d-none" accept="image/*,.svg,.avif,.webp,.png,.jpg,.jpeg,.gif,.bmp,.heic">
                            <div id="bannerFileInfo" class="d-none">
                                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1" style="font-size:0.7rem;">
                                    <i class="bi bi-file-image me-1"></i>
                                    <span id="bannerFileType"></span> &bull; <span id="bannerFileSize"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="position-absolute" style="bottom: -40px; left: 30px;">
                        <div class="vs-card p-1 rounded-circle bg-white shadow-lg" style="width: 100px; height: 100px;">
                            <div class="w-100 h-100 rounded-circle bg-light overflow-hidden d-flex align-items-center justify-content-center">
                                @if($company->logo)
                                    <img src="{{ asset('storage/'.$company->logo) }}" class="w-100 h-100" style="object-fit: cover;" id="logoPreview">
                                @else
                                    <i class="bi bi-building fs-1 text-muted" id="logoPlaceholder"></i>
                                @endif
                            </div>
                            <label for="logoInput" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center cursor-pointer shadow" style="width: 32px; height: 32px; border: 2px solid white;">
                                <i class="bi bi-camera-fill text-xs"></i>
                            </label>
                            <input type="file" name="logo" id="logoInput" class="d-none" accept="image/*,.svg,.avif,.webp,.png,.jpg,.jpeg,.gif,.bmp,.heic">
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center mb-2" style="height: 24px;">
                            <label class="form-label text-sm fw-semibold text-dark mb-0">Nombre público de la empresa *</label>
                        </div>
                        <input type="text" name="name" class="form-control form-control-lg"
                               value="{{ old('name', $company->name) }}" required
                               placeholder="Ej: Mi Negocio Digital">
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex align-items-center justify-content-between mb-2" style="height: 24px;">
                            <label class="form-label text-sm fw-semibold text-dark mb-0">Categoría Principal *</label>
                            <button type="button" class="btn btn-link p-0 text-decoration-none text-xs fw-semibold text-primary d-inline-flex align-items-center gap-1" data-bs-toggle="collapse" data-bs-target="#newCompanyCategoryCollapse">
                                <i class="bi bi-plus-circle"></i>
                                <span>Crear nueva</span>
                            </button>
                        </div>
                        <select name="category" id="company_category_select" class="form-select form-select-lg">
                            <option value="">Seleccionar categoría...</option>
                            @php
                                $currentCat = old('category', $company->category);
                                $tagLabels = isset($tags) ? $tags->pluck('label')->unique()->toArray() : [];
                                $defaultCats = ['Tecnología','Moda','Alimentos','Belleza','Hogar','Deportes','Arte','Educación','Salud','Panadería','Comida y bebida','Otros'];
                                $allCatOptions = array_unique(array_merge($defaultCats, $tagLabels, $currentCat ? [$currentCat] : []));
                                sort($allCatOptions);
                            @endphp
                            @foreach($allCatOptions as $cat)
                                <option value="{{ $cat }}" {{ $currentCat === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Formulario para crear nueva categoría directamente desde la empresa --}}
                    <div class="col-12 collapse mt-2" id="newCompanyCategoryCollapse">
                        <div class="p-3.5 bg-light rounded-4 border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-dark text-xs mb-0 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-magic text-primary"></i> Crear Categoría para tu Empresa y Productos
                                </h6>
                                <span class="badge bg-white text-primary border rounded-pill px-2 py-0.5 text-2xs fw-bold">CATÁLOGO EMPRESARIAL</span>
                            </div>
                            <p class="text-xs text-muted mb-3">
                                Esta categoría quedará disponible de inmediato tanto como rubro de tu empresa como para categorizar cualquiera de tus productos con animación temática.
                            </p>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-6">
                                    <label class="text-2xs fw-semibold text-dark mb-1">Nombre de la categoría</label>
                                    <input type="text" id="company_quick_tag_label" name="new_category_label" class="form-control form-control-sm" placeholder="Ej: Ropa Deportiva, Postres Artesanales">
                                </div>
                                <div class="col-sm-3">
                                    <label class="text-2xs fw-semibold text-dark mb-1">Color temático</label>
                                    <input type="color" id="company_quick_tag_color" name="new_category_color" class="form-control form-control-sm form-control-color w-100" value="#7F1D3F" title="Color temático para la vitrina">
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" id="btn_save_company_tag" class="btn btn-sm btn-primary w-100 fw-semibold text-xs py-1.5">
                                        <i class="bi bi-check-lg me-1"></i> Guardar y Usar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Listado de Categorías Creadas por esta Empresa --}}
                    @if(isset($myCustomTags) && $myCustomTags->count() > 0)
                    <div class="col-12 mt-2 pt-2 border-top" id="customTagsContainerBlock">
                        <label class="form-label text-2xs fw-bold text-muted text-uppercase mb-2 d-flex align-items-center gap-1">
                            <i class="bi bi-bookmark-star-fill text-primary"></i> Categorías Creadas por tu Empresa
                        </label>
                        <div class="d-flex flex-wrap gap-2" id="companyCustomTagsBadges">
                            @foreach($myCustomTags as $customTag)
                                <span class="badge rounded-pill px-3 py-1.5 fw-semibold text-xs d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                      style="background-color: #FFFFFF; border: 1.5px solid {{ $customTag->accent_color }}; color: #29252A;">
                                    <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: {{ $customTag->accent_color }};"></span>
                                    <span>{{ $customTag->label }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <label class="form-label text-sm fw-semibold text-dark">Descripción de la empresa</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Cuéntanos un poco sobre lo que hace tu empresa...">{{ old('description', $company->description) }}</textarea>
                    </div>

                    {{-- ══════════════════════════════════════════════════
                         FOTOGRAFÍAS DEL ESTABLECIMIENTO / LOCAL COMERCIAL
                    ══════════════════════════════════════════════════ --}}
                    <div class="col-12 mt-3 pt-3 border-top">
                        <div class="d-flex flex-column flex-sm-row sm-align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <label class="form-label text-sm fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-images text-primary"></i> Fotografías del Establecimiento
                                </label>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    Sube fotos de la fachada, vitrina o interior de tu local comercial para que los compradores conozcan tu espacio físico.
                                </small>
                            </div>
                            <div>
                                <label for="establishmentPhotosInput" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 text-xs fw-semibold mb-0 cursor-pointer d-inline-flex align-items-center gap-1 shadow-2xs">
                                    <i class="bi bi-cloud-plus-fill fs-6"></i> + Subir fotos del local
                                </label>
                                <input type="file" name="establishment_photos[]" id="establishmentPhotosInput" class="d-none" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            </div>
                        </div>

                        {{-- Galería de Fotos del Establecimiento --}}
                        <div class="row g-2" id="establishmentGalleryRow">
                            @if(!empty($company->establishment_photo_urls))
                                @foreach($company->establishment_photos as $index => $photoPath)
                                    <div class="col-6 col-sm-4 col-md-3" id="est_photo_col_{{ $index }}">
                                        <div class="position-relative rounded-3 overflow-hidden border shadow-2xs" style="height: 110px; background: #FAF7F8;">
                                            <img src="{{ asset('storage/' . $photoPath) }}" class="w-100 h-100" style="object-fit: cover;" alt="Establecimiento">
                                            <label class="position-absolute top-0 end-0 m-1.5 badge bg-danger rounded-circle p-1 cursor-pointer shadow-xs" title="Marcar para eliminar">
                                                <input type="checkbox" name="delete_establishment_photos[]" value="{{ $photoPath }}" class="d-none" onchange="document.getElementById('est_photo_col_{{ $index }}').style.opacity = this.checked ? '0.3' : '1'; document.getElementById('est_photo_col_{{ $index }}').style.filter = this.checked ? 'grayscale(1)' : 'none';">
                                                <i class="bi bi-trash-fill text-white" style="font-size: 0.75rem;"></i>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12" id="emptyEstablishmentNotice">
                                    <div class="p-3.5 rounded-4 border bg-light text-center text-muted">
                                        <i class="bi bi-shop fs-3 d-block text-secondary mb-1"></i>
                                        <span class="text-xs fw-semibold text-dark d-block mb-0.5">Aún no has agregado fotos del establecimiento</span>
                                        <span class="text-2xs text-muted">Haz clic en <strong>"+ Subir fotos del local"</strong> para exhibir tu negocio local en Ubaté.</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div id="newEstablishmentPreviews" class="row g-2 pt-2"></div>
                    </div>
                </div>
            </div>
            {{-- Pie del card con botón guardar --}}
            <div class="section-save-bar">
                <button type="submit" class="btn btn-primary px-4 fw-semibold rounded-pill">
                    <i class="bi bi-floppy me-1"></i> Guardar Vista General
                </button>
            </div>
        </div>
    </form>
    </div>

    {{-- ═══════════════════════════════════
         SECCIÓN 2: ASPECTO LEGAL
    ═══════════════════════════════════ --}}
    <div id="panel-legal" class="settings-panel" style="display: none;">
    <form action="{{ route('business.company.update') }}" method="POST" enctype="multipart/form-data" id="form-legal">
        @csrf
        <input type="hidden" name="section" value="legal">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-success text-white p-3 rounded-top-4 border-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i>Aspecto legal</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-0 bg-opacity-10 text-primary mb-4 d-flex align-items-center">
                    <i class="bi bi-shield-lock-fill fs-3 me-3"></i>
                    <div>
                        <strong>Información Privada</strong><br>
                        <small>Estos datos son confidenciales y solo se utilizan para facturación y validación interna.</small>
                    </div>
                </div>
                @include('business.partials.register-form')
            </div>

            <div class="section-save-bar">
                <button type="submit" class="btn btn-success px-4 fw-semibold rounded-pill">
                    <i class="bi bi-floppy me-1"></i> Guardar Aspecto Legal
                </button>
            </div>
        </div>
    </form>
    </div>

    {{-- ═══════════════════════════════════
         SECCIÓN 3: REDES Y CONTACTO
    ═══════════════════════════════════ --}}
    <div id="panel-contact" class="settings-panel" style="display: none;">
    <form action="{{ route('business.company.update') }}" method="POST" enctype="multipart/form-data" id="form-contact">
        @csrf
        <input type="hidden" name="section" value="contact">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-secondary text-white p-3 rounded-top-4 border-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-globe me-2"></i>Redes y Contacto</h5>
            </div>
            <div class="card-body p-4">

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Información de contacto directo
                </h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-semibold text-dark">Teléfono (WhatsApp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-success"></i></span>
                            <input type="text" name="phone" class="form-control border-start-0"
                                   value="{{ old('phone', $company->phone) }}" placeholder="300 123 4567">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-semibold text-dark">Correo empresarial</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                            <input type="email" name="email" class="form-control border-start-0"
                                   value="{{ old('email', $company->email) }}" placeholder="contacto@empresa.com">
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                    <i class="bi bi-share-fill me-2 text-primary"></i>Redes sociales y Sitio Web
                </h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold text-dark">Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-instagram" style="color:#E1306C;"></i></span>
                            <input type="text" name="instagram" class="form-control border-start-0"
                                   value="{{ old('instagram', $company->instagram) }}" placeholder="@usuario">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold text-dark">Facebook</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-facebook" style="color:#1877F2;"></i></span>
                            <input type="text" name="facebook" class="form-control border-start-0"
                                   value="{{ old('facebook', $company->facebook) }}" placeholder="facebook.com/usuario">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold text-dark">Sitio web</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-globe text-secondary"></i></span>
                            <input type="url" name="website" class="form-control border-start-0"
                                   value="{{ old('website', $company->website) }}" placeholder="https://miweb.com">
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-clock-history me-2 text-primary"></i>Horario de Atención</span>
                    <span class="text-xs text-muted fw-normal">Configura tus días y jornadas de servicio</span>
                </h6>

                {{-- 1. Días de Atención al Público --}}
                <div class="mb-4 pb-2">
                    <div class="d-flex flex-column flex-sm-row sm-align-items-center justify-content-between gap-1 mb-2">
                        <label class="form-label text-sm fw-bold text-dark mb-0 d-flex align-items-center gap-1">
                            <i class="bi bi-calendar3 text-primary"></i> Días de Atención
                        </label>
                        <span class="text-xs text-muted">Selecciona los días en que tu negocio abre sus puertas</span>
                    </div>
                    @php
                        $selectedDays = $company->operating_days ?? ['monday','tuesday','wednesday','thursday','friday','saturday'];
                    @endphp
                    <div class="d-flex flex-wrap gap-2 pt-1">
                        @foreach([
                            'monday'    => 'Lunes',
                            'tuesday'   => 'Martes',
                            'wednesday' => 'Miércoles',
                            'thursday'  => 'Jueves',
                            'friday'    => 'Viernes',
                            'saturday'  => 'Sábado',
                            'sunday'    => 'Domingo'
                        ] as $dayKey => $dayLabel)
                            <div class="day-pill-check">
                                <input class="btn-check" type="checkbox" name="operating_days[]" id="day_{{ $dayKey }}" value="{{ $dayKey }}" {{ in_array($dayKey, $selectedDays) ? 'checked' : '' }}>
                                <label for="day_{{ $dayKey }}">
                                    <i class="bi bi-check-circle-fill d-none day-check-icon"></i>
                                    {{ $dayLabel }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Tarjetas de Horario (Entre Semana y Fines de Semana) en 2 Columnas Simétricas --}}
                <div class="row g-3 mb-4">
                    {{-- Tarjeta 1: Horario Entre Semana --}}
                    <div class="col-md-6">
                        <div class="schedule-card-box h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="schedule-card-icon schedule-icon-weekday">
                                            <i class="bi bi-briefcase-fill"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark text-sm">Lunes a Viernes</h6>
                                            <small class="text-muted text-xs">Jornada habitual entre semana</small>
                                        </div>
                                    </div>
                                    <span class="badge badge-schedule-standard rounded-pill px-2.5 py-1">ESTÁNDAR</span>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="schedule-time-label">Apertura</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-success"><i class="bi bi-door-open"></i></span>
                                            <input type="time" name="opening_time" class="form-control border-start-0 text-sm fw-semibold text-dark"
                                                   value="{{ old('opening_time', $company->opening_time ? substr($company->opening_time, 0, 5) : '08:00') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="schedule-time-label">Cierre</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-danger"><i class="bi bi-door-closed"></i></span>
                                            <input type="time" name="closing_time" class="form-control border-start-0 text-sm fw-semibold text-dark"
                                                   value="{{ old('closing_time', $company->closing_time ? substr($company->closing_time, 0, 5) : '19:00') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 text-xs text-muted border-top d-flex align-items-center gap-1.5">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Horario base de lunes a viernes</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 2: Horario Fines de Semana --}}
                    <div class="col-md-6">
                        <div class="schedule-card-box h-100 d-flex flex-column justify-content-between" id="weekendScheduleCard">
                            <div>
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="schedule-card-icon schedule-icon-weekend">
                                            <i class="bi bi-sun-fill"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark text-sm">Fines de Semana</h6>
                                            <small class="text-muted text-xs">Sábados y Domingos</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-0" title="Activar horario especial">
                                        <input class="form-check-input cursor-pointer" type="checkbox" name="has_weekend_schedule" id="has_weekend_schedule" value="1"
                                               {{ old('has_weekend_schedule', $company->has_weekend_schedule) ? 'checked' : '' }}
                                               onchange="toggleWeekendSchedule(this.checked)">
                                    </div>
                                </div>

                                {{-- Campos para Horario de Fines de Semana --}}
                                <div id="weekendScheduleFields" class="{{ old('has_weekend_schedule', $company->has_weekend_schedule) ? '' : 'd-none' }}">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="schedule-time-label">Apertura</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-success"><i class="bi bi-door-open"></i></span>
                                                <input type="time" name="weekend_opening_time" id="weekend_opening_time" class="form-control border-start-0 text-sm fw-semibold text-dark"
                                                       value="{{ old('weekend_opening_time', $company->weekend_opening_time ? substr($company->weekend_opening_time, 0, 5) : '09:00') }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="schedule-time-label">Cierre</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-danger"><i class="bi bi-door-closed"></i></span>
                                                <input type="time" name="weekend_closing_time" id="weekend_closing_time" class="form-control border-start-0 text-sm fw-semibold text-dark"
                                                       value="{{ old('weekend_closing_time', $company->weekend_closing_time ? substr($company->weekend_closing_time, 0, 5) : '15:00') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mensaje de Ayuda cuando está inactivo --}}
                                <div id="weekendScheduleNotice" class="py-2 text-center {{ old('has_weekend_schedule', $company->has_weekend_schedule) ? 'd-none' : '' }}">
                                    <p class="text-xs text-muted mb-0 fst-italic">
                                        <i class="bi bi-arrow-repeat me-1 text-secondary"></i> Mismo horario de entre semana. Activa el interruptor para personalizar sábados y domingos.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 text-xs text-muted border-top d-flex align-items-center justify-content-between">
                                <span id="weekendStatusBadge" class="d-flex align-items-center gap-1">
                                    @if(old('has_weekend_schedule', $company->has_weekend_schedule))
                                        <span class="badge badge-schedule-active rounded-pill px-2.5 py-1">Horario especial activo</span>
                                    @else
                                        <span class="badge badge-schedule-inactive rounded-pill px-2.5 py-1">Mismo horario estándar</span>
                                    @endif
                                </span>
                                <small class="text-muted">Personalizable</small>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Ubicación Física del Establecimiento</span>
                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1 text-xs fw-semibold">
                        <i class="bi bi-pin-map-fill me-1"></i> Precisión Geográfica
                    </span>
                </h6>

                {{-- Selector de Departamento y Municipio/Ciudad --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-map-fill text-primary"></i> Departamento / Estado <span class="text-danger">*</span>
                        </label>
                        <select name="state" id="company_state_select" class="form-select form-select-sm fw-semibold">
                            {{-- Poblado dinámicamente con colombia-geo.js --}}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-buildings-fill text-primary"></i> Ciudad / Municipio <span class="text-danger">*</span>
                        </label>
                        <select name="city" id="company_city_select" class="form-select form-select-sm fw-semibold">
                            {{-- Poblado dinámicamente según departamento --}}
                        </select>
                    </div>
                </div>

                {{-- Buscador de Dirección Acotado --}}
                <div class="mb-3 p-3 rounded-4 bg-light border">
                    <label class="form-label text-xs fw-bold text-dark mb-1.5 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-signpost-split-fill text-primary me-1"></i> Dirección o Calle en esta Ciudad</span>
                        <span class="text-2xs text-muted fw-normal">Ej. Carrera 7 # 8-20, Calle 10 con 5</span>
                    </label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="address" id="company_address_search" class="form-control form-control-sm border-start-0"
                               value="{{ old('address', $company->address) }}" placeholder="Escribe la dirección física aquí..." autocomplete="off">
                        <button type="button" class="btn btn-sm btn-primary px-3 fw-semibold shadow-2xs" id="btn_search_address_map">
                            <i class="bi bi-search me-1"></i> Ubicar en el mapa
                        </button>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <small class="text-muted text-2xs">
                            <i class="bi bi-info-circle me-1 text-primary"></i>La búsqueda se acota exclusivamente al municipio y departamento seleccionado.
                        </small>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-semibold text-2xs" id="btn_center_current_location">
                            <i class="bi bi-cursor-fill me-1"></i> Centrar con mi GPS actual
                        </button>
                    </div>
                </div>

                {{-- Mapa Leaflet Interactivo --}}
                <div id="map" class="rounded-4 border mb-3 shadow-sm" style="height: 330px;"></div>

                {{-- Coordenadas de Alta Precisión --}}
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="text-2xs text-muted fw-semibold">Latitud exacta</label>
                        <input type="text" name="latitude" id="lat" class="form-control form-control-sm bg-light text-muted"
                               value="{{ old('latitude', $company->latitude) }}" readonly>
                    </div>
                    <div class="col-6">
                        <label class="text-2xs text-muted fw-semibold">Longitud exacta</label>
                        <input type="text" name="longitude" id="lng" class="form-control form-control-sm bg-light text-muted"
                               value="{{ old('longitude', $company->longitude) }}" readonly>
                    </div>
                </div>

                {{-- Dirección Confirmada en Mapa --}}
                <div class="mt-2 p-3 rounded-4 border" style="background: #FFFDF9; border-color: #F1D4DC !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="text-xs fw-bold text-dark mb-0 d-flex align-items-center gap-1">
                            <i class="bi bi-patch-check-fill text-success"></i> Ubicación detectada en el mapa:
                        </label>
                        <span class="badge bg-light text-primary border rounded-pill px-2.5 py-0.5 text-2xs fw-semibold">
                            Arrastra el marcador para afinar
                        </span>
                    </div>
                    <div id="address-display" class="text-xs text-dark fw-medium">
                        {{ $company->address ?: 'Haz clic o arrastra el pin en el mapa para marcar la puerta exacta de tu negocio.' }}
                    </div>
                </div>

            </div>

            <div class="section-save-bar">
                <button type="submit" class="btn btn-secondary px-4 fw-semibold rounded-pill">
                    <i class="bi bi-floppy me-1"></i> Guardar Redes y Contacto
                </button>
            </div>
        </div>
    </form>
    </div>

    {{-- ═══════════════════════════════════
         SECCIÓN 4: DATOS BANCARIOS Y DISPERSIÓN DE PAGOS (WOMPI)
    ═══════════════════════════════════ --}}
    <div id="panel-banking" class="settings-panel" style="display: none;">
    <form action="{{ route('business.company.update') }}" method="POST" id="form-banking">
        @csrf
        <input type="hidden" name="section" value="banking">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-dark text-white p-3 rounded-top-4 border-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold"><i class="bi bi-bank me-2 text-warning"></i>Datos Bancarios y Recaudación (Wompi)</h5>
                <span class="badge rounded-pill fw-semibold" style="background: rgba(255,255,255,0.2); font-size: 0.75rem;">Dispersión Automática</span>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-0 rounded-3 small mb-4">
                    <i class="bi bi-info-circle me-1"></i> Estos datos permiten a la plataforma Spotlight transferir y liquidar de forma exacta el dinero correspondiente a las ventas y despachos de tu comercio procesados en Wompi.
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark small">Entidad Financiera / Banco</label>
                        <select name="bank_name" class="form-select rounded-3">
                            <option value="">-- Seleccionar Banco --</option>
                            <option value="Bancolombia" {{ $company->bank_name === 'Bancolombia' ? 'selected' : '' }}>Bancolombia</option>
                            <option value="Davivienda" {{ $company->bank_name === 'Davivienda' ? 'selected' : '' }}>Davivienda</option>
                            <option value="Banco de Bogotá" {{ $company->bank_name === 'Banco de Bogotá' ? 'selected' : '' }}>Banco de Bogotá</option>
                            <option value="BBVA Colombia" {{ $company->bank_name === 'BBVA Colombia' ? 'selected' : '' }}>BBVA Colombia</option>
                            <option value="Banco de Occidente" {{ $company->bank_name === 'Banco de Occidente' ? 'selected' : '' }}>Banco de Occidente</option>
                            <option value="Nequi" {{ $company->bank_name === 'Nequi' ? 'selected' : '' }}>Nequi</option>
                            <option value="Daviplata" {{ $company->bank_name === 'Daviplata' ? 'selected' : '' }}>Daviplata</option>
                            <option value="Banco Agrario" {{ $company->bank_name === 'Banco Agrario' ? 'selected' : '' }}>Banco Agrario de Colombia</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark small">Tipo de Cuenta</label>
                        <select name="account_type" class="form-select rounded-3">
                            <option value="ahorros" {{ $company->account_type === 'ahorros' ? 'selected' : '' }}>Cuenta de Ahorros</option>
                            <option value="corriente" {{ $company->account_type === 'corriente' ? 'selected' : '' }}>Cuenta Corriente</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark small">Número de Cuenta Bancaria</label>
                        <input type="text" name="account_number" class="form-control rounded-3" value="{{ old('account_number', $company->account_number) }}" placeholder="Ej. 123-456789-00">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark small">Nombre o Razón Social del Titular</label>
                        <input type="text" name="account_holder_name" class="form-control rounded-3" value="{{ old('account_holder_name', $company->account_holder_name) }}" placeholder="Nombre tal como figura en el certificado">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark small">Tipo Doc. Titular</label>
                        <select name="account_holder_doc_type" class="form-select rounded-3">
                            <option value="NIT" {{ $company->account_holder_doc_type === 'NIT' ? 'selected' : '' }}>NIT</option>
                            <option value="CC" {{ $company->account_holder_doc_type === 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía (CC)</option>
                            <option value="CE" {{ $company->account_holder_doc_type === 'CE' ? 'selected' : '' }}>Cédula de Extranjería (CE)</option>
                            <option value="PP" {{ $company->account_holder_doc_type === 'PP' ? 'selected' : '' }}>Pasaporte</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold text-dark small">Número de Documento del Titular</label>
                        <input type="text" name="account_holder_doc_number" class="form-control rounded-3" value="{{ old('account_holder_doc_number', $company->account_holder_doc_number) }}" placeholder="Ej. 901234567-8">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark small">ID de Comercio / Subcuenta Wompi (Opcional)</label>
                        <input type="text" name="wompi_merchant_id" class="form-control rounded-3 font-monospace small" value="{{ old('wompi_merchant_id', $company->wompi_merchant_id) }}" placeholder="sub_test_... o ID Wompi propio">
                        <div class="form-text text-muted small">Aplica si operas bajo esquema de subcuentas independientes en Wompi.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark small">ID Regla de Dispersión / Split Rule</label>
                        <input type="text" name="split_rule_id" class="form-control rounded-3 font-monospace small" value="{{ old('split_rule_id', $company->split_rule_id) }}" placeholder="rule_...">
                        <div class="form-text text-muted small">Regla configurada en Wompi para dispersión automática de comisiones.</div>
                    </div>
                </div>
            </div>

            <div class="section-save-bar">
                <button type="submit" class="btn btn-primary px-4 fw-semibold rounded-pill">
                    <i class="bi bi-floppy me-1"></i> Guardar datos
                </button>
            </div>
        </div>
    </form>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="{{ asset('js/colombia-geo.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Control de Pestañas / Paneles Desplegables ─────────────
    let globalLeafletMap = null;
    let globalLeafletRegMap = null;

    const navButtons = document.querySelectorAll('.settings-nav-btn');
    const panels = document.querySelectorAll('.settings-panel');

    function switchSection(targetId) {
        panels.forEach(p => p.style.display = 'none');
        navButtons.forEach(b => b.classList.remove('active'));

        const targetPanel = document.getElementById(targetId);
        const targetBtn   = document.querySelector(`.settings-nav-btn[data-target="${targetId}"]`);

        if (targetPanel) {
            targetPanel.style.display = 'block';
        }
        if (targetBtn) {
            targetBtn.classList.add('active');
        }

        // Si se abre contacto o legal, invalidar mapas para que rendericen adecuadamente
        if (targetId === 'panel-contact' && globalLeafletMap) {
            setTimeout(() => { globalLeafletMap.invalidateSize(); }, 200);
        }
        if (targetId === 'panel-legal' && typeof window.invalidateRegisterMap === 'function') {
            setTimeout(() => { window.invalidateRegisterMap(); }, 200);
        }

        // Actualizar URL hash de forma limpia sin salto brusco
        if (history.replaceState) {
            history.replaceState(null, null, '#' + targetId.replace('panel-', ''));
        }
    }

    navButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            switchSection(targetId);
        });
    });

    // Detectar sección activa por sesión de Laravel tras guardar o hash en la URL
    const sessionSection = "{{ session('active_section') }}";
    const initialHash = window.location.hash.replace('#', '');
    const activeTarget = sessionSection || initialHash;

    if (activeTarget && ['general', 'legal', 'contact', 'banking'].includes(activeTarget)) {
        switchSection('panel-' + activeTarget);
    }

    // ─── Inicialización de Ubicación Precisa y Mapa Leaflet ────────
    try {
        const locPicker = window.initSpotlightPreciseLocation({
            country: "{{ $company->country ?: 'CO' }}",
            stateSelectId: 'company_state_select',
            citySelectId: 'company_city_select',
            addressInputId: 'company_address_search',
            latInputId: 'lat',
            lngInputId: 'lng',
            searchBtnId: 'btn_search_address_map',
            gpsBtnId: 'btn_center_current_location',
            statusDisplayId: 'address-display',
            mapId: 'map',
            initialState: "{{ old('state', $company->state ?: ($company->isMexican() ? 'Puebla' : 'Cundinamarca')) }}",
            initialCity: "{{ old('city', $company->city ?: ($company->isMexican() ? 'Puebla' : 'Ubaté')) }}",
            initialLat: "{{ old('latitude', $company->latitude ?: ($company->isMexican() ? 19.0414 : 5.3086)) }}",
            initialLng: "{{ old('longitude', $company->longitude ?: ($company->isMexican() ? -98.2063 : -73.8149)) }}"
        });
        globalLeafletMap = locPicker ? locPicker.map : null;
    } catch (err) {
        console.error('Error al inicializar mapa:', err);
        const mapBox = document.getElementById('map');
        if (mapBox) {
            mapBox.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><p>No se pudo cargar el mapa. Verifica tu conexión a internet.</p></div>';
        }
    }

    // ─── Spinner en botones al enviar ──────────────────────
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Guardando...';
            }
        });
    });

    // ─── Control horario fines de semana ───────────────────
    window.toggleWeekendSchedule = function (isChecked) {
        const fields = document.getElementById('weekendScheduleFields');
        const notice = document.getElementById('weekendScheduleNotice');
        const badge  = document.getElementById('weekendStatusBadge');

        if (fields) {
            if (isChecked) {
                fields.classList.remove('d-none');
            } else {
                fields.classList.add('d-none');
            }
        }
        if (notice) {
            if (isChecked) {
                notice.classList.add('d-none');
            } else {
                notice.classList.remove('d-none');
            }
        }
        if (badge) {
            if (isChecked) {
                badge.innerHTML = '<span class="badge badge-schedule-active rounded-pill px-2.5 py-1">Horario especial activo</span>';
            } else {
                badge.innerHTML = '<span class="badge badge-schedule-inactive rounded-pill px-2.5 py-1">Mismo horario estándar</span>';
            }
        }
    };

    // ─── Preview de imágenes ───────────────────────────────
    function previewImage(input, previewId, placeholderId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                let preview     = document.getElementById(previewId);
                let placeholder = document.getElementById(placeholderId);
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                } else if (placeholder) {
                    const img = document.createElement('img');
                    img.id = previewId;
                    img.src = e.target.result;
                    img.className = 'w-100 h-100';
                    img.style.objectFit = 'cover';
                    placeholder.replaceWith(img);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ─── Optimizador de imágenes grandes para evitar límite de servidor ──
    async function processAndOptimizeImage(file, maxDimension = 2400) {
        if (!file || file.type.includes('svg') || file.type.includes('gif')) {
            return file;
        }
        if (file.size <= 1.8 * 1024 * 1024) {
            return file;
        }

        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => {
                let width = img.width;
                let height = img.height;

                if (width > maxDimension || height > maxDimension) {
                    if (width > height) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                const mimeType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
                canvas.toBlob((blob) => {
                    if (!blob || blob.size >= file.size) {
                        resolve(file);
                        return;
                    }
                    const optimized = new File([blob], file.name.replace(/\.[^/.]+$/, "") + (mimeType === 'image/png' ? '.png' : '.jpg'), {
                        type: mimeType,
                        lastModified: Date.now()
                    });
                    resolve(optimized);
                }, mimeType, 0.92);
            };
            img.onerror = () => resolve(file);
            img.src = URL.createObjectURL(file);
        });
    }

    document.getElementById('logoInput')?.addEventListener('change', async function () {
        let file = this.files[0];
        if (!file) return;

        if (file.size > 1.8 * 1024 * 1024 && !file.type.includes('svg') && !file.type.includes('gif')) {
            try {
                const optimized = await processAndOptimizeImage(file, 1200);
                const dt = new DataTransfer();
                dt.items.add(optimized);
                this.files = dt.files;
            } catch (err) {
                console.warn(err);
            }
        }
        previewImage(this, 'logoPreview', 'logoPlaceholder');
    });

    document.getElementById('bannerInput')?.addEventListener('change', async function () {
        let file = this.files[0];
        if (!file) return;

        // Si la imagen es pesada (> 1.8 MB), se optimiza suavemente en el navegador para garantizar que suba sin error de PHP
        if (file.size > 1.8 * 1024 * 1024 && !file.type.includes('svg') && !file.type.includes('gif')) {
            try {
                const optimized = await processAndOptimizeImage(file, 2400);
                const dt = new DataTransfer();
                dt.items.add(optimized);
                this.files = dt.files;
                file = optimized;
            } catch (err) {
                console.warn(err);
            }
        }

        const maxBytes = 10 * 1024 * 1024; // 10 MB
        if (file.size > maxBytes) {
            alert('El banner supera el límite máximo de 10 MB. Por favor selecciona una imagen más liviana.');
            this.value = '';
            document.getElementById('bannerFileInfo')?.classList.add('d-none');
            return;
        }

        // Mostrar tipo y tamaño
        const typeLabel = file.type ? file.type.replace('image/', '').toUpperCase() : file.name.split('.').pop().toUpperCase();
        const sizeMB    = (file.size / (1024 * 1024)).toFixed(2);
        const typeEl = document.getElementById('bannerFileType');
        const sizeEl = document.getElementById('bannerFileSize');
        const infoEl = document.getElementById('bannerFileInfo');
        if (typeEl) typeEl.textContent = typeLabel;
        if (sizeEl) sizeEl.textContent = sizeMB + ' MB';
        if (infoEl) infoEl.classList.remove('d-none');

        // Preview
        previewImage(this, 'bannerPreview', 'bannerPlaceholder');
    });

    // ─── Creación de Categoría rápida para la Empresa ───────
    document.getElementById('btn_save_company_tag')?.addEventListener('click', async function () {
        const labelInput = document.getElementById('company_quick_tag_label');
        const colorInput = document.getElementById('company_quick_tag_color');
        const label = labelInput?.value.trim();
        const accent = colorInput?.value || '#7F1D3F';

        if (!label) {
            alert('Por favor escribe el nombre de la categoría.');
            labelInput?.focus();
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        try {
            const response = await fetch('{{ route('business.products.tags.quick_create') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ label, accent })
            });

            const data = await response.json();
            if (data.success && data.tag) {
                // 1. Agregar y seleccionar la opción en el select principal
                const select = document.getElementById('company_category_select');
                if (select) {
                    let opt = Array.from(select.options).find(o => o.value === data.tag.label);
                    if (!opt) {
                        opt = new Option(data.tag.label, data.tag.label, true, true);
                        select.add(opt);
                    }
                    select.value = data.tag.label;
                }

                // 2. Agregar la insignia visual si existe el contenedor
                const badgesContainer = document.getElementById('companyCustomTagsBadges');
                if (badgesContainer) {
                    const badge = document.createElement('span');
                    badge.className = 'badge rounded-pill px-3 py-1.5 fw-semibold text-xs d-inline-flex align-items-center gap-1.5 shadow-2xs';
                    badge.style.backgroundColor = '#FFFFFF';
                    badge.style.border = `1.5px solid ${data.tag.accent}`;
                    badge.style.color = '#29252A';
                    badge.innerHTML = `<span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: ${data.tag.accent};"></span><span>${data.tag.label}</span>`;
                    badgesContainer.prepend(badge);
                }

                // 3. Limpiar y cerrar collapse
                labelInput.value = '';
                const collapseElem = document.getElementById('newCompanyCategoryCollapse');
                if (collapseElem && window.bootstrap) {
                    const bsCollapse = bootstrap.Collapse.getInstance(collapseElem) || new bootstrap.Collapse(collapseElem, { toggle: false });
                    bsCollapse.hide();
                }
            } else {
                alert(data.message || 'No se pudo crear la categoría.');
            }
        } catch (e) {
            console.error(e);
            alert('Ocurrió un error al crear la categoría.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // ── Previsualización interactiva de fotos del establecimiento ──
    const estInput = document.getElementById('establishmentPhotosInput');
    const estPreviews = document.getElementById('newEstablishmentPreviews');
    const emptyNotice = document.getElementById('emptyEstablishmentNotice');

    estInput?.addEventListener('change', function() {
        if (!estPreviews) return;
        estPreviews.innerHTML = '';
        if (this.files && this.files.length > 0) {
            if (emptyNotice) emptyNotice.style.display = 'none';

            Array.from(this.files).forEach((file, index) => {
                if (file.size > 3 * 1024 * 1024) {
                    alert(`El archivo "${file.name}" supera el límite de 3 MB.`);
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3';
                    col.innerHTML = `
                        <div class="position-relative rounded-3 overflow-hidden border shadow-2xs" style="height: 110px; background: #FAF7F8;">
                            <img src="${e.target.result}" class="w-100 h-100" style="object-fit: cover;" alt="Nuevo local">
                            <span class="position-absolute bottom-0 start-0 end-0 p-1 text-center text-white text-2xs fw-bold" style="background: rgba(127, 29, 63, 0.85);">
                                <i class="bi bi-clock-history me-1"></i> Por guardar
                            </span>
                        </div>
                    `;
                    estPreviews.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }
    });
});
</script>
@endpush
