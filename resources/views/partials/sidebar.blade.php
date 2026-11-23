@php
    $user = auth()->user();
    $role = $user->role;
@endphp

@if($role === 'admin')
    <a href="{{ route('admin.dashboard') }}" class="vs-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid fs-5"></i> Panel principal
    </a>
    <a href="{{ route('admin.users.index') }}" class="vs-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <i class="bi bi-people fs-5"></i> Usuarios
    </a>
    <a href="{{ route('admin.companies.index') }}" class="vs-nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}">
        <i class="bi bi-building fs-5"></i> Empresas
    </a>
    <a href="{{ route('admin.fiscal_verifications.index') }}" class="vs-nav-link {{ request()->routeIs('admin.fiscal_verifications*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-check fs-5"></i> Verificación DIAN
    </a>
    <a href="{{ route('admin.assisted_purchases.index') }}" class="vs-nav-link {{ request()->routeIs('admin.assisted_purchases*') ? 'active' : '' }}">
        <i class="bi bi-globe-americas fs-5"></i> Solicitudes Int.
    </a>
@elseif($role === 'business')
    <a href="{{ route('business.dashboard') }}" class="vs-nav-link {{ request()->routeIs('business.dashboard*') ? 'active' : '' }}">
        <i class="bi bi-grid fs-5"></i> Panel principal
    </a>
    @if(auth()->user()->company && auth()->user()->company->isColombian())
    <a href="{{ route('business.billing.sales') }}" class="vs-nav-link {{ request()->routeIs('business.billing.sales*') || request()->routeIs('business.sales*') ? 'active' : '' }}">
        <i class="bi bi-receipt-cutoff fs-5"></i> Panel de Ventas
    </a>
    @endif
    <a href="{{ route('business.company.edit') }}" class="vs-nav-link {{ request()->routeIs('business.company*') ? 'active' : '' }}">
        <i class="bi bi-building fs-5"></i> Mi empresa
    </a>
    <a href="{{ route('business.products.index') }}" class="vs-nav-link {{ request()->routeIs('business.products*') ? 'active' : '' }}">
        <i class="bi bi-box fs-5"></i> Mis productos
    </a>
    <a href="{{ route('business.expenses.index') }}" class="vs-nav-link {{ request()->routeIs('business.expenses*') ? 'active' : '' }}">
        <i class="bi bi-cash-stack fs-5"></i> Egresos & Finanzas
    </a>
    @if(auth()->user()->company && auth()->user()->company->isColombian())
    <a href="{{ route('business.billing.index') }}" class="vs-nav-link {{ request()->routeIs('business.billing*') && !request()->routeIs('business.billing.sales*') ? 'active' : '' }}">
        <i class="bi bi-shield-check fs-5"></i> Facturación DIAN
    </a>
    @endif
    @if(auth()->user()->company && auth()->user()->company->isInformal())
    <a href="{{ route('business.formalization.guide') }}" class="vs-nav-link {{ request()->routeIs('business.formalization*') ? 'active' : '' }}">
        <i class="bi bi-mortarboard fs-5"></i> Tutoriales Formalización
    </a>
    @endif
    <a href="{{ route('business.shipments.index') }}" class="vs-nav-link {{ request()->routeIs('business.shipments*') ? 'active' : '' }}">
        <i class="bi bi-truck fs-5"></i> Despachos y Envíos
    </a>
    <a href="{{ route('chat.index') }}" class="vs-nav-link {{ request()->is('chat*') ? 'active' : '' }}" id="sidebar-messages-link">
        <i class="bi bi-chat-dots fs-5"></i> Mensajes
        <span id="unread-badge" class="badge badge-warning ms-auto d-none vs-sidebar-unread-badge">0</span>
    </a>
    <a href="{{ route('business.company.edit') }}" class="vs-nav-link">
        <i class="bi bi-gear fs-5"></i> Configuración
    </a>

    <div class="mt-auto pt-4">
        <div class="p-3 rounded-4 vs-sidebar-network-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-geo-alt text-sl-accent"></i>
                <span class="fw-bold vs-sidebar-network-title">RED SPOTLIGHT</span>
            </div>
            <p class="vs-sidebar-network-desc">Comercio artesanal e independiente conectado (🇨🇴 CO • 🇲🇽 MX).</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="vs-sidebar-network-meta">Plataforma activa</span>
                <span class="badge badge-warning vs-sidebar-network-meta">En línea</span>
            </div>
        </div>
    </div>
