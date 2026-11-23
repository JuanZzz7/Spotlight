@php
    $cookieUserScope = auth()->check() ? ('user_' . auth()->id()) : 'guest';
@endphp
<div id="vs-cookie-banner"
     data-consent-scope="{{ $cookieUserScope }}"
     class="position-fixed bottom-0 start-0 w-100 p-2 p-sm-3 p-md-4 vs-cookie-banner"
     style="display: none; opacity: 0; transform: translateY(100%); transition: transform 0.35s ease, opacity 0.35s ease; z-index: 1080;">
    <div class="container-fluid max-w-7xl mx-auto">
        <div class="vs-card border-0 p-3 p-md-4 vs-cookie-card">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-center mb-1.5">
                        <i class="bi bi-cookie text-primary fs-4 me-2"></i>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">Tu privacidad es importante</h5>
                    </div>
                    <p class="text-muted text-sm mb-0" style="font-size: 0.84rem; line-height: 1.45;">
                        Utilizamos cookies propias y de terceros para asegurar el correcto funcionamiento de <strong>Spotlight</strong>, mejorar tu experiencia de navegación y analizar el tráfico. Puedes aceptar todas las cookies o elegir usar solo las esenciales.
                    </p>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-md-end gap-2">
                        <button type="button" id="btn-cookies-essential" class="btn btn-outline-secondary fw-semibold text-sm rounded-pill px-3 py-2">
                            Solo esenciales
                        </button>
                        <button type="button" id="btn-cookies-accept" class="btn btn-primary fw-semibold text-sm shadow-sm rounded-pill px-3 py-2">
                            Aceptar todas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const initCookieBanner = () => {
            const cookieBanner = document.getElementById('vs-cookie-banner');
            if (!cookieBanner) return;

            const scope = cookieBanner.getAttribute('data-consent-scope') || 'guest';
            const storageKey = 'VS_COOKIE_CONSENT_' + scope;
            const cookieKey = 'vs_cookie_consent_' + scope;

            const getCookie = (name) => {
                const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? decodeURIComponent(match[2]) : null;
            };

            const setCookie = (name, value, days) => {
                const d = new Date();
                d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
            };

            // Verificar si este usuario o sesión ya aceptó las cookies
            let hasConsent = false;
            try {
                hasConsent = Boolean(
                    localStorage.getItem(storageKey) ||
                    sessionStorage.getItem(storageKey) ||
                    getCookie(cookieKey) ||
                    (scope !== 'guest' && (localStorage.getItem('VS_COOKIE_CONSENT_guest') || getCookie('vs_cookie_consent_guest')))
                );

                // Si el usuario aceptó siendo invitado e inició sesión en la misma sesión, migramos su consentimiento a su usuario
                if (scope !== 'guest' && !localStorage.getItem(storageKey) && (sessionStorage.getItem('VS_COOKIE_CONSENT_ACTIVE_SESSION') || getCookie('vs_cookie_consent_guest'))) {
                    const inherited = sessionStorage.getItem('VS_COOKIE_CONSENT_ACTIVE_SESSION') || getCookie('vs_cookie_consent_guest');
                    localStorage.setItem(storageKey, inherited);
                    setCookie(cookieKey, inherited, 365);
                    hasConsent = true;
                }
            } catch (e) {
                hasConsent = Boolean(getCookie(cookieKey));
            }

            if (hasConsent) {
                cookieBanner.style.display = 'none';
                cookieBanner.remove();
                return;
            }

            // Mostrar banner suavemente si aún no ha aceptado
            cookieBanner.style.display = 'block';
            setTimeout(() => {
                cookieBanner.style.opacity = '1';
                cookieBanner.style.transform = 'translateY(0)';
            }, 300);

            const saveAndHideBanner = (choice) => {
                try {
                    localStorage.setItem(storageKey, choice);
                    sessionStorage.setItem(storageKey, choice);
                    sessionStorage.setItem('VS_COOKIE_CONSENT_ACTIVE_SESSION', choice);
                } catch (e) {}
                setCookie(cookieKey, choice, 365);

                cookieBanner.style.opacity = '0';
                cookieBanner.style.transform = 'translateY(100%)';
                setTimeout(() => {
                    cookieBanner.style.display = 'none';
                    cookieBanner.remove();
                }, 360);

                if (choice === 'all') {
                    window.dispatchEvent(new CustomEvent('vs-cookies-accepted'));
                }
            };

            document.getElementById('btn-cookies-accept')?.addEventListener('click', () => saveAndHideBanner('all'));
            document.getElementById('btn-cookies-essential')?.addEventListener('click', () => saveAndHideBanner('essential'));
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCookieBanner);
        } else {
            initCookieBanner();
        }
    })();
</script>
@endpush
