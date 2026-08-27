<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'min_score', 'max_score', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // BR-031: below 50 is ALWAYS Fail -- the one fixed rule, everything else configurable.
    public static function resolveLabel(float $finalScore): self
    {
        $scale = static::where('is_active', true)
            ->where('min_score', '<=', $finalScore)
            ->where('max_score', '>=', $finalScore)
            ->orderByDesc('min_score')
            ->first();

        if (! $scale) {
            throw new \RuntimeException("No grade_scales band configured for score {$finalScore}");
        }

        return $scale;
    }
}
