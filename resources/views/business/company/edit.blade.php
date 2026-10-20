@extends('layouts.dashboard')
@section('title','Mi Empresa - Spotlight')
@section('page-title','Personalización de Empresa')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<style>
    #map { z-index: 1; border-radius: 1rem; border: 1px solid #dee2e6; height: 350px; width: 100%; min-height: 350px; }
    .section-save-bar {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 14px 24px;
        border-radius: 0 0 1rem 1rem;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }
    @@keyframes spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
</style>
@endpush

@section('dashboard-content')
<div style="max-width: 720px; margin: 0 auto;">

    {{-- ═══════════════════════════════════
         SECCIÓN 1: VISTA GENERAL
    ═══════════════════════════════════ --}}
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
                                    <span class="small">Banner de la empresa (Recomendado: 1200x400)</span>
                                </div>
                            </div>
                        @endif

                        {{-- Boton cambiar banner superpuesto --}}
                        <div class="position-absolute bottom-0 end-0 p-2 d-flex flex-column align-items-end gap-1">
                            <label for="bannerInput" class="btn btn-dark btn-sm rounded-pill px-3 shadow mb-0">
                                <i class="bi bi-camera me-1"></i> Cambiar Banner
                            </label>
                            <input type="file" name="banner" id="bannerInput" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp,image/bmp">
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
                            <input type="file" name="logo" id="logoInput" class="d-none" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-7">
                        <label class="form-label text-sm fw-semibold text-dark">Nombre público de la empresa *</label>
                        <input type="text" name="name" class="form-control form-control-lg"
                               value="{{ old('name', $company->name) }}" required
                               placeholder="Ej: Mi Negocio Digital">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-sm fw-semibold text-dark">Categoría</label>
                        <select name="category" class="form-select form-select-lg">
                            <option value="">Seleccionar...</option>
                            @foreach(['Tecnología','Moda','Alimentos','Belleza','Hogar','Deportes','Arte','Educación','Salud','Otros'] as $cat)
                                <option value="{{ $cat }}" {{ old('category',$company->category)===$cat?'selected':'' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-sm fw-semibold text-dark">Descripción de la empresa</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Cuéntanos un poco sobre lo que hace tu empresa...">{{ old('description', $company->description) }}</textarea>
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

    {{-- ═══════════════════════════════════
         SECCIÓN 2: ASPECTO LEGAL
    ═══════════════════════════════════ --}}
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

    {{-- ═══════════════════════════════════
         SECCIÓN 3: REDES Y CONTACTO
    ═══════════════════════════════════ --}}
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

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Horario de Atención
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-semibold text-dark">Hora de Apertura</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-door-open text-success"></i></span>
                            <input type="time" name="opening_time" class="form-control border-start-0"
                                   value="{{ old('opening_time', $company->opening_time ? substr($company->opening_time, 0, 5) : '08:00') }}">
                        </div>
                        <small class="text-muted">Hora habitual en la que abre tu local o atención</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-semibold text-dark">Hora de Cierre</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-door-closed text-danger"></i></span>
                            <input type="time" name="closing_time" class="form-control border-start-0"
                                   value="{{ old('closing_time', $company->closing_time ? substr($company->closing_time, 0, 5) : '19:00') }}">
                        </div>
                        <small class="text-muted">Hora habitual en la que finaliza la atención</small>
                    </div>
                    <div class="col-12 mt-2">
                        <label class="form-label text-sm fw-semibold text-dark d-block">Días de Atención</label>
                        @php
                            $selectedDays = $company->operating_days ?? ['monday','tuesday','wednesday','thursday','friday','saturday'];
                        @endphp
                        <div class="d-flex flex-wrap gap-2">
                            @foreach([
                                'monday' => 'Lunes',
                                'tuesday' => 'Martes',
                                'wednesday' => 'Miércoles',
                                'thursday' => 'Jueves',
                                'friday' => 'Viernes',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo'
                            ] as $dayKey => $dayLabel)
                                <div class="form-check form-check-inline bg-light px-3 py-2 rounded-pill border mb-0">
                                    <input class="form-check-input" type="checkbox" name="operating_days[]" id="day_{{ $dayKey }}" value="{{ $dayKey }}" {{ in_array($dayKey, $selectedDays) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold cursor-pointer" for="day_{{ $dayKey }}">{{ $dayLabel }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                    <i class="bi bi-geo-alt-fill me-2 text-danger"></i>Ubicación en el Mapa
                </h6>
                <div id="map" class="rounded-4 border mb-3 shadow-sm"></div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="text-xs text-muted">Latitud</label>
                        <input type="text" name="latitude" id="lat" class="form-control form-control-sm"
                               value="{{ old('latitude', $company->latitude) }}">
                    </div>
                    <div class="col-6">
                        <label class="text-xs text-muted">Longitud</label>
                        <input type="text" name="longitude" id="lng" class="form-control form-control-sm"
                               value="{{ old('longitude', $company->longitude) }}">
                    </div>
                </div>
                <p class="text-xs text-muted"><i class="bi bi-info-circle me-1"></i>Haz clic en el mapa para marcar la ubicación exacta de tu local o negocio.</p>

                {{-- Campo de dirección: auto-completado desde el mapa --}}
                <input type="hidden" name="address" id="address-hidden" value="{{ old('address', $company->address) }}">
                <div class="mt-2 p-3 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <label class="text-xs fw-semibold text-success d-block mb-1">
                        <i class="bi bi-geo-alt-fill me-1"></i>Dirección detectada desde el mapa
                    </label>
                    <div id="address-display" class="text-sm text-dark">
                        @if($company->address)
                            {{ $company->address }}
                        @else
                            <span class="text-muted">Mueve el pin para obtener la dirección automáticamente.</span>
                        @endif
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

    {{-- ═══════════════════════════════════
         SECCIÓN 4: DATOS BANCARIOS Y DISPERSIÓN DE PAGOS (WOMPI)
    ═══════════════════════════════════ --}}
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
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Mapa Leaflet ───────────────────────────────────────
    let safeLat = parseFloat("{{ $company->latitude ?: 5.3086 }}");
    let safeLng = parseFloat("{{ $company->longitude ?: -73.8149 }}");
    if (isNaN(safeLat)) safeLat = 5.3086;
    if (isNaN(safeLng)) safeLng = -73.8149;

    document.getElementById('lat').value = safeLat.toFixed(8);
    document.getElementById('lng').value = safeLng.toFixed(8);

    try {
        delete L.Icon.Default.prototype._getIconUrl;
        L.Icon.Default.mergeOptions({
            iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
            iconUrl:       'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
            shadowUrl:     'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        });

        const map = L.map('map').setView([safeLat, safeLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        let marker = L.marker([safeLat, safeLng], { draggable: true }).addTo(map);
        setTimeout(() => { map.invalidateSize(); }, 400);

        function updateCoords(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(8);
            document.getElementById('lng').value = lng.toFixed(8);
            // Auto-completar la dirección desde el mapa
            fetchAddress(lat, lng);
        }

        function fetchAddress(lat, lng) {
            var display = document.getElementById('address-display');
            var hidden  = document.getElementById('address-hidden');
            if (display) display.innerHTML = '<span class="text-muted"><i class="bi bi-arrow-repeat" style="display:inline-block;animation:spin 1s linear infinite;"></i> Obteniendo dirección...</span>';
            fetch('https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=es')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var a = data.address || {};
                    var label = '';
                    if (a.road)         label += a.road;
                    if (a.house_number) label += ' ' + a.house_number;
                    var city = a.city || a.town || a.municipality || a.village || '';
                    if (city) label += (label ? ', ' : '') + city;
                    if (!label && data.display_name) label = data.display_name.split(',').slice(0,3).join(', ');
                    label = label || (lat.toFixed(4) + ', ' + lng.toFixed(4));
                    if (display) display.textContent = label;
                    if (hidden)  hidden.value = label;
                })
                .catch(function() {
                    var fallback = lat.toFixed(4) + ', ' + lng.toFixed(4);
                    if (display) display.textContent = fallback;
                    if (hidden)  hidden.value = fallback;
                });
        }

        map.on('click', function (e) {
            marker.setLatLng([e.latlng.lat, e.latlng.lng]);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
        marker.on('dragend', function () {
            updateCoords(marker.getLatLng().lat, marker.getLatLng().lng);
        });

    } catch (err) {
        console.error('Error Leaflet:', err);
        document.getElementById('map').innerHTML =
            '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><p>No se pudo cargar el mapa. Verifica tu conexión a internet.</p></div>';
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

    document.getElementById('logoInput').addEventListener('change', function () {
        previewImage(this, 'logoPreview', 'logoPlaceholder');
    });
    document.getElementById('bannerInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const maxBytes = 2 * 1024 * 1024; // 2 MB
        if (file.size > maxBytes) {
            alert('El banner supera el límite de 2 MB permitido por el servidor. Por favor selecciona una imagen de hasta 2 MB.');
            this.value = '';
            document.getElementById('bannerFileInfo').classList.add('d-none');
            return;
        }

        // Mostrar tipo y tamaño
        const typeLabel = file.type.replace('image/', '').toUpperCase();
        const sizeMB    = (file.size / (1024 * 1024)).toFixed(2);
        document.getElementById('bannerFileType').textContent = typeLabel;
        document.getElementById('bannerFileSize').textContent = sizeMB + ' MB';
        document.getElementById('bannerFileInfo').classList.remove('d-none');

        // Preview
        previewImage(this, 'bannerPreview', 'bannerPlaceholder');
    });
});
</script>
@endpush
