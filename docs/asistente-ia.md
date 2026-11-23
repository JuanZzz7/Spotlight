# Spotlight — Módulo de Asistente Virtual Bilingüe

Asistente virtual integrado en **Spotlight** para orientación de compradores y comercios locales en Ubaté, Colombia.

Construido sobre **Laravel 12** con integración a **Google Gemini API** y motor de respaldo local.

---

## Configuración e Instalación

### 1. Clonar / acceder al proyecto

```bash
git clone https://github.com/JuanZzz7/Spotlight.git
cd Spotlight
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar el archivo `.env`

El archivo `.env` ya contiene la clave de Gemini. Verifica que exista esta línea:

```env
# AI Chatbot
GEMINI_API_KEY=tu_clave_de_gemini_aqui
```

> **Cómo obtener una clave gratuita de Gemini:**
> 1. Ve a https://aistudio.google.com/app/apikey
> 2. Crea un proyecto y genera una API Key
> 3. Pégala en el `.env`

### 4. Generar clave de aplicación (si no existe)

```bash
php artisan key:generate
```

### 5. Limpiar caché de configuración

```bash
php artisan config:clear
php artisan cache:clear
```

### 6. Ejecutar migraciones

```bash
php artisan migrate
```

### 7. Levantar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en: **http://localhost:8000**

---

## 🧪 Cómo Probar el Chatbot

1. Inicia sesión con cualquier cuenta de usuario en `/login`
2. El botón flotante del chatbot aparece en la **esquina inferior derecha** de cualquier página
3. Haz clic en el ícono de burbuja de chat para abrir la ventana
4. Escribe un mensaje y presiona **Enter** o el botón de enviar
5. Para cambiar de idioma, usa los botones **ES / EN** en el encabezado del chat

### Ejemplos de mensajes para probar

```
Español:
- "Hola, ¿qué puedes hacer?"
- "Explícame qué es la gestión del conocimiento"
- "¿Cuál es tu filosofía?"
- "Cuéntame sobre el desarrollo humano"

English:
- "Hello, what can you do?"
- "Explain knowledge management"
- "What is your declaration?"
- "Tell me about personal growth"
```

---

## ⚙️ Configuración del `.env`

```env
# AI Chatbot (Google Gemini)
GEMINI_API_KEY=tu_clave_aqui

# Aplicación
APP_NAME=Spotlight
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos
DB_CONNECTION=sqlite
```

---

## 🏗️ Explicación Técnica

### Arquitectura

```
Frontend (Blade Widget)
    │  POST /chatbot/message  (AJAX + Fetch API)
    ▼
ChatbotController.php
    │  Validates + calls Service
    ▼
ChatbotService.php
    │  Detects language + builds prompt + calls API
    ▼
Google Gemini API (gemini-1.5-flash)
    │  Generates contextual response
    ▼
JSON response → Rendered in chat bubble
```

### Archivos creados

| Archivo | Descripción |
|---|---|
| `app/Services/ChatbotService.php` | Lógica de negocio: detección de idioma, construcción de prompts, llamada a Gemini |
| `app/Http/Controllers/ChatbotController.php` | Controlador HTTP: valida entrada, orquesta respuesta |
| `resources/views/components/chatbot-widget.blade.php` | Componente Blade completo (HTML + CSS + JS) |
| `routes/web.php` | Rutas `POST /chatbot/message` y `POST /chatbot/detect-lang` |
| `config/services.php` | Registro de la clave de Gemini |

### Detección de idioma

El servicio detecta automáticamente el idioma basándose en:
- Palabras clave en español vs inglés
- Presencia de caracteres especiales: `á é í ó ú ü ñ ¿ ¡`

El usuario también puede forzar el idioma con los botones **ES / EN**.

### Declaración filosófica

El chatbot incluye como mensaje inicial y parte de su sistema prompt:

> *"Soy LIBRE, AUTÓNOMO Y RESPONSABLE a través del diálogo y la construcción, como ideal regulativo; me dirijo, controlo y dicto mis propias leyes."*

---

## 🎥 Script del Video (English)

### Architecture Overview

> "Spotlight AI Chatbot is a multilingual conversational assistant built into the Spotlight marketplace platform using Laravel 12 and Google Gemini AI."

### Chatbot Flow

> "When a user types a message, the frontend widget sends an AJAX POST request to the `/chatbot/message` endpoint. The Laravel controller passes the message and conversation history to the ChatbotService, which detects the language — Spanish or English — builds a contextual system prompt, and calls the Gemini 1.5 Flash API. The response is streamed back to the frontend and rendered as a chat bubble in under two seconds."

### Technologies Used

> "The stack includes: **Laravel 12** for the backend, **Google Gemini 1.5 Flash** as the AI model, **vanilla JavaScript Fetch API** for AJAX communication, and **CSS custom properties** for the premium floating widget design. No external frontend libraries were added, keeping the bundle lean and performant."

### Design Philosophy

> "The chatbot is grounded in an ethical declaration: *'I am FREE, AUTONOMOUS, and RESPONSIBLE through dialogue and construction, as a regulatory ideal; I guide, control, and dictate my own laws.'* This principle shapes how the AI responds — always empathetic, always empowering the user."

---

## 🛠️ Solución de Problemas

| Problema | Solución |
|---|---|
| El chatbot no aparece | Verifica que hayas iniciado sesión (`@auth` guard) |
| Error 419 (CSRF) | Asegúrate que el layout tiene `<meta name="csrf-token">` |
| "Servicio no configurado" | Verifica `GEMINI_API_KEY` en `.env` y ejecuta `php artisan config:clear` |
| Sin respuesta de la IA | Verifica que la API key de Gemini sea válida en aistudio.google.com |

---

## 📄 Licencia

MIT — Spotlight Ubaté © 2026
