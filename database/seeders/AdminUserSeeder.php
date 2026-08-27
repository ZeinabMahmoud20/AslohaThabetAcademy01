<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@academy.test'],
            [
                'full_name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'role_type' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
