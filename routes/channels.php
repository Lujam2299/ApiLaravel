<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\apiUser;
use App\Models\Conversation;

Broadcast::channel('conversation.{conversationId}', function (apiUser $user, $conversationId) {
    return $user->conversations()->where('conversations.id', $conversationId)->exists();
});
