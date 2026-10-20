{{-- ================================================================
     Spotlight IA — Floating Widget Chatbot
     ================================================================ --}}

{{-- ── Floating Trigger Button (FAB) ─────────────────────────── --}}
<div id="sbot-fab-container">
    <button id="sbot-btn" aria-label="Abrir asistente Spotlight IA" title="Chatea con Spotlight IA">
        <i class="bi bi-robot" id="sbot-icon-open"></i>
        <i class="bi bi-x-lg" id="sbot-icon-close" style="display:none"></i>
        <span class="sbot-fab-badge">IA</span>
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
                <label class="btn btn-sm btn-outline-light py-0 px-2" for="sbot-lang-es" style="font-size: 0.7rem;">ES</label>
                <input type="radio" class="btn-check sbot-lang-radio" name="sbot_lang" id="sbot-lang-en" value="en" autocomplete="off">
                <label class="btn btn-sm btn-outline-light py-0 px-2" for="sbot-lang-en" style="font-size: 0.7rem;">EN</label>
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

    {{-- Input & Quick Replies Area --}}
    <div class="sbot-bottom-area">
        {{-- Quick Replies --}}
        <div class="sbot-quick-replies" id="sbot-quick-replies">
            <button class="sbot-chip" type="button">¿Cómo rastrear mi pedido?</button>
            <button class="sbot-chip" type="button">Ver tiendas locales</button>
            <button class="sbot-chip" type="button">Recomiéndame productos</button>
            <button class="sbot-chip" type="button">Soporte técnico</button>
        </div>

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
     STYLES (Paleta Oficial Spotlight: Borgoña & Rosa Empolvado)
     ================================================================ --}}
<style>
/* ── Variables Spotlight ─────────────────────────────────────────── */
:root {
    --sbot-primary: #7F1D3F;
    --sbot-primary-dark: #5E122C;
    --sbot-accent: #D98C9A;
    --sbot-soft: #F6E7EB;
    --sbot-bg: #FFFCF7;
    --sbot-border: #e2e8f0;
}

/* ── Floating trigger (FAB) ────────────────────────────────────── */
#sbot-fab-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1060;
}

#sbot-btn {
    position: relative;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fff;
    background: linear-gradient(135deg, #7F1D3F 0%, #A22852 100%);
    box-shadow: 0 6px 20px rgba(127, 29, 63, 0.4), 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.22s ease;
    outline: none;
}
#sbot-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 26px rgba(127, 29, 63, 0.5);
}
#sbot-btn:active {
    transform: scale(0.95);
}

.sbot-fab-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ffffff;
    color: #7F1D3F;
    border: 1.5px solid #7F1D3F;
    font-size: 0.62rem;
    font-weight: 800;
    border-radius: 999px;
    padding: 1px 5px;
    line-height: 1;
}

/* Subtle pulse animation behind button */
#sbot-btn::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    background-color: rgba(217, 140, 154, 0.4);
    animation: sbotPulse 2.4s ease-out infinite;
    z-index: -1;
}
@keyframes sbotPulse {
    0%   { transform: scale(0.95); opacity: 0.8; }
    70%  { transform: scale(1.35); opacity: 0; }
    100% { transform: scale(1.35); opacity: 0; }
}

/* ── Chat Window ───────────────────────────────────────────────── */
#sbot-window {
    position: fixed;
    bottom: 92px;
    right: 24px;
    z-index: 1059;
    width: 380px;
    max-width: calc(100vw - 32px);
    height: 520px;
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border: 1px solid rgba(217, 140, 154, 0.3);
    border-radius: 20px;
    box-shadow: 0 16px 40px rgba(127, 29, 63, 0.18), 0 4px 14px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
    animation: sbotIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
#sbot-window[hidden] {
    display: none !important;
}

