@extends('layouts.app')
@section('title', 'Soporte & Legal — Spotlight')

@push('styles')
<link href="{{ asset('css/public.css') }}" rel="stylesheet">
@endpush

@section('content')
{{-- Navbar de Soporte & Legal --}}
<header class="sticky-top sl-legal-header py-3">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight" class="sl-legal-logo">
            <span class="fs-4 sl-legal-brand-title">SPOTLIGHT</span>
            <span class="badge badge-purple ms-1 d-none d-sm-inline-block sl-legal-brand-badge">
                <span class="lang-en">SUPPORT & LEGAL</span><span class="lang-es">SOPORTE & LEGAL</span>
            </span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('home') }}" class="nav-pill-custom d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-house"></i>
                <span class="lang-en">Home</span><span class="lang-es">Inicio</span>
            </a>
            @auth
                @php $dash = match(auth()->user()->role) { 'admin'=>'admin.dashboard','business'=>'business.dashboard',default=>'user.dashboard'}; @endphp
                <a href="{{ route($dash) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                    <span class="lang-en">My Panel</span><span class="lang-es">Mi Panel</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-pill-light px-3 fw-bold">
                    <span class="lang-en">Log in</span><span class="lang-es">Ingresar</span>
                </a>
            @endauth

            <button id="btnSwitchLang" type="button" class="btn btn-sm btn-pill-light fw-bold px-3 js-switch-lang">
                <i class="bi bi-translate me-1"></i> EN / ES
            </button>
        </div>
    </div>
</header>

