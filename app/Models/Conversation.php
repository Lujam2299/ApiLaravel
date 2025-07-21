<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'is_group'];

    public function users()
    {
        return $this->belongsToMany(apiUser::class, 'conversation_user','api_user_id','conversation_id')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    public function latestMessage()
{
    return $this->hasOne(Message::class)->latestOfMany();
}

    
}
