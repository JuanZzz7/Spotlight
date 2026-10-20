@extends('layouts.dashboard')
@section('title', 'Chat con ' . ($receiver->company->name ?? $receiver->name) . ' - Spotlight')
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
        <div class="col-lg-4 h-100 d-none d-lg-block">
            <div class="vs-card h-100 border-0 shadow-sm rounded-4 d-flex flex-column bg-white overflow-hidden">
                <div class="p-4 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <h6 class="fw-bold text-dark mb-0">Conversaciones</h6>
                        <span class="badge rounded-pill ms-auto fw-bold" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); font-size: 0.65rem;">Todos ({{ count($chats) }})</span>
                    </div>
                    
                    <div class="position-relative mb-4">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.85rem;"></i>
                        <input type="text" class="form-control rounded-pill border py-2" placeholder="Buscar por cliente..." style="padding-left: 2.5rem; font-size: 0.85rem; border-color: #cbd5e1 !important; box-shadow: none;">
                    </div>
                </div>

                <div class="flex-fill overflow-auto">
                    @forelse($chats as $chat)
                        <a href="{{ route('chat.show', $chat) }}" class="text-decoration-none">
                            <div class="p-3 border-bottom position-relative cursor-pointer transition-all hover-bg-light {{ $chat->id === $receiver->id ? 'bg-light' : 'bg-white' }}" style="border-color: #f1f5f9 !important;">
                                @if($chat->id === $receiver->id)
                                    <div class="position-absolute" style="left: 0; top: 0; bottom: 0; width: 4px; background: var(--sl-primary, #7F1D3F); border-radius: 0 4px 4px 0;"></div>
                                @endif
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
                            <div class="fw-semibold text-dark mb-1">No hay mensajes recientes</div>
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
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 45px; height: 45px; background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                            {{ strtoupper(substr($receiver->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-bold mb-0 text-dark">{{ $receiver->company->name ?? $receiver->name }}</h6>
                                @if($receiver->role === 'user')
                                    <span class="badge rounded-pill text-dark fw-bold d-none d-md-inline" style="background: #f1f5f9; font-size: 0.6rem;">Cliente Verificado</span>
                                @endif
                            </div>
                            <div class="text-muted fw-medium d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <span class="bg-success rounded-circle" style="width: 6px; height: 6px;"></span> En línea ahora
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-sm btn-link text-muted p-0"><i class="bi bi-telephone fs-5"></i></button>
                        <button class="btn btn-sm btn-link text-muted p-0"><i class="bi bi-info-circle fs-5"></i></button>
                        <button class="btn btn-sm fw-bold rounded-pill border bg-white text-dark shadow-sm px-3 ms-2 d-none d-md-block" style="font-size: 0.75rem; border-color: #cbd5e1 !important;">Ver detalles</button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-fill p-4 overflow-auto" id="chat-messages" style="background: #ffffff; background-image: radial-gradient(#f1f5f9 1px, transparent 1px); background-size: 20px 20px;">
                    @foreach($messages as $msg)
                        @php $isMe = $msg->sender_id === auth()->id(); @endphp
                        <div class="d-flex mb-4 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} align-items-end gap-2">
                            @if(!$isMe)
                                <div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">{{ strtoupper(substr($receiver->name, 0, 2)) }}</div>
                            @endif
                            
                            <div class="p-3 shadow-sm text-dark {{ $isMe ? '' : 'bg-white' }}" style="max-width: 80%; border-radius: {{ $isMe ? '16px 16px 0 16px' : '0 16px 16px 16px' }}; border: 1px solid {{ $isMe ? '#EEDDDC' : '#f1f5f9' }}; {{ $isMe ? 'background: linear-gradient(135deg, #FFFCF7, #F6E7EB);' : '' }}">
                                <div class="fw-medium" style="font-size: 0.85rem; line-height: 1.5; color: {{ $isMe ? '#7F1D3F' : '#29252A' }}; white-space: pre-wrap;">{{ $msg->message }}</div>
                            </div>

                            @if($isMe)
                                <div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">CE</div>
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

                <!-- Input Area -->
                <div class="p-3 bg-white border-top" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex gap-2 mb-3 align-items-center flex-wrap px-2">
                        <span class="text-muted fw-bold" style="font-size: 0.75rem;">Sugerencias:</span>
                        <button class="btn btn-sm rounded-pill fw-semibold border shadow-sm" style="background: white; font-size: 0.75rem; color: #475569;" onclick="document.getElementById('message-input').value='¡Hola! ¿En qué te podemos ayudar hoy?';">Saludo inicial</button>
                        <button class="btn btn-sm rounded-pill fw-semibold border shadow-sm" style="background: white; font-size: 0.75rem; color: #475569;" onclick="document.getElementById('message-input').value='Tenemos disponibilidad inmediata en tienda.';">Confirmar stock</button>
                    </div>
                    <form id="chat-form" class="d-flex gap-3 align-items-center px-2 pb-2">
                        @csrf
                        <button type="button" class="btn btn-link text-muted p-0"><i class="bi bi-paperclip fs-5"></i></button>
                        <div class="flex-fill position-relative">
                            <input type="text" id="message-input" class="form-control border-0 bg-white" placeholder="Escribe tu mensaje..." style="font-size: 0.85rem; box-shadow: none;" autocomplete="off" required>
                        </div>
                        <button type="button" class="btn btn-link text-muted p-0 d-none d-md-block"><i class="bi bi-emoji-smile fs-5"></i></button>
                        <button type="submit" class="btn text-white rounded-pill px-4 py-2 d-flex align-items-center justify-content-center gap-2 fw-semibold shadow-sm border-0" style="background: linear-gradient(135deg, #7F1D3F, #D98C9A);">
                            <span class="d-none d-md-inline">Enviar</span> <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- Original layout for non-business users --}}
    <div class="vs-card d-flex flex-column overflow-hidden border-0 shadow-sm" style="height: calc(100vh - 180px);">
        {{-- Header --}}
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-white">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('chat.index') }}" class="btn btn-sm btn-light rounded-circle"><i class="bi bi-arrow-left"></i></a>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; font-weight: 600;">
                    {{ strtoupper(substr($receiver->name, 0, 1)) }}
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $receiver->company->name ?? $receiver->name }}</h6>
                    <div class="d-flex align-items-center gap-1">
                        <span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                        <span class="text-xs text-muted">En línea ahora</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-light rounded-circle" title="Llamar"><i class="bi bi-telephone"></i></button>
                <button class="btn btn-sm btn-light rounded-circle" title="Info"><i class="bi bi-info-circle"></i></button>
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
                    <input type="text" id="message-input" class="form-control border-0 bg-light rounded-pill px-4 py-3" placeholder="Escribe un mensaje para {{ $receiver->company->name ?? $receiver->name }}..." autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                    <i class="bi bi-send-fill fs-5"></i>
                </button>
            </form>
        </div>
    </div>

    <style>
        .chat-bubble-me { border-bottom-right-radius: 0.25rem !important; }
        .chat-bubble-them { border-bottom-left-radius: 0.25rem !important; }
    </style>
