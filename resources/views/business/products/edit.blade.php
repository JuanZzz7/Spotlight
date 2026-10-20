@extends('layouts.dashboard')
@section('title','Editar Producto')
@section('page-title','Editar Producto')

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
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label text-sm fw-semibold text-dark">Precio de venta (COP) *</label>
                    <input type="number" name="price" id="prod_price" class="form-control" value="{{ old('price', $product->price) }}" min="0" step="100" required>
                    <small class="text-muted" style="font-size: 0.7rem;">A lo que lo vendes al público</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-sm fw-semibold text-dark">Costo del producto (COP)</label>
                    <input type="number" name="cost_price" id="prod_cost_price" class="form-control" value="{{ old('cost_price', $product->cost_price ?: 0) }}" min="0" step="100">
                    <small class="text-muted" style="font-size: 0.7rem;">Lo que te cuesta fabricarlo/comprarlo</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-sm fw-semibold text-dark">Stock disponible *</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" min="0" required>
                    <small class="text-muted" style="font-size: 0.7rem;">Unidades en inventario</small>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-semibold text-dark">Imagen del producto</label>
                <div class="mb-2">
                    <img src="{{ $product->image_url }}" height="80" class="rounded border" style="object-fit: cover;" alt="{{ $product->name }}">
                </div>
                <input type="file" name="image" id="prod_edit_image_input" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i>Límite máximo 2 MB. Deja vacío para conservar la imagen o logo actual.
                </small>
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
document.getElementById('prod_edit_image_input')?.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const maxBytes = 2 * 1024 * 1024; // 2MB
        if (this.files[0].size > maxBytes) {
            alert('La imagen seleccionada supera el límite de 2 MB. Por favor elige una imagen más liviana.');
            this.value = '';
        }
    }
});
</script>
@endpush
@endsection
