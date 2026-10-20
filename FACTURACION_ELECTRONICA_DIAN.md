# SPOTLIGHT — INTEGRACIÓN DE FACTURACIÓN ELECTRÓNICA DIAN (COLOMBIA)

Este documento detalla la arquitectura, el modelo de datos, la capa de servicios desacoplada, la normativa fiscal aplicada y los procedimientos operativos para la Facturación Electrónica en Spotlight (Ubaté, Cundinamarca).

---

## 1. DIAGNÓSTICO DE LA ARQUITECTURA EXISTENTE

Durante la auditoría inicial de Spotlight (Laravel 12, PHP 8.2+, MySQL / SQLite, Blade) se identificaron las siguientes condiciones:
1. **Comercios y Estado:** La tabla `companies` contaba con `tipo_negocio` (`formal`/`informal`) y `status` (`active`/`inactive`/`pending`). No existía distinción entre la existencia mercantil del negocio y su habilitación fiscal técnica y legal ante la DIAN.
2. **Productos:** La tabla `products` no contemplaba tarifas de IVA (0%, 5%, 19%), tipo de tributo, código estándar (UNSPSC) ni unidad de medida estándar DIAN (`94` - Unidad).
3. **Pedidos y Carrito Multi-Vendedor:** La tabla `orders` no estaba vinculada por vendedor; un cliente podía comprar productos de múltiples comercios en una misma orden sin separación contable ni identificación de emisor. `order_details` tenía eliminación en cascada (`onDelete('cascade')`), lo cual destruía el histórico contable si un producto se eliminaba del catálogo.
4. **Pagos:** El método `processPayment` en `CartController` simulaba un pago marcando la orden directamente como `paid`, sin desacoplar la orden comercial del evento fiscal de expedición.

---

## 2. ARQUITECTURA IMPLEMENTADA

### 2.1. Habilitación Fiscal Independiente (`CompanyFiscalProfile`)
Se implementó un ciclo de vida de habilitación tributaria independiente del estado general de la tienda:
- `not_requested`: Comercio no ha solicitado facturación electrónica.
- `pending_documentation`: Documentos tributarios pendientes.
- `in_review`: Solicitud enviada con RUT y Cámara de Comercio, en revisión por el Administrador de Spotlight.
- `rejected`: Solicitud rechazada con observaciones auditables registradas.
- `verified`: Verificación legal y tributaria aprobada por el Administrador.
- `pending_technical_setup`: Verificado legalmente, pendiente de configuración de software y resolución DIAN.
- `enabled`: Habilitado para emitir facturación electrónica (validación técnica y legal superada).
- `suspended`: Habilitación suspendida temporalmente por administración.

> **Regla de Bloqueo:** Los comercios informales no pueden acceder a la emisión. Una empresa formal no verificada o sin configuración técnica tampoco puede emitir facturas.

### 2.2. Capa de Servicios Desacoplada (`App\Services\Billing`)
- **Contrato (`BillingProviderInterface`):**
  - `validateConnection`: Prueba de conectividad y verificación de rangos.
  - `emitInvoice`: Emisión de factura electrónica de venta (01).
  - `emitCreditNote`: Emisión de nota crédito (91).
  - `emitDebitNote`: Emisión de nota débito (92).
  - `queryDocumentStatus`: Consulta de validación previa ante la DIAN.
  - `downloadXml`: Obtención del archivo XML UBL 2.1 firmado.
  - `downloadPdf`: Obtención o renderizado de representación gráfica.
- **Adaptador de Pruebas (`SandboxBillingProvider`):**
  - Implementación completa basada en el Anexo Técnico 1.9 de la DIAN.
  - Cálculo oficial del CUFE en SHA-384.
  - Generación de XML UBL 2.1 con extensiones DIAN (`DianExtensions`).
  - Generación de código QR reglamentario apuntando al catálogo DIAN.
  - Validación de reglas de negocio (rangos de numeración, vigencia de resolución).
