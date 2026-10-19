# 🏗️ Documentación de Arquitectura y Diagramas del Sistema - Visory Shop (Spotlight)

Documento técnico que reúne el **Diagrama Entidad-Relación (Conceptual)**, **Diagrama Físico de Base de Datos**, **Diagrama de Clases UML**, **Diagrama de Componentes** y **Diagramas de Flujo de Datos (DFD Nivel 0 y Nivel 1)** del proyecto **Visory Shop (Spotlight)**.

---

## 1. 🗂️ Diagrama Entidad-Relación (Modelo Conceptual)

Representa las entidades principales del dominio de negocio y la cardinalidad de sus relaciones conceptuales:

- Un **Usuario** (`USER`) con rol empresarial administra una **Empresa** (`COMPANY`) `(1:1)`.
- Un **Usuario** (`USER`) con rol comprador realiza cero o muchos **Pedidos** (`ORDER`) `(1:N)`.
- Un **Usuario** (`USER`) envía y recibe cero o muchos **Mensajes** (`MESSAGE`) `(1:N)`.
- Una **Empresa** (`COMPANY`) publica cero o muchos **Productos** (`PRODUCT`) `(1:N)`.
- Una **Empresa** (`COMPANY`) registra cero o muchos **Gastos Operativos** (`EXPENSE`) `(1:N)`.
- Un **Pedido** (`ORDER`) se compone de uno o muchos **Detalles de Pedido** (`ORDER_DETAIL`) `(1:N)`.
- Un **Producto** (`PRODUCT`) puede estar referenciado en cero o muchos **Detalles de Pedido** (`ORDER_DETAIL`) `(1:N)`.

```mermaid
erDiagram
    USER ||--o| COMPANY : "posee o administra"
    USER ||--o{ ORDER : "realiza"
    USER ||--o{ MESSAGE : "envia"
    USER ||--o{ MESSAGE : "recibe"
    COMPANY ||--o{ PRODUCT : "publica"
    COMPANY ||--o{ EXPENSE : "registra"
    ORDER ||--|{ ORDER_DETAIL : "contiene"
    PRODUCT ||--o{ ORDER_DETAIL : "incluido en"
```

---

## 2. 🗄️ Diagrama Físico de Base de Datos (Esquema Relacional)

Especificación detallada de todas las tablas de la base de datos relacional (MySQL), incluyendo llaves primarias (`PK`), llaves foráneas (`FK`), llaves únicas (`UK`) y tipos de datos exactos definidos en las migraciones de Laravel:

```mermaid
erDiagram
    users {
        bigint id PK
        varchar name
        varchar email UK
        varchar password
        varchar google_id
        timestamp email_verified_at
        enum role "user, business, admin"
        varchar phone
        varchar address
        varchar document
        varchar avatar
        boolean active
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }

    companies {
        bigint id PK
        bigint user_id FK
        varchar name
        enum tipo_negocio "formal, informal"
        varchar razon_social
        varchar nit
        varchar camara_comercio_file
        varchar rut_file
        varchar nombre_representante
        varchar email_representante
        varchar nombre_comercial
        varchar cedula_propietario
        varchar rut_personal_file
        boolean habeas_data_accepted
        varchar logo
        varchar banner
        text description
        varchar category
        varchar address
        varchar phone
        varchar email
        varchar instagram
        varchar facebook
        varchar website
        decimal latitude "precision 10,8"
        decimal longitude "precision 11,8"
        enum status "active, inactive, pending"
        varchar document_update_status
        text document_update_reason
        timestamp created_at
        timestamp updated_at
    }

    products {
        bigint id PK
        bigint company_id FK
        varchar name
        text description
        decimal price "precision 10,2"
        int stock
        varchar image
        boolean active
        boolean featured
        timestamp created_at
        timestamp updated_at
    }

    orders {
        bigint id PK
        bigint user_id FK
        decimal total "precision 10,2"
        enum status "pending, paid, shipped, delivered"
        timestamp created_at
        timestamp updated_at
    }

    order_details {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        int quantity
        decimal unit_price "precision 10,2"
        decimal subtotal "precision 10,2"
        timestamp created_at
        timestamp updated_at
    }

    expenses {
        bigint id PK
        bigint company_id FK
        varchar concept
        decimal amount "precision 10,2"
        date date
        text description
        timestamp created_at
        timestamp updated_at
    }

    messages {
        bigint id PK
        bigint sender_id FK
        bigint receiver_id FK
        text message
        boolean is_read
        timestamp created_at
        timestamp updated_at
    }

    sessions {
        varchar id PK
        bigint user_id FK
        varchar ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    password_reset_tokens {
        varchar email PK
        varchar token
        timestamp created_at
    }

    users ||--o| companies : "user_id"
    users ||--o{ orders : "user_id"
    users ||--o{ messages : "sender_id"
    users ||--o{ messages : "receiver_id"
    users ||--o{ sessions : "user_id"
    companies ||--o{ products : "company_id"
    companies ||--o{ expenses : "company_id"
    orders ||--|{ order_details : "order_id"
    products ||--o{ order_details : "product_id"
```

