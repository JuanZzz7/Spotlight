<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    public function __construct(private readonly ChatbotService $chatbot) {}

    /**
     * Display the dedicated chatbot page.
     */
    public function index()
    {
        return view('user.chatbot');
    }

    /**
     * Handle an incoming chat message.
     * POST /chatbot/message
     */
    public function message(Request $request): JsonResponse
    {
        $userMessage = trim((string) $request->input('message', ''));
        if ($userMessage === '') {
            return response()->json([
                'reply'   => 'Por favor escribe tu pregunta o consulta.',
                'lang'    => 'es',
                'success' => true,
            ]);
        }

        $userMessage = mb_substr($userMessage, 0, 2000);
        $rawHistory  = $request->input('history', []);
        $history     = is_array($rawHistory) ? array_slice($rawHistory, -12) : [];

        $rawLang = $request->input('lang');
        $lang = in_array($rawLang, ['es', 'en'], true)
            ? $rawLang
            : $this->chatbot->detectLanguage($userMessage);

        try {
            $result = $this->chatbot->chat($userMessage, $lang, $history);
            return response()->json([
                'reply'   => $result['reply'] ?? 'Estoy listo para ayudarte. ¿Puedes reformular tu pregunta?',
                'lang'    => $result['lang'] ?? $lang,
                'success' => $result['success'] ?? true,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ChatbotController error: ' . $e->getMessage());
            return response()->json([
                'reply'   => $lang === 'en'
                    ? 'I encountered a temporary hiccup processing that. Could you ask me again?'
                    : 'Tuve un inconveniente temporal al procesar la consulta. ¿Me la repites por favor?',
                'lang'    => $lang,
                'success' => false,
            ]);
        }
    }

    /**
     * Detect the language of a given text.
     * POST /chatbot/detect-lang
     */
    public function detectLang(Request $request): JsonResponse
    {
        $request->validate(['text' => 'required|string|max:500']);
        $lang = $this->chatbot->detectLanguage($request->input('text'));

        return response()->json(['lang' => $lang]);
    }
}
