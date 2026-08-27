<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Named AppNotification (not "Notification") to avoid clashing with
// Illuminate\Notifications\Notification. Maps to the `notifications` table.
class AppNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    public $timestamps = false;

    protected $fillable = [
        'recipient_user_id', 'event_type', 'title', 'body',
        'related_entity_type', 'related_entity_id', 'is_read', 'created_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}
