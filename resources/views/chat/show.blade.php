@extends('layouts.dashboard')
@section('title', 'Chat con ' . ($receiver->company->name ?? $receiver->name) . ' - Spotlight')
@section('page-title', 'Centro de Mensajes')

@section('dashboard-content')
@if(auth()->user()->role === 'business')
    {{-- Header Area con Estado Interactivo y Respuestas Rápidas --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="text-uppercase fw-bold text-dark" style="font-size: 0.75rem; letter-spacing: 0.5px;">CANAL DIRECTO CLIENTES</span>
                <span class="text-muted fw-bold" style="font-size: 0.75rem;">/ Ubaté & Región</span>
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
        <div class="col-lg-4 h-100 d-none d-lg-block">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 d-flex flex-column bg-white overflow-hidden">
                <div class="p-4 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <h6 class="fw-bold text-dark mb-0">Conversaciones</h6>
                        <span class="badge rounded-pill fw-semibold" id="newChatsBadge" style="background: #f1f5f9; color: #475569; font-size: 0.68rem;">
                            {{ $unreadChatsCount > 0 ? "{$unreadChatsCount} nueva" . ($unreadChatsCount > 1 ? 's' : '') : '0 nuevas' }}
                        </span>
                        <span class="badge rounded-pill ms-auto fw-bold text-truncate" style="max-width: 140px; background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.68rem;" title="{{ $company->name ?? auth()->user()->name }}">
                            Todos ({{ count($chats) }})
                        </span>
                    </div>
                    
                    <div class="position-relative mb-2">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.85rem;"></i>
                        <input type="text" id="bizChatSearchInput" class="form-control rounded-pill border py-2" placeholder="Buscar por cliente o mensaje..." style="padding-left: 2.5rem; font-size: 0.85rem; border-color: #cbd5e1 !important; box-shadow: none;">
                    </div>
                </div>

                <div class="flex-fill overflow-auto" id="businessChatsListContainer">
                    @forelse($chats as $chat)
                        <a href="{{ route('chat.show', $chat) }}" class="text-decoration-none business-chat-item-link" data-chat-name="{{ strtolower($chat->display_name) }}">
                            <div class="p-3 border-bottom position-relative cursor-pointer transition-all hover-bg-light {{ $chat->id === $receiver->id ? 'bg-light' : 'bg-white' }}" style="border-color: #f1f5f9 !important;">
                                @if($chat->id === $receiver->id)
                                    <div class="position-absolute" style="left: 0; top: 0; bottom: 0; width: 4px; background: var(--sl-primary, #7F1D3F); border-radius: 0 4px 4px 0;"></div>
                                @endif
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
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-chat-dots fs-1 d-block mb-3" style="color: #cbd5e1;"></i>
                            <div class="fw-semibold text-dark mb-1">No hay mensajes recientes</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Tus conversaciones activas aparecerán aquí.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Window: Chat Active -->
        <div class="col-lg-8 h-100">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 d-flex flex-column bg-white overflow-hidden">
                <!-- Chat Header -->
                <div class="p-3 p-md-4 border-bottom d-flex align-items-center justify-content-between bg-white" style="border-color: #f1f5f9 !important; z-index: 10;">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-light rounded-circle d-lg-none"><i class="bi bi-arrow-left"></i></a>
                        @if($receiver->display_logo)
                            <img src="{{ $receiver->display_logo }}" alt="{{ $receiver->display_name }}" class="rounded-circle shadow-2xs flex-shrink-0" style="width: 45px; height: 45px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-2xs flex-shrink-0" style="width: 45px; height: 45px; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                                {{ $receiver->display_initial }}
                            </div>
                        @endif
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-bold mb-0 text-dark">{{ $receiver->display_name }}</h6>
                                @if($receiver->role === 'user')
                                    <span class="badge rounded-pill text-dark fw-bold d-none d-md-inline" style="background: #f1f5f9; font-size: 0.6rem;">Cliente Verificado</span>
                                @else
                                    <span class="badge rounded-pill text-dark fw-bold d-none d-md-inline" style="background: #f1f5f9; font-size: 0.6rem;">{{ $receiver->company?->category ?: 'Empresa Verificada' }}</span>
                                @endif
                            </div>
                            <div class="text-muted fw-medium d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <span class="bg-success rounded-circle" style="width: 6px; height: 6px;"></span> En línea ahora
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 text-xs fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" data-bs-toggle="modal" data-bs-target="#quickRepliesModal">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span class="d-none d-sm-inline">Respuestas Rápidas</span>
                        </button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-fill p-4 overflow-auto" id="chat-messages" style="background: #ffffff; background-image: radial-gradient(#f1f5f9 1px, transparent 1px); background-size: 20px 20px;">
                    @foreach($messages as $msg)
                        @php $isMe = $msg->sender_id === auth()->id(); @endphp
                        <div class="d-flex mb-4 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} align-items-end gap-2">
                            @if(!$isMe)
                                <div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">{{ $receiver->display_initial }}</div>
                            @endif
                            
                            <div class="p-3 shadow-sm text-dark {{ $isMe ? '' : 'bg-white' }}" style="max-width: 80%; border-radius: {{ $isMe ? '16px 16px 0 16px' : '0 16px 16px 16px' }}; border: 1px solid {{ $isMe ? '#EEDDDC' : '#f1f5f9' }}; {{ $isMe ? 'background: linear-gradient(135deg, #FFFCF7, #F6E7EB);' : '' }}">
                                <div class="fw-medium" style="font-size: 0.85rem; line-height: 1.5; color: {{ $isMe ? '#7F1D3F' : '#29252A' }}; white-space: pre-wrap;">{{ $msg->message }}</div>
                            </div>

                            @if($isMe)
                                <div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">{{ auth()->user()->display_initial }}</div>
                            @endif
                        </div>
                        <div class="mb-4 d-flex {{ $isMe ? 'justify-content-end me-md-5 pe-1' : 'justify-content-start ms-md-5 ps-1' }} align-items-center gap-1 text-muted" style="font-size: 0.6rem; margin-top: -12px;">
                            {{ $msg->created_at->format('H:i') }}
                            @if($isMe)
                                <i class="bi bi-check2-all" style="color: #10b981;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Input Area con Botón Mágico IA -->
                <div class="p-3 bg-white border-top" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex gap-2 mb-2.5 align-items-center flex-wrap px-2">
                        <button type="button" class="btn btn-sm rounded-pill fw-semibold border shadow-2xs d-inline-flex align-items-center gap-1.5" id="btn_ai_suggest_chat"
                                style="background: linear-gradient(135deg, #FFFCF7, #F6E7EB); color: var(--sl-primary, #7F1D3F); border-color: rgba(217, 140, 154, 0.4) !important; font-size: 0.75rem;">
                            <i class="bi bi-stars text-warning"></i> <span>Sugerir con Spotlight IA</span>
                        </button>
                        <button class="btn btn-sm rounded-pill fw-semibold border shadow-2xs bg-white text-muted quick-chip-btn" style="font-size: 0.725rem;" data-text="¡Hola! ¿En qué te podemos ayudar hoy?">Saludo inicial</button>
                        <button class="btn btn-sm rounded-pill fw-semibold border shadow-2xs bg-white text-muted quick-chip-btn" style="font-size: 0.725rem;" data-text="Sí tenemos disponibilidad inmediata en tienda física para entrega hoy mismo.">Confirmar stock</button>
                        <button class="btn btn-sm rounded-pill fw-semibold border shadow-2xs bg-white text-muted quick-chip-btn" style="font-size: 0.725rem;" data-text="Aceptamos transferencias (Nequi/Daviplata), pagos con tarjeta por Wompi y contra entrega.">Medios de pago</button>
                    </div>
                    <form id="chat-form" class="d-flex gap-2 align-items-center px-2 pb-1">
                        @csrf
                        <div class="flex-fill position-relative">
                            <input type="text" id="message-input" class="form-control border-0 bg-light rounded-pill px-4 py-2.5" placeholder="Escribe tu mensaje para {{ $receiver->display_name }}..." style="font-size: 0.85rem; box-shadow: none;" autocomplete="off" required>
                        </div>
                        <button type="submit" class="btn text-white rounded-pill px-4 py-2.5 d-flex align-items-center justify-content-center gap-2 fw-semibold shadow-2xs border-0" style="background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                            <span class="d-none d-md-inline">Enviar</span> <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Respuestas Rápidas e IA --}}
    @include('chat.partials.quick-replies-modal')

@else
    {{-- Original layout for non-business users --}}
    <div class="vs-card d-flex flex-column overflow-hidden border-0 shadow-sm" style="height: calc(100vh - 180px);">
        {{-- Header --}}
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-white">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('chat.index') }}" class="btn btn-sm btn-light rounded-circle"><i class="bi bi-arrow-left"></i></a>
                @if($receiver->display_logo)
                    <img src="{{ $receiver->display_logo }}" alt="{{ $receiver->display_name }}" class="rounded-circle shadow-sm flex-shrink-0" style="width: 45px; height: 45px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 45px; height: 45px; font-weight: 600;">
                        {{ $receiver->display_initial }}
                    </div>
                @endif
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $receiver->display_name }}</h6>
                    <div class="d-flex align-items-center gap-1">
                        <span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                        <span class="text-xs text-muted">En línea ahora</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Messages Area --}}
        <div class="flex-fill p-4 overflow-auto" id="chat-messages" style="background-color: #f8fafc; background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px); background-size: 20px 20px;">
            @foreach($messages as $msg)
                <div class="d-flex mb-4 {{ $msg->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="p-3 shadow-sm {{ $msg->sender_id === auth()->id() ? 'bg-primary text-white chat-bubble-me' : 'bg-white text-dark chat-bubble-them' }}" style="max-width: 75%; border-radius: 1.25rem;">
                        <div class="text-sm" style="white-space: pre-wrap; line-height: 1.5;">{{ $msg->message }}</div>
                        <div class="text-end mt-2 d-flex align-items-center justify-content-end gap-1" style="font-size: 0.65rem; opacity: 0.8;">
                            {{ $msg->created_at->format('H:i') }}
                            @if($msg->sender_id === auth()->id())
                                <i class="bi bi-check2-all text-white"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Input Area --}}
        <div class="p-3 bg-white border-top">
            <form id="chat-form" class="d-flex gap-2 align-items-center">
                @csrf
                <div class="flex-fill position-relative">
                    <input type="text" id="message-input" class="form-control border-0 bg-light rounded-pill px-4 py-3" placeholder="Escribe un mensaje para {{ $receiver->display_name }}..." autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                    <i class="bi bi-send-fill fs-5"></i>
                </button>
            </form>
        </div>
    </div>
