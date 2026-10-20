@extends('layouts.app')
@section('title', 'Finalizar Compra - Spotlight Marketplace')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --sl-burgundy: #7F1D3F;
        --sl-dusty-rose: #D98C9A;
        --sl-soft-rose: #F6E7EB;
        --sl-ivory: #FFFCF7;
        --sl-charcoal: #29252A;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #F8F9FA;
        color: var(--sl-charcoal);
    }

    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }

    .checkout-card {
        background-color: var(--sl-ivory);
        border: 1px solid #ECE7E1;
        border-radius: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .step-badge {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--sl-burgundy), var(--sl-dusty-rose));
        color: #ffffff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--sl-dusty-rose) !important;
        box-shadow: 0 0 0 0.25rem rgba(217, 140, 154, 0.25) !important;
    }

    .payment-method-pill {
        border: 2px solid #E2E8F0;
        border-radius: 16px;
        padding: 14px 16px;
        transition: all 0.25s ease;
        background: #ffffff;
        cursor: pointer;
        user-select: none;
    }
    .payment-method-pill:hover {
        border-color: var(--sl-dusty-rose);
        transform: translateY(-2px);
    }
    .payment-method-pill.active {
        border-color: var(--sl-burgundy) !important;
        background: var(--sl-soft-rose) !important;
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.15);
    }
    .credit-card-visual {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.22);
    }

    .btn-wompi-pay {
        background: linear-gradient(135deg, #7F1D3F 0%, #A32852 100%);
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        border-radius: 50rem;
        padding: 14px 24px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(127, 29, 63, 0.35);
    }
    .btn-wompi-pay:hover:not(:disabled) {
        background: linear-gradient(135deg, #641530 0%, #851e41 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(127, 29, 63, 0.45);
    }

    .vendor-badge {
        background: var(--sl-soft-rose);
        color: var(--sl-burgundy);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="py-5" style="min-height: 90vh;">
    <div class="container">
        {{-- Breadcrumb & Title --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Checkout Seguro Marketplace</span>
                <h2 class="fw-bold mb-0 font-outfit" style="color: var(--sl-charcoal); letter-spacing: -0.5px;">
                    <i class="bi bi-shield-check me-2" style="color: var(--sl-burgundy);"></i>Finalizar Pedido
                </h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-white px-3 py-2 rounded-pill shadow-sm border border-light">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.cart.index') }}" class="text-decoration-none text-muted">Carrito</a></li>
                    <li class="breadcrumb-item active fw-bold" style="color: var(--sl-burgundy);" aria-current="page">Checkout Wompi</li>
                </ol>
            </nav>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div id="checkoutAlertBox" class="alert d-none rounded-4 shadow-sm mb-4"></div>

        <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <div class="row g-4">
                
                {{-- COLUMNA IZQUIERDA: Formulario de Comprador, Envío y Métodos --}}
                <div class="col-lg-7 col-xl-8">
                    
                    {{-- PASO 1: Información de Contacto y Facturación --}}
                    <div class="checkout-card p-4 p-md-5 mb-4 shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="step-badge">1</span>
                            <div>
                                <h4 class="fw-bold mb-0 font-outfit" style="color: var(--sl-charcoal);">Información del Comprador</h4>
                                <small class="text-muted">Tus datos para facturación electrónica y confirmación del pedido</small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control rounded-3 py-2" value="{{ old('first_name', $preloadedUser['first_name'] ?? '') }}" placeholder="Ej. Ana María" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control rounded-3 py-2" value="{{ old('last_name', $preloadedUser['last_name'] ?? '') }}" placeholder="Ej. Martínez Gómez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Correo Electrónico <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="email" id="email" class="form-control rounded-end-3 py-2 border-start-0" value="{{ old('email', $preloadedUser['email'] ?? '') }}" placeholder="nombre@correo.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Celular / Teléfono <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 fw-semibold text-muted">+57</span>
                                    <input type="tel" name="phone" id="phone" class="form-control rounded-end-3 py-2 border-start-0" value="{{ old('phone', $preloadedUser['phone'] ?? '') }}" placeholder="300 123 4567" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">Tipo de Documento <span class="text-danger">*</span></label>
                                <select name="doc_type" id="doc_type" class="form-select rounded-3 py-2" required>
                                    <option value="CC" {{ old('doc_type', $preloadedUser['doc_type'] ?? '') === 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía (CC)</option>
                                    <option value="NIT" {{ old('doc_type', $preloadedUser['doc_type'] ?? '') === 'NIT' ? 'selected' : '' }}>NIT / RUT</option>
                                    <option value="CE" {{ old('doc_type', $preloadedUser['doc_type'] ?? '') === 'CE' ? 'selected' : '' }}>Cédula de Extranjería (CE)</option>
                                    <option value="PP" {{ old('doc_type', $preloadedUser['doc_type'] ?? '') === 'PP' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small text-dark">Número de Documento / NIT <span class="text-danger">*</span></label>
                                <input type="text" name="doc_number" id="doc_number" class="form-control rounded-3 py-2" value="{{ old('doc_number', $preloadedUser['doc_number'] ?? '') }}" placeholder="Ej. 1023456789" required>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 2: Dirección de Envío --}}
                    <div class="checkout-card p-4 p-md-5 mb-4 shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="step-badge">2</span>
                            <div>
                                <h4 class="fw-bold mb-0 font-outfit" style="color: var(--sl-charcoal);">Dirección de Entrega</h4>
                                <small class="text-muted">Cobertura regional Ubaté y envíos a toda Colombia</small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">País</label>
                                <input type="text" name="country" class="form-control rounded-3 py-2 bg-light" value="Colombia" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">Departamento <span class="text-danger">*</span></label>
                                <input type="text" name="department" class="form-control rounded-3 py-2" value="{{ old('department', $preloadedUser['department'] ?? 'Cundinamarca') }}" placeholder="Cundinamarca" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">Municipio / Ciudad <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control rounded-3 py-2" value="{{ old('city', $preloadedUser['city'] ?? 'Ubaté') }}" placeholder="Ej. Villa de San Diego de Ubaté" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">Barrio / Sector</label>
                                <input type="text" name="neighborhood" class="form-control rounded-3 py-2" value="{{ old('neighborhood') }}" placeholder="Ej. San José / Centro">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small text-dark">Dirección Física Detallada <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control rounded-3 py-2" value="{{ old('address', $preloadedUser['address'] ?? '') }}" placeholder="Carrera 7 # 8-45" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Complemento (Apto, Torre, Casa)</label>
                                <input type="text" name="complement" class="form-control rounded-3 py-2" value="{{ old('complement') }}" placeholder="Apto 302 / Torre 1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Instrucciones para el Domiciliario</label>
                                <input type="text" name="delivery_notes" class="form-control rounded-3 py-2" value="{{ old('delivery_notes') }}" placeholder="Dejar en portería, timbre 3...">
                            </div>

                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="different_billing_address" id="different_billing_address" value="1" {{ old('different_billing_address') ? 'checked' : '' }} onchange="toggleBillingAddress(this)">
                                    <label class="form-check-label small text-muted" for="different_billing_address">
                                        Usar una dirección de facturación diferente a la dirección de entrega
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-none" id="billingAddressContainer">
                                <label class="form-label fw-semibold small text-dark">Dirección de Facturación</label>
                                <input type="text" name="billing_address" class="form-control rounded-3 py-2" value="{{ old('billing_address') }}" placeholder="Dirección registrada en el RUT o documento">
                            </div>
                        </div>
                    </div>

                    {{-- PASO 3: Selección de Medio de Pago --}}
                    <div class="checkout-card p-4 p-md-5 mb-4 shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="step-badge">3</span>
                            <div>
                                <h4 class="fw-bold mb-0 font-outfit" style="color: var(--sl-charcoal);">Selecciona tu Medio de Pago</h4>
                                <small class="text-muted">Elige pagar con Tarjeta, transferencia bancaria PSE o Código QR</small>
                            </div>
                        </div>

                        <!-- Selector Interactivo de Métodos -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="payment-method-pill active text-center" id="pill-card" onclick="selectPaymentMethod('card')">
                                    <i class="bi bi-credit-card-2-front fs-2 mb-1 d-block" style="color: var(--sl-burgundy);"></i>
                                    <span class="fw-bold small d-block">Tarjeta Débito/Crédito</span>
                                    <span class="text-muted" style="font-size: 0.72rem;">Visa, Mastercard, Amex</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="payment-method-pill text-center" id="pill-pse" onclick="selectPaymentMethod('pse')">
                                    <i class="bi bi-bank fs-2 mb-1 d-block text-primary"></i>
                                    <span class="fw-bold small d-block">PSE</span>
                                    <span class="text-muted" style="font-size: 0.72rem;">Débito bancario en línea</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="payment-method-pill text-center" id="pill-qr" onclick="selectPaymentMethod('qr')">
                                    <i class="bi bi-qr-code-scan fs-2 mb-1 d-block text-warning"></i>
                                    <span class="fw-bold small d-block">Código QR</span>
                                    <span class="text-muted" style="font-size: 0.72rem;">Bancolombia & Nequi</span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="payment_method" id="selected_payment_method" value="card">

                        <!-- PANEL 1: TARJETA DE CRÉDITO / DÉBITO INTERACTIVA -->
                        <div id="payment-panel-card" class="payment-panel">
                            {{-- Tarjeta Visual Interactiva --}}
                            <div class="mb-4 d-flex justify-content-center">
                                <div id="visual-card" class="credit-card-visual shadow-lg rounded-4 p-4 text-white position-relative overflow-hidden" style="width: 100%; max-width: 420px; height: 230px; background: linear-gradient(135deg, #7F1D3F 0%, #D98C9A 100%); transition: all 0.4s ease; border: 1px solid rgba(255,255,255,0.25);">
                                    <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.12); top: -160px; right: -120px;"></div>
                                    
                                    <div class="d-flex justify-content-between align-items-start mb-4 position-relative z-1">
                                        <div class="card-chip bg-warning bg-opacity-75 rounded-2 shadow-sm" style="width: 46px; height: 34px; border: 1px solid rgba(0,0,0,0.15);"></div>
                                        <i class="bi bi-wifi fs-4 opacity-75" style="transform: rotate(90deg);"></i>
                                    </div>

                                    <div class="card-number-display mb-4 position-relative z-1 text-center" style="font-family: 'Courier New', Courier, monospace; font-size: 1.45rem; letter-spacing: 2px;">
                                        <span id="display-number">#### #### #### ####</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-end position-relative z-1">
                                        <div class="card-holder-group">
                                            <div class="text-xs text-white-50 text-uppercase mb-1" style="font-size: 0.65rem;">Titular</div>
                                            <div id="display-name" class="fw-medium text-truncate" style="max-width: 210px; font-size: 0.88rem;">{{ strtoupper($preloadedUser['first_name'] . ' ' . $preloadedUser['last_name']) ?: 'NOMBRE DEL TITULAR' }}</div>
                                        </div>
                                        <div class="card-expiry-group">
                                            <div class="text-xs text-white-50 text-uppercase mb-1" style="font-size: 0.65rem;">Vence</div>
                                            <div id="display-expiry" class="fw-medium" style="font-size: 0.88rem;">MM/AA</div>
                                        </div>
                                        <div class="card-brand">
                                            <i id="display-brand-icon" class="bi bi-credit-card-2-front fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">Nombre en la tarjeta <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="card_name" id="card_name" class="form-control rounded-end-3 py-2 border-start-0" value="{{ old('card_name', trim($preloadedUser['first_name'] . ' ' . $preloadedUser['last_name'])) }}" placeholder="Como figura en el plástico">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">Número de tarjeta <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-credit-card text-muted"></i></span>
                                        <input type="text" name="card_number" id="card_number" class="form-control rounded-end-3 py-2 border-start-0" placeholder="0000 0000 0000 0000" maxlength="19">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-dark">Vencimiento <span class="text-danger">*</span></label>
                                    <input type="text" name="exp_date" id="exp_date" class="form-control rounded-3 py-2 text-center" placeholder="MM/AA" maxlength="5">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-dark">CVV / CVC <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="cvv" id="cvv" class="form-control rounded-start-3 py-2 text-center" placeholder="123" maxlength="4">
                                        <span class="input-group-text bg-white border-start-0"><i class="bi bi-lock text-muted"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-dark">Cuotas</label>
                                    <select name="installments" id="installments" class="form-select rounded-3 py-2">
                                        <option value="1">1 cuota (Sin interés)</option>
                                        <option value="2">2 cuotas</option>
                                        <option value="3">3 cuotas</option>
                                        <option value="6">6 cuotas</option>
                                        <option value="12">12 cuotas</option>
                                        <option value="24">24 cuotas</option>
                                        <option value="36">36 cuotas</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 2: PSE (DÉBITO BANCARIO) -->
                        <div id="payment-panel-pse" class="payment-panel d-none">
                            <div class="alert alert-light border rounded-4 d-flex align-items-center gap-3 p-3 mb-4">
                                <i class="bi bi-bank2 fs-3 text-primary"></i>
                                <div class="small text-muted">
                                    <strong class="text-dark d-block">Pagos Seguros en Línea (PSE)</strong>
                                    Al completar tu pedido serás redirigido a la aplicación / portal de tu banco para autorizar el débito en línea.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">Selecciona tu Banco o Entidad Financiera <span class="text-danger">*</span></label>
                                    <select name="pse_bank" id="pse_bank" class="form-select rounded-3 py-2">
                                        <option value="Bancolombia" selected>Bancolombia</option>
                                        <option value="Nequi">Nequi</option>
                                        <option value="Davivienda">Davivienda</option>
                                        <option value="Daviplata">Daviplata</option>
                                        <option value="Banco de Bogotá">Banco de Bogotá</option>
                                        <option value="BBVA Colombia">BBVA Colombia</option>
                                        <option value="Banco de Occidente">Banco de Occidente</option>
                                        <option value="Scotiabank Colpatria">Scotiabank Colpatria</option>
                                        <option value="Banco Caja Social">Banco Caja Social</option>
                                        <option value="Banco Popular">Banco Popular</option>
                                        <option value="Banco AV Villas">Banco AV Villas</option>
                                        <option value="Banco Falabella">Banco Falabella</option>
                                        <option value="Banco Itaú">Banco Itaú</option>
                                        <option value="Dale!">Dale!</option>
                                        <option value="Lulo Bank">Lulo Bank</option>
                                        <option value="RappiPay">RappiPay</option>
                                        <option value="Ualá">Ualá</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark">Tipo de Persona <span class="text-danger">*</span></label>
                                    <select name="pse_person_type" id="pse_person_type" class="form-select rounded-3 py-2">
                                        <option value="natural" selected>Persona Natural</option>
                                        <option value="juridica">Persona Jurídica (Empresa)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark">Correo Registrado en PSE <span class="text-danger">*</span></label>
                                    <input type="email" name="pse_email" id="pse_email" class="form-control rounded-3 py-2" value="{{ old('pse_email', $preloadedUser['email'] ?? '') }}" placeholder="correo@ejemplo.com">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-dark">Tipo Documento</label>
                                    <select name="pse_doc_type" id="pse_doc_type" class="form-select rounded-3 py-2">
                                        <option value="CC" selected>Cédula de Ciudadanía (CC)</option>
                                        <option value="NIT">NIT</option>
                                        <option value="CE">Cédula Extranjería (CE)</option>
                                        <option value="PP">Pasaporte</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small text-dark">Número de Documento</label>
                                    <input type="text" name="pse_doc_number" id="pse_doc_number" class="form-control rounded-3 py-2" value="{{ old('pse_doc_number', $preloadedUser['doc_number'] ?? '') }}" placeholder="Ej. 1023456789">
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 3: CÓDIGO QR BANCOLOMBIA & NEQUI -->
                        <div id="payment-panel-qr" class="payment-panel d-none">
                            <div class="text-center p-4 rounded-4" style="background: linear-gradient(135deg, #FFF9E6 0%, #FFFDF7 100%); border: 1.5px dashed #F59E0B;">
                                <div class="d-inline-flex p-3 bg-white rounded-circle shadow-sm mb-3">
                                    <i class="bi bi-qr-code-scan fs-1" style="color: #D97706;"></i>
                                </div>
                                <h5 class="fw-bold font-outfit text-dark mb-1">Pago Inmediato con Código QR</h5>
                                <p class="text-muted small mb-3">Al presionar "Generar Código QR", crearemos un código QR dinámico de alta definición por el valor exacto de:</p>
                                <div class="fs-2 fw-bold font-outfit mb-3" style="color: var(--sl-burgundy);">
                                    ${{ number_format($settlementData['summary']['grand_total'], 0, ',', '.') }} COP
                                </div>
                                <div class="d-flex justify-content-center gap-2 flex-wrap mb-2">
                                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill text-success me-1"></i> App Bancolombia</span>
                                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill text-success me-1"></i> App Nequi</span>
                                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill text-success me-1"></i> Bancolombia A la Mano</span>
                                </div>
                                <small class="text-muted d-block mt-2">Podrás escanearlo directamente desde tu teléfono móvil y autorizar el pago de inmediato.</small>
                            </div>
                        </div>

                        <div class="p-3 rounded-4 mt-4 d-flex align-items-center justify-content-between" style="background: #F8FAFC; border: 1px dashed #CBD5E1;">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-shield-check fs-2 text-success"></i>
                                <div class="small text-muted lh-sm">
                                    <span class="fw-semibold text-dark d-block">Seguridad Cifrada Bancaria</span>
                                    Transacciones cifradas bajo normativa PCI-DSS Nivel 1 y dispersión directa a comercios.
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1.5 rounded-pill small">
                                <i class="bi bi-lock-fill me-1"></i> 100% Protegido
                            </span>
                        </div>
                    </div>

                    {{-- PASO 4: Términos y Condiciones + Habeas Data --}}
                    <div class="checkout-card p-4 p-md-5 shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="step-badge">4</span>
                            <div>
                                <h4 class="fw-bold mb-0 font-outfit" style="color: var(--sl-charcoal);">Autorizaciones Legales</h4>
                                <small class="text-muted">Cumplimiento regulatorio Ley 1581 de 2012</small>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" value="1" required>
                            <label class="form-check-label small text-muted" for="terms_accepted">
                                He leído y acepto los <a href="#" class="fw-semibold text-decoration-none" style="color: var(--sl-burgundy);">Términos y Condiciones</a> del marketplace Spotlight y las políticas de despacho por comercio. <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="privacy_accepted" id="privacy_accepted" value="1" required>
                            <label class="form-check-label small text-muted" for="privacy_accepted">
                                Autorizo de forma expresa el <a href="#" class="fw-semibold text-decoration-none" style="color: var(--sl-burgundy);">Tratamiento de Datos Personales</a> (Habeas Data) para la coordinación logística y emisión de comprobantes. <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- COLUMNA DERECHA: Resumen de Compra Sticky con Desglose Multivendedor --}}
                <div class="col-lg-5 col-xl-4">
                    <div class="checkout-card p-4 p-xl-4 sticky-top shadow-sm" style="top: 2rem;">
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                            <h5 class="fw-bold mb-0 font-outfit" style="color: var(--sl-charcoal);">Resumen del Pedido</h5>
                            <span class="badge rounded-pill fw-bold" style="background: var(--sl-soft-rose); color: var(--sl-burgundy);">
                                {{ count($settlementData['vendors']) }} {{ count($settlementData['vendors']) === 1 ? 'comercio' : 'comercios' }}
                            </span>
                        </div>

                        {{-- Lista de Ítems Agrupados por Comercio --}}
                        <div class="order-items-scroll mb-4" style="max-height: 380px; overflow-y: auto;">
                            @foreach($settlementData['vendors'] as $companyId => $vendor)
                                <div class="mb-3 p-3 rounded-4 bg-white border" style="border-color: #ECE7E1 !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 28px; height: 28px; background: linear-gradient(135deg, #7F1D3F, #D98C9A); font-size: 0.75rem;">
                                                <i class="bi bi-shop"></i>
                                            </div>
                                            <span class="fw-bold text-dark text-truncate small">{{ $vendor['company']->name ?? 'Comercio Local' }}</span>
                                        </div>
                                        <span class="vendor-badge">Vendedor</span>
                                    </div>

                                    @foreach($vendor['items'] as $item)
                                        <div class="d-flex align-items-center justify-content-between gap-2 py-1">
                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" class="rounded-2 object-fit-cover flex-shrink-0" style="width: 38px; height: 38px;">
                                                <div class="overflow-hidden">
                                                    <div class="fw-semibold text-dark text-truncate small" style="font-size: 0.82rem;">{{ $item['product']->name }}</div>
                                                    <span class="text-muted" style="font-size: 0.72rem;">Cant: {{ $item['quantity'] }} &times; ${{ number_format($item['product']->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                            <span class="fw-bold text-dark small flex-shrink-0">${{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach

                                    <div class="mt-2 pt-2 border-top d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                        <span>Envío asignado a este comercio:</span>
                                        <span class="fw-semibold text-dark">
                                            @if($vendor['shipping'] > 0)
                                                ${{ number_format($vendor['shipping'], 0, ',', '.') }}
                                            @else
                                                <span class="text-success fw-bold">¡Gratis!</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Desglose Financiero Total --}}
                        <div class="p-3 rounded-4 mb-4" style="background: #FAF7F2; border: 1px solid #EBE4DC;">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Subtotal Productos:</span>
                                <span class="fw-semibold text-dark">${{ number_format($settlementData['summary']['products_subtotal'], 0, ',', '.') }} COP</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Envío total consolidado:</span>
                                @if($settlementData['summary']['shipping_total'] > 0)
                                    <span class="fw-semibold text-dark">${{ number_format($settlementData['summary']['shipping_total'], 0, ',', '.') }} COP</span>
                                @else
                                    <span class="text-success fw-bold">¡Gratis!</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Impuestos y tasas:</span>
                                <span class="fw-medium">Incluidos (19% IVA)</span>
                            </div>
                            <hr class="my-2 border-secondary opacity-25">
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="fw-bold text-dark fs-6">Total a Pagar:</span>
                                <span class="fw-black fs-3 font-outfit" style="color: var(--sl-burgundy);">
                                    ${{ number_format($settlementData['summary']['grand_total'], 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">COP</span>
                                </span>
                            </div>
                        </div>

                        {{-- Botón de Pago Dinámico según Método Seleccionado --}}
                        <button type="submit" id="btnSubmitPayment" class="btn btn-wompi-pay w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-credit-card-2-front-fill" id="btnSubmitIcon"></i>
                            <span id="btnSubmitText">Pagar ${{ number_format($settlementData['summary']['grand_total'], 0, ',', '.') }} con Tarjeta</span>
                        </button>

                        <div class="text-center mb-3">
                            <a href="{{ route('user.cart.index') }}" class="btn btn-link btn-sm text-decoration-none text-muted">
                                <i class="bi bi-arrow-left me-1"></i> Modificar productos del carrito
                            </a>
                        </div>

                        {{-- Sellos de Confianza y Pasarela --}}
                        <div class="pt-3 border-top text-center">
                            <div class="d-flex align-items-center justify-content-center gap-3 opacity-75 mb-2">
                                <i class="bi bi-shield-check fs-4 text-success"></i>
                                <span class="small text-muted fw-medium">Transacción Cifrada con Firma SHA-256</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                <span class="badge bg-light text-muted border">PSE</span>
                                <span class="badge bg-light text-muted border">Visa</span>
                                <span class="badge bg-light text-muted border">Mastercard</span>
                                <span class="badge bg-light text-muted border">Amex</span>
                                <span class="badge bg-light text-muted border">Nequi</span>
                                <span class="badge bg-light text-muted border">Bancolombia QR</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Script Oficial de Wompi Widget (para fallback o pago nativo) --}}
<script type="text/javascript" src="https://checkout.wompi.co/widget.js"></script>

<script>
function toggleBillingAddress(checkbox) {
    const container = document.getElementById('billingAddressContainer');
    if (checkbox.checked) {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
}

function selectPaymentMethod(method) {
    document.getElementById('selected_payment_method').value = method;

    // Actualizar selector visual (pills)
    document.querySelectorAll('.payment-method-pill').forEach(pill => pill.classList.remove('active'));
    const activePill = document.getElementById('pill-' + method);
    if (activePill) activePill.classList.add('active');

    // Alternar paneles de pago
    document.querySelectorAll('.payment-panel').forEach(panel => panel.classList.add('d-none'));
    const activePanel = document.getElementById('payment-panel-' + method);
    if (activePanel) activePanel.classList.remove('d-none');

    // Cambiar validación requerida de campos
    const cardInputs = ['card_name', 'card_number', 'exp_date', 'cvv'];
    const pseInputs = ['pse_bank', 'pse_email'];

    cardInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.required = (method === 'card');
    });

    pseInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.required = (method === 'pse');
    });

    // Actualizar texto e icono del botón
    const btnText = document.getElementById('btnSubmitText');
    const btnIcon = document.getElementById('btnSubmitIcon');
    const totalFormatted = "${{ number_format($settlementData['summary']['grand_total'], 0, ',', '.') }}";

    if (method === 'card') {
        btnIcon.className = 'bi bi-credit-card-2-front-fill';
        btnText.textContent = `Pagar ${totalFormatted} con Tarjeta`;
    } else if (method === 'pse') {
        btnIcon.className = 'bi bi-bank';
        btnText.textContent = `Continuar a mi Banco (PSE)`;
    } else if (method === 'qr') {
        btnIcon.className = 'bi bi-qr-code';
        btnText.textContent = `Generar Código QR (${totalFormatted})`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const btnSubmit = document.getElementById('btnSubmitPayment');
    const alertBox = document.getElementById('checkoutAlertBox');

    // Inicializar estado requerido por defecto (tarjeta)
    selectPaymentMethod('card');

    // Sincronización en vivo de la Tarjeta Interactiva
    const cardName = document.getElementById('card_name');
    const cardNumber = document.getElementById('card_number');
    const expDate = document.getElementById('exp_date');
    const cvv = document.getElementById('cvv');

    const displayName = document.getElementById('display-name');
    const displayNumber = document.getElementById('display-number');
    const displayExpiry = document.getElementById('display-expiry');
    const visualCard = document.getElementById('visual-card');
    const brandIcon = document.getElementById('display-brand-icon');

    if (cardName) {
        cardName.addEventListener('input', (e) => {
            displayName.textContent = e.target.value.toUpperCase() || 'NOMBRE DEL TITULAR';
        });
    }

    if (cardNumber) {
        cardNumber.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            e.target.value = formatted;
            displayNumber.textContent = formatted || '#### #### #### ####';

            // Cambio de gradiente y logotipo según la franquicia detectada
            if (value.startsWith('4')) {
                visualCard.style.background = 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)';
                brandIcon.className = 'bi bi-credit-card-2-front fs-2';
            } else if (value.startsWith('5')) {
                visualCard.style.background = 'linear-gradient(135deg, #7F1D3F 0%, #A52A4A 100%)';
                brandIcon.className = 'bi bi-credit-card fs-2';
            } else if (value.startsWith('3')) {
                visualCard.style.background = 'linear-gradient(135deg, #064e3b 0%, #0d9488 100%)';
                brandIcon.className = 'bi bi-credit-card-2-front fs-2';
            } else {
                visualCard.style.background = 'linear-gradient(135deg, #7F1D3F 0%, #D98C9A 100%)';
                brandIcon.className = 'bi bi-credit-card-2-front fs-2';
            }
        });
    }

    if (expDate) {
        expDate.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
            displayExpiry.textContent = value || 'MM/AA';
        });
    }

    if (cvv) {
        cvv.addEventListener('focus', () => {
            visualCard.style.transform = 'scale(1.02) rotateY(10deg)';
        });
        cvv.addEventListener('blur', () => {
            visualCard.style.transform = 'scale(1) rotateY(0deg)';
        });
    }

    function showAlert(message, type = 'danger') {
        alertBox.className = `alert alert-${type} alert-dismissible fade show rounded-4 shadow-sm mb-4`;
        alertBox.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-${type === 'danger' ? 'exclamation-triangle-fill' : 'info-circle-fill'} fs-5"></i>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertBox.classList.remove('d-none');
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validaciones cliente básicas
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const method = document.getElementById('selected_payment_method').value;
        const originalBtnHtml = btnSubmit.innerHTML;
        btnSubmit.disabled = true;

        if (method === 'card') {
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Procesando pago seguro con tarjeta...`;
        } else if (method === 'pse') {
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Conectando con tu banco...`;
        } else if (method === 'qr') {
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Generando código QR dinámico...`;
        } else {
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Conectando con pasarela...`;
        }

        const formData = new FormData(form);

        fetch("{{ route('checkout.process') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.error || data.message || 'Error al procesar el pedido con la pasarela.');
                }
                return data;
            });
        })
        .then(result => {
            if (result.success && result.data && result.data.redirect_url) {
                // Redirección directa para Tarjeta, PSE o QR
                window.location.href = result.data.redirect_url;
            } else if (result.success && result.data && result.data.widget_config) {
                // Modal nativo de Wompi en caso de fallback
                try {
                    const checkout = new WidgetCheckout(result.data.widget_config);
                    checkout.open(function(res) {
                        const transaction = res.transaction;
                        const txId = transaction && transaction.id ? transaction.id : '';
                        const redirectUrl = result.data.redirect_url + (txId ? `?id=${txId}` : '');
                        window.location.href = redirectUrl;
                    });
                } catch (widgetError) {
                    window.location.href = result.data.redirect_url;
                }
            } else {
                throw new Error("Respuesta inesperada de la pasarela.");
            }
        })
        .catch(err => {
            console.error("Checkout Error:", err);
            showAlert(err.message || "Ocurrió un error al procesar el pago. Por favor intenta nuevamente.", 'danger');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalBtnHtml;
        });
    });
});
</script>
@endsection
