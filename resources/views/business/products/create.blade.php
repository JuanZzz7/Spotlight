@extends('layouts.dashboard')
@section('title','Nuevo Producto')
@section('page-title','Crear Producto')

@push('styles')
<link href="{{ asset('css/business.css') }}" rel="stylesheet">
<link href="{{ asset('css/user.css') }}" rel="stylesheet">
@endpush

@section('dashboard-content')
<div class="row g-4 justify-content-center" style="max-width: 1180px; margin: 0 auto;">

    {{-- ══════════════════════════════════════════════════════
         COLUMNA IZQUIERDA: FORMULARIO DE CREACIÓN (col-lg-7)
         ══════════════════════════════════════════════════════ --}}
    <div class="col-12 col-lg-7">
        <div class="vs-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam-fill text-primary"></i> Información del Producto
                        </h5>
                        <small class="text-muted">Diligencia los datos y observa la vista previa en tiempo real a la derecha.</small>
                    </div>
                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1 text-xs fw-semibold">
                        <i class="bi bi-shop me-1"></i> {{ $company->name ?: 'Mi Empresa' }}
                    </span>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 mb-4" style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('business.products.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
                    @csrf

                    {{-- 1. Nombre del Producto --}}
                    <div class="mb-3">
                        <label class="form-label text-sm fw-semibold text-dark">Nombre del producto *</label>
                        <input type="text" name="name" id="prod_name" class="form-control form-control-lg text-sm"
                               value="{{ old('name') }}" placeholder="Ej: Café Especial de Origen 500g" required>
                    </div>

                    {{-- 2. Descripción --}}
                    <div class="mb-3">
                        <label class="form-label text-sm fw-semibold text-dark">Descripción</label>
                        <textarea name="description" id="prod_description" class="form-control text-sm" rows="3"
                                  placeholder="Detalla los atributos, ingredientes, materiales o beneficios...">{{ old('description') }}</textarea>
                    </div>

                    {{-- 3. Precios y Stock --}}
                    @php
                        $companyCurrency = $company->isMexican() ? 'MXN' : 'COP';
                        $mxnToCopRate = \App\Models\Product::mxnToCopRate();
                    @endphp
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-semibold text-dark">Precio de venta ({{ $companyCurrency }}) *</label>
                            <input type="number" name="price" id="prod_price" class="form-control"
                                   value="{{ old('price') }}" min="0" step="0.01" required placeholder="{{ $company->isMexican() ? '250.00' : '50000.00' }}">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Precio visible al público en {{ $companyCurrency }} (permite decimales)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-semibold text-dark">Costo unitario ({{ $companyCurrency }})</label>
                            <input type="number" name="cost_price" id="prod_cost_price" class="form-control"
                                    value="{{ old('cost_price', 0) }}" min="0" step="0.01" placeholder="{{ $company->isMexican() ? '120.00' : '30000.00' }}">
                            <small class="text-muted" style="font-size: 0.7rem;">Costo de compra/producción (permite decimales)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-semibold text-dark">Stock disponible *</label>
                            <input type="number" name="stock" id="prod_stock" class="form-control"
                                   value="{{ old('stock', 10) }}" min="0" required placeholder="10">
                            <small class="text-muted" style="font-size: 0.7rem;">Unidades en inventario</small>
                        </div>
                    </div>

                    {{-- 4. Categorías / Etiquetas con Animación Temática --}}
                    <div class="mb-4 p-3 rounded-4 bg-light border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label text-sm fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                <i class="bi bi-tags-fill text-primary"></i> Categorías del Producto
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 text-xs fw-semibold" data-bs-toggle="collapse" data-bs-target="#newCategoryCollapse">
                                <i class="bi bi-plus-circle me-1"></i> Crear nueva categoría
                            </button>
                        </div>
                        <small class="text-muted d-block mb-3" style="font-size: 0.75rem;">
                            Elige una o más categorías para activar la temática de color y la animación al filtrar en la tienda.
                        </small>

                        {{-- Panel para crear categoría personalizada al vuelo --}}
                        <div class="collapse mb-3" id="newCategoryCollapse">
                            <div class="p-3 bg-white rounded-3 border shadow-xs">
                                <h6 class="fw-bold text-dark text-xs mb-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-magic text-primary"></i> Crear Categoría para tu Empresa
                                </h6>
                                <div class="row g-2">
                                    <div class="col-sm-7">
                                        <label class="text-2xs fw-semibold text-muted mb-1">Nombre de la categoría</label>
                                        <input type="text" id="quick_tag_label" class="form-control form-control-sm" placeholder="Ej: Pastelería Fina, Moda Infantil">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="text-2xs fw-semibold text-muted mb-1">Color temático</label>
                                        <input type="color" id="quick_tag_color" class="form-control form-control-sm form-control-color w-100" value="#7F1D3F" title="Color de animación y distintivo">
                                    </div>
                                    <div class="col-sm-2 d-flex align-items-end">
                                        <button type="button" id="btn_save_quick_tag" class="btn btn-sm btn-primary w-100 fw-semibold text-xs">
                                            Guardar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Píldoras de selección de categorías --}}
                        <div class="d-flex flex-wrap gap-2 pt-1" id="tagsPillsContainer" style="max-height: 190px; overflow-y: auto;">
                            @php
                                $assignedTagIds = old('tags', []);
                            @endphp
                            @foreach($tags as $tag)
                                <div class="tag-pill-check">
                                    <input class="btn-check tag-checkbox-item" type="checkbox" name="tags[]" id="tag_{{ $tag->id }}" value="{{ $tag->id }}"
                                           data-label="{{ $tag->label }}" data-accent="{{ $tag->accent_color }}" data-icon="{{ $tag->icon ?: 'bi-tag-fill' }}"
                                           {{ in_array($tag->id, $assignedTagIds) ? 'checked' : '' }}>
                                    <label for="tag_{{ $tag->id }}" class="btn-tag-pill" style="--tag-accent: {{ $tag->accent_color }};">
                                        <i class="bi {{ $tag->icon ?: 'bi-tag-fill' }} me-1" style="color: {{ $tag->accent_color }};"></i>
                                        <span>{{ $tag->label }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 5. Fotografía del Producto (Zona Amplia y Espaciosa) --}}
                    <div class="mb-4 p-4 rounded-4 bg-white border">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <label class="form-label text-sm fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-image-fill text-primary"></i> Fotografía del Producto
                                </label>
                                <small class="text-muted" style="font-size: 0.775rem;">Sube una imagen atractiva y nítida de tu artículo.</small>
                            </div>
                            <span class="badge bg-light text-primary border rounded-pill px-3 py-1 text-xs fw-semibold">
                                <i class="bi bi-file-earmark-image me-1"></i> Máx. 5 MB (JPG, PNG, WebP)
                            </span>
                        </div>

                        {{-- Zona de Carga Espaciosa --}}
                        <div class="spacious-upload-dropzone" id="dropzone_product_img">
                            <input type="file" name="image" id="prod_image_input" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp">

                            {{-- Estado Vacío / Dropzone --}}
                            <div id="dropzone_empty_state" class="py-3">
                                <div class="upload-icon-circle mx-auto">
                                    <i class="bi bi-cloud-arrow-up-fill fs-3"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                    <span class="text-primary text-decoration-underline cursor-pointer">Selecciona una imagen</span> o arrástrala aquí
                                </h6>
                                <p class="text-muted text-xs mb-2">
                                    Formatos compatibles: <strong>JPG, PNG o WebP</strong> en alta resolución.
                                </p>
                                <span class="badge bg-white text-muted border rounded-pill px-3 py-1 text-2xs fw-medium shadow-2xs">
                                    <i class="bi bi-info-circle text-primary me-1"></i> Tamaño máximo por archivo: 5 MB
                                </span>
                            </div>

                            {{-- Estado con Archivo Seleccionado --}}
                            <div id="dropzone_selected_state" class="d-none">
                                <div class="file-selected-card d-flex align-items-center justify-content-between p-3">
                                    <div class="d-flex align-items-center gap-3 text-start min-w-0">
                                        <div class="rounded-3 overflow-hidden border flex-shrink-0" style="width: 54px; height: 54px; background: #FAF7F8;">
                                            <img id="dropzone_file_preview" src="" class="w-100 h-100" style="object-fit: cover;" alt="Previsualización">
                                        </div>
                                        <div class="min-w-0">
                                            <span id="dropzone_file_name" class="fw-bold text-dark text-xs d-block text-truncate">imagen.jpg</span>
                                            <span id="dropzone_file_size" class="badge bg-light text-muted border rounded-pill px-2 py-0.5 text-2xs mt-1">0 KB</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 text-xs fw-semibold" id="btn_change_img">
                                            <i class="bi bi-arrow-repeat me-1"></i> Cambiar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 text-xs fw-semibold" id="btn_remove_img" title="Quitar imagen">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-2.5 px-1 text-muted" style="font-size: 0.75rem;">
                            <span><i class="bi bi-eye-fill text-primary me-1"></i> Se refleja al instante en la tarjeta de vitrina en tiempo real (a la derecha).</span>
                        </div>

                        {{-- Galería de Fotos Adicionales --}}
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <label class="form-label text-xs fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-images text-primary"></i> Galería de Fotos Adicionales (Opcional)
                                    </label>
                                    <small class="text-muted" style="font-size: 0.725rem;">
                                        Agrega hasta 4 fotografías secundarias (ángulos, detalles, empaque).
                                    </small>
                                </div>
                                <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 text-2xs fw-semibold" id="gallery_count_badge">
                                    0 / 4 fotos
                                </span>
                            </div>

                            <input type="file" name="gallery[]" id="prod_gallery_input" class="form-control form-control-sm bg-white" 
                                   multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                Formatos: JPG, PNG, WebP (máx. 5 MB por foto).
                            </small>
                            
                            {{-- Miniaturas de vista previa de la galería --}}
                            <div id="gallery_previews_container" class="d-flex flex-wrap gap-2 mt-2.5 d-none">
                            </div>
                        </div>
                    </div>

                    {{-- 6. Opciones de Publicación --}}
                    <div class="mb-4 d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active',1)?'checked':'' }}>
                            <label class="form-check-label text-dark fw-semibold text-sm" for="active">Activo para venta</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="featured" id="featured" value="1" {{ old('featured')?'checked':'' }}>
                            <label class="form-check-label text-dark fw-semibold text-sm" for="featured">
                                <i class="bi bi-star-fill text-warning me-1"></i> Destacado en vitrina
                            </label>
                        </div>
                    </div>

                    {{-- 7. Botones de Envío --}}
                    <div class="d-flex align-items-center gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-cloud-arrow-up-fill me-1.5"></i> Publicar Producto
                        </button>
                        <a href="{{ route('business.products.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         COLUMNA DERECHA: VISTA PREVIA EN TIEMPO REAL (col-lg-5)
         ══════════════════════════════════════════════════════ --}}
    <div class="col-12 col-lg-5">
        <div class="preview-sticky-dock">
            <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                <div>
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                        <i class="bi bi-eye-fill text-primary"></i> Vista Previa en Vivo
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Así se verá tu producto en la tienda pública</small>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 text-2xs fw-bold d-flex align-items-center gap-1">
                    <span class="rounded-circle bg-success d-inline-block" style="width: 6px; height: 6px;"></span> EN TIEMPO REAL
                </span>
            </div>

            {{-- Réplica Exacta de la Tarjeta Editorial del Producto en Spotlight --}}
            <article class="product-card editorial-product-card shadow-sm border rounded-4 overflow-hidden bg-white">
                {{-- Contenedor de Imagen y Badges Flotantes --}}
                <div class="product-image-container position-relative">
                    <div class="editorial-img-frame position-relative overflow-hidden" style="height: 230px; background-color: #FAF7F8;">
                        <img id="preview_img"
                             src="{{ $company->banner_url ?: ($company->logo_url ?: asset('images/logo-symbol-burgundy.png')) }}"
                             class="w-100 h-100" style="object-fit: cover; transition: opacity 0.2s;"
                             alt="Vista previa">

                        {{-- Insignia Flotante de Categoría con Color Temático --}}
                        <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1 z-2">
                            <span id="preview_cat_badge" class="badge rounded-pill editorial-badge shadow-xs"
                                  style="background-color: var(--theme-accent, #7F1D3F); color: #ffffff;">
                                <i id="preview_cat_icon" class="bi bi-tag-fill me-1"></i>
                                <span id="preview_cat_badge_label">General</span>
                            </span>
                        </div>

                        {{-- Bandera de Origen --}}
                        <div class="position-absolute top-0 end-0 m-2 d-flex align-items-center gap-1 z-2">
                            <span class="editorial-origin-pill" title="Origen">
                                {{ $company->isMexican() ? '🇲🇽 MX' : '🇨🇴 CO' }}
                            </span>
                        </div>

                        {{-- Pie flotante sobre la imagen con Nombre del Establecimiento y Stock --}}
                        <div class="position-absolute bottom-0 start-0 end-0 p-2 d-flex justify-content-between align-items-end z-2 editorial-img-footer"
                             style="background: linear-gradient(transparent, rgba(0,0,0,0.6));">
                            <span class="editorial-store-chip text-white text-truncate text-xs d-flex align-items-center gap-1">
                                <i class="bi bi-shop me-0.5"></i>
                                <span class="text-truncate">{{ $company->name ?: 'Mi Empresa' }}</span>
                                <i class="bi bi-patch-check-fill text-warning ms-1"></i>
                            </span>

                            <span id="preview_stock_badge" class="editorial-stock-chip text-2xs fw-bold px-2 py-0.5 rounded-pill bg-dark bg-opacity-75 text-white">
                                10 disp.
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Cuerpo de la Tarjeta --}}
                <div class="product-card-body p-3.5 d-flex flex-column flex-fill">
                    <div class="product-info-section">
                        {{-- Categoría y Calificación --}}
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                            <span id="preview_cat_subtitle" class="editorial-cat-label text-truncate text-xs fw-bold" style="color: var(--theme-accent, #7F1D3F);">
                                General
                            </span>
                            <span class="editorial-rating-pill text-2xs text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-star-fill text-warning"></i>
                                <span>5.0</span>
                                <small>(0)</small>
                            </span>
                        </div>

                        {{-- Título en Vivo --}}
                        <h6 id="preview_title" class="product-title fw-bold text-dark mb-1 text-truncate" style="font-size: 1.05rem;">
                            Nombre del producto...
                        </h6>

                        {{-- Descripción en Vivo --}}
                        <p id="preview_desc" class="product-desc text-muted text-xs mb-3" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 34px;">
                            Descripción detallada de tu artículo visible para compradores locales...
                        </p>
                    </div>

                    {{-- Pie con Precio y Botón de Acción --}}
                    <div class="product-action-section d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                        <div>
                            <span class="text-2xs text-muted d-block text-uppercase fw-semibold">Precio al público</span>
                            <div id="preview_price" class="product-price-tag fw-black fs-5" style="color: #7F1D3F;">
                                {{ $company->isMexican() ? '$ 250.00' : '$ 50.000' }} <small class="text-xs fw-normal text-muted">{{ $companyCurrency }}</small>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold text-xs" disabled>
                            <i class="bi bi-bag-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </article>

            {{-- Nota explicativa al pie de la vista previa --}}
            <div class="mt-3 p-3 rounded-4 bg-white border shadow-2xs d-flex align-items-start gap-2.5">
                <i class="bi bi-shield-check text-success fs-5"></i>
                <div class="text-xs text-muted">
                    <strong class="text-dark d-block">Garantía de fidelidad visual</strong>
                    La vista previa refleja exactamente el color temático de la categoría seleccionada, la fotografía del producto o establecimiento y el precio formateado.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ─── Estado de Imágenes y URLs del Establecimiento ──────────
