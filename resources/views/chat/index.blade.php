@extends('layouts.dashboard')
@section('title', 'Centro de Mensajes - Spotlight')
@section('page-title', 'Centro de Mensajes')

@section('dashboard-content')
@if(auth()->user()->role === 'business')
    {{-- Header Area con Estado Interactivo y Respuestas Rápidas --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="text-uppercase fw-bold text-dark" style="font-size: 0.75rem; letter-spacing: 0.5px;">CANAL DIRECTO CLIENTES</span>
                <span class="text-muted fw-bold" style="font-size: 0.75rem;">/ {{ (auth()->user()->company && auth()->user()->company->isMexican()) ? (auth()->user()->company->city ?: 'Puebla') . ', México' : 'Ubaté & Región' }}</span>
            </div>
            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Centro de Mensajes</h3>
            <p class="text-muted mb-0" style="font-size: 0.85rem; max-width: 650px;">Gestiona consultas de inventario, garantías y despachos en tiempo real</p>
        </div>
        <div class="d-flex align-items-center gap-3 mt-2 mt-md-0 flex-wrap">
            {{-- Dropdown de Estado y Visibilidad de la Tienda --}}
            <div class="dropdown" id="storeStatusDropdownWrap">
                <button class="btn bg-white border text-dark shadow-2xs rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold dropdown-toggle"
                        type="button" id="storeStatusDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.82rem; border-color: #e2e8f0 !important;">
                    <span class="text-muted fw-normal">Estado:</span>
                    <span class="d-inline-flex align-items-center gap-1.5" id="storeStatusCurrentLabel">
                        <span class="rounded-circle flex-shrink-0" id="storeStatusDot" style="width: 8px; height: 8px; background: {{ $chatStatusData['dot_color'] ?? '#10b981' }};"></span>
                        <span id="storeStatusText">{{ $chatStatusData['label'] ?? 'En línea' }}</span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end store-status-menu shadow-sm">
                    <li class="store-status-header">Disponibilidad de la tienda</li>
                    <li>
                        <button type="button" class="dropdown-item status-option-btn {{ ($chatStatusData['chat_status'] ?? 'online') === 'online' ? 'active' : '' }}" data-status="online">
                            <span class="status-dot-indicator" style="background: #10b981;"></span>
                            <div class="flex-grow-1 text-start">
                                <strong class="status-title">En línea</strong>
                                <small class="status-subtitle">Disponible para atender clientes</small>
                            </div>
                            <i class="bi bi-check2 status-check-icon"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item status-option-btn {{ ($chatStatusData['chat_status'] ?? 'online') === 'away' ? 'active' : '' }}" data-status="away">
                            <span class="status-dot-indicator" style="background: #eab308;"></span>
                            <div class="flex-grow-1 text-start">
                                <strong class="status-title">Ausente</strong>
                                <small class="status-subtitle">De regreso en unos minutos</small>
                            </div>
                            <i class="bi bi-check2 status-check-icon"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item status-option-btn {{ ($chatStatusData['chat_status'] ?? 'online') === 'busy' ? 'active' : '' }}" data-status="busy">
                            <span class="status-dot-indicator" style="background: #ef4444;"></span>
                            <div class="flex-grow-1 text-start">
                                <strong class="status-title">Ocupado</strong>
                                <small class="status-subtitle">Atendiendo clientes en tienda física</small>
                            </div>
                            <i class="bi bi-check2 status-check-icon"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item status-option-btn {{ ($chatStatusData['chat_status'] ?? 'online') === 'offline' ? 'active' : '' }}" data-status="offline">
                            <span class="status-dot-indicator" style="background: #94a3b8;"></span>
                            <div class="flex-grow-1 text-start">
                                <strong class="status-title">Fuera de horario</strong>
                                <small class="status-subtitle">Respuestas diferidas</small>
                            </div>
                            <i class="bi bi-check2 status-check-icon"></i>
                        </button>
                    </li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li class="px-3 py-2 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-xs fw-bold text-dark d-block">Visibilidad en Chat</span>
                            <small class="text-muted" style="font-size: 0.72rem;">Permitir recibir nuevos chats</small>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="storeVisibilityToggle" {{ ($chatStatusData['chat_visibility'] ?? true) ? 'checked' : '' }} style="cursor: pointer;">
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Botón de Respuestas Rápidas & IA --}}
            <button type="button" class="btn bg-white border text-dark shadow-2xs rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 fw-semibold"
                    style="font-size: 0.82rem; border-color: #e2e8f0 !important;" data-bs-toggle="modal" data-bs-target="#quickRepliesModal">
                <i class="bi bi-lightning-charge-fill" style="color: var(--sl-primary, #7F1D3F);"></i>
                <span>Respuestas Rápidas</span>
                <span class="badge rounded-pill bg-light text-primary border px-1.5 py-0.5 text-3xs">IA</span>
            </button>
        </div>
    </div>

    {{-- Main Chat Layout --}}
    <div class="row g-4" style="height: calc(100vh - 180px); min-height: 700px;">
        
        <!-- Left Sidebar: Conversaciones -->
        <div class="col-lg-4 h-100">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 d-flex flex-column bg-white overflow-hidden">
                <div class="p-4 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <h6 class="fw-bold text-dark mb-0">Conversaciones</h6>
                        <span class="badge rounded-pill fw-semibold" id="newChatsBadge" style="background: #f1f5f9; color: #475569; font-size: 0.68rem;">
                            {{ $unreadChatsCount > 0 ? "{$unreadChatsCount} nueva" . ($unreadChatsCount > 1 ? 's' : '') : '0 nuevas' }}
                        </span>
                        <span class="badge rounded-pill ms-auto fw-bold text-truncate" style="max-width: 140px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.68rem;" title="{{ $company->name ?? auth()->user()->name }}">
                            {{ $company->name ?? auth()->user()->name }}
                        </span>
                    </div>
                    
                    <div class="position-relative mb-3">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.85rem;"></i>
                        <input type="text" id="bizChatSearchInput" class="form-control rounded-pill border py-2" placeholder="Buscar por cliente o mensaje..." style="padding-left: 2.5rem; font-size: 0.85rem; border-color: #cbd5e1 !important; box-shadow: none;">
                    </div>

                    {{-- Filtros: Todos (0 si no hay), No leídos (0 si no hay), Recientes --}}
                    <div class="d-flex align-items-center gap-3 border-bottom" style="border-color: #f1f5f9 !important;">
                        <button type="button" class="btn btn-link p-0 text-decoration-none fw-bold pb-2 chat-tab-filter active" data-filter="all" style="font-size: 0.78rem; color: var(--sl-primary, #7F1D3F); border-bottom: 2px solid var(--sl-primary, #7F1D3F) !important; margin-bottom: -1px;">
                            Todos ({{ count($chats) }})
                        </button>
                        <button type="button" class="btn btn-link p-0 text-decoration-none fw-semibold text-muted pb-2 chat-tab-filter" data-filter="unread" style="font-size: 0.78rem; border-bottom: 2px solid transparent; margin-bottom: -1px;">
                            No leídos ({{ $unreadTotal }})
                        </button>
                        <button type="button" class="btn btn-link p-0 text-decoration-none fw-semibold text-muted pb-2 chat-tab-filter" data-filter="recent" style="font-size: 0.78rem; border-bottom: 2px solid transparent; margin-bottom: -1px;">
                            Recientes
                        </button>
                    </div>
                </div>

                <div class="flex-fill overflow-auto" id="businessChatsListContainer">
                    @forelse($chats as $chat)
                        <!-- Chat Item -->
                        <a href="{{ route('chat.show', $chat) }}" class="text-decoration-none business-chat-item-link"
                           data-chat-name="{{ strtolower($chat->display_name) }}"
                           data-has-unread="{{ ($chat->unread_count ?? 0) > 0 ? '1' : '0' }}">
                            <div class="p-3 border-bottom cursor-pointer bg-white transition-all hover-bg-light" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex align-items-start gap-3 ms-2">
                                    @if($chat->display_logo)
                                        <img src="{{ $chat->display_logo }}" alt="{{ $chat->display_name }}" class="rounded-circle shadow-2xs flex-shrink-0" style="width: 42px; height: 42px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-2xs flex-shrink-0" style="width: 42px; height: 42px; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                                            {{ $chat->display_initial }}
                                        </div>
                                    @endif
                                    <div class="flex-fill overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $chat->display_name }}</div>
                                            @if(($chat->unread_count ?? 0) > 0)
                                                <span class="badge rounded-pill bg-danger text-white px-2 py-0.5 text-3xs fw-bold">
                                                    {{ $chat->unread_count }}
                                                </span>
                                            @elseif($chat->last_message)
                                                <small class="text-muted text-3xs">{{ $chat->last_message->created_at->format('H:i') }}</small>
                                            @endif
                                        </div>
                                        <div class="text-muted text-truncate mb-0" style="font-size: 0.75rem;">
                                            {{ $chat->last_message ? Str::limit($chat->last_message->message, 38) : 'Ver mensajes...' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted" id="emptyChatsNotice">
                            <i class="bi bi-chat-dots fs-1 d-block mb-3" style="color: #cbd5e1;"></i>
                            <div class="fw-semibold text-dark mb-1">No hay mensajes recientes</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Tus conversaciones activas aparecerán aquí cuando los clientes te escriban.</div>
                        </div>
                    @endforelse

                    <div id="noFilteredChatsNotice" class="p-4 text-center text-muted d-none">
                        <i class="bi bi-funnel fs-2 d-block mb-2" style="color: #cbd5e1;"></i>
                        <div class="fw-semibold text-dark text-xs mb-1">Sin conversaciones en este filtro</div>
                        <div class="text-muted text-2xs">No hay mensajes con el criterio seleccionado.</div>
                    </div>
                </div>

                <div class="p-3 bg-light d-flex justify-content-between align-items-center" style="font-size: 0.7rem;">
                    <div class="d-flex align-items-center gap-2 fw-semibold text-muted">
                        <i class="bi bi-lightning-fill text-warning"></i> Respuestas Rápidas con IA activa
                    </div>
                    <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none text-2xs" data-bs-toggle="modal" data-bs-target="#quickRepliesModal">
                        Ver plantillas
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Window: No active chat selected -->
        <div class="col-lg-8 h-100">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 d-flex flex-column align-items-center justify-content-center text-center p-5 bg-white">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; background: var(--sl-primary-soft, #F6E7EB);">
                    <i class="bi bi-chat-dots fs-1" style="color: var(--sl-primary, #7F1D3F);"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Selecciona una conversación</h5>
                <p class="text-muted mb-4" style="max-width: 380px;">Escoge una conversación del panel izquierdo para empezar a chatear o ver el historial de mensajes con tus clientes.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 text-xs fw-semibold" data-bs-toggle="modal" data-bs-target="#quickRepliesModal">
                        <i class="bi bi-stars me-1.5 text-warning"></i> Explorar Respuestas con IA
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Respuestas Rápidas e IA --}}
    @include('chat.partials.quick-replies-modal')
