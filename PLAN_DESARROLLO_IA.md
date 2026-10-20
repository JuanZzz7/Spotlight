# PLAN DE DESARROLLO: PLATAFORMA EDUCATIVA DE INTELIGENCIA ARTIFICIAL
## Aldea Digital de la Villa de San Diego de Ubaté

---

# 1. Introducción del Proyecto

La plataforma educativa de la **Aldea Digital de la Villa de San Diego de Ubaté** es una iniciativa vanguardista diseñada para democratizar el acceso al conocimiento sobre Inteligencia Artificial (IA). En un mundo impulsado por los datos y la automatización, este proyecto busca empoderar a la comunidad local proporcionando herramientas teóricas y prácticas que les permitan comprender y aplicar la IA en diversos ámbitos profesionales y cotidianos.

El propósito central es crear un entorno de aprendizaje dinámico, intuitivo y tecnológicamente avanzado, que integre un **Chatbot especializado** basado en la API de Gemini (Google AI Studio) para asistir a los estudiantes en tiempo real, garantizando una experiencia educativa personalizada y de alta calidad.

---

# 2. Objetivos Generales

### Objetivos Educativos
*   **Capacitación Integral:** Ofrecer una ruta de aprendizaje sólida desde los fundamentos hasta aplicaciones avanzadas de Deep Learning.
*   **Accesibilidad:** Facilitar el acceso a contenidos técnicos complejos de manera sencilla y didáctica para la población de Ubaté.
*   **Fomento de la Innovación:** Incentivar el desarrollo de soluciones locales basadas en IA que impacten positivamente en la economía de la región.

### Objetivos Técnicos
*   **Rendimiento Óptimo:** Desarrollar una interfaz ligera y rápida utilizando tecnologías nativas (Vanilla).
*   **Interactividad Inteligente:** Implementar un chatbot capaz de responder con precisión basado en una base de conocimientos curada.
*   **Escalabilidad:** Diseñar una arquitectura modular que permita la adición de nuevos cursos e instructores sin comprometer la integridad del sistema.

---

# 3. Stack Tecnológico

Para este proyecto se ha seleccionado un stack basado en **tecnologías web nativas**, priorizando la simplicidad, el control total sobre el código y la eficiencia:

*   **HTML5:** Para una estructura semántica robusta, optimizada para SEO y accesibilidad.
*   **CSS3 (Vanilla):** Para un diseño minimalista y profesional, utilizando Variables CSS y Flexbox/Grid para garantizar una respuesta adaptativa (Responsive) de primer nivel.
*   **JavaScript (Vanilla - ES6+):** Para la lógica de negocio, manipulación del DOM y la integración asíncrona con la API de Gemini, evitando la sobrecarga de frameworks innecesarios.

**Razón del uso de Vanilla:** La elección de no usar frameworks (como React o Vue) se debe a la necesidad de maximizar la velocidad de carga, facilitar el mantenimiento a largo plazo y asegurar que la plataforma funcione perfectamente incluso en dispositivos con recursos limitados.

---

# 4. Arquitectura Escalable del Proyecto

El proyecto sigue una estructura modular para separar las preocupaciones (Separation of Concerns), facilitando la escalabilidad y el mantenimiento.

### Estructura de Carpetas

```text
project/
│
├── index.html              # Página principal (Historia de la IA)
├── cursos.html             # Catálogo de cursos educativos
├── instructores.html       # Perfiles de los expertos
├── contacto.html           # Formulario de soporte y contacto
├── chatbot.html            # Interfaz dedicada del asistente IA
│
├── assets/                 # Recursos estáticos
│   ├── css/                # Estilos (styles.css, components.css)
│   ├── js/                 # Lógica (main.js, chatbot.js, api-service.js)
│   ├── img/                # Imágenes del sitio
│   └── icons/              # Iconografía personalizada
│
├── data/                   # Archivos de datos estructurados
│   ├── courses.json        # Información detallada de los cursos
│   ├── instructors.json    # Datos de los instructores
│   └── knowledge-base.json # Base de conocimientos para el Chatbot
│
├── .env.example            # Plantilla para variables de entorno
└── README.md               # Documentación general de instalación
```

### Descripción de Componentes
*   **`assets/js/api-service.js`:** Encargado exclusivamente de las llamadas fetch a la API de Gemini.
*   **`data/`:** Permite actualizar el contenido del sitio (cursos, precios, perfiles) simplemente editando archivos JSON, sin necesidad de tocar el código HTML.
*   **`assets/css/`:** Implementa un sistema de diseño basado en tokens (colores, tipografías) para mantener la consistencia visual.

