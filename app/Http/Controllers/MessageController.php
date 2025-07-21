<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\apiUser;
use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function searchUsers(Request $request)
    {
        try {
            $validated = $request->validate([
                'query' => 'required|string|min:3'
            ]);

            $users = apiUser::where('name', 'like', '%' . $validated['query'] . '%')
                        ->select('id', 'name', 'email')
                        ->limit(10)
                        ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            // Log del error (ver storage/logs/laravel.log)
            Log::error("Error en searchUsers: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = apiUser::find($request->user_id);
        $currentUser = Auth::user();

        // Verificar si ya existe una conversación entre estos usuarios
        $conversation = $currentUser->conversations()
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->where('is_group', false)
            ->first();

        if (!$conversation) {
            $conversation = DB::transaction(function () use ($currentUser, $user) {
                $conversation = Conversation::create(['is_group' => false]);
                $conversation->users()->attach([$currentUser->id, $user->id]);
                return $conversation;
            });
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $conversation->messages()->with('user')->get()
        ]);
    }
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string'
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::id(),
            'body' => $request->body
        ]);

        // Cargar relaciones necesarias
        $message->load('user');


        // Disparar evento de WebSocket
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }


    public function getMessages($conversationId)
    {
        // Verificar acceso a la conversación
        $conversation = Auth::user()->conversations()
            ->where('conversations.id', $conversationId)
            ->firstOrFail();

        // Obtener mensajes ordenados
        $messages = $conversation->messages()
            ->with(['user' => function ($query) {
                $query->select('id', 'name'); // Solo datos básicos del usuario
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marcar mensajes como leídos
        $conversation->messages()
            ->whereNull('read_at')
            ->where('user_id', '!=', Auth::id())
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function markAsRead(Message $message)
    {
        if ($message->conversation->users->contains(Auth::id())) {
            $message->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
    public function getConversations(Request $request)
    {
        try {
            $user = $request->user();

            $conversations = $user->conversations()
                ->with(['users', 'latestMessage'])
                ->orderByDesc(
                    Message::select('created_at')
                        ->whereColumn('conversation_id', 'conversations.id')
                        ->latest()
                        ->take(1)
                )
                ->get()
                ->map(function ($conversation) use ($user) {
                    $otherUser = $conversation->users->firstWhere('id', '!=', $user->id);

                    // Obtener el conteo de mensajes no leídos (creados después del last_read_at)
                    $unreadCount = $conversation->messages()
                        ->where('user_id', '!=', $user->id)
                        ->where('created_at', '>', $conversation->pivot->last_read_at ?? '1970-01-01')
                        ->count();

                    return [
                        'id' => $conversation->id,
                        'is_group' => $conversation->is_group,
                        'latest_message' => $conversation->latestMessage,
                        'users' => $conversation->users,
                        'title' => $conversation->is_group ? $conversation->title : $otherUser->name,
                        'unread_count' => $unreadCount,
                        'last_read_at' => $conversation->pivot->last_read_at
                    ];
                });

            return response()->json($conversations);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener conversaciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
