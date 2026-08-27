<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

// Extends Authenticatable (not the base Model) so Guardian can be used as the
// user provider for a SEPARATE auth guard ("guardian"), fully independent
// from the staff `users` table / `web` guard.
class Guardian extends Authenticatable
{
    use HasFactory;

protected $fillable = [
    'full_name', 'phone_number', 'contact_methods', 'relationship_to_student', 'country',
    'access_code', 'access_code_generated_at',
];

    protected $hidden = ['access_code'];

    protected $casts = [
        'contact_methods' => 'array',
        'access_code_generated_at' => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    // Auth::attempt(['phone_number' => .., 'password' => $code], 'guardian')
    // compares $code against THIS column instead of a `password` column.
    public function getAuthPassword(): string
    {
        return $this->access_code;
    }

    // Guardians log in with their phone number, not an email/username column.
    public function getAuthIdentifierName(): string
    {
        return 'phone_number';
    }
}