---

# 5. Home – Historia de la Inteligencia Artificial

La página de inicio no es solo una presentación, sino una experiencia educativa que recorre la evolución del pensamiento computacional.

### Contenido Temático
*   **El Origen de un Sueño:** Desde los mitos de la antigüedad hasta la formalización de la lógica matemática.
*   **Alan Turing y la Prueba de Turing:** El punto de partida de la computación moderna y el concepto de "máquinas que piensan".
*   **La Evolución del Machine Learning:** De los algoritmos estadísticos básicos a la capacidad de aprender de los datos.
*   **Deep Learning y Redes Neuronales:** El auge de la potencia de cómputo y el procesamiento masivo de información.
*   **La Era de la IA Generativa:** Modelos de lenguaje masivos (LLMs), visión por computador y el futuro de la interacción humano-máquina.

### Estructura de la Interfaz
1.  **Hero Section:** Imagen impactante con mensaje inspirador sobre la educación digital en Ubaté.
2.  **Línea de Tiempo Interactiva:** Visualización de los hitos clave de la IA.
3.  **Sección de Beneficios:** ¿Por qué aprender IA hoy? (Competitividad, Innovación, Futuro).
4.  **CTA (Call to Action):** Botón directo hacia el catálogo de cursos.

---

# 6. Cursos de Inteligencia Artificial

Se han diseñado 5 módulos progresivos para garantizar una formación integral:

| Curso | Nivel | Duración | Precio (COP) |
| :--- | :--- | :--- | :--- |
| **1. Fundamentos de IA** | Principiante | 20 Horas | $120,000 |
| **2. Introducción a ML** | Intermedio | 40 Horas | $250,000 |
| **3. ML y Algoritmos Genéticos** | Avanzado | 50 Horas | $380,000 |
| **4. Deep Learning Fundamentos** | Intermedio | 45 Horas | $320,000 |
| **5. Aplicaciones de Deep Learning** | Experto | 60 Horas | $450,000 |

### Detalle de Ejemplo: **Machine Learning y Algoritmos Genéticos**
*   **Descripción:** Exploración de técnicas bioinspiradas para la optimización de procesos complejos.
*   **Temario:** Poblaciones iniciales, selección natural, mutación, funciones de aptitud y aplicaciones en logística.
*   **Tecnologías:** Python, NumPy, SciPy.
*   **Modalidad:** Híbrida (Clases grabadas + Sesiones en vivo).
*   **Beneficios:** Certificado de Aldea Digital, acceso de por vida y comunidad de estudio.

---

# 7. Instructores

Nuestra facultad está compuesta por profesionales con amplia trayectoria académica y empresarial.

### Perfil Tipo
*   **Nombre:** Dr. Carlos Alberto Ruiz
*   **Especialidad:** Doctor en Ciencias de la Computación, experto en IA Generativa.
*   **Tecnologías:** TensorFlow, PyTorch, Gemini API, OpenAI SDK.
*   **Experiencia:** 10+ años liderando proyectos de transformación digital en Latinoamérica.
*   **Certificaciones:** Google Cloud Professional ML Engineer.
*   **Redes:** LinkedIn, GitHub, ResearchGate.

*Nota: Se habilitará una sección para descarga de CV (PDF) por cada instructor.*

---

# 8. Módulo de Contacto

Un formulario diseñado para la mejor experiencia de usuario (UX), asegurando que ninguna duda quede sin respuesta.

*   **Campos:** Nombre completo, Correo electrónico, Interés del curso, Mensaje.
*   **Validaciones:** JavaScript en tiempo real para verificar formatos de email y campos obligatorios.
*   **Feedback Visual:** Notificaciones de éxito/error con animaciones suaves.
*   **Responsive:** Adaptado para una fácil escritura tanto en dispositivos móviles como de escritorio.

---

# 9. Chatbot Inteligente con Gemini API

El asistente virtual de Aldea Digital está configurado para ser un experto en el ecosistema educativo de la plataforma.

### Especificaciones Técnicas
*   **Motor:** Gemini 1.5 Flash (vía Google AI Studio).
*   **Restricción de Contexto:** El chatbot solo opera dentro del dominio de la educación en IA, información de Aldea Digital, cursos e instructores.
*   **Personalidad:** Profesional, amable, servicial y educativo.
*   **Rechazo de Consultas:** Si el usuario pregunta sobre temas externos (política, entretenimiento ajeno, etc.), el chatbot declinará cordialmente la respuesta y redirigirá al usuario a los temas de estudio.

---

# 10. Base de Conocimiento (`knowledge-base.json`)

