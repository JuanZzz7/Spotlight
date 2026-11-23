# Spotlight — Plataforma de Comercio Electrónico y Visibilidad Digital

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)

**Spotlight** es una plataforma web de comercio electrónico multi-vendedor y visibilidad digital diseñada para impulsar emprendimientos, productores y comercios locales en Ubaté (Cundinamarca) y mercados regionales aliados (Colombia y México).

---

## Características Principales

### 1. Marketplace y Experiencia del Comprador (`User`)
- **Catálogo Temático con Filtros Dinámicos**: Exploración por etiquetas comerciales, categorías, comercios verificados y ordenamiento con transiciones temáticas.
- **Conversión Multi-Moneda (`COP` / `MXN`)**: Visualización automática de precios en Pesos Colombianos con conversión de referencia para comercios internacionales.
- **Carrito Multi-Vendedor y Checkout Seguro**: Agrupación inteligente de órdenes por comercio emisor e integración con **Wompi** (Tarjetas, PSE, Nequi, Bancolombia) y **Mercado Pago**.
- **Mapa Interactivo de Comercios**: Geolocalización de tiendas físicas, horarios de apertura en tiempo real y catálogo rápido en mapa.
- **Rastreo de Envíos y Calificaciones Verificadas**: Seguimiento de guías paso a paso y sistema de reseñas vinculadas a compras reales.

### 2. Gestión Comercial y Facturación Electrónica (`Business`)
- **Panel Operativo y Financiero**: Control de ingresos, egresos con soportes en PDF, margen neto, inventario de productos con galería multi-imagen y exportación de reportes contables.
- **Facturación Electrónica DIAN (UBL 2.1)**:
  - Ciclo de habilitación fiscal para comercios formales (`CompanyFiscalProfile`) y guía de formalización empresarial para negocios informales.
  - Emisión de Facturas Electrónicas de Venta (`01`), Notas Crédito (`91`) y Notas Débito (`92`) con cálculo de **CUFE/CUDE SHA-384**, XML firmado y código QR oficial DIAN.
  - Arquitectura desacoplada compatible con proveedor tecnológico **Factus** y entorno **Sandbox DIAN**.
- **Centro Logístico de Despachos**: Generación de guías y actualización de estados de envío con bloqueo preventivo sobre órdenes sin pago verificado.
- **Canal de Mensajería Directa**: Chat en tiempo real entre clientes y comercios con control de disponibilidad y respuestas rápidas.

### 3. Administración y Auditoría (`Admin`)
- **Verificación Fiscal y Moderación**: Auditoría de documentos tributarios (RUT y Cámara de Comercio) para habilitar facturación electrónica en comercios formales.
- **Supervisión de Operaciones**: Gestión de usuarios, comercios, catálogo global, compras internacionales asistidas y métricas de la plataforma.

---

## Arquitectura del Proyecto

```text
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/               # Moderación, auditoría fiscal y reportes globales
│   │   │   ├── Api/                 # Webhooks de pago (Wompi / MercadoPago) y API de rastreo
│   │   │   ├── Auth/                # Autenticación, OAuth Google y verificación OTP
│   │   │   ├── Business/            # Panel comercial, productos, envíos y facturación DIAN
│   │   │   └── User/                # Marketplace, carrito, pedidos, perfil y mapa
│   │   ├── Middleware/              # Control de roles, cabeceras de seguridad y validación fiscal
│   │   └── Requests/                # Validaciones de formularios y checkout
│   ├── Models/                      # Modelos Eloquent (User, Company, Product, Order, Shipment, etc.)
│   ├── Policies/                    # Políticas de autorización sobre documentos electrónicos
│   └── Services/
│       ├── Billing/                 # Motor de Facturación Electrónica DIAN (Factus & Sandbox UBL 2.1)
│       ├── Payment/                 # Integración de pasarelas Wompi, MercadoPago y liquidaciones
│       └── Shipping/                # Adaptadores multi-transportadora y telemetría de envíos
├── config/                          # Configuración de aplicación, base de datos y proveedores
├── database/
│   ├── migrations/                  # Esquema relacional e histórico de migraciones
│   └── seeders/                     # Datos iniciales y catálogo de etiquetas
├── docs/                            # Documentación técnica y especificaciones de arquitectura
├── public/                          # Punto de entrada web y hojas de estilo modulares (Design System)
├── resources/views/                 # Vistas Blade organizadas por módulo (public, user, business, admin)
├── routes/                          # Definición de rutas web y endpoints API REST
└── tests/                           # Pruebas automatizadas (Feature & Unit)
```

---

## Requisitos e Instalación Local

### Requisitos Previos
- **PHP** >= 8.2 con extensiones `pdo_mysql`, `mbstring`, `openssl`, `curl`, `xml`, `gd`
- **Composer** >= 2.x
- **MySQL** >= 8.0 o MariaDB >= 10.6
- **Node.js** >= 18.x & NPM

### Pasos de Instalación

1. **Clonar el repositorio e instalar dependencias**:
   ```bash
   git clone https://github.com/JuanZzz7/Spotlight.git
   cd Spotlight
   composer install
   npm install
   ```

2. **Configurar variables de entorno**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Ajusta las credenciales de base de datos (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) y llaves de servicios externos en `.env`.

3. **Ejecutar migraciones, seeders y enlace simbólico de almacenamiento**:
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

4. **Iniciar el servidor de desarrollo**:
   ```bash
   php artisan serve
   ```
   La aplicación estará disponible en `http://127.0.0.1:8000`.

---

## Pruebas Automatizadas

Para ejecutar la suite de pruebas del sistema (incluyendo validaciones de facturación electrónica DIAN, reglas de negocio y seguridad):

```bash
php artisan test
```

---

## Documentación Técnica

La documentación detallada de cada subsistema se encuentra en el directorio [`docs/`](docs/):

- [**Facturación Electrónica DIAN (UBL 2.1 & Factus)**](docs/facturacion-electronica-dian.md): Arquitectura fiscal, modelo de datos, cálculo de CUFE SHA-384 y habilitación de comercios formales.
- [**API REST y Sistema de Rastreo Logístico**](docs/api-rastreo-envios.md): Especificación de endpoints `/api/v1/tracking`, adaptadores de transportadoras y estados de despacho.
- [**Especificación de Casos de Uso y Actores**](docs/casos-de-uso.md): Diagramas de arquitectura funcional y flujos por rol.
- [**Asistente Virtual Integrado**](docs/asistente-ia.md): Configuración del servicio de asistencia bilingüe y arquitectura de resiliencia.
