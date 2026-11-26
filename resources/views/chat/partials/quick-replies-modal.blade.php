{{-- Modal de Respuestas Rápidas y Redacción con IA --}}
<div class="modal fade" id="quickRepliesModal" tabindex="-1" aria-labelledby="quickRepliesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            {{-- Modal Header --}}
            <div class="modal-header px-4 py-3 bg-white border-bottom" style="border-color: #f1f5f9 !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-2xs flex-shrink-0"
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                        <i class="bi bi-lightning-charge-fill fs-6"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="quickRepliesModalLabel" style="letter-spacing: -0.3px; font-size: 0.95rem;">
                            Respuestas Rápidas & Asistente IA
                        </h6>
                        <small class="text-muted" style="font-size: 0.725rem;">
                            Plantillas automáticas para {{ $company->name ?? auth()->user()->name }}
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- Pestañas Segmentadas y Armónicas (Sin bordes desalineados) --}}
            <div class="px-4 py-2.5 bg-light border-bottom" style="border-color: #f1f5f9 !important;">
                <div class="p-1 rounded-pill bg-white border d-inline-flex align-items-center gap-1 shadow-2xs w-100 w-sm-auto" style="border-color: #e2e8f0 !important;">
                    <button class="btn btn-sm rounded-pill px-3.5 py-1.5 text-xs fw-semibold flex-fill flex-sm-grow-0 d-inline-flex align-items-center justify-content-center gap-1.5 qr-tab-btn active"
                            id="tab-templates-link" data-bs-toggle="pill" data-bs-target="#tab-templates" type="button" role="tab">
                        <i class="bi bi-chat-square-quote"></i>
                        <span>Plantillas Disponibles</span>
                    </button>
                    <button class="btn btn-sm rounded-pill px-3.5 py-1.5 text-xs fw-semibold flex-fill flex-sm-grow-0 d-inline-flex align-items-center justify-content-center gap-1.5 qr-tab-btn"
                            id="tab-ai-gen-link" data-bs-toggle="pill" data-bs-target="#tab-ai-gen" type="button" role="tab">
                        <i class="bi bi-stars text-warning"></i>
                        <span>Redactar con Spotlight IA</span>
                    </button>
                </div>
            </div>

            <style>
                #quickRepliesModal .qr-tab-btn {
                    border: none !important;
                    color: #64748b !important;
                    background: transparent !important;
                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                    font-size: 0.75rem !important;
                    box-shadow: none !important;
                }
                #quickRepliesModal .qr-tab-btn:hover {
                    color: #7F1D3F !important;
                    background: rgba(127, 29, 63, 0.05) !important;
                }
                #quickRepliesModal .qr-tab-btn.active {
                    background: #7F1D3F !important;
                    color: #ffffff !important;
                    box-shadow: 0 2px 8px rgba(127, 29, 63, 0.25) !important;
                }
                #quickRepliesModal .modal-header,
                #quickRepliesModal .modal-footer {
                    border-color: #f1f5f9 !important;
                }
            </style>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-white" style="max-height: 65vh; overflow-y: auto;">
                <div class="tab-content" id="quickRepliesTabContent">
                    
                    {{-- ── TAB 1: Plantillas Guardadas ── --}}
                    <div class="tab-pane fade show active" id="tab-templates" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted text-xs">Usa estas respuestas para contestar en 1 clic:</span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 text-2xs fw-semibold" data-bs-toggle="collapse" data-bs-target="#newTemplateCollapse">
                                <i class="bi bi-plus-circle me-1"></i> Nueva plantilla personalizada
                            </button>
                        </div>

                        {{-- Formulario para crear plantilla personalizada --}}
                        <div class="collapse mb-3" id="newTemplateCollapse">
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark text-xs mb-2">Crear nueva respuesta rápida</h6>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-7">
                                        <input type="text" id="custom_qr_title" class="form-control form-control-sm" placeholder="Título (ej: Descuento fin de mes, Envíos especiales)">
                                    </div>
                                    <div class="col-md-5">
                                        <select id="custom_qr_category" class="form-select form-select-sm">
                                            <option value="General">General</option>
                                            <option value="Horarios">Horarios</option>
                                            <option value="Envíos">Envíos</option>
                                            <option value="Pagos">Pagos</option>
                                            <option value="Inventario">Inventario</option>
                                            <option value="Garantía">Garantía</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <textarea id="custom_qr_message" class="form-control form-control-sm" rows="2" placeholder="Escribe el mensaje exacto que enviarás a tus clientes..."></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-light border text-xs" data-bs-toggle="collapse" data-bs-target="#newTemplateCollapse">Cancelar</button>
                                    <button type="button" id="btn_save_custom_qr" class="btn btn-sm btn-primary text-xs fw-semibold px-3">Guardar Plantilla</button>
                                </div>
                            </div>
                        </div>

                        {{-- Lista de plantillas --}}
                        <div class="row g-2.5" id="quickRepliesListContainer">
                            @foreach($quickReplies ?? [] as $qr)
                                <div class="col-12 qr-item-card" data-qr-id="{{ $qr['id'] ?? '' }}">
                                    <div class="p-3 rounded-4 border bg-white hover-shadow transition-all d-flex flex-column justify-content-between h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <strong class="text-dark text-xs">{{ $qr['title'] ?? 'Respuesta rápida' }}</strong>
                                                <span class="badge rounded-pill bg-light text-muted border text-3xs px-2 py-0.5">
                                                    {{ $qr['category'] ?? 'General' }}
                                                </span>
                                            </div>
                                            @if(!empty($qr['is_custom']))
                                                <button type="button" class="btn btn-link text-danger p-0 text-2xs btn-delete-qr" data-qr-id="{{ $qr['id'] }}" title="Eliminar plantilla">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-muted text-xs mb-2 text-wrap qr-message-content" style="line-height: 1.45;">
                                            {{ $qr['message'] ?? '' }}
                                        </p>
                                        <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                            <button type="button" class="btn btn-sm btn-link text-primary p-0 text-2xs fw-semibold text-decoration-none btn-edit-with-ai" data-text="{{ $qr['message'] ?? '' }}">
                                                <i class="bi bi-magic me-1"></i> Mejorar con IA
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 text-2xs fw-semibold btn-use-qr" data-text="{{ $qr['message'] ?? '' }}">
                                                <i class="bi bi-send-check me-1"></i> Usar esta respuesta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── TAB 2: Generar / Redactar con Spotlight IA ── --}}
                    <div class="tab-pane fade" id="tab-ai-gen" role="tabpanel">
                        <div class="p-3 rounded-4 border mb-3" style="background: linear-gradient(135deg, #FFFCF7, #F6E7EB); border-color: rgba(217, 140, 154, 0.4) !important;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge rounded-pill text-white px-2.5 py-1 text-3xs fw-bold" style="background: var(--sl-primary, #7F1D3F);">
                                    SPOTLIGHT IA
                                </span>
                                <span class="text-xs fw-bold text-dark">Generador Inteligente de Atención al Cliente</span>
                            </div>
                            <p class="text-xs text-muted mb-0">
                                Cuéntale a la IA la situación o el mensaje del cliente y generará una respuesta profesional, amable y personalizada con los datos de tu empresa.
                            </p>
                        </div>

                        {{-- Sugerencia mágica si hay un cliente en chat abierto --}}
                        @if(isset($lastCustomerMsg) && $lastCustomerMsg)
                            <div class="p-3 bg-light rounded-4 border mb-3 d-flex align-items-center justify-content-between">
                                <div class="overflow-hidden me-2">
                                    <small class="text-muted text-2xs d-block">Último mensaje del cliente ({{ $receiver->display_name ?? 'Cliente' }}):</small>
                                    <strong class="text-dark text-xs text-truncate d-block">"{{ Str::limit($lastCustomerMsg->message, 80) }}"</strong>
                                </div>
                                <button type="button" id="btn_ai_reply_last_customer" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 text-2xs fw-semibold flex-shrink-0"
                                        data-last-msg="{{ $lastCustomerMsg->message }}">
                                    <i class="bi bi-stars me-1 text-warning"></i> Responder este mensaje con IA
                                </button>
                            </div>
                        @endif

                        {{-- Chips de consulta rápida --}}
                        <div class="mb-3">
                            <label class="form-label text-2xs fw-bold text-dark mb-1">Ideas rápidas:</label>
                            <div class="d-flex flex-wrap gap-1.5">
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 text-2xs ai-prompt-chip" data-prompt="Informar nuestros horarios de atención y punto físico en {{ (auth()->user()->company && auth()->user()->company->isMexican()) ? (auth()->user()->company->city ?: 'Puebla') : 'Ubaté' }}">Horarios y punto físico</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 text-2xs ai-prompt-chip" data-prompt="Confirmar disponibilidad inmediata de stock y envío local">Stock y envíos locales</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 text-2xs ai-prompt-chip" data-prompt="Explicar los medios de pago disponibles (Nequi, Daviplata, PSE/Wompi)">Medios de pago</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 text-2xs ai-prompt-chip" data-prompt="Avisar que hay congestión en la tienda pero que atenderemos su pedido en breve">Aviso de congestión</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 text-2xs ai-prompt-chip" data-prompt="Explicar la política de garantía directa y satisfacción de la empresa">Garantía y respaldo</button>
                            </div>
                        </div>

                        {{-- Campo de entrada para la IA --}}
                        <div class="mb-3">
                            <label class="form-label text-2xs fw-bold text-dark mb-1">¿Qué deseas responder o qué situación quieres atender?</label>
                            <textarea id="ai_reply_input" class="form-control text-xs" rows="3" placeholder="Ej: Avísale al cliente que su paquete ya salió a entrega con el domiciliario y llegará en 20 minutos..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" id="btn_execute_ai_generate" class="btn btn-primary rounded-pill px-4 py-1.5 text-xs fw-semibold shadow-2xs d-inline-flex align-items-center gap-2">
                                <i class="bi bi-stars text-warning"></i>
                                <span>Generar Respuesta con IA</span>
                            </button>
                        </div>

                        {{-- Resultado de la IA --}}
                        <div id="ai_result_box" class="p-3.5 rounded-4 border bg-white d-none">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="text-2xs fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-check2-circle text-success"></i> Propuesta generada por Spotlight IA
                                </label>
                                <span class="badge bg-light text-muted border text-3xs px-2 py-0.5">Editable</span>
                            </div>
                            <textarea id="ai_result_textarea" class="form-control text-xs mb-3" rows="3"></textarea>
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <button type="button" id="btn_save_ai_as_template" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-2xs fw-semibold">
                                    <i class="bi bi-bookmark-plus me-1"></i> Guardar como plantilla permanente
                                </button>
                                <div class="d-flex gap-2">
                                    <button type="button" id="btn_use_ai_reply" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1 text-2xs fw-semibold">
                                        <i class="bi bi-send-check me-1"></i> Usar esta respuesta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between">
                <small class="text-muted text-3xs">
                    <i class="bi bi-shield-check text-success me-1"></i>Spotlight IA adaptada a las políticas comerciales de tu negocio.
                </small>
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 text-xs" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