- **Proveedor Tecnológico Autorizado (`FactusBillingProvider`):**
  - Integración REST API con el proveedor tecnológico Factus Colombia (Sandbox y Producción).
  - Autenticación OAuth2 (`/oauth/token`).
  - Validación de facturas (`POST /v1/bills/validate`), notas crédito (`POST /v1/credit-notes/validate`), consulta y descarga de XML/PDF.
- **Gestor de Proveedores (`BillingManager`):** Factory para instanciar el proveedor configurado para cada comercio.
- **Servicio Central (`ElectronicDocumentService`):**
  - Bloqueo pesimista (`lockForUpdate`) para asignación atómica e inmutable de consecutivos sin saltos ni duplicados.
  - Preservación de snapshots históricos de emisor, adquirente y productos.
  - Registro de auditoría y trazabilidad en `billing_logs`.
  - Almacenamiento seguro en disco privado (`storage/app/electronic_documents/...`).

---

## 3. SEPARACIÓN DE PEDIDOS MULTI-VENDEDOR Y CONTROL DE PAGOS

### 3.1. Tratamiento Legal del Carrito Multi-Vendedor
Spotlight actúa como plataforma de intermediación tecnológica y no como mandatario comercial central con representación. Conforme al Estatuto Tributario colombiano y el Anexo Técnico DIAN:
- Cada comercio emisor debe facturar única y exclusivamente los bienes y servicios que él mismo suministra.
- Al procesar el checkout, los ítems del carrito se agrupan por `company_id`.
- Se genera un pedido (`Order`) independiente por cada vendedor.
- Los detalles de la orden (`OrderDetail`) preservan snapshots inmutables: `product_name`, `product_sku`, `unit_price`, `tax_type`, `tax_rate`, `tax_amount`.
- Si el comercio edita el precio, nombre o elimina el producto en el futuro, las órdenes históricas y facturas emitidas permanecen 100% inalteradas.
- La eliminación física de productos con ventas registradas está bloqueada en los controladores empresarial y administrativo para proteger la trazabilidad contable.

---

## 4. BASE DE DATOS Y NUEVAS TABLAS

1. `company_fiscal_profiles`:
   - Datos fiscales DIAN (Tipo persona, NIT, DV calculado mediante Módulo 11, Razón Social, Régimen fiscal, Responsabilidades O-13, O-15, O-23, O-47, R-99-PN, Código CIIU, Domicilio en Ubaté 25843).
   - Documentación de respaldo (RUT, Cámara de Comercio, Cédula del representante).
   - Trazabilidad de aprobación (`submitted_at`, `verified_at`, `verified_by_user_id`, `enabled_at`).
2. `billing_configurations`:
   - Proveedor tecnológico (`sandbox`, `factus`), ambiente (`sandbox`, `production`).
   - Resolución DIAN (número, prefijo, rango desde-hasta, consecutivo actual, clave técnica, fechas de vigencia).
   - Consecutivos y prefijos de notas crédito (`NC`) y notas débito (`ND`).
   - Credenciales cifradas (`api_token`, `api_secret`, `software_id`, `software_pin`, `test_set_id`).
3. `products` (Campos añadidos):
   - `sku`, `tax_type` (01: IVA), `tax_rate` (19.00, 5.00, 0.00), `unit_measure` (94: Unidad), `standard_code`.
4. `orders` y `order_details` (Campos añadidos):
   - `orders.company_id`, `orders.billing_status`, `orders.payment_method_code`.
   - `order_details.company_id`, `order_details.product_name`, `order_details.product_sku`, `order_details.tax_type`, `order_details.tax_rate`, `order_details.tax_amount`.
5. `electronic_documents`:
   - Facturas (01), Notas Crédito (91), Notas Débito (92).
   - Consecutivo, número completo (`SETP-100`), CUFE / CUDE SHA-384, fecha y hora de emisión.
   - Snapshots inmutables de emisor y adquirente.
   - Totales monetarios: Base gravable, Descuentos, IVA total, Total a pagar.
   - Estado DIAN (`draft`, `sent`, `validated`, `rejected`, `cancelled`), mensajes de validación y errores.
   - Rutas privadas de XML UBL y Representación Gráfica PDF/HTML.
6. `electronic_document_items`:
   - Líneas de factura con base gravable, tasa de impuesto y valor de IVA discriminado.