---

## 3. 🏛️ Diagramas de Clases UML

### 3.1 Diagrama de Clases de Dominio (Modelos Eloquent)

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        -string password
        +string role
        +string phone
        +string address
        +string document
        +string avatar
        +bool active
        +string google_id
        +datetime email_verified_at
        +company() HasOne
        +orders() HasMany
        +isAdmin() bool
        +isBusiness() bool
        +isUser() bool
    }

    class Company {
        +int id
        +int user_id
        +string name
        +string description
        +string category
        +string logo
        +string banner
        +string tipo_negocio
        +string razon_social
        +string nit
        +string nombre_comercial
        +string cedula_propietario
        +bool habeas_data_accepted
        +string camara_comercio_file
        +string rut_file
        +string rut_personal_file
        +string address
        +string phone
        +string email
        +string instagram
        +string facebook
        +string website
        +float latitude
        +float longitude
        +string status
        +string document_update_status
        +string document_update_reason
        +user() BelongsTo
        +products() HasMany
        +expenses() HasMany
        +orderDetails() HasManyThrough
    }

    class Product {
        +int id
        +int company_id
        +string name
        +string description
        +float price
        +int stock
        +string image
        +bool active
        +bool featured
        +company() BelongsTo
        +orderDetails() HasMany
        +getImageUrlAttribute() string
    }

    class Order {
        +int id
        +int user_id
        +float total
        +string status
        +user() BelongsTo
        +details() HasMany
    }

    class OrderDetail {
        +int id
        +int order_id
        +int product_id
        +int quantity
        +float unit_price
        +float subtotal
        +order() BelongsTo
        +product() BelongsTo
    }

    class Expense {
        +int id
        +int company_id
        +string concept
        +float amount
        +date date
        +string description
        +company() BelongsTo
    }

    class Message {
        +int id
        +int sender_id
        +int receiver_id
        +string message
        +bool is_read
        +sender() BelongsTo
        +receiver() BelongsTo
    }

    User "1" --> "0..1" Company : administra
    User "1" --> "0..*" Order : realiza
    User "1" --> "0..*" Message : envia_recibe
    Company "1" --> "0..*" Product : ofrece
    Company "1" --> "0..*" Expense : registra
    Order "1" *-- "1..*" OrderDetail : contiene
    Product "1" --> "0..*" OrderDetail : referenciado_en
```

### 3.2 Diagrama de Clases de Aplicación (Controladores, Servicios y Middlewares)

```mermaid
classDiagram
    class Controller {
        <<abstract>>
    }

    class AuthController {
        +showLogin()
        +login(Request)
        +showRegister()
        +register(Request)
        +logout(Request)
    }

    class GoogleController {
        +redirectToGoogle()
        +handleGoogleCallback()
    }

    class EmailVerificationController {
        +notice()
        +showVerifyForm()
        +verify(Request)
        +resend(Request)
    }

    class CartController {
        +index()
        +add(Request, Product)
        +update(Request, Product)
        +remove(Product)
        +checkout()
        +processCheckout(Request)
    }

    class BusinessProductController {
        +index()
        +create()
        +store(Request)
        +edit(Product)
        +update(Request, Product)
        +destroy(Product)
    }

    class BusinessCompanyController {
        +edit()
        +update(Request)
        +requestDocumentUpdate(Request)
    }

    class BusinessExpenseController {
        +index()
        +store(Request)
        +destroy(Expense)
    }

    class AdminCompanyController {
        +index()
        +show(Company)
        +updateStatus(Request, Company)
        +approveDocumentUpdate(Company)
        +rejectDocumentUpdate(Request, Company)
    }

    class AdminUserController {
        +index()
        +create()
        +store(Request)
        +edit(User)
        +update(Request, User)
        +toggle(User)
        +destroy(User)
    }

    class ChatController {
        +index()
        +show(User)
        +send(Request, User)
        +unreadCount()
    }

    class ChatbotController {
        -ChatbotService chatbotService
        +sendMessage(Request)
    }

    class ChatbotService {
        -string apiKey
        -string apiUrl
        +sendMessage(string message, string lang) array
        -getSystemPrompt(string lang) string
    }

    class RoleMiddleware {
        +handle(Request, Closure, string role)
    }

    Controller <|-- AuthController
    Controller <|-- GoogleController
    Controller <|-- EmailVerificationController
    Controller <|-- CartController
    Controller <|-- BusinessProductController
    Controller <|-- BusinessCompanyController
    Controller <|-- BusinessExpenseController
    Controller <|-- AdminCompanyController
    Controller <|-- AdminUserController
    Controller <|-- ChatController
    Controller <|-- ChatbotController
    ChatbotController --> ChatbotService : utiliza
