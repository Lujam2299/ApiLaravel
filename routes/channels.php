<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\apiUser;
use App\Models\Conversation;

Broadcast::channel('conversation.{conversationId}', function (apiUser $user, $conversationId) {
    // Verificación más eficiente con caché
    return Cache::remember("user_{$user->id}_conversation_{$conversationId}_access", 
        now()->addMinutes(5), 
        function () use ($user, $conversationId) {
            return $user->conversations()
                ->where('conversations.id', $conversationId)
                ->exists();
        }
    );
});