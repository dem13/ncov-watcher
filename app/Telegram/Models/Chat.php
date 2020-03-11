<?php

namespace App\Telegram\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $incrementing = false;

    protected $fillable = ['type', 'title', 'username', 'first_name', 'last_name', 'description', 'invite_link', 'photo', 'permissions'];

    protected $casts = [
        'photo' => 'array',
        'permissions' => 'array',
    ];
}