7. `billing_logs`:
   - Bitácora de peticiones, endpoints, cargas útiles, respuestas JSON, estado HTTP y clave de idempotencia.

---

## 5. VARIABLES DE ENTORNO (.env)

```env
# Facturación Electrónica DIAN
DIAN_BILLING_PROVIDER=sandbox
DIAN_ENVIRONMENT=sandbox
DIAN_QR_BASE_URL="https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey="
FACTUS_SANDBOX_URL="https://api-sandbox.factus.co"
FACTUS_PROD_URL="https://api.factus.co"
FACTUS_CLIENT_ID=
FACTUS_CLIENT_SECRET=
```

---

## 6. INSTRUCCIONES PARA EJECUTAR EN XAMPP

1. Iniciar Apache y MySQL en el Panel de Control de XAMPP.
2. Si se utiliza la base de datos MySQL de XAMPP:
   - Crear la base de datos en phpMyAdmin: `CREATE DATABASE spotlight CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
   - Configurar en `.env`:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=spotlight
     DB_USERNAME=root
     DB_PASSWORD=
     ```
3. Ejecutar las migraciones:
   ```bash
   php artisan migrate
   ```
4. Para correr la suite de pruebas automatizadas:
   ```bash
   php artisan test
   ```

---

## 7. PASOS PARA HABILITAR EL AMBIENTE DE PRUEBAS DIAN

1. **Perfil Fiscal:** El comerciante formal ingresa a `/business/billing/profile` y completa su Razón Social, NIT, CIIU y adjunta su RUT y Cámara de Comercio.
2. **Revisión Administrativa:** El administrador ingresa a `/admin/fiscal-verifications`, revisa los documentos y aprueba la verificación legal.
3. **Configuración Técnica:** El comerciante ingresa a `/business/billing/configuration`, selecciona el proveedor (`Sandbox` o `Factus`), ingresa los rangos de prueba autorizados y la clave técnica, y pulsa **"Probar Conexión y Activar"**.
4. **Emisión de Pruebas:**
   - Ingresar a `/business/billing/sales` para facturar un pedido de prueba o a `/business/billing/invoices/create` para una factura directa.
   - El sistema calculará el CUFE oficial en SHA-384, generará el XML UBL 2.1 y la representación gráfica con código QR.
   - Para anulación, se ingresa al detalle del documento (`/business/billing/documents/{id}`) y se emite la Nota Crédito correspondiente.

---

## 8. REQUISITOS PENDIENTES PARA SALIR A PRODUCCIÓN

1. **Resolución de Facturación en Producción:** Cada comercio emisor debe solicitar ante la DIAN su resolución de numeración de facturación electrónica de venta (Formulario 1876) mediante la plataforma Muisca.
2. **Asociación de Software:** Registrar el software (propio o del proveedor tecnológico) en el portal de la DIAN para obtener el `Software ID` y la `Clave Técnica` de producción.
3. **Certificado Digital / Firma Electrónica:** Si se utiliza software propio, contar con el certificado digital X.509 vigente de firma digital emitido por una entidad de certificación abierta autorizada por la ONAC (ej. Certicámara, GSE). Si se utiliza un proveedor tecnológico como Factus, la firma delegada la asume el proveedor.
4. **Recepción Electrónica y Radian (Opcional):** Si los comercios emiten facturas a crédito que circularán como título valor, implementar los eventos de acuse de recibo y recepción de bienes del sistema Radian.

---

## 9. ADVERTENCIA TRIBUTARIA Y VALIDACIÓN PROFESIONAL

> [!WARNING]
> La implementación provista en este software cubre la arquitectura técnica, estructuras UBL 2.1, cálculos matemáticos, políticas de autorización y flujos operativos conforme al Anexo Técnico 1.9 de la DIAN. Sin embargo, **Spotlight no sustituye la asesoría de un contador público titulado o especialista tributario**. Cada comerciante es legalmente responsable de sus declaraciones tributarias periódicas, aplicación de retenciones en la fuente, retenciones de ICA en el municipio de Ubaté y exactitud de sus responsabilidades fiscales registradas en el RUT.
