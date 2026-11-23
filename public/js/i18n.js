/**
 * ==========================================================================
 * SPOTLIGHT — Global Full-Page i18n Engine (ES <-> EN)
 * Traduce de forma integral, instantánea y reversible toda la interfaz,
 * incluyendo barra superior, menús, tarjetas, filtros, modales, banner de
 * cookies, asistente Spotlight IA y textos dinámicos.
 * ==========================================================================
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'spotlight-lang';

    // 1. Diccionario exacto Español -> Inglés (case-insensitive lookup con preservación de mayúsculas)
    const EXACT_DICT = {
        // Marca, Navegación Global, Topbar y Sidebar
        'ALDEA DIGITAL': 'DIGITAL VILLAGE',
        'Aldea Digital': 'Digital Village',
        'EMPRESAS & COMERCIO': 'BUSINESS & COMMERCE',
        'ADMINISTRACIÓN': 'ADMINISTRATION',
        'Comercio Verificado': 'Verified Business',
        'Administrador': 'Administrator',
        'Comunidad Spotlight': 'Spotlight Community',
        'COMERCIO': 'BUSINESS',
        'Menú': 'Menu',
        'Navegación': 'Navigation',
        'Tienda / Mercado': 'Store / Marketplace',
        'Mi Perfil': 'My Profile',
        'Mapa de Empresas': 'Business Map',
        'Carrito de Compras': 'Shopping Cart',
        'Mis Solicitudes Int.': 'My Int. Requests',
        'Cerrar sesión': 'Log out',
        'Cerrar Sesión': 'Log out',
        'Iniciar sesión': 'Log in',
        'Iniciar Sesión': 'Log in',
        'Ingresar': 'Log in',
        'Registrarse': 'Sign up',
        'Mi Panel': 'My Panel',
        'Carrito': 'Cart',
        'Panel de Empresa': 'Business Panel',
        'Panel Negocio': 'Business Panel',
        'Mi empresa': 'My Company',
        'Mi Empresa': 'My Company',
        'Mis productos': 'My Products',
        'Mis Productos': 'My Products',
        'Ventas y egresos': 'Sales & Expenses',
        'Ventas y Egresos': 'Sales & Expenses',
        'Facturación DIAN': 'DIAN Billing',
        'Tutoriales de Formalización': 'Formalization Tutorials',
        'Despachos y Envíos': 'Shipments & Deliveries',
        'Mensajes': 'Messages',
        'Panel': 'Dashboard',
        'General': 'General',
        'GENERAL': 'GENERAL',
        'Usuarios': 'Users',
        'Empresas': 'Companies',
        'Verificación Fiscal DIAN': 'DIAN Tax Verification',
        'Moderación de Productos': 'Product Moderation',
        'Reportes y Estadísticas': 'Reports & Analytics',
        'Compras Asistidas Int.': 'Int. Assisted Purchases',
        'Inicio': 'Home',
        'Tienda': 'Store',
        'Productos': 'Products',
        'Tiendas': 'Shops',
        'Comunidad': 'Community',

        // Landing Page (home/index.blade.php) & Componentes Públicos
        'SOPORTE & LEGAL': 'SUPPORT & LEGAL',
        'Soporte & Legal': 'Support & Legal',
        'Centro de Soporte & Legal': 'Support & Legal Center',
        'DIRECTORIO LEGAL': 'LEGAL DIRECTORY',
        'Política de Privacidad': 'Privacy Policy',
        'Términos del Servicio': 'Terms of Service',
        'Política de cookies': 'Cookie Policy',
        'Política de Cookies': 'Cookie Policy',
        'Devoluciones y reembolsos': 'Returns & Refunds',
        'Devoluciones y Reembolsos': 'Returns & Refunds',
        'PQRS & Garantías': 'PQRS & Warranties',
        'Portal de PQRS & Garantías': 'PQRS & Warranties Portal',
        'Rastreo de Envíos': 'Order Tracking',
        'Todos los derechos reservados.': 'All rights reserved.',
        'Reseñas': 'Reviews',
        'Comunidad Viva': 'Living Community',
        'Creatividad sin fronteras': 'Creativity without borders',
        'Agregar al carrito': 'Add to cart',
        'Inicia sesión para comprar': 'Log in to buy',
        'Teléfono': 'Phone',
        'Emprendimiento verificado de la red Spotlight.': 'Verified business in the Spotlight network.',
        'Ubicación en el mapa': 'Map Location',
        'Cargando ubicación...': 'Loading location...',
        'Cargando dirección...': 'Loading address...',
        'Ver en el mapa': 'View on map',
        'Chatear': 'Chat',
        'Ver catálogo completo': 'View full catalog',
        'Seguir viendo productos': 'Keep browsing products',
        'Volver a seguir viendo productos': 'Back to keep browsing products',

        // Categorías y Etiquetas (Tags)
        'Tecnología': 'Technology',
        'Moda': 'Fashion',
        'Alimentos': 'Food & Groceries',
        'Comida y bebida': 'Food & Drinks',
        'Panadería': 'Bakery',
        'Ropa y moda': 'Clothing & Fashion',
        'Calzado': 'Footwear',
        'Hogar y deco': 'Home & Decor',
        'Belleza': 'Beauty',
        'Salud y farmacia': 'Health & Pharmacy',
        'Deporte': 'Sports',
        'Juguetes': 'Toys',
        'Libros y papelería': 'Books & Stationery',
        'Mascotas': 'Pets',
        'Automotriz': 'Automotive',
        'Agricultura': 'Agriculture',
        'Construcción': 'Construction',
        'Servicios': 'Services',
        'Educación': 'Education',
        'Arte y artesanía': 'Art & Crafts',
        'Música': 'Music',
        'Turismo': 'Tourism',
        'Eventos': 'Events',
        'Mayorista': 'Wholesale',

        // Productos y Empresas de Demostración (Seeders)
        'Audífonos Bluetooth': 'Bluetooth Headphones',
        'Audífonos inalámbricos con cancelación de ruido': 'Wireless noise-cancelling headphones',
        'Audífonos Bluetooth Noise Cancelling': 'Noise Cancelling Bluetooth Headphones',
        'Audífonos inalámbricos de alta definición con cancelación activa de ruido.': 'High-definition wireless headphones with active noise cancellation.',
        'Mouse Gamer RGB': 'RGB Gaming Mouse',
        'Mouse gaming con 7 botones programables': 'Gaming mouse with 7 programmable buttons',
        'Mouse Ergonómico Inalámbrico RGB': 'RGB Wireless Ergonomic Mouse',
        'Sensor óptico de alta precisión con 6 niveles de DPI ajustables.': 'High-precision optical sensor with 6 adjustable DPI levels.',
        'Teclado Mecánico': 'Mechanical Keyboard',
        'Teclado mecánico retroiluminado': 'Backlit mechanical keyboard',
        'Teclado Mecánico Switch Brown': 'Brown Switch Mechanical Keyboard',
        'Teclado para oficina y desarrollo con retroiluminación y distribución en español.': 'Office and development keyboard with backlighting and Spanish layout.',
        'Cable USB-C Reforzado 2 Metros': '2m Reinforced USB-C Cable',
        'Cable de carga rápida 65W compatible con laptops y teléfonos.': '65W fast-charging cable compatible with laptops and phones.',
        'Camiseta Premium': 'Premium T-Shirt',
        'Camiseta 100% algodón colección 2024': '100% cotton t-shirt 2024 collection',
        'Bolso de Cuero': 'Leather Bag',
        'Bolso artesanal de cuero genuino': 'Handcrafted genuine leather bag',
        'Soluciones tecnológicas, equipos informáticos y accesorios.': 'Tech solutions, computer equipment, and accessories.',
        'Taller artesanal de ropa y accesorios de moda en Ubaté.': 'Artisanal clothing and fashion accessories workshop in Ubaté.',
        'Producción de quesos madurados, arequipe y derivados lácteos tradicionales.': 'Production of aged cheeses, caramel spread, and traditional dairy products.',

        // Vitrina / Marketplace Consumidor (user/dashboard.blade.php)
        'VITRINA LOCAL': 'LOCAL SHOWCASE',
        'Productos Destacados': 'Featured Products',
        'Productos destacados': 'Featured products',
        'Explora lo mejor de nuestros comercios aliados en tiempo real.': 'Explore the best from our partner stores in real time.',
        'Ver mapa': 'View map',
        'Activos:': 'Active:',
        'Restablecer todo': 'Reset all',
        'AGOTADO': 'SOLD OUT',
        'Agotado': 'Out of stock',
        '🔥 TOP VENTAS': '🔥 BEST SELLER',
        '✨ NUEVO': '✨ NEW',
        '⚡ ENVÍO HOY': '⚡ SHIPS TODAY',
        'ARTESANAL / LOCAL': 'HANDCRAFTED / LOCAL',
        'Vitrina Spotlight': 'Spotlight Showcase',
        'Precio unitario': 'Unit price',
        'PRECIO UNITARIO': 'UNIT PRICE',
        'Añadir': 'Add',
        'Añadir al carrito': 'Add to cart',
        'Sin inventario': 'Out of stock',
        'Disminuir cantidad': 'Decrease quantity',
        'Aumentar cantidad': 'Increase quantity',
        'No encontramos productos con esos filtros': 'No products found with those filters',
        'Prueba con otra palabra clave o explora todas las categorías y comercios de la vitrina.': 'Try another keyword or explore all categories and stores in the showcase.',
        'Ver todo el catálogo': 'View full catalog',
        'PANEL DE EXPLORACIÓN': 'EXPLORATION DOCK',
        'Filtra a tu medida': 'Filter your way',
        '01 · Búsqueda directa': '01 · Direct search',
        'Buscar...': 'Search...',
        'Buscar': 'Search',
        '02 · Categoría': '02 · Category',
        'Categoría (Todas)': 'Category (All)',
        'Todas': 'All',
        '03 · Comercios': '03 · Stores',
        'Comercios (Todos)': 'Stores (All)',
        'Ordenar vitrina': 'Sort showcase',
        'ORDENAR VITRINA': 'SORT SHOWCASE',
        'Más recientes primero': 'Newest first',
        'Menor a mayor precio': 'Price: Low to High',
        'Mayor a menor precio': 'Price: High to Low',
        'Nombre (A - Z)': 'Name (A - Z)',
        'Mercado regional': 'Regional market',
        'MERCADO REGIONAL': 'REGIONAL MARKET',
        '🌐 Todos': '🌐 All',
        'Todos': 'All',
        'Explorar en el Mapa': 'Explore on Map',
        'Ubica comercios cerca de ti': 'Find stores near you',
        'Limpiar filtros activos': 'Clear active filters',

        // Panel de Usuario / Perfil (user/panel.blade.php)
        'MIEMBRO DE LA ALDEA DIGITAL': 'DIGITAL VILLAGE MEMBER',
        'Seguir explorando': 'Keep exploring',
        'Ver mi carrito': 'View my cart',
        'Total Pedidos': 'Total Orders',
        'Entregados': 'Delivered',
        'En Proceso': 'In Progress',
        'Total Invertido': 'Total Spent',
        'Mis Pedidos': 'My Orders',
        'Rastrear Envío': 'Track Shipment',
        'Compras Internacionales': 'International Purchases',
        'Editar Perfil': 'Edit Profile',
        'Seguridad': 'Security',
        'Historial de Compras': 'Purchase History',
        'Consulta el estado, factura electrónica y guía de cada pedido': 'Check the status, electronic invoice, and tracking guide for each order',
        'Nueva compra': 'New purchase',
        'Pagado': 'Paid',
        'Enviado': 'Shipped',
        'Entregado': 'Delivered',
        'Cancelado': 'Cancelled',
        'Pendiente': 'Pending',
        'Guía:': 'Tracking:',
        'Factura Electrónica DIAN': 'DIAN Electronic Invoice',
        'Descargar PDF': 'Download PDF',
        'Ver detalle': 'View details',
        'Aún no has realizado pedidos': 'You have not placed any orders yet',
        'Explora las tiendas locales y realiza tu primera compra en Spotlight.': 'Explore local stores and make your first purchase on Spotlight.',
        'Ir a la tienda': 'Go to store',
        'Rastreo de Envíos en Tiempo Real': 'Real-Time Shipment Tracking',
        'Ingresa tu número de guía para conocer la ubicación exacta de tu paquete.': 'Enter your tracking number to find the exact location of your package.',
        'Ej: SPL-2026-000123': 'E.g.: SPL-2026-000123',
        'Consultar guía': 'Track package',
        'Envíos activos de tus compras recientes': 'Active shipments from your recent purchases',
        'Rastrear': 'Track',
        'Solicitudes de Compra Asistida Internacional': 'International Assisted Purchase Requests',
        '¿Viste un producto de otro país? Nosotros gestionamos la compra y envío por ti.': 'Saw a product from another country? We handle the purchase and shipping for you.',
        'Nueva solicitud': 'New request',
        'Cotizado': 'Quoted',
        'Aprobado': 'Approved',
        'En revisión': 'In review',
        'Información Personal': 'Personal Information',
        'Actualiza tus datos de contacto para agilizar tus compras y envíos.': 'Update your contact details to speed up your purchases and deliveries.',
        'Nombre completo': 'Full name',
        'Correo electrónico': 'Email address',
        'Teléfono / WhatsApp': 'Phone / WhatsApp',
        'Dirección de entrega principal': 'Primary delivery address',
        'Guardar cambios': 'Save changes',
        'Cambiar Contraseña': 'Change Password',
        'Mantén tu cuenta segura utilizando una contraseña fuerte.': 'Keep your account secure by using a strong password.',
        'Contraseña actual': 'Current password',
        'Nueva contraseña': 'New password',
        'Confirmar nueva contraseña': 'Confirm new password',
        'Actualizar contraseña': 'Update password',

        // Carrito de Compras y Checkout (user/cart/index.blade.php, checkout.blade.php)
        'PORTAL COMPRADOR / ALDEA DIGITAL / CARRITO DE COMPRAS': 'BUYER PORTAL / DIGITAL VILLAGE / SHOPPING CART',
        'Revisa tus productos seleccionados antes de proceder al pago seguro y apoyar a la comunidad de emprendedores.': 'Review your selected products before proceeding to secure checkout and supporting our community of entrepreneurs.',
        'Envío': 'Shipping',
        'Pago Seguro': 'Secure Payment',
        'Tu carrito está vacío': 'Your cart is empty',
        'Explora la plataforma y agrega productos a tu carrito.': 'Explore the platform and add products to your cart.',
        'Explorar Marketplace': 'Explore Marketplace',
        'PRODUCTO & TIENDA': 'PRODUCT & STORE',
        'PRECIO': 'PRICE',
        'CANTIDAD': 'QUANTITY',
        'SUBTOTAL/ACCIÓN': 'SUBTOTAL/ACTION',
        'ENVÍO GRATIS': 'FREE SHIPPING',
        'Garantía 12m': '12m Warranty',
        'Fresco Hoy': 'Fresh Today',
        'D.O. Protegida': 'Protected Origin',
        'Vaciar Carrito': 'Empty Cart',
        'Resumen de Compra': 'Order Summary',
        'Subtotal': 'Subtotal',
        'Envío estimado': 'Estimated shipping',
        'Gratis': 'Free',
        'Total a Pagar': 'Total to Pay',
        'Proceder al Pago': 'Proceed to Checkout',
        'Seguir Comprando': 'Continue Shopping',

        // Mapa Geo-Comercial (user/map.blade.php)
        'PORTAL / EXPLORAR COMERCIOS LOCALES': 'PORTAL / EXPLORE LOCAL STORES',
        'Mapa Geo-Comercial de Tiendas y Comercios': 'Geo-Commercial Map of Stores & Businesses',
        'Explora negocios locales verificados, consulta sus horarios en tiempo real y agrega productos directo a tu carrito.': 'Explore verified local businesses, check their opening hours in real time, and add products directly to your cart.',
        'Mapa': 'Map',
        'Lista': 'List',
        'Producto agregado al carrito': 'Product added to cart',
        'Buscar comercio o producto...': 'Search store or product...',
        'Cerrar': 'Close',
        'VERIFICADO': 'VERIFIED',
        'Comercio': 'Store',
        'Nombre de la Tienda': 'Store Name',
        'Ubicación': 'Location',
        'ESTADO DE ATENCIÓN': 'OPENING STATUS',
        'Horario': 'Hours',
        'CALIFICACIÓN': 'RATING',
        'PRODUCTOS': 'PRODUCTS',
        'Abierto ahora': 'Open now',
        'Cerrado ahora': 'Closed now',
        'Visitar tienda': 'Visit store',
        'Cómo llegar': 'Get directions',

        // Cookie Banner (components/cookie-banner.blade.php)
        'Tu privacidad es importante': 'Your privacy matters',
        'Solo esenciales': 'Essential only',
        'Aceptar todas': 'Accept all',

        // Chatbot Spotlight IA (components/chatbot-widget.blade.php)
        'Spotlight IA': 'Spotlight AI',
        '¡Hola! Soy tu asistente de Spotlight IA. ¿En qué puedo ayudarte hoy? Puedo buscar productos, rastrear pedidos o guiarte en las tiendas locales.': 'Hello! I am your Spotlight AI assistant. How can I help you today? I can search for products, track orders, or guide you through local stores.',
        'Abrir asistente Spotlight IA': 'Open Spotlight AI assistant',
        'Chatea con Spotlight IA': 'Chat with Spotlight AI',
        'Asistente en línea': 'Online assistant',
        'En línea': 'Online',
        'Cerrar chat': 'Close chat',
        'Hoy': 'Today',
        '¿Cómo rastrear mi pedido?': 'How do I track my order?',
        'Ver tiendas locales': 'View local stores',
        'Recomiéndame productos': 'Recommend products',
        'Soporte técnico': 'Technical support',
        'Pregúntale a Spotlight IA...': 'Ask Spotlight AI...',
        'Mensaje': 'Message',
        'Enviar': 'Send',
        'Error al guardar:': 'Error saving:'
    };

    // Mapa insensible a mayúsculas/minúsculas para búsqueda rápida
    const LOWER_DICT = Object.create(null);
    Object.keys(EXACT_DICT).forEach(k => {
        LOWER_DICT[k.toLowerCase()] = EXACT_DICT[k];
    });

    // 2. Patrones dinámicos (Regex) para textos con números o variables
    const REGEX_PATTERNS = [
        { re: /^(\d+)\s+productos?$/i, en: '$1 products' },
        { re: /^(\d+)\s+disp\.$/i, en: '$1 avail.' },
        { re: /^(\d+)\s+disponibles?$/i, en: '$1 available' },
        { re: /^(\d+)\s+tiendas?$/i, en: '$1 stores' },
        { re: /^(\d+)\s+comercios?\s+activos?$/i, en: '$1 Active Stores' },
        { re: /^(\d+)\s+art[íi]culos?\s+reservados?$/i, en: '$1 RESERVED ITEMS' },
        { re: /^Todas\s*\((\d+)\)$/i, en: 'All ($1)' },
        { re: /^\((\d+)\s+reseñas?\)$/i, en: '($1 reviews)' },
        { re: /^Productos de\s+(.+)$/i, en: 'Products by $1' },
        { re: /^Redirigiendo al carrito con\s+"(.+)"\.\.\.$/i, en: 'Redirecting to cart with "$1"...' },
        { re: /^Búsqueda:\s*"(.+)"$/i, en: 'Search: "$1"' },
        { re: /^Categoría:\s*(.+)$/i, en: 'Category: $1' },
        { re: /^Etiqueta:\s*(.+)$/i, en: 'Tag: $1' },
        { re: /^Comercio:\s*(.+)$/i, en: 'Store: $1' },
        { re: /^País:\s*(.+)$/i, en: 'Country: $1' },
        { re: /^Pedido\s+#(\d+)$/i, en: 'Order #$1' }
    ];

    /**
     * Traduce una cadena individual del español al inglés.
     * Si no coincide con ningún término o patrón, devuelve null.
     */
    function translateStringToEn(rawStr) {
        if (!rawStr) return null;
        const trimmed = rawStr.trim();
        if (!trimmed) return null;

        // 1. Coincidencia exacta
        if (EXACT_DICT[trimmed]) {
            return rawStr.replace(trimmed, EXACT_DICT[trimmed]);
        }

        // 2. Coincidencia insensible a mayúsculas
        const lower = trimmed.toLowerCase();
        if (LOWER_DICT[lower]) {
            const target = LOWER_DICT[lower];
            if (trimmed === trimmed.toUpperCase() && trimmed.length > 2) {
                return rawStr.replace(trimmed, target.toUpperCase());
            }
            return rawStr.replace(trimmed, target);
        }

        // 3. Patrones dinámicos (Regex)
        for (let i = 0; i < REGEX_PATTERNS.length; i++) {
            const pat = REGEX_PATTERNS[i];
            if (pat.re.test(trimmed)) {
                const replaced = trimmed.replace(pat.re, pat.en);
                return rawStr.replace(trimmed, replaced);
            }
        }

        // 4. Párrafo de cookies (contiene etiqueta <strong>Spotlight</strong>)
        if (trimmed.startsWith('Utilizamos cookies propias y de terceros')) {
            return rawStr.replace(trimmed, 'We use our own and third-party cookies to ensure the proper functioning of');
        }
        if (trimmed.startsWith(', mejorar tu experiencia de navegación y analizar el tráfico.')) {
            return rawStr.replace(trimmed, ', improve your browsing experience, and analyze traffic. You can accept all cookies or choose essential ones only.');
        }

        return null;
    }

    /**
     * Verifica si un nodo está dentro de un contenedor que no debe tocarse
     * (por ejemplo, .lang-en o .lang-es que ya se alternan por CSS, o scripts/styles).
     */
    function shouldSkipElement(el) {
        if (!el || el.nodeType !== 1) return true;
        const tag = el.tagName;
        if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT' || tag === 'CODE' || tag === 'PRE') {
            return true;
        }
        if (el.closest('.lang-en, .lang-es, [data-no-i18n]')) {
            return true;
        }
        return false;
    }

    /**
     * Recorre y traduce (o restaura a español) todos los nodos de texto y atributos dentro de root.
     */
    function applyTranslationsToDOM(lang, root) {
        const container = root || document.body;
        if (!container) return;

        // 1. Nodos de texto
        const walker = document.createTreeWalker(
            container,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function (node) {
                    if (!node.nodeValue || !node.nodeValue.trim()) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    if (shouldSkipElement(node.parentElement)) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    return NodeFilter.FILTER_ACCEPT;
                }
            }
        );

        const textNodes = [];
        let currentNode;
        while ((currentNode = walker.nextNode())) {
            textNodes.push(currentNode);
        }

        for (let i = 0; i < textNodes.length; i++) {
            const node = textNodes[i];
            if (lang === 'en') {
                const sourceText = node.__slOrigEs !== undefined ? node.__slOrigEs : node.nodeValue;
                const translated = translateStringToEn(sourceText);
                if (translated !== null && translated !== node.nodeValue) {
                    if (node.__slOrigEs === undefined) {
                        node.__slOrigEs = node.nodeValue;
                    }
                    node.nodeValue = translated;
                }
            } else {
                if (node.__slOrigEs !== undefined) {
                    node.nodeValue = node.__slOrigEs;
                }
            }
        }

        // 2. Atributos traducibles (placeholder, title, aria-label)
        const attrSelector = '[placeholder], [title], [aria-label]';
        const elements = container.querySelectorAll ? container.querySelectorAll(attrSelector) : [];
        const attrs = ['placeholder', 'title', 'aria-label'];

        for (let i = 0; i < elements.length; i++) {
            const el = elements[i];
            if (shouldSkipElement(el)) continue;

            for (let j = 0; j < attrs.length; j++) {
                const attr = attrs[j];
                const val = el.getAttribute(attr);
                if (!val) continue;

                const origKey = '__slOrigAttr_' + attr;
                if (lang === 'en') {
                    const sourceVal = el[origKey] !== undefined ? el[origKey] : val;
                    const translated = translateStringToEn(sourceVal);
                    if (translated !== null && translated !== val) {
                        if (el[origKey] === undefined) {
                            el[origKey] = val;
                        }
                        el.setAttribute(attr, translated);
                    }
                } else {
                    if (el[origKey] !== undefined) {
                        el.setAttribute(attr, el[origKey]);
                    }
                }
            }
        }

        // 3. Sincronizar estado visual de todos los botones de idioma (#btnSwitchLang, .js-switch-lang)
        updateLanguageButtonsUI(lang);

        // 4. Sincronizar el widget de Spotlight IA si está presente
        syncChatbotLanguage(lang);
    }

    /**
     * Actualiza el indicador visual de todos los botones de cambio de idioma en la página.
     */
    function updateLanguageButtonsUI(lang) {
        const buttons = document.querySelectorAll('#btnSwitchLang, .js-switch-lang');
        buttons.forEach(btn => {
            btn.setAttribute('aria-label', lang === 'en' ? 'Switch language to Spanish' : 'Cambiar idioma a Inglés');
            btn.setAttribute('title', lang === 'en' ? 'Switch to Español (ES)' : 'Switch to English (EN)');
            btn.innerHTML = `<i class="bi bi-translate me-1"></i><span>${lang === 'en' ? 'ES / <strong>EN</strong>' : '<strong>ES</strong> / EN'}</span>`;
        });
    }

    /**
     * Sincroniza el selector de idioma interno del chatbot Spotlight IA con el idioma global.
     */
    function syncChatbotLanguage(lang) {
        const radioId = lang === 'en' ? 'sbot-lang-en' : 'sbot-lang-es';
        const radio = document.getElementById(radioId);
        if (radio && !radio.checked) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    /**
     * Establece el idioma global de Spotlight y persiste la preferencia.
     */
    function setSpotlightLanguage(lang) {
        const normalized = lang === 'en' ? 'en' : 'es';
        localStorage.setItem(STORAGE_KEY, normalized);
        if (document.documentElement.getAttribute('lang') !== normalized) {
            document.documentElement.setAttribute('lang', normalized);
        }
        applyTranslationsToDOM(normalized, document.body);
    }

    // Exponer API global por si algún componente la requiere
    window.setSpotlightLanguage = setSpotlightLanguage;
    window.getSpotlightLanguage = function () {
        return localStorage.getItem(STORAGE_KEY) || 'es';
    };

    // Aplicar atributo lang al <html> inmediatamente antes de que pinte el body
    const initialLang = localStorage.getItem(STORAGE_KEY) || 'es';
    document.documentElement.setAttribute('lang', initialLang);

    // Interceptar clics en cualquier botón #btnSwitchLang o .js-switch-lang en fase de captura
    // para evitar doble toggle en vistas que tenían listeners antiguos.
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('#btnSwitchLang, .js-switch-lang');
        if (!btn) return;
        e.preventDefault();
        e.stopImmediatePropagation();

        const current = document.documentElement.getAttribute('lang') || 'es';
        const nextLang = current === 'en' ? 'es' : 'en';
        setSpotlightLanguage(nextLang);
    }, true);

    // Si el usuario cambia el idioma dentro del header del Chatbot (ES / EN), sincronizar toda la página
    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList && e.target.classList.contains('sbot-lang-radio')) {
            setSpotlightLanguage(e.target.value);
        }
    });

    // Ejecutar al cargar el DOM y observar cambios dinámicos
    document.addEventListener('DOMContentLoaded', function () {
        const savedLang = localStorage.getItem(STORAGE_KEY) || 'es';
        setSpotlightLanguage(savedLang);

        // Observar inserciones dinámicas en el DOM (ej. mensajes del chatbot, popups del mapa)
        let debounceTimer = null;
        const observer = new MutationObserver(function (mutations) {
            const currentLang = document.documentElement.getAttribute('lang') || 'es';
            if (currentLang !== 'en') return;

            let hasAddedNodes = false;
            for (let i = 0; i < mutations.length; i++) {
                if (mutations[i].addedNodes && mutations[i].addedNodes.length > 0) {
                    hasAddedNodes = true;
                    break;
                }
            }
            if (!hasAddedNodes) return;

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                applyTranslationsToDOM('en', document.body);
            }, 60);
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
})();
