<?php

namespace App\Http\Controllers;

use App\Models\{User, Message};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Obtener usuarios con los que se ha chateado
        $sentTo = Message::where('sender_id', $user->id)->pluck('receiver_id')->toArray();
        $receivedFrom = Message::where('receiver_id', $user->id)->pluck('sender_id')->toArray();
        $userIds = array_unique(array_merge($sentTo, $receivedFrom));
        
        $chats = User::whereIn('id', $userIds)->get();
        
        // Si es un empresario y no tiene chats, podríamos sugerirle esperar clientes
        // Si es un usuario, podría ver una lista de empresas para chatear
        $companies = [];
        if ($user->role === 'user') {
            $companies = User::where('role', 'business')->has('company')->get();
        }

        return view('chat.index', compact('chats', 'companies'));
    }

    public function show(User $receiver)
    {
        $sender = Auth::user();
        $messages = Message::where(function($q) use ($sender, $receiver) {
            $q->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
        })->orWhere(function($q) use ($sender, $receiver) {
            $q->where('sender_id', $receiver->id)->where('receiver_id', $sender->id);
        })->orderBy('created_at', 'asc')->get();

        // Marcar como leídos
        Message::where('sender_id', $receiver->id)->where('receiver_id', $sender->id)->update(['is_read' => true]);

        // Get chats for sidebar
        $sentTo = Message::where('sender_id', $sender->id)->pluck('receiver_id')->toArray();
        $receivedFrom = Message::where('receiver_id', $sender->id)->pluck('sender_id')->toArray();
        $userIds = array_unique(array_merge($sentTo, $receivedFrom));
        $chats = User::whereIn('id', $userIds)->get();

        return view('chat.show', compact('receiver', 'messages', 'chats'));
    }

    public function send(Request $request, User $receiver)
    {
        $request->validate(['message' => 'required|string']);

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

    /**
     * Devuelve el total de mensajes no leídos del usuario autenticado.
     * Usado por el polling global del sidebar para mostrar el badge.
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
