<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            'المستوى الأول',
            'المستوى الثاني',
            'المستوى الثالث',
            'المستوى الرابع',
            'المستوى الخامس',
            'المستوى السادس',
        ];

        foreach ($levels as $index => $name) {
            Level::updateOrCreate(
                ['order_index' => $index + 1],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