const defaultEstablishmentImage = "{{ $company->banner_url ?: ($company->logo_url ?: asset('images/logo-symbol-burgundy.png')) }}";
const companyBannerUrl = "{{ $company->banner_url ?: '' }}";
const companyLogoUrl = "{{ $company->logo_url ?: '' }}";
const currencyCode = "{{ $company->isMexican() ? 'MXN' : 'COP' }}";
const mxnToCopRate = {{ \App\Models\Product::mxnToCopRate() }};

// ─── Sincronización en Tiempo Real de Textos y Precios ───────
document.getElementById('prod_name')?.addEventListener('input', function() {
    const val = this.value.trim();
    document.getElementById('preview_title').textContent = val || 'Nombre del producto...';
});

document.getElementById('prod_description')?.addEventListener('input', function() {
    const val = this.value.trim();
    document.getElementById('preview_desc').textContent = val || 'Descripción detallada de tu artículo visible para compradores locales...';
});

document.getElementById('prod_price')?.addEventListener('input', function() {
    const rawVal = parseFloat(this.value) || 0;
    const hasDecimals = rawVal % 1 !== 0;
    const formatted = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: (currencyCode === 'MXN' || hasDecimals) ? 2 : 0,
        maximumFractionDigits: 2
    }).format(rawVal);

    document.getElementById('preview_price').innerHTML = `$ ${formatted} <small class="text-xs fw-normal text-muted">${currencyCode}</small>`;
});

