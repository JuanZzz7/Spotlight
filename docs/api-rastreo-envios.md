# SPOTLIGHT — API Y SISTEMA DE RASTREO LOGÍSTICO EN TIEMPO REAL

Este documento detalla la arquitectura, endpoints de la API, modelo de datos, adaptadores de transportadoras y flujos operativos del **Sistema de Rastreo Logístico en Tiempo Real** para la plataforma **Spotlight**.

---

## 1. RESUMEN DEL SISTEMA

El módulo logístico de Spotlight permite el seguimiento transparente y en tiempo real de los pedidos realizados en el marketplace. Cuando un comprador finaliza su compra en el carrito, se inicializa automáticamente una entidad de despacho (`Shipment`) que acompaña el ciclo de vida del paquete desde su empaque hasta la entrega final al destinatario.

### Características Principales
* **API REST Nativa:** Endpoints JSON para consulta por número de guía y por orden de compra.
* **Arquitectura Multi-Transportadora:** Conectores desacoplados para las principales empresas de logística de Colombia e internacionales.
* **Línea de Tiempo en Vivo:** Registro de eventos (checkpoints) con fecha, hora, ubicación geográfica e icono representativo.
* **Actualización Asíncrona:** Interfaz con polling automático en segundo plano sin recargas de página.
* **Motor de Simulación Integrado:** Endpoint para avanzar de forma interactiva el estado del paquete durante pruebas o demostraciones.
* **Panel de Despachos para Vendedores:** Gestión y generación de guías oficiales desde el panel empresarial.

---

## 2. ARQUITECTURA Y PATRÓN DE DISEÑO

El sistema está construido siguiendo el patrón **Adapter / Provider**:

```
[ Cliente / Frontend / App Móvil ]
                 │
                 ▼  (HTTP JSON)
[ ShipmentTrackingApiController ] ───> [ routes/api.php ]
                 │
                 ▼
[ ShipmentTrackingService ]
        │                 │
        ▼                 ▼
[ Eloquent Models ]   [ CarrierApiService ] ───> [ ShipmentCarrierInterface ]
(Shipment & Events)       │
                          ├── Coordinadora Mercantil (CRD)
                          ├── Servientrega (SER)
                          ├── Inter Rapidísimo (IRP)
                          ├── Envía Colvanes (ENV)
                          ├── DHL Express (DHL)
                          └── Spotlight Express Local (SLX)
```

### Contrato [`ShipmentCarrierInterface`](app/Services/Shipping/Contracts/ShipmentCarrierInterface.php)
Define las operaciones requeridas por cualquier proveedor logístico:
* `generateTrackingNumber(string $carrier): string`: Genera el código alfanumérico según la nomenclatura de la transportadora.
* `fetchTrackingData(string $trackingNumber): array`: Consulta la telemetría y estado en el servicio externo.
* `estimateDeliveryDate(string $origin, string $destination, string $carrier): Carbon`: Calcula los días hábiles según la distancia y el operador.

---

## 3. CATÁLOGO DE TRANSPORTADORAS INTEGRADAS

| Slug | Nombre Oficial | Prefijo Guía | Cobertura | Teléfono Soporte |
| :--- | :--- | :--- | :--- | :--- |
| `coordinadora` | Coordinadora Mercantil | `CRD` | Nacional (Colombia) | 01 8000 915 525 |
| `servientrega` | Servientrega | `SER` | Nacional (Colombia) | 01 8000 518 888 |
| `interrapidisimo` | Inter Rapidísimo | `IRP` | Nacional (Colombia) | (601) 560 5000 |
| `envia` | Envía Colvanes | `ENV` | Nacional (Colombia) | 01 8000 111 210 |
| `dhl` | DHL Express | `DHL` | Internacional | 01 8000 123 345 |
| `spotlight_express` | Spotlight Express Local | `SLX` | Local / Aldea Digital | +57 (300) 123 4567 |

---

## 4. ESPECIFICACIÓN DE LA API REST (JSON)

### 4.1. Consulta Pública de Rastreo por Número de Guía

