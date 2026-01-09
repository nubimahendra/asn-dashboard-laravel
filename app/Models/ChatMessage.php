<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_handled_by_bot' => 'boolean',
        'is_read' => 'boolean',
    ];
}