@endif

<style>
    #chat-messages::-webkit-scrollbar { width: 6px; }
    #chat-messages::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateX(-50%) translateY(16px); }
        to   { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const receiverId = "{{ $receiver->id }}";
    const isBusiness = {{ auth()->user()->role === 'business' ? 'true' : 'false' }};

    chatMessages.scrollTop = chatMessages.scrollHeight;

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
            
            if (!isMe) innerHTML += `<div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">{{ strtoupper(substr($receiver->name, 0, 2)) }}</div>`;
            
            innerHTML += `
                <div class="p-3 shadow-sm text-dark ${isMe ? '' : 'bg-white'}" style="max-width: 80%; border-radius: ${isMe ? '16px 16px 0 16px' : '0 16px 16px 16px'}; border: 1px solid ${isMe ? '#EEDDDC' : '#f1f5f9'}; ${isMe ? 'background: linear-gradient(135deg, #FFFCF7, #F6E7EB);' : ''}">
                    <div class="fw-medium" style="font-size: 0.85rem; line-height: 1.5; color: ${isMe ? '#7F1D3F' : '#29252A'}; white-space: pre-wrap;">${text}</div>
                </div>
            `;
            
            if (isMe) innerHTML += `<div class="fw-bold text-muted mb-1 d-none d-md-block" style="font-size: 0.65rem;">CE</div>`;
            
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
});
</script>
@endsection