* **Endpoint:** `GET /api/v1/tracking/{tracking_number}`
* **Acceso:** Público (no requiere autenticación ni cookies).
* **Parámetros URL:**
  * `tracking_number`: Código de guía (ejemplo: `CRD-261019-8A1B2`).

#### Ejemplo de Petición cURL:
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/tracking/CRD-261019-8A1B2" \
     -H "Accept: application/json"
```

#### Ejemplo de Respuesta Exitosa (`200 OK`):
```json
{
  "success": true,
  "tracking_number": "CRD-261019-8A1B2",
  "carrier": {
    "code": "coordinadora",
    "name": "Coordinadora Mercantil",
    "phone": "01 8000 915 525",
    "website": "https://www.coordinadora.com",
    "logo_icon": "bi-truck",
    "color": "#00529b"
  },
  "order": {
    "id": 14,
    "status": "shipped",
    "total": 180000,
    "company": "Artesanías del Valle"
  },
  "status": {
    "code": "in_transit",
    "label": "En Tránsito hacia Destino",
    "color": "#8b5cf6",
    "progress_percent": 72,
    "is_delivered": false
  },
  "current_location": "Centro Logístico Siberia, Bogotá",
  "origin_city": "Ubaté",
  "destination_city": "Bogotá D.C.",
  "destination_address": "Carrera 15 # 85-30, Chapinero",
  "recipient_name": "Valeria Gómez",
  "estimated_delivery_at": "21 de Octubre, 2026 - 18:00",
  "shipped_at": "19 de Octubre, 2026 - 10:45",
  "delivered_at": null,
  "live_api": {
    "provider_status": "ONLINE",
    "api_latency_ms": 68,
    "last_sync_at": "2026-10-19T10:48:00-05:00"
  },
  "timeline": [
    {
      "id": 28,
      "status": "in_transit",
      "title": "En tránsito nacional hacia destino",
      "description": "Vehículo de carga en movimiento intermunicipal hacia terminal destino.",
      "location": "Centro Logístico Siberia, Bogotá",
      "occurred_at": "19 Oct, 2026 - 11:15 AM",
      "icon": "bi-arrow-left-right"
    },
    {
      "id": 27,
      "status": "dispatched",
      "title": "Paquete recolectado y despachado",
      "description": "Guía CRD-261019-8A1B2 generada formalmente con Coordinadora Mercantil.",
      "location": "Ubaté",
      "occurred_at": "19 Oct, 2026 - 10:45 AM",
      "icon": "bi-truck"
    },
    {
      "id": 26,
      "status": "preparing",
      "title": "Paquete en preparación y embalaje",
      "description": "El vendedor Artesanías del Valle está empacando y verificando tus productos.",
      "location": "Ubaté",
      "occurred_at": "19 Oct, 2026 - 10:20 AM",
      "icon": "bi-box-seam"
    },
    {
      "id": 25,
      "status": "order_confirmed",
      "title": "Pago confirmado y orden registrada",
      "description": "Tu compra fue aprobada con éxito. El comercio ha recibido la orden.",
      "location": "Ubaté",
      "occurred_at": "19 Oct, 2026 - 10:15 AM",
      "icon": "bi-receipt"
    }
  ],
  "server_time": "2026-10-19T12:00:00-05:00"
}
```

#### Respuesta de Error (`404 Not Found`):
```json
{
  "success": false,
  "message": "No se encontró ningún envío asociado al número de guía 'GUIA-INEXISTENTE'. Por favor verifica el código."
}
```

---

### 4.2. Consulta de Rastreo por ID de Pedido (Comprador)

* **Endpoint:** `GET /api/v1/orders/{order}/tracking`
* **Acceso:** Autenticado (`buyer`, `business` vendedor o `admin`).
* Retorna la misma estructura JSON que la consulta por número de guía, verificando que el usuario tenga autorización sobre la orden.

---

### 4.3. Simulación de Avance en Tiempo Real

* **Endpoint:** `POST /api/v1/shipments/{shipment}/simulate-advance`
* **Acceso:** Autorizado / pruebas.
* **Propósito:** Avanza el envío paso a paso por su ciclo natural:
  $$\text{preparing} \longrightarrow \text{dispatched} \longrightarrow \text{in\_transit} \longrightarrow \text{out\_for\_delivery} \longrightarrow \text{delivered}$$
* Cada avance genera automáticamente un nuevo evento en `shipment_events` y actualiza la ubicación y fecha de entrega.

---

## 5. MODELO DE DATOS Y BASE DE DATOS

### Tabla `shipments`
```sql
CREATE TABLE `shipments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `tracking_number` VARCHAR(60) NOT NULL UNIQUE,
  `carrier` VARCHAR(40) NOT NULL DEFAULT 'coordinadora',
  `carrier_name` VARCHAR(80) NOT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'preparing',
  `origin_city` VARCHAR(100) NOT NULL,
  `destination_city` VARCHAR(100) NOT NULL,
  `destination_address` TEXT NOT NULL,
  `recipient_name` VARCHAR(150) NOT NULL,
  `recipient_phone` VARCHAR(40) NULL,
  `current_location` VARCHAR(150) NULL,
  `estimated_delivery_at` DATETIME NULL,
  `shipped_at` DATETIME NULL,
  `delivered_at` DATETIME NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
);
```

