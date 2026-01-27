<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'nip_sender',
        'nama_sender',
        'message',
        'is_from_bot',
        'is_read',
        'source',
    ];

    protected $casts = [
        'is_from_bot' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
