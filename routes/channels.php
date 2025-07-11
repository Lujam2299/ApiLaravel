<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\apiUser;
use App\Models\Conversation;

Broadcast::channel('private-conversation.{conversationId}', function ($user, $conversationId) {
    // Verifica que el usuario pertenezca a la conversación
    return Conversation::where('id', $conversationId)
        ->where(function($query) use ($user) {
            $query->where('user_one', $user->id)
                  ->orWhere('user_two', $user->id);
        })
        ->exists();
});