<main class="py-5 sl-legal-main">
    <div class="container">

        {{-- Encabezado Principal --}}
        <div class="text-center mb-5 mx-auto" style="max-width: 720px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3 sl-legal-hero-pill">
                <i class="bi bi-shield-check"></i>
                <span class="lang-en">TRANSPARENCY, COMPLIANCE & USER TRUST</span>
                <span class="lang-es">TRANSPARENCIA, NORMATIVA Y CONFIANZA</span>
            </div>
            <h1 class="display-6 fw-bold text-dark mb-2" style="font-family: var(--sl-font-display, 'Outfit', sans-serif); letter-spacing: -0.5px;">
                <span class="lang-en">Support & Legal Center</span>
                <span class="lang-es">Centro de Soporte & Legal</span>
            </h1>
            <p class="text-muted" style="font-size: 0.95rem;">
                <span class="lang-en">Everything you need to know about privacy, warranties, refunds, terms of use, and resolution of inquiries on Spotlight.</span>
                <span class="lang-es">Conoce las políticas, términos de uso, garantías, devoluciones y radicación de PQRS que respaldan a compradores y comercios en Spotlight.</span>
            </p>
        </div>

        @if(session('pqrs_success'))
            <div class="alert alert-success border-0 rounded-4 shadow-sm p-4 mb-4 d-flex align-items-start gap-3" style="background: rgba(16, 185, 129, 0.12); color: #065f46;">
                <i class="bi bi-check-circle-fill fs-3 text-success flex-shrink-0"></i>
                <div>
                    <h5 class="fw-bold mb-1">
                        <span class="lang-en">Ticket Registered Successfully</span>
                        <span class="lang-es">Solicitud Radicada Exitosamente</span>
                    </h5>
                    <p class="mb-0 text-sm">{{ session('pqrs_success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 mb-4 d-flex align-items-start gap-3" style="background: rgba(245, 158, 11, 0.12); color: #92400e;">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning flex-shrink-0"></i>
                <div>
                    <h5 class="fw-bold mb-1">
                        <span class="lang-en">Please review the form</span>
                        <span class="lang-es">Por favor revisa el formulario</span>
                    </h5>
                    <p class="mb-0 text-sm">{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <div class="row g-4">
            {{-- Navegación Lateral de Secciones --}}
            <div class="col-lg-4 col-md-5">
                <div class="sl-legal-nav-card">
                    <div class="sl-legal-nav-title">
                        <span class="lang-en">LEGAL DIRECTORY</span>
                        <span class="lang-es">DIRECTORIO LEGAL</span>
                    </div>

                    <a href="{{ route('public.legal', 'privacidad') }}" class="sl-legal-nav-btn {{ $section === 'privacidad' ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>
                            <span class="lang-en">Privacy Policy</span>
                            <span class="lang-es">Política de Privacidad</span>
                        </span>
                    </a>

                    <a href="{{ route('public.legal', 'terminos') }}" class="sl-legal-nav-btn {{ $section === 'terminos' ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>
                            <span class="lang-en">Terms of Service</span>
                            <span class="lang-es">Términos del Servicio</span>
                        </span>
                    </a>

                    <a href="{{ route('public.legal', 'cookies') }}" class="sl-legal-nav-btn {{ $section === 'cookies' ? 'active' : '' }}">
                        <i class="bi bi-cookie"></i>
                        <span>
                            <span class="lang-en">Cookie Policy</span>
                            <span class="lang-es">Política de cookies</span>
                        </span>
                    </a>

                    <a href="{{ route('public.legal', 'devoluciones') }}" class="sl-legal-nav-btn {{ $section === 'devoluciones' ? 'active' : '' }}">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>
                            <span class="lang-en">Returns & Refunds</span>
                            <span class="lang-es">Devoluciones y reembolsos</span>
                        </span>
                    </a>

                    <a href="{{ route('public.legal', 'pqrs') }}" class="sl-legal-nav-btn {{ $section === 'pqrs' ? 'active' : '' }}">
                        <i class="bi bi-headset"></i>
                        <span>
                            <span class="lang-en">PQRS & Warranties</span>
                            <span class="lang-es">PQRS & Garantías</span>
                        </span>
                    </a>

                    <hr class="my-3 border-sl-subtle">

                    <div class="p-3 rounded-3 bg-sl-primary-soft">
                        <div class="d-flex align-items-center gap-2 mb-1 text-sl-primary fw-bold small">
                            <i class="bi bi-patch-question-fill fs-5"></i>
                            <span class="lang-en">Need direct assistance?</span>
                            <span class="lang-es">¿Requieres atención directa?</span>
                        </div>
                        <p class="text-muted small mb-2">
                            <span class="lang-en">Our digital village support desk is available to assist you via email or online assistant.</span>
                            <span class="lang-es">Nuestro canal de soporte de la aldea está disponible para guiarte en todo momento.</span>
                        </p>
                        <a href="mailto:soporte@spotlight.com" class="text-sl-primary fw-bold text-decoration-none small d-flex align-items-center gap-1">
                            <i class="bi bi-envelope"></i> soporte@spotlight.com
                        </a>
                    </div>
                </div>
            </div>

            {{-- Contenido de la Sección Seleccionada --}}
            <div class="col-lg-8 col-md-7">
                <div class="sl-legal-content-card">

                    {{-- 1. POLÍTICA DE PRIVACIDAD --}}
                    @if($section === 'privacidad')
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-sl-subtle">
                            <div class="sl-legal-icon-box">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <div>
                                <span class="badge badge-purple mb-1 sl-legal-pill-badge">LEY 1581 DE 2012 / LFPDPPP</span>
                                <h3 class="fw-bold text-dark mb-0">
                                    <span class="lang-en">Privacy Policy & Habeas Data</span>
                                    <span class="lang-es">Política de Privacidad y Habeas Data</span>
                                </h3>
                            </div>
                        </div>

                        <div class="sl-legal-block">
                            <h5>1. Responsable del Tratamiento</h5>
                            <p>
                                <strong>Spotlight — Aldea Digital</strong>, con domicilio principal en el municipio de Villa de San Diego de Ubaté, Cundinamarca, Colombia, es el responsable del tratamiento de los datos personales recolectados a través de esta plataforma web en cumplimiento de la <strong>Ley Estatutaria 1581 de 2012</strong>, su Decreto Reglamentario 1377 de 2013 y demás normatividad aplicable.
                            </p>
                        </div>

                        <div class="sl-legal-block">
                            <h5>2. Finalidades de la Recolección</h5>
                            <ul class="mb-0">
                                <li>Gestionar el registro, autenticación y administración de perfiles de compradores y comercios en la Aldea Digital.</li>
                                <li>Procesar órdenes de compra, despacho logístico, generación de guías y facturación electrónica DIAN a través de medios autorizados.</li>
                                <li>Permitir la comunicación directa y transparente entre comerciantes locales y compradores a través del sistema de mensajería interna.</li>
                                <li>Prevenir fraudes, mitigar incidentes de seguridad informática y cumplir requerimientos de autoridades judiciales y tributarias.</li>
                            </ul>
                        </div>

                        <div class="sl-legal-block">
                            <h5>3. Derechos del Titular de los Datos</h5>
                            <p class="mb-2">Como titular de datos personales registrados en Spotlight, tienes derecho a:</p>
                            <ul class="mb-0">
                                <li><strong>Conocer, actualizar y rectificar</strong> tus datos personales frente a información inexacta, incompleta o fraccionada.</li>
                                <li><strong>Solicitar prueba</strong> de la autorización otorgada para el tratamiento de tus datos.</li>
                                <li><strong>Revocar la autorización</strong> o solicitar la supresión de datos cuando no medie un deber legal o contractual de conservarlos.</li>
                                <li><strong>Acceder de forma gratuita</strong> a tus datos personales que hayan sido objeto de tratamiento.</li>
                            </ul>
                        </div>

                        <div class="sl-legal-block">
                            <h5>4. Seguridad de la Información</h5>
                            <p class="mb-0">
                                Spotlight implementa controles técnicos, encriptación en tránsito (HTTPS/TLS), directivas de seguridad para mitigar vulnerabilidades y mecanismos de aislamiento para proteger la confidencialidad e integridad de la información comercial y personal.
                            </p>
                        </div>

                    {{-- 2. TÉRMINOS DEL SERVICIO --}}
                    @elseif($section === 'terminos')
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-sl-subtle">
                            <div class="sl-legal-icon-box">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <div>
                                <span class="badge badge-purple mb-1 sl-legal-pill-badge">CONDICIONES GENERALES</span>
                                <h3 class="fw-bold text-dark mb-0">
                                    <span class="lang-en">Terms of Service</span>
                                    <span class="lang-es">Términos del Servicio</span>
                                </h3>
                            </div>
                        </div>

                        <div class="sl-legal-block">
                            <h5>1. Naturaleza de la Plataforma</h5>
                            <p>
                                Spotlight es un ecosistema digital y marketplace que visibiliza, conecta y facilita transacciones comerciales entre pequeños y medianos comerciantes, artesanos y productores locales con compradores regionales e internacionales.
                            </p>
                        </div>

                        <div class="sl-legal-block">
                            <h5>2. Roles y Cuentas de Usuario</h5>
                            <ul class="mb-0">
                                <li><strong>Compradores / Consumidores:</strong> Usuarios registrados para explorar la vitrina comercial, adquirir productos, gestionar envíos y calificar su experiencia de compra.</li>
                                <li><strong>Comercios Informales / Emprendimientos:</strong> Negocios en fase de crecimiento con acceso a vitrina, control de inventario y tutoriales de formalización empresarial.</li>
                                <li><strong>Comercios Formales Verificados:</strong> Empresas con Registro Único Tributario (RUT) y habilitación para emisión de Facturación Electrónica DIAN.</li>
                            </ul>
                        </div>

                        <div class="sl-legal-block">
                            <h5>3. Responsabilidad sobre Productos y Precios</h5>
                            <p class="mb-0">
                                Cada comercio es responsable de la veracidad de la descripción, precios, calidad, disponibilidad física e idoneidad de los productos ofertados en su catálogo. Spotlight proporciona la infraestructura tecnológica de conexión y validación de comercios.
                            </p>
                        </div>

                        <div class="sl-legal-block">
                            <h5>4. Métodos de Pago y Transacciones</h5>
                            <p class="mb-0">
                                Los pagos electrónicos son procesados mediante pasarelas de pago seguras (Wompi para Colombia y Mercado Pago para transacciones internacionales/México), sujetas a validaciones antifraude y cifrado bancario de nivel institucional.
                            </p>
                        </div>

                    {{-- 3. POLÍTICA DE COOKIES --}}
                    @elseif($section === 'cookies')
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-sl-subtle">
                            <div class="sl-legal-icon-box">
                                <i class="bi bi-cookie"></i>
                            </div>
                            <div>
                                <span class="badge badge-purple mb-1 sl-legal-pill-badge">GESTIÓN DE COOKIES</span>
                                <h3 class="fw-bold text-dark mb-0">
                                    <span class="lang-en">Cookie Policy & Tracking</span>
                                    <span class="lang-es">Política de Cookies</span>
                                </h3>
                            </div>
                        </div>

                        <div class="sl-legal-block">
                            <h5>1. ¿Qué son las Cookies?</h5>
                            <p>
                                Las cookies son pequeños archivos de datos que se almacenan de forma segura en tu navegador cuando visitas Spotlight. Su objetivo es recordar tus preferencias de navegación, mantener la seguridad de tu sesión y permitir el funcionamiento eficiente de la plataforma.
                            </p>
                        </div>

                        <div class="sl-legal-block">
                            <h5>2. Categorías de Cookies en la Plataforma</h5>
                            <p class="mb-3">
                                En Spotlight protegemos la privacidad de nuestra comunidad. Solo implementamos cookies y mecanismos de almacenamiento indispensables para la operación del sitio:
                            </p>
                            <ul class="mb-0">
                                <li class="mb-2">
                                    <strong>Cookies Técnicas y de Seguridad:</strong> Esenciales para autenticar sesiones de usuarios, validar transacciones de compra y prevenir ataques informáticos contra las cuentas y comercios.
                                </li>
                                <li class="mb-2">
                                    <strong>Cookies de Preferencias y Funcionalidad:</strong> Almacenan opciones elegidas por el usuario, como el idioma de visualización de la plataforma (ES / EN) y el contenido de tu carrito de compras activo.
                                </li>
                                <li class="mb-0">
                                    <strong>Cookies de Gestión de Privacidad:</strong> Guardan de manera anónima tu elección respecto al consentimiento de navegación en el portal.
                                </li>
                            </ul>
                        </div>

                        <div class="sl-legal-block">
                            <h5>3. Privacidad y Control del Usuario</h5>
                            <p class="mb-3">
                                No compartimos identificadores ni cookies con redes publicitarias de terceros ni realizamos perfilamiento comercial invasivo. Puedes restablecer tus preferencias o retirar tu consentimiento en cualquier momento mediante el siguiente botón:
                            </p>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" onclick="localStorage.removeItem('VS_COOKIE_CONSENT'); location.reload();">
                                <i class="bi bi-sliders me-1"></i> Restablecer Preferencias de Cookies
                            </button>
                        </div>

                    {{-- 4. DEVOLUCIONES Y REEMBOLSOS --}}
                    @elseif($section === 'devoluciones')
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-sl-subtle">
                            <div class="sl-legal-icon-box">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </div>
                            <div>
                                <span class="badge badge-purple mb-1 sl-legal-pill-badge">LEY 1480 DE 2011 (ESTATUTO DEL CONSUMIDOR)</span>
                                <h3 class="fw-bold text-dark mb-0">
                                    <span class="lang-en">Returns, Right of Withdrawal & Refunds</span>
                                    <span class="lang-es">Devoluciones y Reembolsos</span>
                                </h3>
                            </div>
                        </div>

                        <div class="sl-legal-block">
                            <h5>1. Derecho de Retracto (Compras no presenciales)</h5>
                            <p>
                                De conformidad con el artículo 47 de la <strong>Ley 1480 de 2011</strong> (Estatuto del Consumidor en Colombia), el comprador tiene derecho a retractarse de la compra dentro de los <strong>cinco (5) días hábiles</strong> siguientes a la entrega del producto. El producto debe devolverse en las mismas condiciones en que fue recibido, sin uso, en su empaque original y con todas sus etiquetas y accesorios.
                            </p>
                        </div>

                        <div class="sl-legal-block">
                            <h5>2. Proceso de Devolución</h5>
                            <div class="d-flex flex-column gap-3 mt-3">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="sl-legal-step-num">1</span>
                                    <div>
                                        <div class="fw-bold text-dark">Radicación de la solicitud</div>
                                        <p class="text-muted small mb-0">Ingresa a la sección de PQRS & Garantías o contacta al comercio adjuntando tu número de orden y evidencia fotográfica del producto.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <span class="sl-legal-step-num">2</span>
                                    <div>
                                        <div class="fw-bold text-dark">Validación y Guía de Retorno</div>
                                        <p class="text-muted small mb-0">El comercio verifica las condiciones y coordina la recolección del producto mediante el operador logístico asignado.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <span class="sl-legal-step-num">3</span>
                                    <div>
                                        <div class="fw-bold text-dark">Reembolso del Dinero</div>
                                        <p class="text-muted small mb-0">Una vez recibido y verificado el producto, se efectúa la devolución del dinero por el mismo medio de pago dentro de los siguientes quince (15) días calendario.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sl-legal-block">
                            <h5>3. Excepciones al Retracto</h5>
                            <p class="mb-1 text-muted small">No procede el derecho de retracto en los siguientes casos previstos por ley:</p>
                            <ul class="mb-0">
                                <li>Bienes confeccionados conforme a las especificaciones del consumidor o claramente personalizados (artesanías sobre medida).</li>
                                <li>Bienes que por su naturaleza puedan deteriorarse o caducar con rapidez (alimentos frescos o perecederos).</li>
                                <li>Bienes de uso personal, higiene o prendas íntimas una vez desprecintadas.</li>
                            </ul>
                        </div>

                    {{-- 5. PQRS & GARANTÍAS --}}
                    @elseif($section === 'pqrs')
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-sl-subtle">
                            <div class="sl-legal-icon-box">
                                <i class="bi bi-headset"></i>
                            </div>
                            <div>
                                <span class="badge badge-purple mb-1 sl-legal-pill-badge">ATENCIÓN AL USUARIO</span>
                                <h3 class="fw-bold text-dark mb-0">
                                    <span class="lang-en">PQRS & Warranties Portal</span>
                                    <span class="lang-es">Portal de PQRS & Garantías</span>
                                </h3>
                            </div>
                        </div>

                        <div class="sl-legal-block">
                            <h5>Política de Garantías Legales</h5>
                            <p class="mb-0">
                                Todos los productos comercializados en Spotlight cuentan con la garantía legal mínima establecida por el Estatuto del Consumidor contra defectos de fabricación o fallas de funcionamiento. Los comercios están obligados a responder por la reparación, cambio o reintegro según corresponda.
                            </p>
                        </div>

                        <div class="sl-legal-block">
                            <h5>Plazos de Respuesta</h5>
                            <p class="mb-0">
                                Las peticiones, quejas, reclamos y sugerencias serán atendidas en un plazo máximo de <strong>quince (15) días hábiles</strong> posteriores a su radicación formal.
                            </p>
                        </div>

                        {{-- Formulario de Radicación de PQRS --}}
                        <div class="mt-4 pt-3 border-top border-sl-subtle">
                            <h4 class="fw-bold text-dark mb-3">
                                <i class="bi bi-pencil-square text-sl-primary me-1"></i>
                                <span class="lang-en">File a Request, Claim or Warranty</span>
                                <span class="lang-es">Radicar Petición, Queja, Reclamo o Garantía</span>
                            </h4>

                            <form action="{{ route('public.legal.pqrs') }}" method="POST" class="row g-3">
                                @csrf

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Tipo de Solicitud *</label>
                                    <select name="type" class="form-select rounded-3" required>
                                        <option value="peticion">Petición</option>
                                        <option value="queja">Queja</option>
                                        <option value="reclamo">Reclamo</option>
                                        <option value="garantia">Solicitud de Garantía</option>
                                        <option value="sugerencia">Sugerencia</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Número de Pedido / Guía (Opcional)</label>
                                    <input type="text" name="order_number" class="form-control rounded-3" placeholder="Ej: SPL-2026-000123" value="{{ old('order_number') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Nombre Completo *</label>
                                    <input type="text" name="name" class="form-control rounded-3" placeholder="Tu nombre" required value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Correo Electrónico de Contacto *</label>
                                    <input type="email" name="email" class="form-control rounded-3" placeholder="nombre@correo.com" required value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Teléfono / WhatsApp</label>
                                    <input type="text" name="phone" class="form-control rounded-3" placeholder="Ej: 3101234567" value="{{ old('phone') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Asunto *</label>
                                    <input type="text" name="subject" class="form-control rounded-3" placeholder="Resumen del caso" required value="{{ old('subject') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Descripción Detallada *</label>
                                    <textarea name="message" rows="4" class="form-control rounded-3" placeholder="Describe claramente los hechos, producto involucrado y la solución esperada..." required>{{ old('message') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkLegalConsent" required checked>
                                        <label class="form-check-label text-muted small" for="checkLegalConsent">
                                            Autorizo el tratamiento de mis datos personales de acuerdo con la <a href="{{ route('public.legal', 'privacidad') }}" target="_blank" class="text-sl-primary fw-semibold">Política de Privacidad</a> de Spotlight para el trámite de esta solicitud.
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-xs">
                                        <i class="bi bi-send-fill me-1"></i>
                                        <span class="lang-en">Submit Ticket</span>
                                        <span class="lang-es">Radicar Solicitud</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</main>
@endsection

