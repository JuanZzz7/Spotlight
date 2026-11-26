{{-- ================================================================
     Spotlight IA — Floating Widget Chatbot
     ================================================================ --}}

{{-- ── Floating Trigger Button (FAB) ─────────────────────────── --}}
<div id="sbot-fab-container">
    <button id="sbot-btn" type="button" aria-label="Abrir asistente Spotlight IA" title="Chatea con Spotlight IA">
        <span class="sbot-btn-icon-wrap">
            <i class="bi bi-robot" id="sbot-icon-open"></i>
            <i class="bi bi-x-lg sbot-icon-hidden" id="sbot-icon-close"></i>
        </span>
        <span class="sbot-fab-label">Spotlight IA</span>
        <span class="sbot-fab-dot" aria-hidden="true"></span>
    </button>
</div>

{{-- ── Chat Window ─────────────────────────────────────────────── --}}
<div id="sbot-window" hidden>

    {{-- Header --}}
    <div id="sbot-header">
        <div class="sbot-hd-left">
            <div class="sbot-avatar-wrap">
                <div class="sbot-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <span class="sbot-status-dot" title="En línea"></span>
            </div>
            <div class="sbot-hd-info">
                <div class="sbot-hd-name">Spotlight IA</div>
                <div class="sbot-hd-status">
                    <span class="sbot-status-text">Asistente en línea</span>
                </div>
            </div>
        </div>
        <div class="sbot-hd-actions">
            <!-- Language switcher -->
            <div class="btn-group btn-group-sm sbot-lang-group" role="group">
                <input type="radio" class="btn-check sbot-lang-radio" name="sbot_lang" id="sbot-lang-es" value="es" autocomplete="off" checked>
                <label class="btn btn-sm btn-outline-light py-0 px-2 sbot-lang-label" for="sbot-lang-es">ES</label>
                <input type="radio" class="btn-check sbot-lang-radio" name="sbot_lang" id="sbot-lang-en" value="en" autocomplete="off">
                <label class="btn btn-sm btn-outline-light py-0 px-2 sbot-lang-label" for="sbot-lang-en">EN</label>
            </div>
            <button id="sbot-close-btn" aria-label="Cerrar chat" class="btn btn-sm btn-link text-white p-1 ms-1 text-decoration-none">
                <i class="bi bi-x-lg fs-6"></i>
            </button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div id="sbot-msgs" role="log" aria-label="Chat">
        {{-- Date separator --}}
        <div class="sbot-date-sep"><span>Hoy</span></div>
    </div>

    {{-- Typing indicator (Hidden by default) --}}
    <div id="sbot-typing" hidden>
        <div class="sbot-bubble sbot-bubble-bot">
            <span class="sbot-dot-anim"></span>
            <span class="sbot-dot-anim"></span>
            <span class="sbot-dot-anim"></span>
        </div>
    </div>

    {{-- Input Area --}}
    <div class="sbot-bottom-area">
        {{-- Form --}}
        <form id="sbot-form" autocomplete="off" novalidate>
            @csrf
            <div class="sbot-input-group">
                <div class="sbot-input-wrapper">
                    <textarea
                        id="sbot-input"
                        placeholder="Pregúntale a Spotlight IA..."
                        rows="1"
                        maxlength="2000"
                        aria-label="Mensaje"
                    ></textarea>
                </div>
                <button type="submit" id="sbot-send" aria-label="Enviar">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================
     JAVASCRIPT
     ================================================================ --}}