```

---

## 4. 🧩 Diagrama de Componentes y Arquitectura

Representa la arquitectura multicapa (MVC + Servicios) de **Visory Shop (Spotlight)** y sus integraciones con servicios externos:

```mermaid
flowchart TB
    subgraph Capa_Cliente["Capa de Presentacion (Cliente Web)"]
        UI_Blade["Vistas Blade + Bootstrap 5"]
        UI_Map["Mapa Interactivo (Leaflet / Geolocalizacion)"]
        UI_Chatbot["Widget Chatbot IA (JS Fetch)"]
        UI_Chat["Modulo de Mensajeria (AJAX / Blade)"]
    end

    subgraph Capa_Seguridad["Capa de Seguridad y Enrutamiento"]
        Router["Enrutador Web (routes/web.php)"]
        MW_Auth["Auth Middleware"]
        MW_Role["RoleMiddleware (user / business / admin)"]
        MW_Sec["SecurityHeaders Middleware"]
    end

    subgraph Capa_Controladores["Capa de Aplicacion (Controladores)"]
        C_Auth["Modulo Auth (Auth / Google / OTP)"]
        C_User["Modulo Comprador (Cart / Orders / Profile / Map)"]
        C_Biz["Modulo Empresario (Company / Products / Expenses)"]
        C_Admin["Modulo Admin (Users / Companies / Reports / Moderation)"]
        C_Comm["Modulo Comunicacion (ChatController / ChatbotController)"]
    end

    subgraph Capa_Servicios_Dominio["Capa de Servicios y Dominio (Eloquent ORM)"]
        S_AI["ChatbotService"]
        S_Mail["Mailable (GoogleVerificationMail / CompanyStatusUpdatedMail)"]
        ORM["Modelos Eloquent (User, Company, Product, Order, Expense, Message)"]
    end

    subgraph Capa_Persistencia["Capa de Datos y Almacenamiento"]
        DB[("Base de Datos MySQL")]
        Storage[("Almacenamiento Local / Publico (Logos, Productos, RUT, Camara Comercio)")]
    end

    subgraph Servicios_Externos["Servicios Externos (APIs)"]
        Ext_Google["Google OAuth 2.0 (Socialite)"]
        Ext_Gemini["Google Gemini 2.5 Flash API"]
        Ext_SMTP["Servidor de Correo (SMTP / Resend)"]
    end

    UI_Blade --> Router
    UI_Map --> Router
    UI_Chatbot --> Router
    UI_Chat --> Router

    Router --> MW_Sec
    MW_Sec --> MW_Auth
    MW_Auth --> MW_Role

    MW_Role --> C_Auth
    MW_Role --> C_User
    MW_Role --> C_Biz
    MW_Role --> C_Admin
    MW_Role --> C_Comm

    C_Auth --> Ext_Google
    C_Auth --> S_Mail
    C_Admin --> S_Mail
    S_Mail --> Ext_SMTP

    C_Comm --> S_AI
    S_AI --> Ext_Gemini

    C_Auth --> ORM
    C_User --> ORM
    C_Biz --> ORM
    C_Admin --> ORM
    C_Comm --> ORM

    C_Biz --> Storage
    C_User --> Storage
    ORM --> DB
