<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return [
        'id' => $user->id,
        'name' => $user->name
    ];
});