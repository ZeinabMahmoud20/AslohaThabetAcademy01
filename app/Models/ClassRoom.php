<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Named ClassRoom (not "Class") because "class" is a reserved PHP keyword.
// Maps to the `classes` table.
class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['level_id', 'name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(ClassTeacherAssignment::class, 'class_id');
    }

    // The ONLY correct way to ask "who currently teaches this class" -- never a column.
    public function activeTeacherAssignment(): HasOne
    {
        return $this->hasOne(ClassTeacherAssignment::class, 'class_id')->where('is_active', true);
    }

    public function supervisorAssignments(): HasMany
    {
        return $this->hasMany(ClassSupervisorAssignment::class, 'class_id');
    }

    public function activeSupervisorAssignments(): HasMany
    {
        return $this->hasMany(ClassSupervisorAssignment::class, 'class_id')->where('is_active', true);
    }

    public function studentClassAssignments(): HasMany
    {
        return $this->hasMany(StudentClassAssignment::class, 'class_id');
    }
}