document.getElementById('prod_stock')?.addEventListener('input', function() {
    const stock = parseInt(this.value) || 0;
    const badge = document.getElementById('preview_stock_badge');
    if (stock <= 0) {
        badge.textContent = 'Agotado';
        badge.className = 'editorial-stock-chip text-2xs fw-bold px-2 py-0.5 rounded-pill bg-danger text-white';
    } else {
        badge.textContent = `${stock} disp.`;
        badge.className = 'editorial-stock-chip text-2xs fw-bold px-2 py-0.5 rounded-pill bg-dark bg-opacity-75 text-white';
    }
});

// ─── Sincronización en Tiempo Real de Categoría / Tema ──────
function updatePreviewCategory() {
    const checkedBoxes = Array.from(document.querySelectorAll('.tag-checkbox-item:checked'));
    const previewBadge = document.getElementById('preview_cat_badge');
    const previewBadgeLabel = document.getElementById('preview_cat_badge_label');
    const previewIcon = document.getElementById('preview_cat_icon');
    const previewSub = document.getElementById('preview_cat_subtitle');

    if (checkedBoxes.length > 0) {
        const first = checkedBoxes[0];
        const label = first.getAttribute('data-label') || 'Categoría';
        const accent = first.getAttribute('data-accent') || '#7F1D3F';
        const icon = first.getAttribute('data-icon') || 'bi-tag-fill';

        previewBadgeLabel.textContent = label;
        previewBadge.style.backgroundColor = accent;
        previewIcon.className = `bi ${icon} me-1`;
        previewSub.textContent = label;
        previewSub.style.color = accent;
        document.documentElement.style.setProperty('--theme-accent', accent);
    } else {
        previewBadgeLabel.textContent = 'General';
        previewBadge.style.backgroundColor = '#7F1D3F';
        previewIcon.className = 'bi bi-tag-fill me-1';
        previewSub.textContent = 'General';
        previewSub.style.color = '#7F1D3F';
        document.documentElement.style.setProperty('--theme-accent', '#7F1D3F');
    }
}