@else
    <a href="{{ route('user.dashboard') }}" class="vs-nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <i class="bi bi-shop fs-5"></i> Tienda
    </a>
    <a href="{{ route('user.panel') }}" class="vs-nav-link {{ request()->routeIs('user.panel') ? 'active' : '' }}">
        <i class="bi bi-person-badge fs-5"></i> Mi Perfil
    </a>
    <a href="{{ route('user.map') }}" class="vs-nav-link {{ request()->routeIs('user.map') ? 'active' : '' }}">
        <i class="bi bi-geo-alt fs-5"></i> Mapa de Empresas
    </a>
    <a href="{{ route('user.cart.index') }}" class="vs-nav-link {{ request()->routeIs('user.cart*') ? 'active' : '' }}">
        <i class="bi bi-cart3 fs-5"></i> Carrito
        @if(session('cart') && count(session('cart')) > 0)
            <span class="badge badge-warning ms-auto">{{ count(session('cart')) }}</span>
        @endif
    </a>
    <a href="{{ route('user.assisted_purchases.index') }}" class="vs-nav-link {{ request()->routeIs('user.assisted_purchases*') ? 'active' : '' }}">
        <i class="bi bi-globe-americas fs-5"></i> Mis Compras Int.
    </a>
@endif

@if($role === 'admin')
<a href="{{ route('chat.index') }}" class="vs-nav-link {{ request()->is('chat*') ? 'active' : '' }}" id="sidebar-messages-link">
    <i class="bi bi-chat-dots fs-5"></i> Mensajes
    <span id="unread-badge" class="badge badge-danger ms-auto d-none vs-sidebar-unread-admin"></span>
</a>
@endif

{{-- Toast de nuevo mensaje --}}
<div id="new-msg-toast" class="vs-msg-toast">
    <div class="d-flex align-items-center gap-3">
        <div class="vs-msg-toast-icon">
            <i class="bi bi-chat-dots-fill fs-5 text-sl-accent"></i>
        </div>
        <div>
            <div class="fw-bold small mb-1">💬 Nuevos mensajes</div>
            <div id="toast-msg-text" class="small text-white-50 lh-sm">Tienes mensajes sin leer</div>
        </div>
        <button onclick="document.getElementById('new-msg-toast').style.display='none'" class="vs-msg-toast-close" title="Cerrar">✕</button>
    </div>
</div>

<script>
(function() {
    const badge   = document.getElementById('unread-badge');
    const toast   = document.getElementById('new-msg-toast');
    const toastTxt = document.getElementById('toast-msg-text');
    const apiUrl  = "{{ route('chat.unread.count') }}";
    const chatUrl = "{{ route('chat.index') }}";

    let prevCount = null;
    let toastTimer = null;

    // Al hacer clic en el toast, ir a mensajes
    if (toast) {
        toast.addEventListener('click', function(e) {
            if (e.target.tagName === 'BUTTON') return;
            window.location.href = chatUrl;
        });
    }

    function showToast(count) {
        if (!toast) return;
        toastTxt.textContent = count === 1
            ? 'Tienes 1 mensaje sin leer'
            : `Tienes ${count} mensajes sin leer`;
        toast.style.display = 'block';
        toast.style.animation = 'none';
        // Efecto de entrada
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        requestAnimationFrame(() => {
            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            toast.style.opacity    = '1';
            toast.style.transform  = 'translateY(0)';
        });
        // Auto-ocultar después de 6s
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.style.opacity   = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 6000);
    }

    function checkUnread() {
        fetch(apiUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            const count = data.count || 0;

            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }

            // Mostrar toast solo cuando hay mensajes nuevos (el count subió)
            if (prevCount !== null && count > prevCount) {
                showToast(count);
            }

            prevCount = count;
        })
        .catch(() => {});
    }

    // Primera comprobación al cargar
    checkUnread();
    // Polling cada 5 segundos
    setInterval(checkUnread, 5000);
})();
</script>

