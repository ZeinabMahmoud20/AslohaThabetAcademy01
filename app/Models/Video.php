<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_plan_id', 'title', 'description', 'video_url', 'file_path',
        'duration_seconds', 'publish_date', 'status',
    ];

    protected $casts = ['publish_date' => 'date'];

    public function weeklyPlan(): BelongsTo
    {
        return $this->belongsTo(WeeklyPlan::class);
    }
}