document.querySelectorAll('.tag-checkbox-item').forEach(chk => {
    chk.addEventListener('change', updatePreviewCategory);
});

// ─── Vista Previa y Manejo de la Fotografía del Producto ────────
const defaultPlaceholderImage = "{{ $company->logo_url ?: asset('images/logo-symbol-burgundy.png') }}";
const prodImageInput = document.getElementById('prod_image_input');
const dropzone = document.getElementById('dropzone_product_img');
const emptyState = document.getElementById('dropzone_empty_state');
const selectedState = document.getElementById('dropzone_selected_state');
const dropzonePreview = document.getElementById('dropzone_file_preview');
const dropzoneFileName = document.getElementById('dropzone_file_name');
const dropzoneFileSize = document.getElementById('dropzone_file_size');
const previewImg = document.getElementById('preview_img');
const btnChangeImg = document.getElementById('btn_change_img');
const btnRemoveImg = document.getElementById('btn_remove_img');

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    else return (bytes / 1048576).toFixed(2) + ' MB';
}

function processSelectedFile(file) {
    const maxBytes = 5 * 1024 * 1024; // 5 MB
    if (file.size > maxBytes) {
        alert('La imagen seleccionada supera el límite de 5 MB. Por favor elige una imagen más liviana.');
        resetImageUpload();
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const resultUrl = e.target.result;
        // 1. Actualizar miniatura del dropzone
        if (dropzonePreview) dropzonePreview.src = resultUrl;
        if (dropzoneFileName) dropzoneFileName.textContent = file.name;
        if (dropzoneFileSize) dropzoneFileSize.textContent = formatFileSize(file.size);

        if (emptyState) emptyState.classList.add('d-none');
        if (selectedState) selectedState.classList.remove('d-none');

        // 2. Actualizar tarjeta de vitrina en tiempo real
        if (previewImg) previewImg.src = resultUrl;
    };
    reader.readAsDataURL(file);
}

