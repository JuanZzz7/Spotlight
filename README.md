# Spotlight

Plataforma de comercio digital con 3 roles: Administrador, Empresario y Usuario.  
Stack: Laravel 12 · PHP 8.2+ · MySQL · Bootstrap 5

---

## Instalación

### 1. Crear proyecto Laravel e instalar dependencias

```bash
composer create-project laravel/laravel spotlight
cd spotlight
```

### 2. Copiar los archivos de este ZIP sobre el proyecto

Reemplaza los archivos existentes. Los directorios principales a sobrescribir son:
- `app/`
- `database/`
- `resources/views/`
- `routes/web.php`
- `bootstrap/app.php`

### 3. Configurar .env

```env
APP_NAME="Spotlight"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visory_shop
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Crear base de datos

```sql
CREATE DATABASE visory_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

### 6. Crear enlace de almacenamiento

```bash
php artisan storage:link
```

### 7. Iniciar servidor

```bash
php artisan serve
```

Accede en: http://localhost:8000

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── CompanyController.php
│   │   │   ├── ProductController.php
│   │   │   └── ReportController.php
│   │   ├── Business/
│   │   │   ├── DashboardController.php
│   │   │   ├── CompanyController.php
│   │   │   ├── ProductController.php
│   │   │   └── ExpenseController.php
│   │   └── User/
│   │       ├── DashboardController.php
│   │       ├── ProfileController.php
│   │       ├── CartController.php
│   │       └── OrderController.php
│   └── Middleware/
│       └── RoleMiddleware.php
├── Models/
│   ├── User.php
│   ├── Company.php
│   ├── Product.php
│   ├── Order.php
│   ├── OrderDetail.php
│   └── Expense.php
database/
├── migrations/
└── seeders/
    └── DatabaseSeeder.php
resources/views/
├── layouts/
│   ├── app.blade.php
│   └── dashboard.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── home/index.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── users/
│   ├── companies/
│   ├── products/
│   └── reports/
├── business/
│   ├── dashboard.blade.php
│   ├── company/
│   ├── products/
│   └── expenses/
└── user/
    ├── dashboard.blade.php
    ├── profile/
    ├── cart/
    └── orders/
