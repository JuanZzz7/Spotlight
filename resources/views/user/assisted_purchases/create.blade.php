@extends('layouts.dashboard')

@section('page-title', 'Solicitar Compra Internacional Asistida')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            {{-- Breadcrumb y Volver --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <a href="{{ route('public.product', $product->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Producto
                </a>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                    <i class="bi bi-globe-americas me-1 text-primary"></i> Comercio Transfronterizo Asistido
                </span>
            </div>

            {{-- Banner informativo de Comercio Internacional Asistido --}}
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #29252A 0%, #3D1222 50%, #7F1D3F 100%); color: #ffffff; border-radius: 18px;">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 54px; height: 54px; background: rgba(255, 255, 255, 0.15); flex-shrink: 0;">
                            <i class="bi bi-airplane-engines fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-1">Compra Internacional Asistida ({{ $product->country_flag }} {{ $product->country_name }} ➔ {{ $buyerCountry === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }})</h5>
                            <p class="mb-0 text-white-50 small">
                                Este producto proviene de un comercio en <strong>{{ $product->company->name }}</strong> ({{ $product->company->city ?? '' }}, {{ $product->country_name }}). 
                                Coordinamos directamente contigo y con el vendedor el cálculo de flete internacional, tiempos de aduana y medios de pago habilitados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen del Producto --}}
            <div class="vs-card p-4 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #e2e8f0;">
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-dark text-white rounded-pill" style="font-size: 0.7rem;">
                                {{ $product->country_flag }} Origen: {{ $product->country_name }}
                            </span>
                            <span class="badge bg-light text-muted border rounded-pill" style="font-size: 0.7rem;">
                                {{ $product->company->name }}
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $product->name }}</h5>
                        <div class="text-muted small">
                            Precio de lista: <strong class="text-dark fs-6">{{ $product->formatted_price }}</strong>
                            • Disponibles: {{ $product->stock }} unidades
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario de Solicitud Asistida --}}
            <div class="vs-card p-4 p-md-5 border-0 shadow-sm" style="border-radius: 18px;">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>Datos de Entrega y Cotización
                </h5>
                <p class="text-muted small mb-4">
                    Ingresa con exactitud tu ubicación de entrega en destino. El vendedor calculará la mejor tarifa logística para ti.
                </p>

                <form action="{{ route('assisted_purchases.store', $product->id) }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Cantidad solicitada <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                   value="{{ old('quantity', 1) }}" min="1" max="{{ max(1, $product->stock) }}" required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">País de Destino <span class="text-danger">*</span></label>
                            <select name="destination_country" class="form-select @error('destination_country') is-invalid @enderror" required>
                                <option value="CO" {{ old('destination_country', $preloaded['destination_country']) === 'CO' ? 'selected' : '' }}>🇨🇴 Colombia</option>
                                <option value="MX" {{ old('destination_country', $preloaded['destination_country']) === 'MX' ? 'selected' : '' }}>🇲🇽 México</option>
                            </select>
                            @error('destination_country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Estado / Departamento <span class="text-danger">*</span></label>
                            <input type="text" name="destination_state" class="form-control @error('destination_state') is-invalid @enderror" 
                                   value="{{ old('destination_state', $preloaded['destination_state']) }}" 
                                   placeholder="Ej: Jalisco, Cundinamarca, CDMX" required>
                            @error('destination_state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Ciudad / Municipio <span class="text-danger">*</span></label>
                            <input type="text" name="destination_city" class="form-control @error('destination_city') is-invalid @enderror" 
                                   value="{{ old('destination_city', $preloaded['destination_city']) }}" 
                                   placeholder="Ej: Guadalajara, Bogotá, Ubaté" required>
                            @error('destination_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Código Postal</label>
                            <input type="text" name="destination_postal_code" class="form-control @error('destination_postal_code') is-invalid @enderror" 
                                   value="{{ old('destination_postal_code') }}" 
                                   placeholder="Ej: 44100 (México) o 250420 (Colombia)">
                            @error('destination_postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Dirección completa de entrega <span class="text-danger">*</span></label>
                            <input type="text" name="destination_address" class="form-control @error('destination_address') is-invalid @enderror" 
                                   value="{{ old('destination_address', $preloaded['destination_address']) }}" 
                                   placeholder="Calle, número, colonia/barrio, interior o referencias de entrega" required>
                            @error('destination_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Nombre del Destinatario <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror" 
                                   value="{{ old('recipient_name', $preloaded['recipient_name']) }}" required>
                            @error('recipient_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Teléfono de Contacto (con indicativo) <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_phone" class="form-control @error('recipient_phone') is-invalid @enderror" 
                                   value="{{ old('recipient_phone', $preloaded['recipient_phone']) }}" 
                                   placeholder="+52 33 1234 5678 o +57 310 123 4567" required>
                            @error('recipient_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="recipient_email" class="form-control @error('recipient_email') is-invalid @enderror" 
                                   value="{{ old('recipient_email', $preloaded['recipient_email']) }}" required>
                            @error('recipient_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notas, preguntas o instrucciones para el vendedor</label>
                            <textarea name="buyer_notes" rows="3" class="form-control @error('buyer_notes') is-invalid @enderror" 
                                      placeholder="Especificaciones de color, tallas, requerimientos aduanales o tiempos máximos de entrega deseadas...">{{ old('buyer_notes') }}</textarea>
                            @error('buyer_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="alert alert-light border d-flex align-items-center gap-3 mt-4 mb-4 small text-muted">
                        <i class="bi bi-shield-check text-success fs-4"></i>
                        <div>
                            <strong>Compromiso Spotlight Transfronterizo:</strong> 
                            Esta solicitud congela el precio base original del producto. No se realizará ningún cobro hasta que ambas partes confirmen la cotización de flete y el medio de pago acordado.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('public.product', $product->id) }}" class="btn btn-outline-secondary px-4 rounded-pill">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-spotlight px-5 py-2 rounded-pill shadow-sm fw-semibold">
                            <i class="bi bi-send-check me-2"></i> Enviar Solicitud Internacional
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