function resetImageUpload() {
    if (prodImageInput) prodImageInput.value = '';
    if (dropzonePreview) dropzonePreview.src = '';
    if (emptyState) emptyState.classList.remove('d-none');
    if (selectedState) selectedState.classList.add('d-none');
    if (previewImg) previewImg.src = defaultPlaceholderImage;
}

// Abrir selector al hacer clic en el dropzone (si no se cliquea en botones internos)
dropzone?.addEventListener('click', function(e) {
    if (e.target.closest('#btn_change_img') || e.target.closest('#btn_remove_img')) {
        return;
    }
    prodImageInput?.click();
});

btnChangeImg?.addEventListener('click', function(e) {
    e.stopPropagation();
    prodImageInput?.click();
});

btnRemoveImg?.addEventListener('click', function(e) {
    e.stopPropagation();
    resetImageUpload();
});

// Evento Change del Input File
prodImageInput?.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        processSelectedFile(this.files[0]);
    } else {
        resetImageUpload();
    }
});

// Soporte Drag & Drop
if (dropzone) {
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files[0]) {
            prodImageInput.files = dt.files;
            processSelectedFile(dt.files[0]);
        }
    });
}

// Guardar categoría rápida creada por la empresa
document.getElementById('btn_save_quick_tag')?.addEventListener('click', async function() {
    const labelInput = document.getElementById('quick_tag_label');
    const colorInput = document.getElementById('quick_tag_color');
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
            const container = document.getElementById('tagsPillsContainer');
            const newPill = document.createElement('div');
            newPill.className = 'tag-pill-check';
            newPill.innerHTML = `
                <input class="btn-check tag-checkbox-item" type="checkbox" name="tags[]" id="tag_${data.tag.id}" value="${data.tag.id}"
                       data-label="${data.tag.label}" data-accent="${data.tag.accent}" data-icon="${data.tag.icon}" checked>
                <label for="tag_${data.tag.id}" class="btn-tag-pill" style="--tag-accent: ${data.tag.accent};">
                    <i class="bi bi-tag-fill me-1" style="color: ${data.tag.accent};"></i>
                    <span>${data.tag.label}</span>
                </label>
            `;
            container.prepend(newPill);
            newPill.querySelector('input').addEventListener('change', updatePreviewCategory);
            updatePreviewCategory();
            labelInput.value = '';

            const collapseElem = document.getElementById('newCategoryCollapse');
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

// Gestión de fotos adicionales de galería
const galleryInput = document.getElementById('prod_gallery_input');
const galleryPreviewsContainer = document.getElementById('gallery_previews_container');
const galleryCountBadge = document.getElementById('gallery_count_badge');

galleryInput?.addEventListener('change', function() {
    if (!this.files || this.files.length === 0) {
        galleryPreviewsContainer.innerHTML = '';
        galleryPreviewsContainer.classList.add('d-none');
        galleryCountBadge.textContent = '0 / 4 fotos';
        return;
    }

    if (this.files.length > 4) {
        alert('Solo puedes seleccionar hasta 4 imágenes secundarias para la galería.');
        this.value = '';
        galleryPreviewsContainer.innerHTML = '';
        galleryPreviewsContainer.classList.add('d-none');
        galleryCountBadge.textContent = '0 / 4 fotos';
        return;
    }

    galleryPreviewsContainer.innerHTML = '';
    galleryPreviewsContainer.classList.remove('d-none');
    let validCount = 0;

    Array.from(this.files).forEach((file, idx) => {
        if (file.size > 5 * 1024 * 1024) {
            alert(`El archivo "${file.name}" supera los 5 MB.`);
            return;
        }
        validCount++;
        const col = document.createElement('div');
        col.className = 'position-relative border rounded-3 p-1 bg-white shadow-2xs';
        col.style.width = '64px';
        col.style.height = '64px';

        const img = document.createElement('img');
        img.className = 'w-100 h-100 rounded-2';
        img.style.objectFit = 'cover';
        img.alt = file.name;

        const reader = new FileReader();
        reader.onload = (e) => { img.src = e.target.result; };
        reader.readAsDataURL(file);

        col.appendChild(img);
        galleryPreviewsContainer.appendChild(col);
    });

    galleryCountBadge.textContent = `${validCount} / 4 fotos`;
});

// Inicializar preview al cargar
updatePreviewCategory();
</script>
@endpush
@endsection
