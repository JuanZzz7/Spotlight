@extends('layouts.dashboard')
@section('title', 'Centro de Mensajes - Spotlight')
@section('page-title', 'Centro de Mensajes')

@section('dashboard-content')
@if(auth()->user()->role === 'business')
    {{-- Header Area --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="text-uppercase fw-bold text-dark" style="font-size: 0.75rem; letter-spacing: 0.5px;">CANAL DIRECTO CLIENTES</span>
                <span class="text-muted fw-bold" style="font-size: 0.75rem;">/ Ubaté & Región</span>
            </div>
            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Centro de Mensajes</h3>
            <p class="text-muted mb-0" style="font-size: 0.85rem; max-width: 650px;">Gestiona consultas de inventario, garantías y despachos en tiempo real</p>
        </div>
        <div class="d-flex align-items-center gap-4 mt-2 mt-md-0">
            <div class="d-flex align-items-center gap-2 fw-semibold" style="font-size: 0.85rem;">
                <span class="text-dark">Estado Tienda:</span>
                <span class="d-flex align-items-center gap-2 text-dark"><span class="rounded-circle" style="width: 8px; height: 8px; background: #10b981;"></span> En línea</span>
            </div>
            <button class="btn bg-white border text-dark shadow-sm rounded-pill px-4 py-2 d-flex align-items-center gap-2 fw-semibold" style="font-size: 0.85rem; border-color: #f1f5f9 !important;">
                <i class="bi bi-lightning-charge" style="color: var(--sl-primary, #7F1D3F);"></i> Respuestas Rápidas
            </button>
        </div>
    </div>

    {{-- Main Chat Layout --}}
    <div class="row g-4" style="height: calc(100vh - 180px); min-height: 700px;">
        
        <!-- Left Sidebar: Conversaciones -->
        <div class="col-lg-4 h-100">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 d-flex flex-column bg-white overflow-hidden">
                <div class="p-4 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <h6 class="fw-bold text-dark mb-0">Conversaciones</h6>
                        <span class="badge rounded-pill fw-semibold" style="background: #f1f5f9; color: #475569; font-size: 0.65rem;">2 nuevas</span>
                        <span class="badge rounded-pill ms-auto fw-bold" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.65rem;">TechStore Pro</span>
                    </div>
                    
                    <div class="position-relative mb-4">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.85rem;"></i>
                        <input type="text" class="form-control rounded-pill border py-2" placeholder="Buscar por cliente o producto..." style="padding-left: 2.5rem; font-size: 0.85rem; border-color: #cbd5e1 !important; box-shadow: none;">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark pb-1 border-bottom border-2" style="border-color: var(--sl-primary, #7F1D3F) !important; font-size: 0.75rem; cursor: pointer;">Todos (12)</span>
                        <span class="fw-semibold text-muted pb-1" style="font-size: 0.75rem; cursor: pointer;">No leídos (2)</span>
                        <span class="fw-semibold text-muted pb-1" style="font-size: 0.75rem; cursor: pointer;">Garantías</span>
                    </div>
                </div>

                <div class="flex-fill overflow-auto">
                    @forelse($chats as $chat)
                        <!-- Chat Item -->
                        <a href="{{ route('chat.show', $chat) }}" class="text-decoration-none">
                            <div class="p-3 border-bottom cursor-pointer bg-white transition-all hover-bg-light" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex align-items-start gap-3 ms-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 42px; height: 42px; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                                        {{ strtoupper(substr($chat->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-fill overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $chat->name }}</div>
                                        </div>
                                        <div class="text-muted text-truncate mb-2" style="font-size: 0.75rem;">Ver mensajes...</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-chat-dots fs-1 d-block mb-3" style="color: #cbd5e1;"></i>
                            <div class="fw-semibold text-dark mb-1">No hay mensajes recientes</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Tus conversaciones activas aparecerán aquí.</div>
                        </div>
                    @endforelse
                </div>

                <div class="p-3 bg-light d-flex justify-content-between align-items-center" style="font-size: 0.7rem;">
                    <div class="d-flex align-items-center gap-2 fw-semibold text-muted">
                        <i class="bi bi-lightning-fill text-warning"></i> SLA respuesta promedio: <span class="text-dark">6 min</span>
                    </div>
                    <div class="fw-bold text-dark">Modo Referencia</div>
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
                <p class="text-muted" style="max-width: 350px;">Escoge una conversación del panel izquierdo para empezar a chatear o ver el historial de mensajes con tus clientes.</p>
            </div>
        </div>
    </div>
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
                        <a href="{{ route('chat.show', $chat) }}" class="list-group-item list-group-item-action p-3 border-0 border-bottom recent-chat-item" data-chat-name="{{ strtolower($chat->name) }}" style="border-color: #f1f5f9 !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width: 44px; height: 44px; flex-shrink: 0; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                                    {{ strtoupper(substr($chat->name, 0, 2)) }}
                                </div>
                                <div class="overflow-hidden flex-fill">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ $chat->name }}</div>
                                    <div class="text-muted text-xs text-truncate">
                                        {{ $chat->role === 'business' ? 'Empresa local' : 'Cliente' }}
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
    const businessChatInput = document.querySelector('input[placeholder*="Buscar por cliente"]');
    if (businessChatInput) {
        businessChatInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            const bChats = document.querySelectorAll('.vs-card .flex-fill a');
            bChats.forEach(function(link) {
                const text = link.textContent.toLowerCase();
                link.style.display = (!query || text.includes(query)) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