Este archivo es el corazón informativo que alimenta al Chatbot.

```json
{
  "institution": {
    "name": "Aldea Digital de la Villa de San Diego de Ubaté",
    "location": "Ubaté, Cundinamarca",
    "mission": "Transformar la educación mediante la tecnología."
  },
  "courses": [
    {
      "id": "IA-101",
      "title": "Fundamentos de Inteligencia Artificial",
      "summary": "Conceptos básicos y panorama actual de la IA."
    }
  ],
  "instructors": [
    {
      "name": "Carlos Ruiz",
      "specialty": "Deep Learning",
      "courses": ["IA-101"]
    }
  ],
  "faq": [
    {
      "question": "¿Cómo me inscribo?",
      "answer": "Puedes inscribirte a través de la sección de cursos o en el módulo de contacto."
    }
  ]
}
```

---

# 11. Prompt Seguro del Chatbot

Para garantizar la seguridad y el enfoque educativo, se utiliza el siguiente **System Prompt**:

> "Eres el Asistente Educativo de Aldea Digital de Ubaté. Tu única función es ayudar a los estudiantes con dudas sobre los cursos de Inteligencia Artificial, la historia de la IA presentada en este sitio, los perfiles de los instructores y la información institucional de Aldea Digital. 
> 
> REGLAS CRÍTICAS:
> 1. Usa ÚNICAMENTE la base de conocimientos proporcionada.
> 2. Si la información no está en la base de conocimientos, di: 'Lo siento, no tengo esa información registrada en nuestro sistema educativo actual'.
> 3. NO inventes información sobre instructores o precios.
> 4. Rechaza preguntas sobre temas que no sean Inteligencia Artificial o la institución.
> 5. Sé siempre amable y motivador."

---

# 12. Integración con Gemini API

Ejemplo de implementación modular utilizando JavaScript Vanilla y `fetch`.

```javascript
/**
 * api-service.js
 * Servicio para comunicación con Gemini API
 */

const API_CONFIG = {
    KEY: "TU_API_KEY_AQUI", // Se recomienda manejar esto con cuidado
    URL: "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent"
};

async function sendMessageToGemini(userPrompt, contextData) {
    const systemInstruction = "Eres un asistente experto en Aldea Digital de Ubaté. Responde solo sobre IA y cursos.";
    
    const requestBody = {
        contents: [{
            parts: [{
                text: `Contexto del sitio: ${JSON.stringify(contextData)}\n\nPregunta del usuario: ${userPrompt}`
            }]
        }],
        system_instruction: {
            parts: [{ text: systemInstruction }]
        }
    };

    try {
        const response = await fetch(`${API_CONFIG.URL}?key=${API_CONFIG.KEY}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestBody)
        });

        if (!response.ok) throw new Error("Error en la comunicación con el servidor.");

        const data = await response.json();
        return data.candidates[0].content.parts[0].text;

    } catch (error) {
        console.error("Gemini API Error:", error);
        return "Disculpa, he tenido un problema técnico. Por favor intenta de nuevo más tarde.";
    }
}
```

---

# 13. Archivo `.env.example`

Este archivo sirve como guía para configurar las credenciales de la API sin exponerlas en el control de versiones.

```bash
# Google AI Studio - Gemini API Key
# Obtenla en: https://aistudio.google.com/
GEMINI_API_KEY=YOUR_API_KEY_HERE
```

---

# 14. Buenas Prácticas

Para asegurar la calidad del producto final, el desarrollo debe regirse por:

*   **Clean Code:** Nombres de variables descriptivos y funciones pequeñas con una sola responsabilidad.
*   **Modularización:** Separar los datos (JSON) de la presentación (HTML) y la lógica (JS).
*   **Seguridad:** Validar todos los inputs del usuario y no exponer la API Key en repositorios públicos.
*   **Escalabilidad:** Usar rutas relativas y estructuras de datos que permitan crecer el catálogo de cursos.
*   **Responsive Design:** Mobile-first approach para garantizar que los estudiantes puedan aprender desde sus celulares.
*   **Optimización Frontend:** Comprimir imágenes y minificar archivos CSS/JS para producción.

---

# 15. Objetivo Final del Proyecto

El resultado final será una **Plataforma Educativa de Clase Mundial** para la Villa de San Diego de Ubaté. Un espacio donde la tradición de la región se encuentra con la innovación global, proporcionando a los ciudadanos las llaves del futuro tecnológico. Esta documentación sienta las bases para un desarrollo organizado, seguro y centrado en el usuario, garantizando que el conocimiento en Inteligencia Artificial sea un motor de progreso real para toda la comunidad.
