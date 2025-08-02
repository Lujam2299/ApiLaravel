<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $authorized = $user->conversations->contains($conversationId);
    Log::info("Autenticación canal conversación", [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'authorized' => $authorized,
        'conversations' => $user->conversations->pluck('id')
    ]);
    return $authorized ? $user->id : false;
});
Broadcast::channel('conversation.{id}', function ($user, $id) {
    Log::info("Autenticando canal conversation.{$id} para usuario {$user->id}");
    
    $conversation = \App\Models\Conversation::find($id);
    if (!$conversation) return false;

    $result = $conversation->users()->where('users.id', $user->id)->exists();
    Log::info("Acceso al canal: " . ($result ? 'permitido' : 'denegado'));
    return $result;
});