@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const ENDPOINT = '{{ route("chatbot.message") }}';
    const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const GREETING_ES = "¡Hola! Soy tu asistente interactivo Spotlight IA. Puedes preguntarme sobre cualquier tema (negocios, tecnología, cálculos, redacción o cultura general) o pedirme que busque productos, tiendas y pedidos en Spotlight. ¿En qué te ayudo hoy?";
    const GREETING_EN = "Hello! I am your interactive Spotlight AI assistant. You can ask me about any topic (business, tech, math, writing, or general knowledge) or ask me to find products, stores, and orders on Spotlight. How can I help you today?";

    let history = [];
    let busy    = false;
    let open    = false;
    let currentLang = localStorage.getItem('spotlight-lang') || 'es';

    const btn       = document.getElementById('sbot-btn');
    const win       = document.getElementById('sbot-window');
    const msgs      = document.getElementById('sbot-msgs');
    const form      = document.getElementById('sbot-form');
    const input     = document.getElementById('sbot-input');
    const sendBtn   = document.getElementById('sbot-send');
    const typing    = document.getElementById('sbot-typing');
    const iconOpen  = document.getElementById('sbot-icon-open');
    const iconClose = document.getElementById('sbot-icon-close');
    const closeBtn  = document.getElementById('sbot-close-btn');
    const chips     = document.querySelectorAll('.sbot-chip');
    const langRadios = document.querySelectorAll('.sbot-lang-radio');

    if (!btn || !win || !msgs || !form) return;

    langRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            currentLang = this.value;
        });
    });

    function formatTime() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(text));
        return d.innerHTML;
    }

    function formatMarkdown(text) {
        let html = escapeHtml(text);
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/`(.*?)`/g, '<code class="sbot-inline-code">$1</code>');
        html = html.replace(/\n/g, '<br>');
        return html;
    }

    function scrollBottom() {
        requestAnimationFrame(() => {
            msgs.scrollTop = msgs.scrollHeight;
        });
    }

    function addMsg(role, text) {
        const row = document.createElement('div');
        row.className = `sbot-row ${role}`;
        const bubbleClass = role === 'user' ? 'sbot-bubble-user' : 'sbot-bubble-bot';

        row.innerHTML = `
            <div class="sbot-bubble ${bubbleClass}">${formatMarkdown(text)}</div>
            <span class="sbot-ts">${formatTime()}</span>
        `;
        msgs.appendChild(row);
        scrollBottom();
    }

    function openChat() {
        open = true;
        win.hidden = false;
        iconOpen.classList.add('sbot-icon-hidden');
        iconClose.classList.remove('sbot-icon-hidden');

        if (msgs.querySelectorAll('.sbot-row').length === 0) {
            setTimeout(() => {
                const greeting = currentLang === 'en' ? GREETING_EN : GREETING_ES;
                addMsg('bot', greeting);
                history.push({ role: 'bot', content: greeting });
            }, 250);
        }
        if (input && window.innerWidth > 768) input.focus();
    }

    function closeChat() {
        open = false;
        win.hidden = true;
        iconOpen.classList.remove('sbot-icon-hidden');
        iconClose.classList.add('sbot-icon-hidden');
    }

    window.toggleSpotlightChat = function () {
        if (open) closeChat(); else openChat();
    };
    window.openSpotlightChat = openChat;
    window.closeSpotlightChat = closeChat;

    btn.addEventListener('click', () => open ? closeChat() : openChat());
    if (closeBtn) closeBtn.addEventListener('click', closeChat);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && open) closeChat();
    });

    if (input) {
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 80) + 'px';
        });

        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    }

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            if (busy) return;
            sendMessage(chip.textContent.trim());
        });
    });

    async function sendMessage(text) {
        if (!text || busy) return;
        busy = true;

        if (input) {
            input.value = '';
            input.style.height = 'auto';
        }
        if (sendBtn) sendBtn.disabled = true;

        addMsg('user', text);
        history.push({ role: 'user', content: text });

        if (typing) typing.hidden = false;
        scrollBottom();

        try {
            const res = await fetch(ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: text,
                    lang: currentLang,
                    history: history.slice(-10)
                })
            });

            const data = await res.json();
            if (typing) typing.hidden = true;

            const reply = data.reply || (currentLang === 'en' ? "Sorry, I could not process your request." : "Lo siento, no pude procesar tu solicitud.");
            addMsg('bot', reply);
            history.push({ role: 'bot', content: reply });

        } catch (err) {
            if (typing) typing.hidden = true;
            addMsg('bot', currentLang === 'en' ? "Connection error. Please try again." : "Hubo un error de conexión. Por favor intenta de nuevo.");
        } finally {
            busy = false;
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
            }
            if (input && window.innerWidth > 768) input.focus();
        }
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        e.stopPropagation();
        if (sendBtn) {
            sendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
        }
        const text = input.value.trim();
        sendMessage(text);
    });
});
</script>
@endpush
@endonce
