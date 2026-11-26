@extends('layouts.dashboard')
@section('title','Editar Producto')
@section('page-title','Editar Producto')

@push('styles')
<link href="{{ asset('css/business.css') }}" rel="stylesheet">
@endpush

@section('dashboard-content')
<div class="vs-card" style="max-width:600px">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger border-0" style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626;">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('business.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label text-sm fw-semibold text-dark">Nombre del producto *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-semibold text-dark">Descripción</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>
            @php
                $editCurrency = ($company && $company->isMexican()) || $product->effective_currency === 'MXN' ? 'MXN' : 'COP';
            @endphp
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label text-sm fw-semibold text-dark">Precio de venta ({{ $editCurrency }}) *</label>
                    <input type="number" name="price" id="prod_price" class="form-control" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">A lo que lo vendes al público en {{ $editCurrency }} (permite decimales)</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-sm fw-semibold text-dark">Costo del producto ({{ $editCurrency }})</label>
                    <input type="number" name="cost_price" id="prod_cost_price" class="form-control" value="{{ old('cost_price', $product->cost_price ?: 0) }}" min="0" step="0.01">
                    <small class="text-muted" style="font-size: 0.7rem;">Lo que te cuesta fabricarlo/comprarlo (permite decimales)</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-sm fw-semibold text-dark">Stock disponible *</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" min="0" required>
                    <small class="text-muted" style="font-size: 0.7rem;">Unidades en inventario</small>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 SECCIÓN: CATEGORÍAS / ETIQUETAS DEL PRODUCTO
            ══════════════════════════════════════════════════ --}}
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
                    Selecciona una o más categorías para que los compradores encuentren tu producto con el filtro temático.
                </small>

                {{-- Panel para crear categoría personalizada --}}
                <div class="collapse mb-3" id="newCategoryCollapse">
                    <div class="p-3 bg-white rounded-3 border shadow-xs">
                        <h6 class="fw-bold text-dark text-xs mb-2 d-flex align-items-center gap-1">
                            <i class="bi bi-magic text-primary"></i> Crear Categoría para tu Empresa
                        </h6>
                        <div class="row g-2">
                            <div class="col-sm-7">
                                <label class="text-2xs fw-semibold text-muted mb-1">Nombre de la categoría</label>
                                <input type="text" id="quick_tag_label" class="form-control form-control-sm" placeholder="Ej: Moda Infantil, Postres Caseros">
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
                <div class="d-flex flex-wrap gap-2 pt-1" id="tagsPillsContainer" style="max-height: 200px; overflow-y: auto;">
                    @php
                        $assignedTagIds = old('tags', $product->tags->pluck('id')->toArray());
                    @endphp
                    @foreach($tags as $tag)
                        <div class="tag-pill-check">
                            <input class="btn-check" type="checkbox" name="tags[]" id="tag_{{ $tag->id }}" value="{{ $tag->id }}" {{ in_array($tag->id, $assignedTagIds) ? 'checked' : '' }}>
                            <label for="tag_{{ $tag->id }}" class="btn-tag-pill" style="--tag-accent: {{ $tag->accent_color }};">
                                <i class="bi {{ $tag->icon ?: 'bi-tag-fill' }} me-1" style="color: {{ $tag->accent_color }};"></i>
                                <span>{{ $tag->label }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 5. Fotografía del Producto --}}
            <div class="mb-4 p-4 rounded-4 bg-white border">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <label class="form-label text-sm fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                        <i class="bi bi-image-fill text-primary"></i> Fotografía del Producto
                    </label>
                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1 text-xs fw-semibold">
                        <i class="bi bi-file-earmark-image me-1"></i> Máx. 5 MB (JPG, PNG, WebP)
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-4 border">
                    <img id="prod_edit_current_thumb" src="{{ $product->image_url }}" height="72" width="72" class="rounded-3 border shadow-2xs" style="object-fit: cover;" alt="{{ $product->name }}">
                    <div class="text-xs text-muted">
                        <strong class="text-dark d-block fs-6" id="prod_edit_thumb_label">Fotografía actual</strong>
                        <span>Puedes conservar esta imagen o cargar una nueva fotografía de hasta 5 MB.</span>
                    </div>
                </div>

                {{-- Campo para subir nueva imagen --}}
                <div class="p-3 bg-white rounded-3 border">
                    <label class="form-label text-xs fw-semibold text-dark mb-1">Cargar nueva fotografía de reemplazo</label>
                    <input type="file" name="image" id="prod_edit_image_input" class="form-control bg-white" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <small class="text-muted d-block mt-1.5" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i>Formatos admitidos: JPG, PNG, WebP. Deja este campo vacío si deseas mantener la fotografía actual.
                    </small>
                </div>

                {{-- Galería de Fotos Adicionales --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <label class="form-label text-xs fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                <i class="bi bi-images text-primary"></i> Galería de Fotos Adicionales (Opcional)
                            </label>
                            <small class="text-muted" style="font-size: 0.725rem;">
                                Administra hasta 4 fotos secundarias del producto.
                            </small>
                        </div>
                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 text-2xs fw-semibold" id="edit_gallery_count_badge">
                            {{ count($product->gallery_urls) }} / 4 fotos
                        </span>
                    </div>

                    {{-- Fotos actuales de la galería con opción para marcarlas para eliminación --}}
                    @if(!empty($product->gallery) && count($product->gallery) > 0)
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <span class="text-2xs fw-semibold text-muted text-uppercase d-block mb-2">
                                <i class="bi bi-check2-circle text-success me-1"></i> Fotos actuales en galería (marca para eliminar):
                            </span>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($product->gallery as $idx => $galPath)
                                    <div class="position-relative border rounded-3 p-1 bg-white shadow-2xs text-center" style="width: 80px;">
                                        <img src="{{ asset('storage/' . $galPath) }}" class="rounded-2 w-100" style="height: 60px; object-fit: cover;" alt="Galería {{ $idx + 1 }}">
                                        <div class="form-check d-flex justify-content-center align-items-center mt-1 mb-0">
                                            <input class="form-check-input" type="checkbox" name="delete_gallery_images[]" value="{{ $galPath }}" id="del_gal_{{ $idx }}" title="Marcar para eliminar">
                                            <label class="form-check-label text-danger ms-1 cursor-pointer" for="del_gal_{{ $idx }}" style="font-size: 0.65rem;">
                                                <i class="bi bi-trash"></i> Quitar
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Cargar nuevas fotos adicionales --}}
                    @php
                        $currentCount = !empty($product->gallery) ? count($product->gallery) : 0;
                        $availableSlots = max(0, 4 - $currentCount);
                    @endphp
                    <div class="p-3 bg-white rounded-3 border">
                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                            Añadir nuevas fotos a la galería (Disponibles: {{ $availableSlots }} de 4)
                        </label>
                        <input type="file" name="gallery[]" id="prod_edit_gallery_input" class="form-control bg-white" 
                               multiple accept="image/jpeg,image/png,image/jpg,image/webp" {{ $availableSlots <= 0 ? 'disabled' : '' }}>
                        <small class="text-muted d-block mt-1" style="font-size: 0.725rem;">
                            {{ $availableSlots > 0 ? 'Puedes seleccionar hasta ' . $availableSlots . ' foto(s) nueva(s). Formatos: JPG, PNG, WebP (máx. 5 MB por foto).' : 'Has alcanzado el límite máximo de 4 fotos. Elimina alguna arriba si deseas subir nuevas.' }}
                        </small>
                        <div id="edit_gallery_previews_container" class="d-flex flex-wrap gap-2 mt-2.5 d-none">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4 d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ $product->active?'checked':'' }}>
                    <label class="form-check-label text-dark" for="active">Activo</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="featured" id="featured" value="1" {{ $product->featured?'checked':'' }}>
                    <label class="form-check-label text-dark" for="featured"><i class="bi bi-star-fill me-1" style="color: var(--vs-secondary);"></i>Destacado</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Guardar cambios</button>
                <a href="{{ route('business.products.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const originalProductThumb = "{{ $product->image_url }}";