### Tabla `shipment_events`
```sql
CREATE TABLE `shipment_events` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `shipment_id` BIGINT UNSIGNED NOT NULL,
  `status` VARCHAR(40) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `location` VARCHAR(150) NULL,
  `occurred_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`shipment_id`) REFERENCES `shipments`(`id`) ON DELETE CASCADE
);
```

---

## 6. RUTAS WEB Y EXPERIENCIA DE USUARIO

| URL | Ruta Nombrada | Controlador | Vista |
| :--- | :--- | :--- | :--- |
| `/user/orders` | `user.orders.index` | `OrderController@index` | Listado de pedidos con botón y badge de rastreo |
| `/user/orders/{order}/tracking` | `user.orders.tracking` | `OrderController@tracking` | Pantalla completa interactiva con stepper y eventos en vivo |
| `/rastreo?guia={numero}` | `public.tracking` | `PublicController@trackingPortal` | Portal público de rastreo abierto a clientes externos |
| `/business/shipments` | `business.shipments.index` | `ShipmentController@index` | Centro de despachos para el vendedor |
| `POST /business/shipments/{shipment}/dispatch` | `business.shipments.dispatch` | `ShipmentController@dispatchOrder` | Despacho formal de la orden |
| `POST /business/shipments/{shipment}/status` | `business.shipments.status` | `ShipmentController@updateStatus` | Actualización de checkpoints y ubicación |

---

## 7. CÓMO PROBAR EL SISTEMA

### 1. Ejecutar las Pruebas Automatizadas
```bash
php artisan test --filter=ShipmentTrackingTest
```

### 2. Probar el Rastreo en el Navegador
1. Inicia sesión como usuario comprador (`ana@ejemplo.com` o tu usuario de prueba).
2. Ve a **Mis Compras** (`/user/orders`).
3. Haz clic en el botón **"Rastrear Envío"**.
4. Podrás ver el stepper, el número de guía, la transportadora y la línea de tiempo.
5. Puedes hacer clic en **"Simular Avance En Vivo"** para ver cómo la barra y el estado avanzan a *Despachado*, *En Tránsito*, *En Reparto* y *Entregado* sin recargar la pantalla.

### 3. Probar el Despacho como Vendedor
1. Inicia sesión con una cuenta de comercio (rol `business`).
2. En el menú lateral, selecciona **"Despachos y Envíos"** (o desde el Dashboard en *Gestionar despachos*).
3. Selecciona una orden y pulsa **"Despachar"**.
4. Elige la transportadora (ej. *Coordinadora* o *Servientrega*), confirma la guía y pulsa **Confirmar Despacho**.
5. Inmediatamente el comprador verá su orden actualizada a "Despachado con transportadora".
