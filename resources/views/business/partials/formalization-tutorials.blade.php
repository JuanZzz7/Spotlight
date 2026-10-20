{{-- Componente Reutilizable: Centro de Tutoriales y Guía de Formalización --}}
<div class="formalization-tutorials-wrapper">
    {{-- AVISO LEGAL DE NO INTERMEDIACIÓN --}}
    <div class="alert alert-warning border-0 rounded-4 p-4 mb-4 shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #d97706 !important;">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(217, 119, 6, 0.15); width: 44px; height: 44px;">
                <i class="bi bi-shield-exclamation text-warning fs-4" style="color: #b45309 !important;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1" style="color: #92400e;">
                    <i class="bi bi-info-circle me-1"></i> Aviso Importante: Spotlight NO realiza trámites de formalización
                </h6>
                <p class="small mb-2 text-dark" style="line-height: 1.55;">
                    Nuestra plataforma es un canal tecnológico y marketplace para impulsar tus ventas. 
                    <strong>Nosotros NO gestionamos, NO cobramos, NO intermediamos ni radicamos ningún trámite mercantil, legal ni tributario</strong> 
                    ante la Cámara de Comercio ni ante la DIAN.
                </p>
                <div class="small fw-semibold" style="color: #b45309;">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    Este apartado es una <strong>guía pedagógica con tutoriales paso a paso y enlaces a portales oficiales</strong> para que tú o tu contador de confianza realicen el proceso de forma directa, autónoma y segura.
                </div>
            </div>
        </div>
    </div>

    {{-- TARJETAS RESUMEN DE BENEFICIOS DE VOLVERSE FORMAL --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 bg-white rounded-4 border shadow-xs h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-circle p-2" style="background: #F6E7EB; color: #7F1D3F;"><i class="bi bi-receipt fs-6"></i></span>
                    <strong class="small text-dark">Facturación Electrónica DIAN</strong>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.78rem;">Podrás emitir facturas legales validadas ante la DIAN para venderle a empresas y entidades estatales.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-white rounded-4 border shadow-xs h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-circle p-2 bg-success bg-opacity-10 text-success"><i class="bi bi-patch-check fs-6"></i></span>
                    <strong class="small text-dark">Sello de Empresa Verificada</strong>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.78rem;">Mayor confianza para tus compradores en Spotlight con insignia oficial de comercio formal.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-white rounded-4 border shadow-xs h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-circle p-2 bg-primary bg-opacity-10 text-primary"><i class="bi bi-bank fs-6"></i></span>
                    <strong class="small text-dark">Acceso Bancario y Créditos</strong>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.78rem;">Acceso a cuentas bancarias comerciales a nombre del negocio y líneas de crédito empresarial.</p>
            </div>
        </div>
    </div>

    {{-- SECCIÓN DE TUTORIALES EN VIDEO / MULTIMEDIA --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-camera-video-fill text-danger fs-5"></i>
                <span>Tutoriales en Video Recomendados (Paso a Paso)</span>
            </h6>
            <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.72rem;">Recursos Educativos Gratuitos</span>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                {{-- Video 1: Inscripción RUT DIAN --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 overflow-hidden shadow-xs hover-shadow transition">
                        <div class="position-relative bg-dark" style="height: 120px;">
                            <img src="https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=400&q=80" class="w-100 h-100" style="object-fit: cover; opacity: 0.65;" alt="Tutorial RUT DIAN">
                            <span class="position-absolute top-50 start-50 translate-middle badge rounded-pill bg-danger p-2 px-3 shadow">
                                <i class="bi bi-play-fill fs-5"></i>
                            </span>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 small" style="font-size: 0.68rem;">DIAN Virtual</span>
                        </div>
                        <div class="p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Cómo Inscribir el RUT por Primera Vez</h6>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem; line-height: 1.35;">Guía oficial para crear tu usuario y obtener tu RUT en línea sin salir de casa.</p>
                            </div>
                            <a href="https://www.youtube.com/results?search_query=como+inscribir+el+rut+por+primera+vez+dian+virtual" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger btn-sm rounded-pill w-100 mt-2" style="font-size: 0.75rem;">
                                <i class="bi bi-youtube me-1"></i> Ver Tutorial en Video
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Video 2: Matrícula Mercantil CCB --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 overflow-hidden shadow-xs hover-shadow transition">
                        <div class="position-relative bg-dark" style="height: 120px;">
                            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=400&q=80" class="w-100 h-100" style="object-fit: cover; opacity: 0.65;" alt="Tutorial Cámara de Comercio">
                            <span class="position-absolute top-50 start-50 translate-middle badge rounded-pill bg-primary p-2 px-3 shadow">
                                <i class="bi bi-play-fill fs-5"></i>
                            </span>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 small" style="font-size: 0.68rem;">CCB Ubaté</span>
                        </div>
                        <div class="p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Matrícula Mercantil en Línea</h6>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem; line-height: 1.35;">Paso a paso para diligenciar el formulario RUES virtual en la Cámara de Comercio.</p>
                            </div>
                            <a href="https://www.youtube.com/results?search_query=como+matricular+empresa+camara+de+comercio+de+bogota+en+linea" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill w-100 mt-2" style="font-size: 0.75rem;">
                                <i class="bi bi-youtube me-1"></i> Ver Tutorial en Video
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Video 3: Persona Natural vs SAS --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 overflow-hidden shadow-xs hover-shadow transition">
                        <div class="position-relative bg-dark" style="height: 120px;">
                            <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=400&q=80" class="w-100 h-100" style="object-fit: cover; opacity: 0.65;" alt="Persona Natural vs SAS">
                            <span class="position-absolute top-50 start-50 translate-middle badge rounded-pill bg-success p-2 px-3 shadow">
                                <i class="bi bi-play-fill fs-5"></i>
                            </span>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 small" style="font-size: 0.68rem;">Estructura Legal</span>
                        </div>
                        <div class="p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Persona Natural vs. Empresa S.A.S.</h6>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem; line-height: 1.35;">¿Cuál figura te conviene más según tus ventas, activos y responsabilidades?</p>
                            </div>
                            <a href="https://www.youtube.com/results?search_query=persona+natural+comerciante+vs+sas+colombia+cual+conviene" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm rounded-pill w-100 mt-2" style="font-size: 0.75rem;">
                                <i class="bi bi-youtube me-1"></i> Ver Tutorial en Video
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Video 4: Homonimia en RUES --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border rounded-3 overflow-hidden shadow-xs hover-shadow transition">
                        <div class="position-relative bg-dark" style="height: 120px;">
                            <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=400&q=80" class="w-100 h-100" style="object-fit: cover; opacity: 0.65;" alt="Consulta RUES">
                            <span class="position-absolute top-50 start-50 translate-middle badge rounded-pill bg-warning text-dark p-2 px-3 shadow">
                                <i class="bi bi-play-fill fs-5"></i>
                            </span>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 small" style="font-size: 0.68rem;">Consulta RUES</span>
                        </div>
                        <div class="p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Consulta de Homonimia Nacional</h6>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem; line-height: 1.35;">Aprende a buscar si el nombre de tu marca o empresa ya está tomado en Colombia.</p>
                            </div>
                            <a href="https://www.youtube.com/results?search_query=como+consultar+homonimia+en+el+rues+colombia" target="_blank" rel="noopener noreferrer" class="btn btn-outline-warning text-dark btn-sm rounded-pill w-100 mt-2" style="font-size: 0.75rem;">
                                <i class="bi bi-youtube me-1"></i> Ver Tutorial en Video
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GUÍA PASO A PASO INTERACTIVA --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-list-check fs-5" style="color: #7F1D3F;"></i>
                <span>La Ruta Oficial de Formalización en Colombia (Paso a Paso)</span>
            </h6>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="accordion accordion-flush" id="formalizationStepsAccordion">

                {{-- Paso 1 --}}
                <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingStep1">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep1" aria-expanded="false" aria-controls="collapseStep1">
                            <span class="badge rounded-circle me-3 p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #7F1D3F; color: white;">1</span>
                            <div>
                                <span class="d-block text-dark">Paso 1: Consulta de Homonimia y Nombre de Marca</span>
                                <small class="text-muted fw-normal" style="font-size: 0.78rem;">Verifica en el RUES que nadie más haya registrado tu nombre comercial en Colombia.</small>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseStep1" class="accordion-collapse collapse" aria-labelledby="headingStep1" data-bs-parent="#formalizationStepsAccordion">
                        <div class="accordion-body bg-light border-top">
                            <p class="small text-muted mb-3">
                                En Colombia no pueden coexistir dos empresas o establecimientos con el mismo nombre o nombres altamente idénticos. Antes de imprimir papelería o redactar estatutos, debes consultar el portal del <strong>RUES</strong> (Registro Único Empresarial y Social).
                            </p>
                            <div class="bg-white p-3 rounded-3 border mb-3">
                                <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-check2-circle text-success me-1"></i> Cómo realizar la consulta:</h6>
                                <ol class="small text-muted mb-0 ps-3 d-flex flex-column gap-1">
                                    <li>Ingresa a la página oficial de <strong>www.rues.org.co</strong>.</li>
                                    <li>Ve a la sección <strong>"Consultas y Transacciones" &gt; "Consulta de Homonimia"</strong>.</li>
                                    <li>Escribe el nombre propuesto para tu negocio y haz clic en Buscar.</li>
                                    <li>Verifica que no existan resultados idénticos o que generen confusión comercial.</li>
                                </ol>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="https://www.rues.org.co/homonimia" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-spotlight rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Ir a Consulta de Homonimia RUES
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Paso 2 --}}
                <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingStep2">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep2" aria-expanded="false" aria-controls="collapseStep2">
                            <span class="badge rounded-circle me-3 p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #7F1D3F; color: white;">2</span>
                            <div>
                                <span class="d-block text-dark">Paso 2: Elección de la Estructura Legal</span>
                                <small class="text-muted fw-normal" style="font-size: 0.78rem;">Decide si te registrarás como Persona Natural o Persona Jurídica (S.A.S.).</small>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseStep2" class="accordion-collapse collapse" aria-labelledby="headingStep2" data-bs-parent="#formalizationStepsAccordion">
                        <div class="accordion-body bg-light border-top">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-white p-3 rounded-3 border h-100">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-person fs-5 text-primary"></i>
                                            <h6 class="fw-bold mb-0 text-dark small">Persona Natural Comerciante</h6>
                                        </div>
                                        <ul class="small text-muted mb-0 ps-3 d-flex flex-column gap-1">
                                            <li>Registro a título personal con tu cédula de ciudadanía.</li>
                                            <li>Trámite más ágil y menor costo de constitución inicial.</li>
                                            <li>El comerciante responde con su propio patrimonio por las deudas u obligaciones del negocio.</li>
                                            <li>Ideal para emprendimientos unipersonales o talleres familiares.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-white p-3 rounded-3 border h-100">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-buildings fs-5 text-success"></i>
                                            <h6 class="fw-bold mb-0 text-dark small">Sociedad por Acciones Simplificada (S.A.S.)</h6>
                                        </div>
                                        <ul class="small text-muted mb-0 ps-3 d-flex flex-column gap-1">
                                            <li>Crea una persona jurídica independiente con su propio NIT.</li>
                                            <li>La responsabilidad se limita estrictamente a los aportes de capital (protege tu patrimonio personal).</li>
                                            <li>Permite uno o varios socios accionistas.</li>
                                            <li>Mayor facilidad para solicitar préstamos bancarios de gran escala o incorporar socios.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Paso 3 --}}
                <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingStep3">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep3" aria-expanded="false" aria-controls="collapseStep3">
                            <span class="badge rounded-circle me-3 p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #7F1D3F; color: white;">3</span>
                            <div>
                                <span class="d-block text-dark">Paso 3: Matrícula Mercantil ante la Cámara de Comercio</span>
                                <small class="text-muted fw-normal" style="font-size: 0.78rem;">Registro oficial ante la Cámara de Comercio de Bogotá (Sede Regional Ubaté o en línea).</small>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseStep3" class="accordion-collapse collapse" aria-labelledby="headingStep3" data-bs-parent="#formalizationStepsAccordion">
                        <div class="accordion-body bg-light border-top">
                            <p class="small text-muted mb-3">
                                Los municipios de la provincia de Ubaté (Ubaté, Simijaca, Susa, Fúquene, Lenguazaque, Cucunubá, Guachetá, Carmen de Carupa, Tausa y Sutatausa) están adscritos a la <strong>Cámara de Comercio de Bogotá (CCB)</strong>.
                            </p>
                            <div class="bg-white p-3 rounded-3 border mb-3">
                                <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-globe me-1 text-primary"></i> Modalidad Virtual (Recomendada):</h6>
                                <p class="small text-muted mb-2">
                                    Ingresa a <strong>www.ccb.org.co</strong> o a la <strong>Ventanilla Única Empresarial (VUE)</strong> en <strong>www.vue.gov.co</strong>, donde puedes diligenciar el formulario único RUES, firmar digitalmente y pagar los derechos mediante PSE.
                                </p>
                                <h6 class="fw-bold small text-dark mb-2 mt-3"><i class="bi bi-geo-alt me-1 text-danger"></i> Modalidad Presencial (Sede Ubaté):</h6>
                                <p class="small text-muted mb-0">
                                    Puedes radicar tus documentos en la sede de la Cámara de Comercio de Bogotá en Ubaté: <strong>Calle 9 # 7-42, Ubaté, Cundinamarca</strong>. Horario: Lunes a Viernes de 8:00 AM a 4:30 PM.
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="https://www.ccb.org.co/creacion-de-empresa" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Portal Cámara de Comercio de Bogotá
                                </a>
                                <a href="https://www.vue.gov.co" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Ventanilla Única Empresarial (VUE)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Paso 4 --}}
                <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingStep4">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep4" aria-expanded="false" aria-controls="collapseStep4">
                            <span class="badge rounded-circle me-3 p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #7F1D3F; color: white;">4</span>
                            <div>
                                <span class="d-block text-dark">Paso 4: Inscripción en el RUT y Obtención del NIT (DIAN)</span>
                                <small class="text-muted fw-normal" style="font-size: 0.78rem;">Registro de responsabilidades fiscales y código de actividad económica CIIU.</small>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseStep4" class="accordion-collapse collapse" aria-labelledby="headingStep4" data-bs-parent="#formalizationStepsAccordion">
                        <div class="accordion-body bg-light border-top">
                            <p class="small text-muted mb-3">
                                La <strong>DIAN</strong> asigna el Número de Identificación Tributaria (NIT) y certifica las obligaciones fiscales de tu negocio (por ejemplo, si eres responsable o no responsable de IVA).
                            </p>
                            <div class="bg-white p-3 rounded-3 border mb-3">
                                <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-file-earmark-text text-danger me-1"></i> Requisitos e instrucciones:</h6>
                                <ul class="small text-muted mb-0 ps-3 d-flex flex-column gap-1">
                                    <li>Ingresa a <strong>www.dian.gov.co</strong> en la opción <strong>"Asignación de Citas"</strong> o <strong>"Inscripción en el RUT"</strong>.</li>
                                    <li>Si realizas la matrícula en Cámara de Comercio mediante el portal VUE, la CCB gestiona automáticamente el Pre-RUT ante la DIAN en el mismo trámite.</li>
                                    <li>Identifica el código <strong>CIIU</strong> que describe la actividad que ejerces (ej. Comercio al por menor de prendas de vestir, artesanías, calzado, etc.).</li>
                                    <li>Una vez aprobado, descarga tu RUT en formato PDF con el texto <em>"Certificado Formal"</em> y firma digital.</li>
                                </ul>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="https://www.dian.gov.co/tramites-servicios/rut/Paginas/default.aspx" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Portal de Trámites RUT - DIAN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Paso 5 --}}
                <div class="accordion-item border rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingStep5">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep5" aria-expanded="false" aria-controls="collapseStep5">
                            <span class="badge rounded-circle me-3 p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #7F1D3F; color: white;">5</span>
                            <div>
                                <span class="d-block text-dark">Paso 5: Actualizar tu Empresa en Spotlight a Comercio Formal</span>
                                <small class="text-muted fw-normal" style="font-size: 0.78rem;">Adjunta tu Certificado de Cámara de Comercio y RUT para activar Facturación DIAN.</small>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseStep5" class="accordion-collapse collapse" aria-labelledby="headingStep5" data-bs-parent="#formalizationStepsAccordion">
                        <div class="accordion-body bg-light border-top">
                            <p class="small text-muted mb-3">
                                ¡Listo! Una vez que cuentas con tus documentos oficiales, el proceso en Spotlight es inmediato:
                            </p>
                            <ol class="small text-muted mb-3 ps-3 d-flex flex-column gap-2">
                                <li>Dirígete a la sección <strong>"Mi Empresa" &gt; "Aspecto Legal"</strong> en tu panel de Spotlight.</li>
                                <li>Cambia la selección de tipo de negocio a <strong>"Empresa registrada"</strong>.</li>
                                <li>Ingresa tu Razón Social, NIT y datos del Representante Legal.</li>
                                <li>Adjunta el archivo PDF de tu <strong>Cámara de Comercio</strong> (vigencia menor a 90 días) y tu <strong>RUT</strong>.</li>
                                <li>Haz clic en <strong>"Guardar Aspecto Legal"</strong>. Nuestro equipo validará los archivos y se activará de inmediato el módulo de <strong>Facturación Electrónica DIAN</strong> y tu insignia de formalidad.</li>
                            </ol>
                            <a href="{{ route('business.company.edit') }}" class="btn btn-sm btn-spotlight rounded-pill px-3">
                                <i class="bi bi-pencil-square me-1"></i> Ir a Editar Mi Empresa
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- DIRECTORIO DE ENLACES OFICIALES --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-link-45deg fs-5 text-primary"></i>
                <span>Directorio Oficial de Entidades y Trámites</span>
            </h6>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block text-dark small">Cámara de Comercio de Bogotá</strong>
                            <span class="text-muted small" style="font-size: 0.75rem;">Portal de registro y consulta mercantil</span>
                        </div>
                        <a href="https://www.ccb.org.co" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-dark rounded-pill">
                            ccb.org.co <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block text-dark small">DIAN Colombia</strong>
                            <span class="text-muted small" style="font-size: 0.75rem;">Inscripción RUT y Facturación Electrónica</span>
                        </div>
                        <a href="https://www.dian.gov.co" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger rounded-pill">
                            dian.gov.co <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block text-dark small">RUES Colombia</strong>
                            <span class="text-muted small" style="font-size: 0.75rem;">Consulta Nacional de Homonimia y Empresas</span>
                        </div>
                        <a href="https://www.rues.org.co" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill">
                            rues.org.co <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block text-dark small">VUE (Ventanilla Única Empresarial)</strong>
                            <span class="text-muted small" style="font-size: 0.75rem;">Creación de empresa 100% digital</span>
                        </div>
                        <a href="https://www.vue.gov.co" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill">
                            vue.gov.co <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PREGUNTAS FRECUENTES (FAQ) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-2">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-question-circle-fill text-warning fs-5"></i>
                <span>Preguntas Frecuentes sobre la Formalización</span>
            </h6>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column gap-3">
                <div class="border-bottom pb-3">
                    <h6 class="fw-bold text-dark small mb-1">¿Spotlight puede hacer este trámite en mi nombre si pago una tarifa?</h6>
                    <p class="text-muted small mb-0">
                        <strong>No.</strong> Spotlight es una plataforma tecnológica de marketplace y comercio electrónico. No somos gestores ni intermediarios legales o tributarios. Las leyes colombianas exigen que los trámites ante la Cámara de Comercio y la DIAN sean firmados y radicados directamente por el titular, su representante legal o un apoderado debidamente facultado.
                    </p>
                </div>
                <div class="border-bottom pb-3">
                    <h6 class="fw-bold text-dark small mb-1">¿Es obligatorio volverme formal para seguir vendiendo en Spotlight?</h6>
                    <p class="text-muted small mb-0">
                        No es obligatorio. En Spotlight acogemos con orgullo a los emprendimientos locales y talleres artesanales bajo la modalidad de <strong>Comercio Informal / Emprendimiento Local</strong>, permitiéndote vender, cobrar por Wompi y despachar tus productos. Volverte formal solo es requerido si deseas emitir facturas electrónicas oficiales DIAN para clientes corporativos o empresas.
                    </p>
                </div>
                <div class="border-bottom pb-3">
                    <h6 class="fw-bold text-dark small mb-1">¿Cuánto cuesta registrarse en la Cámara de Comercio y en la DIAN?</h6>
                    <p class="text-muted small mb-0">
                        La inscripción en el RUT ante la DIAN es <strong>100% gratuita</strong>. La consulta de homonimia en RUES también es gratuita. La matrícula mercantil ante la Cámara de Comercio tiene una tarifa oficial regulada anualmente por el Gobierno Nacional, calculada en función de los activos con los que inicie tu negocio.
                    </p>
                </div>
                <div>
                    <h6 class="fw-bold text-dark small mb-1">¿Cuánto tiempo toma el trámite?</h6>
                    <p class="text-muted small mb-0">
                        Si realizas el trámite de forma virtual a través de la Ventanilla Única Empresarial (VUE) o el portal de la CCB, la matrícula y el NIT se obtienen generalmente en un plazo de <strong>24 a 72 horas hábiles</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