const editImageInput = document.getElementById('prod_edit_image_input');
const editThumb = document.getElementById('prod_edit_current_thumb');
const editThumbLabel = document.getElementById('prod_edit_thumb_label');

editImageInput?.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        const maxBytes = 5 * 1024 * 1024; // 5MB
        if (file.size > maxBytes) {
            alert('La imagen seleccionada supera el límite de 5 MB. Por favor elige una imagen más liviana.');
            this.value = '';
            if (editThumb) editThumb.src = originalProductThumb;
            if (editThumbLabel) editThumbLabel.textContent = 'Fotografía actual';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            if (editThumb) editThumb.src = e.target.result;
            if (editThumbLabel) editThumbLabel.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Nueva imagen seleccionada (previsualización en vivo)</span>';
        };
        reader.readAsDataURL(file);
    } else {
        if (editThumb) editThumb.src = originalProductThumb;
        if (editThumbLabel) editThumbLabel.textContent = 'Fotografía actual';
    }
});

// Previsualización de nuevas fotos de galería
const editGalleryInput = document.getElementById('prod_edit_gallery_input');
const editGalleryPreviewsContainer = document.getElementById('edit_gallery_previews_container');

editGalleryInput?.addEventListener('change', function() {
    if (!this.files || this.files.length === 0) {
        editGalleryPreviewsContainer.innerHTML = '';
        editGalleryPreviewsContainer.classList.add('d-none');
        return;
    }

    editGalleryPreviewsContainer.innerHTML = '';
    editGalleryPreviewsContainer.classList.remove('d-none');

    Array.from(this.files).forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            alert(`El archivo "${file.name}" supera los 5 MB.`);
            return;
        }

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
        editGalleryPreviewsContainer.appendChild(col);
    });
});

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
                <input class="btn-check" type="checkbox" name="tags[]" id="tag_${data.tag.id}" value="${data.tag.id}" checked>
                <label for="tag_${data.tag.id}" class="btn-tag-pill" style="--tag-accent: ${data.tag.accent};">
                    <i class="bi bi-tag-fill me-1" style="color: ${data.tag.accent};"></i>
                    <span>${data.tag.label}</span>
                </label>
            `;
            container.prepend(newPill);
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
</script>
@endpush
@endsection
