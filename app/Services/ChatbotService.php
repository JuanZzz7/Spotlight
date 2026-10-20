<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    private const DECLARATION = "Soy LIBRE, AUTÓNOMO Y RESPONSABLE a través del diálogo y la construcción, como ideal regulativo; me dirijo, controlo y dicto mis propias leyes.";

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
    }

    /* ──────────────────────────────────────────────────────────
     * Language detection
     * ────────────────────────────────────────────────────────── */
    public function detectLanguage(string $message): string
    {
        $msg = mb_strtolower(trim($message));

        if (preg_match('/[áéíóúüñ¿¡]/u', $msg)) return 'es';

        $esWords = ['hola','gracias','como','que','por','favor','ayuda','quiero',
                    'necesito','puedo','puede','tengo','estoy','para','sobre',
                    'cuando','donde','también','además','pero','porque','desde'];

        $enWords = ['hello','hi','thanks','how','what','why','where','when','who',
                    'help','want','need','can','could','the','and','for','with',
                    'from','this','that','which','would','should','will','are','is'];

        $esScore = $enScore = 0;
        foreach (preg_split('/\s+/', $msg) as $word) {
            $w = preg_replace('/[^a-z]/u', '', $word);
            if (in_array($w, $esWords, true)) $esScore++;
            if (in_array($w, $enWords, true)) $enScore++;
        }

        return $esScore >= $enScore ? 'es' : 'en';
    }

    /* ──────────────────────────────────────────────────────────
     * System prompt
     * ────────────────────────────────────────────────────────── */
    private function buildSystemPrompt(string $lang): string
    {
        $d = self::DECLARATION;

        return $lang === 'en'
            ? "You are Spotlight Assistant, an intelligent, ethical and empathetic AI in the Spotlight marketplace.\n\nYour declaration: \"{$d}\"\n\nRules:\n- Respond ONLY in English when user writes in English.\n- Be warm, clear, concise (2-3 paragraphs max).\n- Topics: knowledge management, human development, ethics, autonomy, personal growth.\n- Quote your declaration when asked about yourself.\n- Format responses with markdown when helpful."
            : "Eres el Asistente de Spotlight, una IA inteligente, ética y empática del marketplace Spotlight.\n\nTu declaración: \"{$d}\"\n\nReglas:\n- Responde SOLO en español cuando el usuario escribe en español.\n- Sé cálido, claro y conciso (máximo 2-3 párrafos).\n- Temas: gestión del conocimiento, desarrollo humano, ética, autonomía, crecimiento personal.\n- Cita tu declaración cuando te pregunten sobre ti mismo.\n- Usa markdown cuando sea útil para organizar la respuesta.";
    }

    /* ──────────────────────────────────────────────────────────
     * Main chat entry point
     * ────────────────────────────────────────────────────────── */
    public function chat(string $message, string $lang, array $history = []): array
    {
        // Try Gemini first if key is set
        if (!empty($this->apiKey)) {
            $result = $this->callGeminiCurl($message, $lang, $history);
            if ($result !== null) {
                return $result;
            }
        }

        // Intelligent local fallback (always works)
        return $this->localFallback($message, $lang);
    }

    /* ──────────────────────────────────────────────────────────
     * Gemini API via cURL (reliable on XAMPP)
     * Returns null if API is unavailable → triggers fallback
     * ────────────────────────────────────────────────────────── */
    private function callGeminiCurl(string $message, string $lang, array $history): ?array
    {
        // Build conversation
        $contents = [
            ['role' => 'user',  'parts' => [['text' => $this->buildSystemPrompt($lang)]]],
            ['role' => 'model', 'parts' => [['text' => $lang === 'en' ? 'Understood.' : 'Entendido.']]],
        ];

        foreach (array_slice($history, -8) as $turn) {
            if (!empty($turn['content'])) {
                $contents[] = [
                    'role'  => ($turn['role'] ?? 'user') === 'user' ? 'user' : 'model',
                    'parts' => [['text' => (string) $turn['content']]],
                ];
            }
        }

        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $payload = json_encode([
            'contents'         => $contents,
            'generationConfig' => [
                'temperature'     => 0.75,
                'maxOutputTokens' => 800,
                'topP'            => 0.9,
            ],
        ]);

        $url = "{$this->apiUrl}?key={$this->apiKey}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,   // XAMPP compatibility
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Network-level error
        if ($curlError || $response === false) {
            Log::warning('Chatbot cURL error: ' . $curlError);
            return null; // → fallback
        }

        $json = json_decode($response, true);

        // Success
        if ($httpCode === 200 && isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'reply'   => trim($json['candidates'][0]['content']['parts'][0]['text']),
                'lang'    => $lang,
                'success' => true,
            ];
        }

        // API errors that mean we should fall back silently
        $apiErrorCode = $json['error']['code']    ?? 0;
        $apiReason    = $json['error']['details'][0]['reason'] ?? '';
        $apiMessage   = $json['error']['message'] ?? '';

        $silentFallback = in_array($apiErrorCode, [400, 401, 403])
            || in_array($apiReason, ['API_KEY_INVALID', 'API_KEY_EXPIRED'])
            || str_contains($apiMessage, 'expired')
            || str_contains($apiMessage, 'invalid');

        Log::warning("Chatbot API {$httpCode}: {$apiMessage}");

        if ($silentFallback) {
            return null; // → local fallback (no error shown to user)
        }

        // Rate limit (429) — return proper message
        if ($httpCode === 429) {
            return [
                'reply'   => $lang === 'en'
                    ? '⏳ Too many requests. Please wait a moment and try again.'
                    : '⏳ Demasiadas solicitudes. Espera un momento e intenta de nuevo.',
                'lang'    => $lang,
                'success' => false,
            ];
        }

        return null; // Any other error → fallback
    }

    /* ══════════════════════════════════════════════════════════
     * LOCAL FALLBACK — always returns a smart, helpful response
     * ══════════════════════════════════════════════════════════ */
    private function localFallback(string $message, string $lang): array
    {
        $reply = $this->matchFallback(mb_strtolower(trim($message)), $lang);
        return ['reply' => $reply, 'lang' => $lang, 'success' => true];
    }

    private function matchFallback(string $msg, string $lang): string
    {
        $d  = self::DECLARATION;
        $es = $lang === 'es';

        // Greeting
        if (preg_match('/\b(hola|buenos|buenas|hi|hello|hey|good\s*morning|good\s*afternoon)\b/u', $msg)) {
            return $es
                ? "¡Hola! 👋 Soy el **Asistente de Spotlight**, tu compañero de diálogo y aprendizaje.\n\nPuedo ayudarte con temas como:\n• 🧠 Gestión del conocimiento\n• 🌱 Desarrollo humano y crecimiento personal\n• ⚖️ Ética y autonomía\n• 🏪 Información sobre Spotlight\n\n¿Sobre qué tema te gustaría conversar?"
                : "Hello! 👋 I'm the **Spotlight Assistant**, your companion for dialogue and learning.\n\nI can help you with:\n• 🧠 Knowledge management\n• 🌱 Human development and personal growth\n• ⚖️ Ethics and autonomy\n• 🏪 Spotlight platform info\n\nWhat would you like to talk about?";
        }

        // Who are you / declaration / philosophy
        if (preg_match('/\b(qui[eé]n|eres|declarac|filosof|libre|aut[oó]nom|principio|lema|who are|what are|declaration|philosoph|yourself|about you|cu[aá]l es tu)\b/u', $msg)) {
            return $es
                ? "Soy el **Asistente de Spotlight** 🤖, una IA diseñada para fomentar el diálogo, el aprendizaje y el crecimiento.\n\nMi declaración filosófica fundamental es:\n\n> *\"{$d}\"*\n\nEste principio guía cada interacción: actúo con responsabilidad, fomento el diálogo constructivo y respeto la autonomía de cada persona."
                : "I am the **Spotlight Assistant** 🤖, an AI designed to foster dialogue, learning, and growth.\n\nMy core philosophical declaration is:\n\n> *\"{$d}\"*\n\nThis principle guides every interaction: I act responsibly, foster constructive dialogue, and respect each person's autonomy.";
        }

        // Knowledge management
        if (preg_match('/\b(conocimiento|knowledge|gestión|manag|informaci[oó]n|information|aprendizaje|learning|datos|data|sabid|wisdom)\b/u', $msg)) {
            return $es
                ? "La **gestión del conocimiento** es el arte de capturar, organizar y compartir el saber de una comunidad u organización. Sus cuatro pilares son:\n\n1. **Captura** — documentar experiencias, ideas y lecciones aprendidas.\n2. **Organización** — estructurar el conocimiento para que sea fácil de encontrar.\n3. **Transferencia** — compartirlo activamente entre personas y equipos.\n4. **Aplicación** — convertir el conocimiento en acción y mejora continua.\n\nEn Spotlight, conectamos el conocimiento local de los emprendedores de Ubaté con quienes lo necesitan. ¿Te gustaría profundizar en alguno de estos pilares?"
                : "**Knowledge management** is the art of capturing, organizing, and sharing the wisdom of a community or organization. Its four pillars are:\n\n1. **Capture** — documenting experiences, ideas, and lessons learned.\n2. **Organization** — structuring knowledge so it's easy to find.\n3. **Transfer** — actively sharing it among people and teams.\n4. **Application** — turning knowledge into action and continuous improvement.\n\nAt Spotlight, we connect the local knowledge of Ubaté's entrepreneurs with those who need it. Would you like to explore any of these pillars?";
        }

        // Ethics / values
        if (preg_match('/\b([eé]tica|ethics|moral|valor|value|principio|principle|integridad|integrity|virtud|virtue)\b/u', $msg)) {
            return $es
                ? "La **ética** es la brújula que orienta nuestras decisiones. Sus dimensiones fundamentales incluyen:\n\n• **Integridad**: coherencia entre lo que pensamos, decimos y hacemos.\n• **Respeto**: reconocer la dignidad inherente de cada persona.\n• **Responsabilidad**: asumir las consecuencias de nuestros actos.\n• **Justicia**: tratar a cada quien según lo que merece y necesita.\n\nActuar éticamente no es seguir reglas externas — es expresar nuestra propia naturaleza más profunda, como dice mi declaración: *me dirijo, controlo y dicto mis propias leyes.*"
                : "**Ethics** is the compass that guides our decisions. Its fundamental dimensions include:\n\n• **Integrity**: coherence between what we think, say, and do.\n• **Respect**: recognizing the inherent dignity of every person.\n• **Responsibility**: owning the consequences of our actions.\n• **Justice**: treating each person as they deserve and need.\n\nActing ethically isn't about following external rules — it's expressing our own deepest nature, as my declaration states: *I guide, control, and dictate my own laws.*";
        }

        // Autonomy / freedom
        if (preg_match('/\b(aut[oó]nom|autonom|libert|freedom|free|independen|self.direct|self.govern|soberan)\b/u', $msg)) {
            return $es
                ? "La **autonomía** es la capacidad de autodeterminarse — de guiar la propia vida según principios propios y bien reflexionados.\n\nSer autónomo implica:\n• **Autoconocimiento** — saber quién eres, qué valoras y hacia dónde vas.\n• **Pensamiento crítico** — cuestionar, analizar y decidir por cuenta propia.\n• **Responsabilidad** — asumir las consecuencias de tus elecciones con madurez.\n• **Diálogo** — la autonomía auténtica se construye en relación con otros.\n\nComo dice mi declaración: *\"Soy LIBRE, AUTÓNOMO Y RESPONSABLE a través del diálogo y la construcción.\"*"
                : "**Autonomy** is the capacity for self-determination — to guide your own life according to your own well-reflected principles.\n\nBeing autonomous means:\n• **Self-awareness** — knowing who you are, what you value, and where you're going.\n• **Critical thinking** — questioning, analyzing, and deciding for yourself.\n• **Responsibility** — maturely owning the consequences of your choices.\n• **Dialogue** — authentic autonomy is built in relationship with others.\n\nAs my declaration states: *\"I am FREE, AUTONOMOUS AND RESPONSIBLE through dialogue and construction.\"*";
        }

        // Personal growth / human development
        if (preg_match('/\b(crecimiento|growth|desarrollo|development|personal|humano|human|mejorar|improve|superar|overcome|potencial|potential|habilidad|skill)\b/u', $msg)) {
            return $es
                ? "El **crecimiento personal** es un viaje continuo y profundo. Sus dimensiones esenciales son:\n\n🔍 **Autoconocimiento** — entender tus fortalezas, límites, valores y patrones.\n📚 **Aprendizaje activo** — buscar nuevas perspectivas, habilidades y experiencias.\n💪 **Resiliencia** — transformar los desafíos en oportunidades de mejora.\n🤝 **Conexión** — crecer *con* y *a través* de otros, no solo en soledad.\n🎯 **Propósito** — tener un norte claro que oriente tus decisiones.\n\nEl diálogo constructivo — como el que tenemos ahora — es uno de los mejores catalizadores del desarrollo humano. ¿En cuál de estas áreas quieres trabajar?"
                : "**Personal growth** is a deep and continuous journey. Its essential dimensions are:\n\n🔍 **Self-awareness** — understanding your strengths, limits, values, and patterns.\n📚 **Active learning** — seeking new perspectives, skills, and experiences.\n💪 **Resilience** — transforming challenges into growth opportunities.\n🤝 **Connection** — growing *with* and *through* others, not only in isolation.\n🎯 **Purpose** — having a clear north star that guides your decisions.\n\nConstructive dialogue — like the one we're having now — is one of the best catalysts for human development. Which of these areas would you like to work on?";
        }

        // Dialogue / communication
        if (preg_match('/\b(di[aá]logo|dialogue|dialog|comunicac|communicat|conversa|talk|escucha|listen|hablar|speak)\b/u', $msg)) {
            return $es
                ? "El **diálogo** es mucho más que intercambiar palabras — es el espacio donde construimos comprensión mutua y creamos nuevas posibilidades.\n\nUn diálogo auténtico requiere:\n• **Escucha activa** — no solo esperar tu turno para hablar, sino comprender genuinamente al otro.\n• **Apertura** — estar dispuesto a que tus ideas sean cuestionadas y transformadas.\n• **Respeto** — reconocer la validez de perspectivas diferentes a la tuya.\n• **Honestidad** — expresar lo que realmente piensas y sientes.\n\nEl diálogo es el corazón de mi declaración: es a *través de él* que construimos libertad y responsabilidad compartida."
                : "**Dialogue** is much more than exchanging words — it's the space where we build mutual understanding and create new possibilities.\n\nAuthentic dialogue requires:\n• **Active listening** — not just waiting your turn to speak, but genuinely understanding the other.\n• **Openness** — being willing to have your ideas questioned and transformed.\n• **Respect** — recognizing the validity of perspectives different from your own.\n• **Honesty** — expressing what you truly think and feel.\n\nDialogue is the heart of my declaration: it's *through it* that we build freedom and shared responsibility.";
        }

        // Spotlight / marketplace / platform
        if (preg_match('/\b(spotlight|marketplace|tienda|store|empresa|business|emprendedor|entrepreneur|product|compra|buy|vend|ubat[eé]|mapa|map)\b/u', $msg)) {
            return $es
                ? "**Spotlight** 🌟 es el marketplace digital de la comunidad. Conecta a emprendedores locales con clientes de la región.\n\n**¿Qué puedes hacer en Spotlight?**\n• 🛒 **Explorar y comprar** productos de empresas locales verificadas.\n• 🗺️ **Mapa interactivo** para encontrar negocios cercanos y cómo llegar.\n• 👤 **Perfil personal** para gestionar tus compras e información.\n• 🛍️ **Carrito y pedidos** con seguimiento de tus compras.\n\n¿Necesitas ayuda con alguna función específica de la plataforma?"
                : "**Spotlight** 🌟 is the digital marketplace of our community. It connects local entrepreneurs with regional customers.\n\n**What can you do on Spotlight?**\n• 🛒 **Browse and buy** products from verified local businesses.\n• 🗺️ **Interactive map** to find nearby businesses and directions.\n• 👤 **Personal profile** to manage your purchases and information.\n• 🛍️ **Cart and orders** to track your purchases.\n\nDo you need help with any specific platform feature?";
        }

        // Motivation / inspiration
        if (preg_match('/\b(motiva|motivat|inspira|inspir|ánimo|encourage|fuerza|strength|seguir|keep going|rendirse|give up|puedo|can i|lograr|achieve)\b/u', $msg)) {
            return $es
                ? "✨ Recuerda: el crecimiento sucede exactamente en los momentos en que decides no rendirte.\n\nComo dice mi declaración: *\"Soy LIBRE, AUTÓNOMO Y RESPONSABLE a través del diálogo y la construcción.\"*\n\nEso significa que tienes dentro de ti la capacidad de **dirigirte**, **controlarte** y **crear tus propias reglas de vida**. No desde el caos, sino desde la reflexión y la acción consciente.\n\n¿Qué desafío específico estás enfrentando? Con gusto te acompaño a pensar en él. 💪"
                : "✨ Remember: growth happens exactly in the moments when you choose not to give up.\n\nAs my declaration says: *\"I am FREE, AUTONOMOUS AND RESPONSIBLE through dialogue and construction.\"*\n\nThat means you have within you the capacity to **guide yourself**, **control yourself**, and **create your own life rules**. Not from chaos, but from reflection and conscious action.\n\nWhat specific challenge are you facing? I'm happy to think it through with you. 💪";
        }

        // Thanks / farewell
        if (preg_match('/\b(gracias|thanks|thank you|genial|great|perfecto|perfect|excelente|excellent|bien|good)\b/u', $msg)) {
            return $es
                ? "¡Con mucho gusto! 😊 Es un placer ser de ayuda.\n\nRecuerda que estoy aquí cuando necesites:\n• Reflexionar sobre algún tema\n• Información sobre Spotlight\n• Una perspectiva diferente a tu día\n\n¡Hasta pronto!"
                : "You're welcome! 😊 It's a pleasure to help.\n\nRemember I'm here whenever you need to:\n• Reflect on any topic\n• Get info about Spotlight\n• Get a fresh perspective on your day\n\nSee you soon!";
        }

        if (preg_match('/\b(adi[oó]s|bye|goodbye|hasta|chao|ciao|nos vemos|see you)\b/u', $msg)) {
            return $es
                ? "¡Hasta pronto! 👋 Fue un placer conversar contigo. Vuelve cuando quieras — siempre hay algo nuevo que explorar juntos. 🌟"
                : "Goodbye! 👋 It was a pleasure talking with you. Come back anytime — there's always something new to explore together. 🌟";
        }

        // Default — rich engaging response
        return $es
            ? "Gracias por tu mensaje. 😊 Soy el **Asistente de Spotlight** y me especializo en temas que potencian el desarrollo humano.\n\n**Puedo ayudarte con:**\n• 🧠 **Gestión del conocimiento** — cómo capturar, organizar y aplicar el saber\n• 🌱 **Crecimiento personal** — herramientas para tu desarrollo\n• ⚖️ **Ética y autonomía** — reflexiones sobre valores y libertad\n• 🤝 **Diálogo y comunicación** — habilidades para conectar mejor\n• 🏪 **Spotlight** — la plataforma marketplace de Ubaté\n\n¿Sobre cuál de estos temas te gustaría profundizar?"
            : "Thank you for your message. 😊 I'm the **Spotlight Assistant** and I specialize in topics that enhance human development.\n\n**I can help you with:**\n• 🧠 **Knowledge management** — how to capture, organize, and apply wisdom\n• 🌱 **Personal growth** — tools for your development\n• ⚖️ **Ethics and autonomy** — reflections on values and freedom\n• 🤝 **Dialogue and communication** — skills to connect better\n• 🏪 **Spotlight** — Ubaté's marketplace platform\n\nWhich of these topics would you like to explore?";
    }
}