@else
    {{-- Original layout for non-business users --}}
    <div class="row g-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="col-lg-4">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 bg-white overflow-hidden d-flex flex-column">
                <div class="p-3 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">Conversaciones recientes</h6>
                        <span class="badge rounded-pill fw-semibold" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.7rem;">{{ count($chats) }}</span>
                    </div>
                    <div class="position-relative mt-2">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 14px; transform: translateY(-50%); font-size: 0.8rem;"></i>
                        <input type="text" id="recentChatsSearchInput" class="form-control rounded-pill border py-1.5 ps-5" placeholder="Filtrar conversaciones..." style="font-size: 0.8rem; border-color: #e2e8f0 !important; outline: none; box-shadow: none;">
                    </div>
                </div>
                <div class="list-group list-group-flush overflow-auto flex-fill" id="recentChatsList" style="max-height: 600px;">
                    @forelse($chats as $chat)
                        <a href="{{ route('chat.show', $chat) }}" class="list-group-item list-group-item-action p-3 border-0 border-bottom recent-chat-item" data-chat-name="{{ strtolower($chat->display_name) }}" style="border-color: #f1f5f9 !important;">
                            <div class="d-flex align-items-center gap-3">
                                @if($chat->display_logo)
                                    <img src="{{ $chat->display_logo }}" alt="{{ $chat->display_name }}" class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover; flex-shrink: 0;">
                                @else
                                    <div class="text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width: 44px; height: 44px; flex-shrink: 0; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                                        {{ $chat->display_initial }}
                                    </div>
                                @endif
                                <div class="overflow-hidden flex-fill">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ $chat->display_name }}</div>
                                    <div class="text-muted text-xs text-truncate">
                                        {{ $chat->role === 'business' ? ($chat->company?->category ?: 'Empresa local') : 'Cliente' }}
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted small"></i>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-chat-left-text fs-1 d-block mb-2 text-muted opacity-50"></i>
                            <div class="fw-semibold text-dark mb-1">No tienes mensajes aún</div>
                            <small class="text-muted">Inicia una conversación con una empresa local</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if(auth()->user()->role === 'user')
                <div class="vs-card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="border-color: #f1f5f9 !important;">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.7rem;">DIRECTORIO</span>
                                <span class="text-muted small">Spotlight Marketplace</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">Contactar con empresas locales</h5>
                        </div>
                        <div class="position-relative" style="min-width: 280px; max-width: 380px;">
                            <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.85rem;"></i>
                            <input type="text" id="companySearchInput" class="form-control rounded-pill border py-2" placeholder="Buscar empresa o negocio..." style="padding-left: 2.6rem; padding-right: 2.4rem; font-size: 0.85rem; border-color: #cbd5e1 !important; outline: none; box-shadow: none;">
                            <button type="button" id="clearCompanySearchBtn" class="btn btn-link position-absolute text-muted p-0 d-none" style="top: 50%; right: 14px; transform: translateY(-50%); text-decoration: none;" title="Limpiar búsqueda">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-3" id="companiesGrid">
                            @forelse($companies as $biz)
                                <div class="col-md-6 company-card-item" data-company-name="{{ strtolower($biz->company->name ?? $biz->name) }}" data-company-desc="{{ strtolower($biz->company->description ?? '') }}">
                                    <div class="p-3 border rounded-4 d-flex align-items-center justify-content-between hover-shadow transition-all bg-white h-100" style="border-color: #e2e8f0 !important;">
                                        <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                                                <i class="bi bi-shop fs-6"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;">{{ $biz->company->name ?? $biz->name }}</div>
                                                <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-patch-check-fill text-success"></i>
                                                    <span>Empresa verificada</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('chat.show', $biz) }}" class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-semibold shadow-sm flex-shrink-0 d-inline-flex align-items-center gap-1" style="background: #7F1D3F; font-size: 0.8rem;">
                                            <span>Chatear</span>
                                            <i class="bi bi-chat-text"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5 text-muted">
                                    <i class="bi bi-building-slash fs-1 d-block mb-2 text-muted opacity-50"></i>
                                    <div class="fw-semibold text-dark">No hay empresas disponibles</div>
                                    <small class="text-muted">Aún no se han registrado empresas para chat directo.</small>
                                </div>
                            @endforelse

                            <div id="noCompaniesFound" class="col-12 text-center py-5 d-none">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px; background: #f8fafc; color: #94a3b8; border: 1px dashed #cbd5e1;">
                                    <i class="bi bi-search fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">No se encontraron empresas</h6>
                                <p class="text-muted small mb-0">No encontramos resultados para "<span id="searchTermText" class="fw-semibold text-dark"></span>". Prueba con otro término de búsqueda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="vs-card h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 border-0 shadow-sm rounded-4 bg-white">
                    <div class="rounded-circle d-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px; background: var(--sl-primary-soft, #F6E7EB);">
                        <i class="bi bi-chat-quote fs-1" style="color: var(--sl-primary, #7F1D3F);"></i>
                    </div>
                    <h5 class="fw-bold text-dark" style="font-family: 'Outfit', sans-serif;">Selecciona una conversación</h5>
                    <p class="text-muted small">Tus clientes aparecerán aquí cuando te escriban un mensaje.</p>
                </div>
            @endif
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search empresas in user directory
    const companySearchInput = document.getElementById('companySearchInput');
    const clearBtn = document.getElementById('clearCompanySearchBtn');
    const companyCards = document.querySelectorAll('.company-card-item');
    const noResults = document.getElementById('noCompaniesFound');
    const searchTermText = document.getElementById('searchTermText');

    if (companySearchInput) {
        companySearchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            if (clearBtn) {
                if (query.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }
            }

            companyCards.forEach(function(card) {
                const name = card.getAttribute('data-company-name') || '';
                const desc = card.getAttribute('data-company-desc') || '';
                if (!query || name.includes(query) || desc.includes(query)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noResults) {
                if (visibleCount === 0 && companyCards.length > 0) {
                    noResults.classList.remove('d-none');
                    if (searchTermText) searchTermText.textContent = query;
                } else {
                    noResults.classList.add('d-none');
                }
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                companySearchInput.value = '';
                companySearchInput.dispatchEvent(new Event('input'));
                companySearchInput.focus();
            });
        }
    }

    // Filter recent chats in user sidebar
    const recentChatsSearch = document.getElementById('recentChatsSearchInput');
    const chatItems = document.querySelectorAll('.recent-chat-item');
    if (recentChatsSearch && chatItems.length > 0) {
        recentChatsSearch.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            chatItems.forEach(function(item) {
                const name = item.getAttribute('data-chat-name') || '';
                item.style.display = (!query || name.includes(query)) ? '' : 'none';
            });
        });
    }

    // Filter chats in business sidebar
    const bizChatInput = document.getElementById('bizChatSearchInput');
    const bizChatLinks = document.querySelectorAll('.business-chat-item-link');
    const emptyNotice = document.getElementById('emptyChatsNotice');
    const noFilteredNotice = document.getElementById('noFilteredChatsNotice');

    function applyBizChatFilters() {
        const query = (bizChatInput?.value || '').trim().toLowerCase();
        const activeTab = document.querySelector('.chat-tab-filter.active')?.getAttribute('data-filter') || 'all';

        let visibleCount = 0;
        bizChatLinks.forEach(function(link) {
            const name = link.getAttribute('data-chat-name') || '';
            const text = link.textContent.toLowerCase();
            const hasUnread = link.getAttribute('data-has-unread') === '1';

            const matchesQuery = !query || name.includes(query) || text.includes(query);
            let matchesTab = true;
            if (activeTab === 'unread') {
                matchesTab = hasUnread;
            }

            if (matchesQuery && matchesTab) {
                link.style.display = '';
                visibleCount++;
            } else {
                link.style.display = 'none';
            }
        });

        if (bizChatLinks.length > 0) {
            if (visibleCount === 0) {
                noFilteredNotice?.classList.remove('d-none');
            } else {
                noFilteredNotice?.classList.add('d-none');
            }
        }
    }

    if (bizChatInput) {
        bizChatInput.addEventListener('input', applyBizChatFilters);
    }

    // Tabs "Todos", "No leídos", "Recientes"
    const chatTabFilters = document.querySelectorAll('.chat-tab-filter');
    chatTabFilters.forEach(tab => {
        tab.addEventListener('click', function() {
            chatTabFilters.forEach(t => {
                t.classList.remove('active', 'fw-bold');
                t.classList.add('fw-semibold', 'text-muted');
                t.style.color = '';
                t.style.borderBottom = '2px solid transparent';
            });
            this.classList.add('active', 'fw-bold');
            this.classList.remove('text-muted');
            this.style.color = 'var(--sl-primary, #7F1D3F)';
            this.style.borderBottom = '2px solid var(--sl-primary, #7F1D3F)';
            applyBizChatFilters();
        });
    });

    // ─── Estado y Visibilidad de Tienda ────────────────────────
    const statusBtns = document.querySelectorAll('.status-option-btn');
    const storeStatusDot = document.getElementById('storeStatusDot');
    const storeStatusText = document.getElementById('storeStatusText');
    const storeVisibilityToggle = document.getElementById('storeVisibilityToggle');

    statusBtns.forEach(btn => {
        btn.addEventListener('click', async function() {
            const status = this.getAttribute('data-status');
            statusBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            try {
                const res = await fetch('{{ route('chat.status.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ chat_status: status })
                });
                const data = await res.json();
                if (data.success) {
                    if (storeStatusDot) storeStatusDot.style.background = data.dot_color;
                    if (storeStatusText) storeStatusText.textContent = data.label;
                }
            } catch (err) {
                console.error('Error al actualizar estado:', err);
            }
        });
    });

    if (storeVisibilityToggle) {
        storeVisibilityToggle.addEventListener('change', async function() {
            try {
                await fetch('{{ route('chat.status.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ chat_visibility: this.checked })
                });
            } catch (err) {
                console.error('Error al actualizar visibilidad:', err);
            }
        });
    }

    // ─── Modal Respuestas Rápidas e IA ─────────────────────────
    // Usar respuesta: copiar al portapapeles
    document.addEventListener('click', function(e) {
        const useBtn = e.target.closest('.btn-use-qr');
        if (useBtn) {
            const text = useBtn.getAttribute('data-text') || '';
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = useBtn.innerHTML;
                useBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> ¡Copiado!';
                useBtn.classList.replace('btn-primary', 'btn-success');
                setTimeout(() => {
                    useBtn.innerHTML = originalHtml;
                    useBtn.classList.replace('btn-success', 'btn-primary');
                }, 2000);
            });
        }

        // Mejorar con IA
        const editAiBtn = e.target.closest('.btn-edit-with-ai');
        if (editAiBtn) {
            const text = editAiBtn.getAttribute('data-text') || '';
            const aiInput = document.getElementById('ai_reply_input');
            if (aiInput) aiInput.value = `Adapta y mejora esta respuesta: "${text}"`;
            document.getElementById('tab-ai-gen-link')?.click();
        }

        // Prompt Chips de IA
        const chipBtn = e.target.closest('.ai-prompt-chip');
        if (chipBtn) {
            const prompt = chipBtn.getAttribute('data-prompt') || '';
            const aiInput = document.getElementById('ai_reply_input');
            if (aiInput) {
                aiInput.value = prompt;
                aiInput.focus();
            }
        }
    });

    // Botón generar con IA
    const btnExecuteAi = document.getElementById('btn_execute_ai_generate');
    const aiReplyInput = document.getElementById('ai_reply_input');
    const aiResultBox = document.getElementById('ai_result_box');
    const aiResultTextarea = document.getElementById('ai_result_textarea');

    async function triggerAiGeneration(promptText) {
        if (!promptText) {
            alert('Por favor escribe una indicación para que la IA redacte tu respuesta.');
            aiReplyInput?.focus();
            return;
        }

        if (btnExecuteAi) {
            btnExecuteAi.disabled = true;
            btnExecuteAi.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status"></span> Redactando con IA...';
        }

        try {
            const res = await fetch('{{ route('chat.quick-replies.ai') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ input: promptText })
            });
            const data = await res.json();
            if (data.success && data.reply) {
                if (aiResultTextarea) aiResultTextarea.value = data.reply;
                if (aiResultBox) aiResultBox.classList.remove('d-none');
            } else {
                alert(data.message || 'No se pudo generar la respuesta.');
            }
        } catch (err) {
            console.error('Error al generar respuesta con IA:', err);
            alert('Ocurrió un error al contactar al asistente IA.');
        } finally {
            if (btnExecuteAi) {
                btnExecuteAi.disabled = false;
                btnExecuteAi.innerHTML = '<i class="bi bi-stars text-warning me-1"></i> Generar Respuesta con IA';
            }
        }
    }

    btnExecuteAi?.addEventListener('click', function() {
        triggerAiGeneration(aiReplyInput?.value.trim());
    });

    // Copiar resultado de IA
    document.getElementById('btn_use_ai_reply')?.addEventListener('click', function() {
        const text = aiResultTextarea?.value.trim();
        if (text) {
            navigator.clipboard.writeText(text).then(() => {
                this.innerHTML = '<i class="bi bi-check-lg me-1"></i> ¡Copiado al portapapeles!';
                this.classList.replace('btn-primary', 'btn-success');
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-send-check me-1"></i> Usar esta respuesta';
                    this.classList.replace('btn-success', 'btn-primary');
                }, 2000);
            });
        }
    });

    // Guardar resultado de IA como plantilla permanente
    document.getElementById('btn_save_ai_as_template')?.addEventListener('click', async function() {
        const text = aiResultTextarea?.value.trim();
        if (!text) return;

        const title = prompt('Nombre o título para esta plantilla:', 'Respuesta Asistida');
        if (!title) return;

        const btn = this;
        btn.disabled = true;
        try {
            const res = await fetch('{{ route('chat.quick-replies.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title, message: text, category: 'Personalizada' })
            });
            const data = await res.json();
            if (data.success) {
                alert('¡Plantilla guardada con éxito en tu catálogo de respuestas!');
                location.reload();
            }
        } catch (err) {
            console.error(err);
        } finally {
            btn.disabled = false;
        }
    });

    // Guardar plantilla manual desde formulario de creación
    document.getElementById('btn_save_custom_qr')?.addEventListener('click', async function() {
        const title = document.getElementById('custom_qr_title')?.value.trim();
        const category = document.getElementById('custom_qr_category')?.value || 'General';
        const message = document.getElementById('custom_qr_message')?.value.trim();

        if (!title || !message) {
            alert('Por favor diligencia el título y el mensaje de la plantilla.');
            return;
        }

        const btn = this;
        btn.disabled = true;
        try {
            const res = await fetch('{{ route('chat.quick-replies.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title, message, category })
            });
            const data = await res.json();
            if (data.success) {
                location.reload();
            }
        } catch (err) {
            console.error(err);
        } finally {
            btn.disabled = false;
        }
    });

    // Eliminar plantilla personalizada
    document.addEventListener('click', async function(e) {
        const delBtn = e.target.closest('.btn-delete-qr');
        if (delBtn) {
            if (!confirm('¿Deseas eliminar esta plantilla rápida?')) return;
            const qrId = delBtn.getAttribute('data-qr-id');
            try {
                const res = await fetch(`/chat/quick-replies/${qrId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    delBtn.closest('.qr-item-card')?.remove();
                }
            } catch (err) {
                console.error(err);
            }
        }
    });
});
</script>
@endpush
@endsection