```

---

## 5. 🔄 Diagramas de Flujo de Datos (DFD)

### 5.1 DFD Nivel 0 (Diagrama de Contexto)

Muestra el sistema **Visory Shop (Spotlight)** como un proceso central único y el intercambio de datos de entrada y salida con todas las entidades externas:

```mermaid
flowchart LR
    E_Guest["Visitante"]
    E_Client["Cliente"]
    E_Biz["Empresario"]
    E_Admin["Administrador"]
    E_Google["Google OAuth"]
    E_Gemini["API Google Gemini"]
    E_Mail["Servidor Correo SMTP"]

    P0(("0. Sistema Visory Shop (Spotlight)"))

    E_Guest -- "Busqueda de productos, Datos de registro" --> P0
    P0 -- "Catalogo publico, Perfiles de empresas" --> E_Guest

    E_Client -- "Items carrito, Orden de compra, Mensajes, Consultas IA" --> P0
    P0 -- "Confirmacion de pedido, Mapa de tiendas, Respuestas Chat e IA" --> E_Client

    E_Biz -- "Datos de empresa, Documentos legales, Productos, Gastos" --> P0
    P0 -- "Metricas de ventas, Pedidos recibidos, Estado de aprobacion" --> E_Biz

    E_Admin -- "Aprobacion o rechazo empresas, Moderacion usuarios y productos" --> P0
    P0 -- "Reportes globales, Expedientes legales, Alertas de auditoria" --> E_Admin

    P0 -- "Solicitud de autorizacion OAuth" --> E_Google
    E_Google -- "Token e identidad de usuario" --> P0

    P0 -- "Prompt contextualizado y consulta del usuario" --> E_Gemini
    E_Gemini -- "Respuesta generada en Markdown" --> P0

    P0 -- "Codigo OTP y Notificacion estado de empresa" --> E_Mail
```

---

### 5.2 DFD Nivel 1 (Procesos Principales y Almacenes de Datos)

Descompone el sistema en sus **6 procesos principales**, mostrando cómo fluyen los datos entre actores externos, procesos internos y almacenes de datos (`D1` a `D6`):

```mermaid
flowchart TB
    User_Ext["Usuario / Cliente"]
    Biz_Ext["Empresario"]
    Admin_Ext["Administrador"]
    Google_Ext["Google OAuth / SMTP"]
    Gemini_Ext["API Google Gemini"]

    P1(["1.0 Gestion de Autenticacion y Cuentas"])
    P2(["2.0 Gestion de Empresa y Documentacion"])
    P3(["3.0 Gestion de Catalogo y Gastos"])
    P4(["4.0 Procesamiento de Carrito y Pedidos"])
    P5(["5.0 Moderacion, Auditoria y Reportes"])
    P6(["6.0 Mensajeria Directa y Asistente IA"])

    D1[("D1: users")]
    D2[("D2: companies")]
    D3[("D3: products")]
    D4[("D4: orders / order_details")]
    D5[("D5: expenses")]
    D6[("D6: messages")]

    User_Ext -- "Credenciales / Codigo OTP" --> P1
    Biz_Ext -- "Registro empresarial" --> P1
    P1 <-- "Validacion OAuth y Envio OTP" --> Google_Ext
    P1 -- "Crea o actualiza cuenta" --> D1
    D1 -- "Datos de sesion y rol" --> P1

    Biz_Ext -- "Perfil comercial, RUT, Camara Comercio, Coordenadas" --> P2
    P2 -- "Guarda perfil y documentos" --> D2
    D2 -- "Ubicaciones para mapa y perfil publico" --> User_Ext

    Biz_Ext -- "Alta o edicion de productos y registro de gastos" --> P3
    P3 -- "Actualiza inventario y precios" --> D3
    P3 -- "Registra egresos operativos" --> D5

    D3 -- "Disponibilidad y precios de productos" --> P4
    User_Ext -- "Seleccion de carrito y Checkout" --> P4
    P4 -- "Descuenta stock" --> D3
    P4 -- "Registra orden y detalle" --> D4
    D4 -- "Historial de compras y ventas" --> User_Ext
    D4 -- "Metricas de ingresos" --> Biz_Ext

    Admin_Ext -- "Auditoria de empresas, usuarios y productos" --> P5
    P5 -- "Cambia estado active/inactive" --> D1
    P5 -- "Aprueba o rechaza empresa y documentos" --> D2
    P5 -- "Modera visibilidad de productos" --> D3
    D4 -- "Datos consolidados de ventas" --> P5
    D5 -- "Datos de gastos" --> P5
    P5 -- "Notificacion de estado de empresa" --> Google_Ext
    P5 -- "Dashboard y reportes globales" --> Admin_Ext

    User_Ext -- "Mensaje de chat / Consulta IA" --> P6
    Biz_Ext -- "Respuesta de chat / Consulta IA" --> P6
    P6 -- "Almacena y marca lectura de mensajes" --> D6
    D6 -- "Historial de conversacion" --> P6
    P6 <-- "Prompt regulativo y Respuesta IA" --> Gemini_Ext
    P6 -- "Respuesta en tiempo real" --> User_Ext
```
