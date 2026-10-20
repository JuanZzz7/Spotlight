@extends('layouts.dashboard')

@section('title', 'Editar Egreso')
@section('page-title', 'Editar Egreso')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="vs-card p-4 p-md-5 border-0 shadow-sm" style="border-radius: 20px; background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <span class="badge rounded-pill fw-bold px-3 py-1 mb-2" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.65rem;">
                            MODIFICACIÓN DE GASTO
                        </span>
                        <h4 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">Editar Egreso #{{ $expense->id }}</h4>
                        <p class="text-muted small mb-0">Actualiza los datos del gasto, su categoría o adjunta un nuevo soporte / factura.</p>
                    </div>
                    <a href="{{ route('business.expenses.index') }}" class="btn btn-light border btn-sm rounded-pill px-3 py-2 fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 d-flex align-items-center mb-4 rounded-4" style="background-color: #fee2e2; color: #991b1b;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2 flex-shrink-0"></i>
                        <div class="small">
                            <strong>Por favor revisa los errores:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('business.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label text-sm fw-semibold text-dark">Concepto del Egreso *</label>
                            <input type="text" name="concept" class="form-control rounded-3" value="{{ old('concept', $expense->concept) }}" placeholder="Ej: Compra de empaques y cajas" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-sm fw-semibold text-dark">Categoría *</label>
                            <select name="category" class="form-select rounded-3 text-dark">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $expense->category) === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Monto en Pesos (COP) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold">$</span>
                                <input type="number" name="amount" class="form-control rounded-end-3" value="{{ old('amount', (float)$expense->amount) }}" min="0" step="50" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Fecha del Gasto *</label>
                            <input type="date" name="date" class="form-control rounded-3" value="{{ old('date', $expense->date ? $expense->date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-sm fw-semibold text-dark">Descripción u Observaciones</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Información adicional, proveedor, número de factura física...">{{ old('description', $expense->description) }}</textarea>
                    </div>

                    {{-- Soporte / Factura Adjunta --}}
                    <div class="p-3 mb-4 rounded-4 border bg-light">
                        <label class="form-label text-sm fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-paperclip fs-5" style="color: var(--sl-primary, #7F1D3F);"></i>
                            Soporte / Factura Adjunta
                        </label>

                        @if($expense->invoice_path)
                            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 rounded-3 bg-white border mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: {{ $expense->isPdf() ? '#fee2e2' : '#dcfce7' }};">
                                        <i class="bi {{ $expense->isPdf() ? 'bi-file-earmark-pdf-fill text-danger' : 'bi-file-earmark-image-fill text-success' }} fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark small">{{ $expense->invoice_filename ?: 'Factura_adjunta' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Soporte actual guardado</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                    <a href="{{ $expense->invoice_url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Ver Soporte
                                    </a>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remove_invoice" value="1" id="removeInvoiceCheck">
                                <label class="form-check-label text-sm text-danger fw-semibold" for="removeInvoiceCheck">
                                    Eliminar soporte actual sin reemplazarlo
                                </label>
                            </div>
                        @endif

                        <div>
                            <label class="form-label small text-muted mb-1">
                                {{ $expense->invoice_path ? 'Reemplazar con un nuevo archivo:' : 'Subir factura o soporte de pago:' }}
                            </label>
                            <input type="file" name="invoice" class="form-control rounded-3" accept=".pdf,.png,.jpg,.jpeg,.webp">
                            <div class="form-text small text-muted">
                                Formatos permitidos: PDF, JPG, PNG o WEBP (Máximo 10MB).
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('business.expenses.index') }}" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-spotlight rounded-pill px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
