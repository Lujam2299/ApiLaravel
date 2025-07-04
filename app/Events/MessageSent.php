<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        // Carga relaciones necesarias y asegura formato consistente
        $this->message = $message->loadMissing(['user', 'parent']);
    }

    public function broadcastOn()
    {
        // Usa PresenceChannel para chats privados (opcional)
        return new Channel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastAs()
    {
        // Nombre estándar para eventos de Pusher/Reverb
        return 'message.sent';
    }

    public function broadcastWith()
    {
        // Estructura consistente para el frontend
        return [
            'message' => [
                'id' => $this->message->id,
                'body' => $this->message->body,
                'user_id' => $this->message->user_id,
                'conversation_id' => $this->message->conversation_id,
                'created_at' => $this->message->created_at->toISOString(),
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    // Agrega más campos si son necesarios
                ],
                // Incluye datos del mensaje padre si existe
                'parent' => $this->message->parent ? [
                    'id' => $this->message->parent->id,
                    'body' => $this->message->parent->body
                ] : null
            ],
            'sent_at' => now()->toISOString()
        ];
    }
}