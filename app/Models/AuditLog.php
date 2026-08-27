<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'entity_type', 'entity_id', 'old_value', 'new_value',
        'ip_address', 'created_at',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper so business code never hand-rolls audit rows inconsistently.
    public static function record(
        int $userId,
        string $action,
        string $entityType,
        int $entityId,
        ?array $old = null,
        ?array $new = null,
        ?string $ip = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_value' => $old,
            'new_value' => $new,
            'ip_address' => $ip,
            'created_at' => now(),
        ]);
    }
}
