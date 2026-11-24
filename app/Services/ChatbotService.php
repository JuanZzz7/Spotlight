<?php

namespace App\Services;

use App\Models\{Company, Order, Product};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private string $apiKey;
    private array $geminiModels = [
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
    ];

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key', '');
    }

    /* ──────────────────────────────────────────────────────────
     * Language detection
     * ────────────────────────────────────────────────────────── */
    public function detectLanguage(string $message): string
    {
        $msg = mb_strtolower(trim($message));

        if (preg_match('/[áéíóúüñ¿¡]/u', $msg)) {
            return 'es';
        }

        $esWords = [
            'hola', 'gracias', 'como', 'que', 'por', 'favor', 'ayuda', 'quiero',
            'necesito', 'puedo', 'puede', 'tengo', 'estoy', 'para', 'sobre',
            'cuando', 'donde', 'tambien', 'ademas', 'pero', 'porque', 'desde',
            'cuanto', 'cual', 'cuales', 'quien', 'tienda', 'producto', 'pedido',
        ];

        $enWords = [
            'hello', 'hi', 'thanks', 'how', 'what', 'why', 'where', 'when', 'who',
            'help', 'want', 'need', 'can', 'could', 'the', 'and', 'for', 'with',
            'from', 'this', 'that', 'which', 'would', 'should', 'will', 'are', 'is',
        ];

        $esScore = $enScore = 0;
        foreach (preg_split('/\s+/', $msg) as $word) {
            $w = preg_replace('/[^a-z]/u', '', $word);
            if (in_array($w, $esWords, true)) {
                $esScore++;
            }
            if (in_array($w, $enWords, true)) {
                $enScore++;
            }
        }

        return $esScore >= $enScore ? 'es' : 'en';
    }

    /* ──────────────────────────────────────────────────────────
     * Live Database Snapshot for Context-Aware Answers
     * ────────────────────────────────────────────────────────── */
    private function buildLivePlatformContext(): string
    {
        try {
            $lines = [];

            if (Auth::check()) {
                $user = Auth::user();
                $lines[] = "Usuario actual: {$user->name} (Rol: {$user->role})";
                $latestOrder = Order::where('user_id', $user->id)->with(['company', 'shipment'])->latest()->first();
                if ($latestOrder) {
                    $st = $latestOrder->shipment?->status_label ?? ($latestOrder->order_status ?: $latestOrder->status);
                    $guide = $latestOrder->shipment?->tracking_number ?: 'En preparación';
                    $comp = $latestOrder->company?->name ?: 'Comercio aliado';
                    $lines[] = "Último pedido del usuario: Orden #{$latestOrder->id} en {$comp}, Estado: {$st}, Guía: {$guide}, Total: $" . number_format($latestOrder->total, 0, ',', '.') . " COP.";
                } else {
                    $lines[] = "Pedidos del usuario actual: 0 pedidos registrados hasta el momento.";
                }
            }

            $companies = Company::where('status', 'active')
                ->select(['id', 'name', 'category', 'city', 'country'])
                ->take(12)
                ->get();

            if ($companies->isNotEmpty()) {
                $compList = $companies->map(function ($c) {
                    $loc = ($c->city ?: 'Ubaté') . ($c->country === 'MX' ? ' (México)' : ' (Colombia)');
                    return "{$c->name} [{$c->category}, {$loc}]";
                })->implode('; ');
                $lines[] = "Tiendas activas en Spotlight ({$companies->count()} mostradas): {$compList}.";
            }

            $products = Product::where('active', true)
                ->where('stock', '>', 0)
                ->with('company:id,name,country')
                ->latest()
                ->take(14)
                ->get();

            if ($products->isNotEmpty()) {
                $prodList = $products->map(function ($p) {
                    $price = $p->formatted_price_cop ?? ('$' . number_format($p->price, 0, ',', '.') . ' COP');
                    $store = $p->company?->name ?? 'Tienda Local';
                    return "{$p->name} ({$price} - {$store})";
                })->implode('; ');
                $lines[] = "Catálogo de productos disponibles ahora: {$prodList}.";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /* ──────────────────────────────────────────────────────────
     * Versatile Open-Domain System Prompt
     * ────────────────────────────────────────────────────────── */
    private function buildSystemPrompt(string $lang): string
    {
        $context = $this->buildLivePlatformContext();

        if ($lang === 'en') {
            return "You are Spotlight IA, an intelligent, versatile, friendly, and open-domain AI assistant integrated into Spotlight (a digital marketplace connecting entrepreneurs and buyers in Colombia and Mexico).\n\n"
                . "Key capabilities & behavior:\n"
                . "1. OPEN-DOMAIN ASSISTANT: You are NOT limited only to Spotlight topics. You can answer ANY question the user asks across ANY domain: general knowledge, math & calculations, programming & technology, business & marketing advice, writing & copywriting, science, history, recipes, daily tips, translations, or casual conversation.\n"
                . "2. SPOTLIGHT EXPERT: When the user asks about products, stores, orders, shipping, or DIAN electronic billing, use the real-time platform data below to give accurate, specific answers.\n"
                . "3. STYLE: Respond in English, be clear, helpful, natural, and concise (use bullet points or bold text when helpful).\n\n"
                . ($context ? "Real-time Spotlight context:\n{$context}" : "");
        }

        return "Eres Spotlight IA, un asistente virtual inteligente, versátil, amigable y de propósito general integrado en Spotlight (plataforma de comercio digital que conecta emprendedores y compradores en Colombia y México).\n\n"
            . "Capacidades y reglas clave:\n"
            . "1. ASISTENTE MULTIDOMINIO ABIERTO: NO estás limitado únicamente a temas de Spotlight. Puedes responder CUALQUIER pregunta de cualquier ámbito que el usuario te haga: cultura general, matemáticas y cálculos, programación y tecnología, consejos de negocios y marketing, redacción de textos, ciencia, historia, recetas, estudio, traducciones o conversación libre.\n"
            . "2. EXPERTO EN SPOTLIGHT: Cuando el usuario pregunte por productos, tiendas, pedidos, rastreo de envíos o facturación electrónica DIAN, utiliza los datos reales en vivo que aparecen abajo para dar respuestas concretas y útiles.\n"
            . "3. ESTILO: Responde en español de forma natural, cálida, directa y bien estructurada (usa viñetas o negritas cuando aporte claridad).\n\n"
            . ($context ? "Contexto en tiempo real de Spotlight:\n{$context}" : "");
    }

    /* ──────────────────────────────────────────────────────────
     * Main chat entry point (Multi-Engine)
     * ────────────────────────────────────────────────────────── */
    public function chat(string $message, string $lang, array $history = []): array
    {
        // 1. Intentar Google Gemini API si la llave está configurada
        if (!empty($this->apiKey)) {
            $result = $this->callGeminiCurl($message, $lang, $history);
            if ($result !== null) {
                return $result;
            }
        }

        // 2. Intentar motor LLM generativo abierto (compatible con OpenAI, sin llave requerida)
        $openLlmResult = $this->callOpenGenerativeLlm($message, $lang, $history);
        if ($openLlmResult !== null) {
            return $openLlmResult;
        }

        // 3. Respaldo local inteligente multidominio + consulta directa a BD
        return $this->localFallback($message, $lang);
    }

    /* ──────────────────────────────────────────────────────────
     * Engine 1: Google Gemini API via cURL
     * ────────────────────────────────────────────────────────── */
    private function callGeminiCurl(string $message, string $lang, array $history): ?array
    {
        $contents = [
            ['role' => 'user',  'parts' => [['text' => $this->buildSystemPrompt($lang)]]],
            ['role' => 'model', 'parts' => [['text' => $lang === 'en' ? 'Understood. Ready to help with any topic.' : 'Entendido. Listo para ayudar en cualquier ámbito.']]],
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
                'temperature'     => 0.7,
                'maxOutputTokens' => 900,
                'topP'            => 0.9,
            ],
        ]);

        foreach ($this->geminiModels as $endpoint) {
            $url = "{$endpoint}?key={$this->apiKey}";
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT        => 18,
                CURLOPT_CONNECTTIMEOUT => 8,
            ]);

            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError || $response === false) {
                Log::warning('Chatbot Gemini cURL error: ' . $curlError);
                continue;
            }

            $json = json_decode($response, true);

            if ($httpCode === 200 && isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'reply'   => trim($json['candidates'][0]['content']['parts'][0]['text']),
                    'lang'    => $lang,
                    'success' => true,
                ];
            }
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────
     * Engine 2: Open Generative LLM Fallback (OpenAI-compatible)
     * Permite responder cualquier pregunta abierta aunque no haya
     * GEMINI_API_KEY activa en el servidor.
     * ────────────────────────────────────────────────────────── */
    private function callOpenGenerativeLlm(string $message, string $lang, array $history): ?array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt($lang)],
        ];

        foreach (array_slice($history, -6) as $turn) {
            if (!empty($turn['content'])) {
                $messages[] = [
                    'role'    => ($turn['role'] ?? 'user') === 'user' ? 'user' : 'assistant',
                    'content' => (string) $turn['content'],
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = json_encode([
            'model'       => 'openai',
            'messages'    => $messages,
            'temperature' => 0.7,
        ]);

        $ch = curl_init('https://text.pollinations.ai/openai');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 16,
            CURLOPT_CONNECTTIMEOUT => 6,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || !$response || $httpCode !== 200) {
            return null;
        }

        $json = json_decode($response, true);
        $text = $json['choices'][0]['message']['content'] ?? null;

        if (is_string($text) && trim($text) !== '') {
            return [
                'reply'   => trim($text),
                'lang'    => $lang,
                'success' => true,
            ];
        }

        return null;
    }

    /* ══════════════════════════════════════════════════════════
     * Engine 3: Local Multi-Domain & Live Database Fallback
     * ══════════════════════════════════════════════════════════ */
    private function localFallback(string $message, string $lang): array
    {
        $reply = $this->matchFallback(trim($message), $lang);
        return ['reply' => $reply, 'lang' => $lang, 'success' => true];
    }

    private function matchFallback(string $rawMsg, string $lang): string
    {
        $msg = mb_strtolower($rawMsg);
        $es  = $lang === 'es';

        // 1. Cálculos matemáticos directos (ej. "cuánto es 250 * 14", "150000 * 0.19", "45 + 89")
        if (preg_match('/(-?\d+(?:[\.,]\d+)?)\s*([\+\-\*\/x÷%])\s*(-?\d+(?:[\.,]\d+)?)/u', $msg, $m)) {
            $a = (float) str_replace(',', '.', $m[1]);
            $op = $m[2];
            $b = (float) str_replace(',', '.', $m[3]);
            $res = match ($op) {
                '+' => $a + $b,
                '-' => $a - $b,
                '*', 'x' => $a * $b,
                '/', '÷' => $b != 0 ? $a / $b : null,
                '%' => ($a * $b) / 100,
                default => null,
            };
            if ($res !== null) {
                $formatted = fmod($res, 1) == 0 ? number_format($res, 0, ',', '.') : number_format($res, 2, ',', '.');
                return $es
                    ? "El resultado de **{$m[1]} {$op} {$m[3]}** es **{$formatted}**.\n\nSi necesitas hacer otro cálculo (por ejemplo márgenes de ganancia, IVA del 19% o conversión entre pesos mexicanos `MXN` y pesos colombianos `COP`), escríbelo y lo calculo al instante."
                    : "The result of **{$m[1]} {$op} {$m[3]}** is **{$formatted}**.";
            }
        }

        // 2. Rastreo de pedidos y estado de órdenes (consulta real del usuario)
        if (preg_match('/\b(rastrear|rastreo|pedido|orden|env[ií]o|gu[ií]a|paquete|tracking|order|shipment)\b/u', $msg)) {
            if (Auth::check()) {
                $latest = Order::where('user_id', Auth::id())->with(['company', 'shipment'])->latest()->first();
                if ($latest) {
                    $st = $latest->shipment?->status_label ?? ($latest->order_status ?: $latest->status);
                    $guide = $latest->shipment?->tracking_number ?: 'En preparación por el comercio';
                    $store = $latest->company?->name ?: 'Comercio aliado';
                    return $es
                        ? "📦 **Estado de tu pedido más reciente:**\n\n• **Orden:** #{$latest->id}\n• **Tienda:** {$store}\n• **Estado actual:** {$st}\n• **Número de guía:** `{$guide}`\n• **Total:** $" . number_format($latest->total, 0, ',', '.') . " COP\n\nPuedes ver el mapa y la línea de tiempo en vivo desde **Mis Compras** (`/user/orders`) o en el portal público `/rastreo`."
                        : "📦 **Your latest order status:**\n\n• **Order:** #{$latest->id}\n• **Store:** {$store}\n• **Status:** {$st}\n• **Tracking:** `{$guide}`";
                }
            }
            return $es
                ? "📦 **¿Cómo rastrear un pedido en Spotlight?**\n\n1. Ve a **Mi Perfil → Mis Compras** (`/user/orders`) y pulsa el botón **Rastrear Envío** en tu orden.\n2. Si tienes un código de guía (ej. `CRD-...`, `SER-...`), también puedes consultarlo sin iniciar sesión en el portal público **`/rastreo`**.\n\nActualmente tu cuenta no registra envíos pendientes en curso."
                : "📦 **How to track an order:** Go to **My Orders** (`/user/orders`) and click **Track Shipment**, or enter your tracking number at `/rastreo`.";
        }

        // 3. Recomendación de productos reales desde la base de datos
        if (preg_match('/\b(recomi[eé]ndame|producto|productos|catalogo|cat[aá]logo|comprar|oferta|precio|barato|venden|recommend|products)\b/u', $msg)) {
            $prods = Product::where('active', true)->where('stock', '>', 0)->with('company')->latest()->take(5)->get();
            if ($prods->isNotEmpty()) {
                $list = $prods->map(function ($p) {
                    $price = $p->formatted_price_cop ?? ('$' . number_format($p->price, 0, ',', '.') . ' COP');
                    $store = $p->company?->name ?? 'Comercio Verificado';
                    return "• **{$p->name}** — **{$price}** *(por {$store})*";
                })->implode("\n");

                return $es
                    ? "🛍️ **Aquí tienes algunos productos destacados disponibles hoy en Spotlight:**\n\n{$list}\n\nPuedes filtrarlos por categoría o país (`CO` / `MX`) directamente en la vitrina principal. ¿Buscas algún tipo de producto en especial (tecnología, moda, alimentos, artesanías, deportes)?"
                    : "🛍️ **Here are some featured products available today on Spotlight:**\n\n{$list}";
            }
        }

        // 4. Consulta de tiendas y comercios locales (datos reales de BD)
        if (preg_match('/\b(tienda|tiendas|comercio|comercios|emprendimiento|empresas|locales|ubicaci[oó]n|puebla|ubat[eé]|m[eé]xico|colombia|shops|stores)\b/u', $msg)) {
            $comps = Company::where('status', 'active')->take(6)->get();
            if ($comps->isNotEmpty()) {
                $list = $comps->map(function ($c) {
                    $country = $c->country === 'MX' ? 'México' : 'Colombia';
                    $city = $c->city ?: ($c->country === 'MX' ? 'Puebla' : 'Ubaté');
                    return "• **{$c->name}** — *{$c->category}* ({$city}, {$country})";
                })->implode("\n");

                return $es
                    ? "🏪 **Comercios activos en la red Spotlight:**\n\n{$list}\n\nPuedes explorar todas las tiendas y su ubicación exacta en el **Mapa Geo-Comercial** (`/user/map`)."
                    : "🏪 **Active stores on Spotlight:**\n\n{$list}";
            }
        }

        // 5. Soporte técnico, pagos, facturación DIAN, cuenta
        if (preg_match('/\b(soporte|ayuda|problema|error|pago|wompi|mercadopago|factura|dian|contrase[ñn]a|cuenta|support|billing)\b/u', $msg)) {
            return $es
                ? "🛠️ **Centro de Soporte Spotlight:**\n\n• **Pagos y Checkout:** Aceptamos tarjetas, PSE, Nequi y Bancolombia (vía Wompi en Colombia) y Mercado Pago (para comercios de México).\n• **Facturación Electrónica DIAN:** Los comercios formales verificados emiten factura UBL 2.1 con CUFE descargable desde **Mis Compras**.\n• **Chat directo con tiendas:** En la sección **Mensajes** (`/chat`) puedes hablar sin intermediarios con cualquier vendedor.\n\n¿Con cuál de estos puntos necesitas ayuda específica?"
                : "🛠️ **Spotlight Support:** We support Wompi (Colombia), Mercado Pago (Mexico), DIAN electronic invoicing, and direct merchant chat at `/chat`. How can I assist you?";
        }

        // 6. Ámbito: Programación, Tecnología e Inteligencia Artificial
        if (preg_match('/\b(programar|programaci[oó]n|c[oó]digo|software|web|html|css|javascript|php|laravel|python|sql|base de datos|ia|inteligencia artificial|algoritmo|coding|tech)\b/u', $msg)) {
            return $es
                ? "💻 **Tecnología, Programación e IA:**\n\n¡Claro! Puedo ayudarte con desarrollo de software, arquitectura web, bases de datos (`MySQL`/`SQL`), `PHP` / `Laravel`, `JavaScript`, `Python`, `HTML/CSS` o conceptos de Inteligencia Artificial.\n\nPor ejemplo, puedes pedirme:\n• *\"Explícame cómo funciona una API REST o el patrón MVC\"*\n• *\"Dame un ejemplo de consulta SQL o función en JavaScript\"*\n• *\"¿Cómo optimizar el rendimiento de una aplicación web?\"*\n\n¿Qué tema técnico o fragmento de código quieres que revisemos?"
                : "💻 **Tech & Programming:** I can help you with web development, PHP/Laravel, JavaScript, Python, SQL, and AI concepts. What technical question would you like to solve?";
        }

        // 7. Ámbito: Negocios, Marketing, Ventas y Finanzas
        if (preg_match('/\b(vender|ventas|marketing|publicidad|negocio|finanzas|clientes|redes sociales|instagram|estrategia|emprender|ganancia|margen|marca)\b/u', $msg)) {
            return $es
                ? "📈 **Estrategia de Negocios y Marketing:**\n\nPara impulsar las ventas de un negocio o emprendimiento, te recomiendo enfocarte en 4 claves prácticas:\n\n1. **Fotografía y propuesta clara:** Productos con fotos limpias desde varios ángulos y descripciones que resuelvan dudas aumentan la conversión hasta un 40%.\n2. **Precio y margen saludable:** Calcula tu costo unitario real y asegúrate de mantener un margen bruto claro (`(Precio - Costo) / Precio`).\n3. **Respuesta rápida:** Atender preguntas de clientes en menos de 15 minutos multiplica el cierre de ventas.\n4. **Fidelización:** Ofrece pequeños beneficios o seguimiento post-venta para que el comprador regrese.\n\n¿Quieres que redactemos juntos una descripción atractiva para un producto o una estrategia para tu tienda?"
                : "📈 **Business & Marketing Strategy:** Focus on clear product photography, healthy profit margins, fast customer response times, and customer retention. Would you like help writing a product description or sales pitch?";
        }

        // 8. Saludo general
        if (preg_match('/\b(hola|buenos|buenas|hi|hello|hey|saludos|qu[eé] tal)\b/u', $msg)) {
            return $es
                ? "¡Hola! 👋 Soy **Spotlight IA**, tu asistente virtual interactivo.\n\nPuedes preguntarme tanto sobre **Spotlight** (productos, tiendas locales, rastreo de pedidos) como sobre **cualquier otro ámbito**:\n• 💡 Cultura general, ciencia, historia o estudio\n• 🔢 Cálculos matemáticos, porcentajes y finanzas\n• 📈 Ideas de marketing, ventas y redacción de textos\n• 💻 Tecnología, programación e informática\n\n¿Qué te gustaría consultar o hacer hoy?"
                : "Hello! 👋 I'm **Spotlight IA**, your interactive virtual assistant. You can ask me about Spotlight (products, stores, order tracking) or any other topic (math, business, tech, writing, general knowledge). How can I help you today?";
        }

        // 9. Respuesta abierta interactiva para cualquier otra consulta
        return $es
            ? "¡Con gusto te ayudo con tu consulta sobre **\"{$rawMsg}\"**! 💡\n\nSoy **Spotlight IA** y estoy diseñado para asistirte de forma abierta en distintos ámbitos:\n• **En la plataforma:** recomendarte productos reales del catálogo, mostrarte tiendas de Colombia y México o verificar el estado de tus pedidos.\n• **En otros ámbitos:** resolver cálculos matemáticos (ej. `25000 * 19%`), darte estrategias de ventas y marketing, redactar textos comerciales o explicar temas de tecnología y cultura general.\n\n¿Me das un poco más de detalle sobre lo que necesitas para darte una respuesta exacta?"
            : "I'd be happy to help you with **\"{$rawMsg}\"**! 💡 You can ask me for specific calculations, business/marketing copy, tech explanations, or live product and store recommendations on Spotlight. Could you share a bit more detail?";
    }

    /* ══════════════════════════════════════════════════════════
     * MERCHANT QUICK REPLIES GENERATOR (SPOTLIGHT IA)
     * ══════════════════════════════════════════════════════════ */
    public function generateMerchantQuickReply(string $input, ?\App\Models\Company $company = null, string $category = 'general'): string
    {
        $bizName    = $company?->name ?: 'nuestra tienda';
        $bizAddress = $company?->address ?: 'Ubaté';
        $bizPhone   = $company?->phone ?: '';
        $schedule   = $company ? ($company->opening_time ? substr($company->opening_time, 0, 5) . ' a ' . substr($company->closing_time, 0, 5) : 'horario habitual') : 'horario comercial';

        $prompt = "Eres un redactor experto en atención al cliente para la plataforma de comercio local Spotlight.\n"
            . "Tu tarea es redactar una respuesta rápida, cálida, concisa (máximo 2 o 3 oraciones cortas, directa y lista para ser enviada por el comerciante de \"{$bizName}\" a un cliente).\n"
            . "Datos del negocio:\n"
            . "- Nombre del comercio: {$bizName}\n"
            . "- Ubicación: {$bizAddress}\n"
            . "- Horario de atención: {$schedule}\n"
            . "- Teléfono/Contacto: {$bizPhone}\n"
            . "- Contexto/Categoría: {$category}\n\n"
            . "Instrucción o mensaje del cliente:\n\"{$input}\"\n\n"
            . "Reglas críticas:\n"
            . "1. Redacta en primera persona como el comercio (ej: '¡Hola! En {$bizName}...', 'Con gusto te confirmamos...').\n"
            . "2. Sé muy empático, claro y amigable.\n"
            . "3. NO incluyas introducciones como 'Aquí tienes la respuesta:', devuelve DIRECTAMENTE el texto exacto que el comerciante enviará al cliente.\n"
            . "4. Si el comerciante solicita información de horarios, envíos o pagos, incluye los datos pertinentes.";

        if (!empty($this->apiKey)) {
            $res = $this->callGeminiCurl($prompt, 'es', []);
            if ($res && !empty($res['reply'])) {
                return trim($res['reply'], "\"'");
            }
        }

        $openRes = $this->callOpenGenerativeLlm($prompt, 'es', []);
        if ($openRes && !empty($openRes['reply'])) {
            return trim($openRes['reply'], "\"'");
        }

        return $this->localMerchantQuickReplyFallback($input, $bizName, $bizAddress, $schedule, $category);
    }

    private function localMerchantQuickReplyFallback(string $input, string $bizName, string $bizAddress, string $schedule, string $category): string
    {
        $lower = mb_strtolower($input);
        if (str_contains($lower, 'horario') || str_contains($lower, 'hora') || str_contains($lower, 'abre') || str_contains($lower, 'abierto')) {
            return "¡Hola! En {$bizName} te atendemos con gusto de {$schedule} en nuestro punto de atención en {$bizAddress}. ¡Estaremos encantados de recibirte!";
        }
        if (str_contains($lower, 'envio') || str_contains($lower, 'domicilio') || str_contains($lower, 'entrega') || str_contains($lower, 'despacho')) {
            return "¡Hola! Sí realizamos envíos y domicilios seguros. Por favor facilítanos tu dirección exacta y con gusto coordinamos la entrega de tu pedido.";
        }
        if (str_contains($lower, 'pago') || str_contains($lower, 'transferencia') || str_contains($lower, 'nequi') || str_contains($lower, 'daviplata') || str_contains($lower, 'tarjeta') || str_contains($lower, 'wompi')) {
            return "¡Hola! En {$bizName} aceptamos pagos seguros con tarjetas débito/crédito, transferencias y pagos en línea. ¿Cuál medio de pago te resulta más cómodo?";
        }
        if (str_contains($lower, 'garantia') || str_contains($lower, 'garantía') || str_contains($lower, 'cambio') || str_contains($lower, 'reclamo')) {
            return "¡Hola! En {$bizName} todos nuestros productos cuentan con garantía directa y respaldo de calidad. Indícanos los detalles de tu compra y con gusto te asistiremos de inmediato.";
        }
        if (str_contains($lower, 'demora') || str_contains($lower, 'retraso') || str_contains($lower, 'ocupado') || str_contains($lower, 'espera')) {
            return "Hola, muchas gracias por tu paciencia. En este momento estamos atendiendo en tienda física, pero ya revisamos tu mensaje y te brindaremos respuesta completa en unos minutos.";
        }
        if (str_contains($lower, 'precio') || str_contains($lower, 'costo') || str_contains($lower, 'cuanto') || str_contains($lower, 'cuánto') || str_contains($lower, 'stock') || str_contains($lower, 'tienes') || str_contains($lower, 'disponible')) {
            return "¡Hola! Con mucho gusto te confirmamos disponibilidad y cotización en {$bizName}. ¿Cuál producto o referencia específica te gustaría adquirir?";
        }

        return "¡Hola! Gracias por comunicarte con {$bizName}. Estamos a tu entera disposición para resolver cualquier duda sobre nuestros productos y servicios. ¿En qué te podemos asesorar hoy?";
    }
}
