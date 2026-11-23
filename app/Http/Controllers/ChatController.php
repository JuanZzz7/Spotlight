<?php

namespace App\Http\Controllers;

use App\Models\{User, Message, Company};
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(private readonly ?ChatbotService $chatbot = null)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        // Obtener usuarios con los que se ha chateado
        $sentTo = Message::where('sender_id', $user->id)->pluck('receiver_id')->toArray();
        $receivedFrom = Message::where('receiver_id', $user->id)->pluck('sender_id')->toArray();
        $userIds = array_unique(array_merge($sentTo, $receivedFrom));

        $chats = User::whereIn('id', $userIds)->with('company')->get();
        $chats = $this->enrichChats($chats, $user);

        $unreadTotal = Message::where('receiver_id', $user->id)->where('is_read', false)->count();
        $unreadChatsCount = $chats->where('unread_count', '>', 0)->count();

        $companies = [];
        if ($user->role === 'user') {
            $companies = User::where('role', 'business')->has('company')->with('company')->get();
        }

        $quickReplies = $this->getQuickReplies($company);
        $chatStatusData = $this->resolveChatStatus($company);

        return view('chat.index', compact('chats', 'companies', 'company', 'unreadTotal', 'unreadChatsCount', 'quickReplies', 'chatStatusData'));
    }

    public function show(User $receiver)
    {
        $sender = Auth::user();
        $company = $sender->company;

        $messages = Message::where(function($q) use ($sender, $receiver) {
            $q->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
        })->orWhere(function($q) use ($sender, $receiver) {
            $q->where('sender_id', $receiver->id)->where('receiver_id', $sender->id);
        })->orderBy('created_at', 'asc')->get();

        // Marcar como leídos
        Message::where('sender_id', $receiver->id)->where('receiver_id', $sender->id)->update(['is_read' => true]);
        $receiver->loadMissing('company');

        // Get chats for sidebar
        $sentTo = Message::where('sender_id', $sender->id)->pluck('receiver_id')->toArray();
        $receivedFrom = Message::where('receiver_id', $sender->id)->pluck('sender_id')->toArray();
        $userIds = array_unique(array_merge($sentTo, $receivedFrom));
        $chats = User::whereIn('id', $userIds)->with('company')->get();
        $chats = $this->enrichChats($chats, $sender);

        $unreadTotal = Message::where('receiver_id', $sender->id)->where('is_read', false)->count();
        $unreadChatsCount = $chats->where('unread_count', '>', 0)->count();

        $quickReplies = $this->getQuickReplies($company);
        $chatStatusData = $this->resolveChatStatus($company);

        // Ultimo mensaje del cliente para sugerencias automáticas de IA
        $lastCustomerMsg = $messages->where('sender_id', $receiver->id)->last();

        return view('chat.show', compact('receiver', 'messages', 'chats', 'company', 'unreadTotal', 'unreadChatsCount', 'quickReplies', 'chatStatusData', 'lastCustomerMsg'));
    }

    public function send(Request $request, User $receiver)
    {
        $request->validate(['message' => 'required|string|max:5000']);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'message' => $request->message
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back();
    }

    public function getMessages(User $receiver)
    {
        $sender = Auth::user();
        $messages = Message::where('sender_id', $receiver->id)
            ->where('receiver_id', $sender->id)
            ->where('is_read', false)
            ->get();

        foreach ($messages as $m) {
            $m->update(['is_read' => true]);
        }

        return response()->json($messages);
    }

    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'chat_status'     => 'nullable|in:online,away,busy,offline',
            'chat_visibility' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $company = $user->company;
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Empresa no encontrada'], 404);
        }

        if ($request->has('chat_status')) {
            $company->chat_status = $request->input('chat_status');
        }
        if ($request->has('chat_visibility')) {
            $company->chat_visibility = (bool) $request->input('chat_visibility');
        }

        try {
            $company->save();
        } catch (\Throwable $e) {
            cache()->put("company_chat_status_{$company->id}", [
                'chat_status' => $company->chat_status,
                'chat_visibility' => $company->chat_visibility,
            ], now()->addDays(30));
        }

        $resolved = $this->resolveChatStatus($company);

        return response()->json(array_merge(['success' => true], $resolved));
    }

    public function aiGenerateReply(Request $request)
    {
        $request->validate([
            'input'    => 'required|string|max:1000',
            'category' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $company = $user->company;

        $service = $this->chatbot ?: app(ChatbotService::class);
        $reply = $service->generateMerchantQuickReply(
            $request->input('input'),
            $company,
            $request->input('category', 'general')
        );

        return response()->json([
            'success' => true,
            'reply'   => $reply,
        ]);
    }

    public function saveQuickReply(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:80',
            'message'  => 'required|string|max:1000',
            'category' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $company = $user->company;
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Empresa no encontrada'], 404);
        }

        $cachedCustom = cache()->get("company_custom_replies_{$company->id}", []);
        $custom = ($company->chat_quick_replies ?: []) ?: $cachedCustom;

        $newReply = [
            'id'        => 'custom_' . time() . '_' . rand(100, 999),
            'category'  => $request->input('category') ?: 'Personalizada',
            'title'     => $request->input('title'),
            'message'   => $request->input('message'),
            'is_custom' => true,
        ];
        $custom[] = $newReply;

        $company->chat_quick_replies = $custom;
        try {
            $company->save();
        } catch (\Throwable $e) {
            cache()->put("company_custom_replies_{$company->id}", $custom, now()->addDays(30));
        }

        return response()->json([
            'success' => true,
            'reply'   => $newReply,
        ]);
    }

    public function deleteQuickReply(string $id)
    {
        $user = Auth::user();
        $company = $user->company;
        if (!$company) {
            return response()->json(['success' => false], 404);
        }

        $cachedCustom = cache()->get("company_custom_replies_{$company->id}", []);
        $custom = ($company->chat_quick_replies ?: []) ?: $cachedCustom;
        $custom = array_values(array_filter($custom, fn($r) => ($r['id'] ?? '') !== $id));

        $company->chat_quick_replies = $custom;
        try {
            $company->save();
        } catch (\Throwable $e) {
            cache()->put("company_custom_replies_{$company->id}", $custom, now()->addDays(30));
        }

        return response()->json(['success' => true]);
    }

    private function enrichChats($chats, $user)
    {
        foreach ($chats as $chat) {
            $chat->unread_count = Message::where('sender_id', $chat->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            $chat->last_message = Message::where(function($q) use ($user, $chat) {
                $q->where('sender_id', $user->id)->where('receiver_id', $chat->id);
            })->orWhere(function($q) use ($user, $chat) {
                $q->where('sender_id', $chat->id)->where('receiver_id', $user->id);
            })->latest()->first();
        }

        return $chats->sortByDesc(fn($c) => $c->last_message?->created_at ?? $c->created_at)->values();
    }

    private function resolveChatStatus(?Company $company): array
    {
        if (!$company) {
            return [
                'chat_status'     => 'online',
                'chat_visibility' => true,
                'label'           => 'En línea',
                'dot_color'       => '#10b981',
                'desc'            => 'Recibiendo mensajes normalmente',
            ];
        }

        $cachedStatus = cache()->get("company_chat_status_{$company->id}", null);
        $rawStatus = $company->chat_status ?? ($cachedStatus['chat_status'] ?? 'online');
        $visibility = $company->chat_visibility ?? ($cachedStatus['chat_visibility'] ?? true);

        $label = match($rawStatus) {
            'away'    => 'Ausente',
            'busy'    => 'Ocupado',
            'offline' => 'Fuera de horario',
            default   => 'En línea',
        };

        $dotColor = match($rawStatus) {
            'away'    => '#eab308',
            'busy'    => '#ef4444',
            'offline' => '#94a3b8',
            default   => '#10b981',
        };

        $desc = match($rawStatus) {
            'away'    => 'De regreso en unos minutos',
            'busy'    => 'Atendiendo clientes en tienda',
            'offline' => 'Fuera de horario comercial',
            default   => 'Disponible para atender clientes',
        };

        return [
            'chat_status'     => $rawStatus,
            'chat_visibility' => (bool) $visibility,
            'label'           => $label,
            'dot_color'       => $dotColor,
            'desc'            => $desc,
        ];
    }

    private function getQuickReplies(?Company $company): array
    {
        $bizName    = $company?->name ?: 'nuestra tienda';
        $bizAddress = $company?->address ?: 'Ubaté';
        $schedule   = $company ? ($company->opening_time ? substr($company->opening_time, 0, 5) . ' a ' . substr($company->closing_time, 0, 5) : '08:00 a 19:00') : '08:00 a 19:00';

        $defaults = [
            [
                'id'        => 'horario',
                'category'  => 'Horarios',
                'title'     => 'Horario y Ubicación',
                'message'   => "¡Hola! En {$bizName} con gusto te atendemos de {$schedule} en nuestra sede en {$bizAddress}. ¡Te esperamos!",
                'is_custom' => false,
            ],
            [
                'id'        => 'envios',
                'category'  => 'Envíos',
                'title'     => 'Envíos y Domicilios',
                'message'   => "¡Hola! Sí realizamos envíos y domicilios en Ubaté y la región. ¿En qué dirección te encuentras y qué productos deseas solicitar?",
                'is_custom' => false,
            ],
            [
                'id'        => 'pagos',
                'category'  => 'Pagos',
                'title'     => 'Medios de Pago',
                'message'   => "Aceptamos pagos por transferencia bancaria (Nequi, Daviplata, Bancolombia), pagos en línea con Wompi (tarjetas / PSE) y contra entrega.",
                'is_custom' => false,
            ],
            [
                'id'        => 'stock',
                'category'  => 'Inventario',
                'title'     => 'Confirmación de Stock',
                'message'   => "¡Hola! Sí contamos con disponibilidad de este producto en {$bizName}. ¿Te gustaría apartarlo o prefieres recibirlo hoy mismo?",
                'is_custom' => false,
            ],
            [
                'id'        => 'garantia',
                'category'  => 'Garantía',
                'title'     => 'Garantía y Confianza',
                'message'   => "Todos nuestros productos cuentan con garantía directa y respaldo de calidad en {$bizName}. Compras con total seguridad.",
                'is_custom' => false,
            ],
        ];

        $cachedCustom = $company ? cache()->get("company_custom_replies_{$company->id}", []) : [];
        $custom = ($company?->chat_quick_replies ?: []) ?: $cachedCustom;

        return array_merge($defaults, $custom);
    }
}
