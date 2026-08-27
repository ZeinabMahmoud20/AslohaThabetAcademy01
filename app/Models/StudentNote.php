<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentNote extends Model
{
    public $timestamps = false;

    protected $fillable = ['level_attempt_id', 'author_id', 'note', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function levelAttempt(): BelongsTo
    {
        return $this->belongsTo(LevelAttempt::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
