<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'telegram_chat_id', 'telegram_username', 'is_verified', 'connected_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'connected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