@endif

@push('styles')
<link href="{{ asset('css/user.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const receiverId = "{{ $receiver->id }}";
    const isBusiness = {{ auth()->user()->role === 'business' ? 'true' : 'false' }};

    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = messageInput.value.trim();
            if (!msg) return;

            messageInput.value = '';

            fetch("{{ route('chat.send', $receiver) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: msg })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    appendMessage(msg, true);
                    chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: 'smooth' });
                }
            });
        });
    }

    setInterval(() => {
        fetch(`{{ route('chat.messages', $receiver) }}`)
        .then(res => res.json())
        .then(messages => {
            if (messages.length > 0) {
                messages.forEach(m => {
                    appendMessage(m.message, false);
                });
                chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: 'smooth' });
            }
        });
    }, 3000);

    function appendMessage(text, isMe) {
        const div = document.createElement('div');
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        if (isBusiness) {
            div.className = `d-flex mb-4 ${isMe ? 'justify-content-end' : 'justify-content-start'} align-items-end gap-2`;
            let innerHTML = '';
            
            if (!isMe) innerHTML += `<div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">{{ $receiver->display_initial }}</div>`;
            
            innerHTML += `
                <div class="p-3 shadow-sm text-dark ${isMe ? '' : 'bg-white'}" style="max-width: 80%; border-radius: ${isMe ? '16px 16px 0 16px' : '0 16px 16px 16px'}; border: 1px solid ${isMe ? '#EEDDDC' : '#f1f5f9'}; ${isMe ? 'background: linear-gradient(135deg, #FFFCF7, #F6E7EB);' : ''}">
                    <div class="fw-medium" style="font-size: 0.85rem; line-height: 1.5; color: ${isMe ? '#7F1D3F' : '#29252A'}; white-space: pre-wrap;">${text}</div>
                </div>
            `;
            
            if (isMe) innerHTML += `<div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">{{ auth()->user()->display_initial }}</div>`;
            
            div.innerHTML = innerHTML;
            chatMessages.appendChild(div);

            const timeDiv = document.createElement('div');
            timeDiv.className = `mb-4 d-flex ${isMe ? 'justify-content-end me-md-5 pe-1' : 'justify-content-start ms-md-5 ps-1'} align-items-center gap-1 text-muted`;
            timeDiv.style.cssText = "font-size: 0.6rem; margin-top: -12px;";
            timeDiv.innerHTML = `${time} ${isMe ? '<i class="bi bi-check2-all" style="color: #10b981;"></i>' : ''}`;
            chatMessages.appendChild(timeDiv);

        } else {
            div.className = `d-flex mb-4 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;
            div.innerHTML = `
                <div class="p-3 shadow-sm ${isMe ? 'bg-primary text-white chat-bubble-me' : 'bg-white text-dark chat-bubble-them'}" style="max-width: 75%; border-radius: 1.25rem;">
                    <div class="text-sm" style="white-space: pre-wrap; line-height: 1.5;">${text}</div>
                    <div class="text-end mt-2 d-flex align-items-center justify-content-end gap-1" style="font-size: 0.65rem; opacity: 0.8;">
                        ${time} ${isMe ? '<i class="bi bi-check2-all text-white"></i>' : ''}
                    </div>
                </div>
            `;
            chatMessages.appendChild(div);
        }
    }

    // ─── Chips Rápidos en el Input ─────────────────────────────
    document.querySelectorAll('.quick-chip-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-text');
            if (messageInput && text) {
                messageInput.value = text;
                messageInput.focus();
            }
        });
    });

    // ─── Sugerir con Spotlight IA directamente en el chat ──────
    const btnAiSuggest = document.getElementById('btn_ai_suggest_chat');
    if (btnAiSuggest) {
        btnAiSuggest.addEventListener('click', async function() {
            const lastMsg = @json($lastCustomerMsg?->message ?? '');
            const promptText = lastMsg
                ? `El cliente escribió: "${lastMsg}". Redacta una respuesta directa, cálida y profesional.`
                : 'Genera un saludo comercial y ofrece asesoría personalizada en nuestros productos.';

            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Redactando...';

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
                if (data.success && data.reply && messageInput) {
                    messageInput.value = data.reply;
                    messageInput.focus();
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.disabled = false;
                this.innerHTML = originalHtml;
            }
        });
    }

    // ─── Interacción con Modal Respuestas Rápidas ──────────────
    document.addEventListener('click', function(e) {
        // Al usar una plantilla desde el modal en el chat abierto
        const useBtn = e.target.closest('.btn-use-qr');
        if (useBtn) {
            const text = useBtn.getAttribute('data-text') || '';
            if (messageInput) {
                messageInput.value = text;
                messageInput.focus();
                const modalEl = document.getElementById('quickRepliesModal');
                if (modalEl && window.bootstrap) {
                    const bsModal = bootstrap.Modal.getInstance(modalEl);
                    bsModal?.hide();
                }
            }
        }

        // Mejorar con IA desde el modal
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

    // Responder último mensaje del cliente con IA desde el modal
    document.getElementById('btn_ai_reply_last_customer')?.addEventListener('click', function() {
        const lastMsg = this.getAttribute('data-last-msg') || '';
        const aiInput = document.getElementById('ai_reply_input');
        if (aiInput && lastMsg) {
            aiInput.value = `Responde al cliente que preguntó: "${lastMsg}"`;
            document.getElementById('btn_execute_ai_generate')?.click();
        }
    });

    // Botón generar con IA en modal
    const btnExecuteAi = document.getElementById('btn_execute_ai_generate');
    const aiReplyInput = document.getElementById('ai_reply_input');
    const aiResultBox = document.getElementById('ai_result_box');
    const aiResultTextarea = document.getElementById('ai_result_textarea');

    btnExecuteAi?.addEventListener('click', async function() {
        const promptText = aiReplyInput?.value.trim();
        if (!promptText) {
            alert('Por favor escribe qué deseas responder.');
            aiReplyInput?.focus();
            return;
        }

        const originalHtml = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5"></span> Redactando con IA...';

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
            }
        } catch (err) {
            console.error(err);
        } finally {
            this.disabled = false;
            this.innerHTML = originalHtml;
        }
    });

    // Usar resultado de IA en el chat
    document.getElementById('btn_use_ai_reply')?.addEventListener('click', function() {
        const text = aiResultTextarea?.value.trim();
        if (text && messageInput) {
            messageInput.value = text;
            messageInput.focus();
            const modalEl = document.getElementById('quickRepliesModal');
            if (modalEl && window.bootstrap) {
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                bsModal?.hide();
            }
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
                alert('¡Plantilla guardada con éxito!');
                location.reload();
            }
        } catch (err) {
            console.error(err);
        } finally {
            btn.disabled = false;
        }
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

    // Filtro de búsqueda en la barra lateral
    const bizChatSearch = document.getElementById('bizChatSearchInput');
    if (bizChatSearch) {
        bizChatSearch.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            document.querySelectorAll('.business-chat-item-link').forEach(link => {
                const name = link.getAttribute('data-chat-name') || '';
                const text = link.textContent.toLowerCase();
                link.style.display = (!query || name.includes(query) || text.includes(query)) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