routes/web.php
bootstrap/app.php
```

---

## Integración de IA (Spotlight AI Chatbot)

El proyecto incluye un asistente virtual inteligente impulsado por IA, diseñado para potenciar la experiencia de los usuarios en el marketplace, respetando principios de ética y autonomía.

### Arquitectura Técnica
- **Motor principal**: Google Gemini 2.5 Flash API (v1beta).
- **Backend (Laravel)**: `ChatbotService.php` gestiona la lógica, el enrutamiento de la API, y el manejo de excepciones. Las llamadas se realizan mediante `cURL` con verificación SSL adaptada para entornos locales (XAMPP).
- **Frontend (Blade/JS)**: Interfaz construida con un diseño *glassmorphic* (sistema Stitch), integrada directamente en el menú de navegación del usuario. Incluye animaciones fluidas, chips de respuestas rápidas y control de estado (typing indicators).
- **Soporte Bilingüe**: Detección automática del idioma basada en análisis léxico en el backend, complementada con un selector manual (ES/EN) en la interfaz gráfica que traduce dinámicamente toda la UI y fuerza a la IA a responder en el idioma seleccionado.

### Declaración Persona Transhumana (Ideal Regulativo)
El chatbot está fundamentado en principios de desarrollo humano y responsabilidad. Su *System Prompt* y su interfaz gráfica exponen la siguiente declaración institucional como núcleo filosófico:

> *"Soy LIBRE, AUTÓNOMO Y RESPONSABLE a través del diálogo y la construcción, como ideal regulativo; me dirijo, controlo y dicto mis propias leyes."*

Esta declaración actúa como la directriz principal del bot, asegurando que sus respuestas promuevan el pensamiento crítico, el conocimiento y el empoderamiento del usuario local.

### Sistema de Fallback (Resiliencia)
En caso de que la API de Google Gemini alcance su límite de peticiones (Rate Limit) o la API Key sea inválida, el sistema cuenta con un modelo de *Fallback Local Inteligente*. Este mecanismo asegura que el bot siempre responda usando algoritmos de expresiones regulares (`preg_match`) para ofrecer respuestas estructuradas sobre ética, conocimiento y la plataforma Spotlight, garantizando 100% de disponibilidad.

---

## Módulo de Rastreo Logístico en Tiempo Real (API REST)

Spotlight cuenta con un subsistema logístico desacoplado bajo el patrón **Adapter / Multi-Carrier**, permitiendo que compradores y vendedores rastreen paquetes con telemetría en tiempo real.

> 📖 **Documentación Técnica Completa:** Consulta [RASTREO_ENVIOS_API.md](file:///c:/xampp/htdocs/Spotligth/RASTREO_ENVIOS_API.md) para la especificación detallada de los endpoints, payloads JSON, adaptadores de transportadoras y diagramas de flujo.

### Endpoints de la API (`routes/api.php`)
* `GET /api/v1/tracking/{tracking_number}`: API pública de consulta en tiempo real por número de guía.
* `GET /api/v1/orders/{order}/tracking`: API autenticada para consulta de rastreo del pedido del comprador.
* `POST /api/v1/shipments/{shipment}/simulate-advance`: Simulador de avance de estado para demostraciones interactivas.

### Transportadoras Soportadas (`App\Services\Shipping`)
* **Coordinadora Mercantil** (`coordinadora`)
* **Servientrega** (`servientrega`)
* **Inter Rapidísimo** (`interrapidisimo`)
* **Envía Colvanes** (`envia`)
* **DHL Express** (`dhl`)
* **Spotlight Express Local** (`spotlight_express`)

### Vistas y Flujo Operativo
* **Comprador:** En **Mis Compras** (`/user/orders`), cada pedido cuenta con el botón **"Rastrear Envío"** que abre el dashboard interactivo (`/user/orders/{order}/tracking`) con stepper visual, pulso en vivo y actualización asíncrona periódica (polling cada 15s).
* **Portal Público:** Portal abierto de búsqueda de guías en `/rastreo`.
* **Vendedor:** Centro de despachos en `/business/shipments` para asignar transportadoras, generar números de guía y emitir checkpoints.

---

## Módulo de Calificaciones y Reseñas de Productos

Sistema de reputación y feedback transparente integrado en el catálogo de productos:

* **Modelo `Review`:** Calificación de 1 a 5 estrellas, comentario en texto e insignia automática de **"Compra Verificada"** (`is_verified_purchase`) cruzada con el historial de pedidos completados del cliente.
* **Scorecard Dinámico:** Desglose porcentual y barras de distribución por estrella en `Product::rating_stars_array`.
* **Vista Pública de Producto (`/productos/{product}`):** Ficha detallada con formulario interactivo de calificación, edición sin duplicidad (`updateOrCreate`) y muro cronológico de opiniones de la comunidad.
* **Integración en Mis Compras (`/user/orders`):** Botón directo **"Calificar"** en cada producto adquirido.

---

## Módulo de Facturación Electrónica DIAN

Integración formal y legal de Facturación Electrónica DIAN bajo normativa UBL 2.1 colombiana:

* **Habilitación y Verificación:** Separación de roles formales/informales mediante `CompanyFiscalProfile`.
* **Cálculo de CUFE SHA-384 y XML UBL:** Generado en `App\Services\Billing\ElectronicDocumentService`.
* **Documentación Técnica:** Ver [FACTURACION_ELECTRONICA_DIAN.md](file:///c:/xampp/htdocs/Spotligth/FACTURACION_ELECTRONICA_DIAN.md).

---

## Pruebas Automatizadas

La suite completa de pruebas unitarias y de integración se ejecuta con:

```bash
php artisan test
```

* **Pruebas de Rastreo Logístico:** `php artisan test --filter=ShipmentTrackingTest`
* **Pruebas de Calificaciones y Reseñas:** `php artisan test --filter=ProductReviewTest`
* **Pruebas de Facturación Electrónica:** `php artisan test --filter=ElectronicBillingTest`
* **Pruebas de Autenticación y Usuarios:** `php artisan test --filter=UserLoginTest`