@keyframes sbotIn {
    from { opacity: 0; transform: scale(0.88) translateY(12px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

/* ── Header ────────────────────────────────────────────────────── */
#sbot-header {
    flex-shrink: 0;
    background: linear-gradient(135deg, #7F1D3F 0%, #5E122C 100%);
    color: #ffffff;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
}
.sbot-hd-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sbot-avatar-wrap {
    position: relative;
}
.sbot-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: #ffffff;
    color: #7F1D3F;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.sbot-status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background-color: #10b981;
    border: 2px solid #7F1D3F;
}
.sbot-hd-name {
    font-weight: 700;
    font-size: 1rem;
    color: #ffffff;
    line-height: 1.2;
    letter-spacing: -0.3px;
}
.sbot-hd-status {
    font-size: 0.72rem;
    color: #F6E7EB;
}
.sbot-hd-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ── Messages Area ─────────────────────────────────────────────── */
#sbot-msgs {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background-color: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 12px;
    scroll-behavior: smooth;
}

.sbot-date-sep {
    text-align: center;
    margin: 6px 0;
}
.sbot-date-sep span {
    background: #e2e8f0;
    color: #475569;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 999px;
}

/* Message rows */
.sbot-row {
    display: flex;
    flex-direction: column;
    max-width: 82%;
    animation: sbotMsgIn 0.2s ease-out forwards;
}
@keyframes sbotMsgIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.sbot-row.user {
    align-self: flex-end;
    align-items: flex-end;
}
.sbot-row.bot {
    align-self: flex-start;
    align-items: flex-start;
}

/* Bubbles */
.sbot-bubble {
    padding: 10px 14px;
    font-size: 0.88rem;
    line-height: 1.45;
    word-break: break-word;
}

.sbot-bubble-user {
    background: #7F1D3F;
    color: #ffffff;
    border-radius: 18px 18px 4px 18px;
    box-shadow: 0 2px 8px rgba(127, 29, 63, 0.25);
}

.sbot-bubble-bot {
    background: #ffffff;
    color: #29252A;
    border: 1px solid #e2e8f0;
    border-radius: 18px 18px 18px 4px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

.sbot-ts {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 3px;
    padding: 0 4px;
}

/* ── Typing Animation ──────────────────────────────────────────── */
#sbot-typing {
    padding: 0 16px 8px;
    background: #f8fafc;
}
.sbot-dot-anim {
    display: inline-block;
    width: 6px;
    height: 6px;
    background-color: #7F1D3F;
    border-radius: 50%;
    margin-right: 3px;
    animation: sbotBounce 1.2s infinite;
}
.sbot-dot-anim:nth-child(2) { animation-delay: 0.2s; }
.sbot-dot-anim:nth-child(3) { animation-delay: 0.4s; }

@keyframes sbotBounce {
    0%, 80%, 100% { transform: translateY(0); opacity: 0.4; }
    40%           { transform: translateY(-5px); opacity: 1; }
}

/* ── Bottom: Quick Replies & Input ─────────────────────────────── */
.sbot-bottom-area {
    flex-shrink: 0;
    background: #ffffff;
    border-top: 1px solid var(--sbot-border);
}

.sbot-quick-replies {
    display: flex;
    gap: 6px;
    padding: 10px 14px 6px;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-width: none;
}
.sbot-quick-replies::-webkit-scrollbar { display: none; }

.sbot-chip {
    background: #F6E7EB;
    color: #7F1D3F;
    border: 1px solid #D98C9A;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 0.74rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s ease;
    flex-shrink: 0;
}
.sbot-chip:hover {
    background: #7F1D3F;
    color: #ffffff;
    border-color: #7F1D3F;
    transform: translateY(-1px);
}

#sbot-form {
    padding: 8px 12px 12px;
}
.sbot-input-group {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1.5px solid #D98C9A;
    border-radius: 24px;
    padding: 4px 6px 4px 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.sbot-input-group:focus-within {
    border-color: #7F1D3F;
    box-shadow: 0 0 0 3px rgba(127, 29, 63, 0.12);
    background: #ffffff;
}

.sbot-input-wrapper {
    flex: 1;
}

#sbot-input {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 0.88rem;
    color: #1e293b;
    outline: none;
    resize: none;
    padding: 6px 0;
    max-height: 80px;
    line-height: 1.35;
    font-family: inherit;
}

#sbot-send {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: #7F1D3F;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.95rem;
    transition: transform 0.2s, background-color 0.2s;
    flex-shrink: 0;
}
#sbot-send:hover {
    background: #5E122C;
    transform: scale(1.05);
}
#sbot-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Mobile */
@media (max-width: 480px) {
    #sbot-fab-container { bottom: 18px; right: 18px; }
    #sbot-window { right: 12px; bottom: 84px; width: calc(100vw - 24px); height: calc(100vh - 110px); }
}
</style>

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
    const GREETING_ES = "¡Hola! Soy tu asistente de Spotlight IA. ¿En qué puedo ayudarte hoy? Puedo buscar productos, rastrear pedidos o guiarte en las tiendas locales.";
    const GREETING_EN = "Hello! I am your Spotlight AI assistant. How can I help you today? I can search for products, track orders, or guide you through local stores.";

    let history = [];
    let busy    = false;
    let open    = false;
    let currentLang = 'es';

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
        html = html.replace(/`(.*?)`/g, '<code style="background:rgba(0,0,0,0.06);padding:2px 4px;border-radius:4px;">$1</code>');
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
        iconOpen.style.display = 'none';
        iconClose.style.display = 'inline';

        if (msgs.querySelectorAll('.sbot-row').length === 0) {
            setTimeout(() => {
                const greeting = currentLang === 'en' ? GREETING_EN : GREETING_ES;
                addMsg('bot', greeting);
                history.push({ role: 'bot', content: greeting });
            }, 250);
        }
        if (input) input.focus();
    }

    function closeChat() {
        open = false;
        win.hidden = true;
        iconOpen.style.display = 'inline';
        iconClose.style.display = 'none';
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
            if (sendBtn) sendBtn.disabled = false;
            if (input) input.focus();
        }
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        const text = input.value.trim();
        sendMessage(text);
    });
});
</script>
@endpush
@endonce
