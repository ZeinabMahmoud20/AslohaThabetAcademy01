<?php

namespace Database\Seeders;

use App\Models\EvaluationItem;
use Illuminate\Database\Seeder;

class EvaluationItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'الحضور',   'default_max_score' => 10, 'default_order' => 1, 'is_attendance_type' => true],
            ['name' => 'الواجبات', 'default_max_score' => 10, 'default_order' => 2],
            ['name' => 'القرآن الكريم', 'default_max_score' => 20, 'default_order' => 3],
            ['name' => 'الحديث الشريف', 'default_max_score' => 15, 'default_order' => 4],
            ['name' => 'الذكر',    'default_max_score' => 10, 'default_order' => 5],
            ['name' => 'العقيدة',  'default_max_score' => 15, 'default_order' => 6],
            ['name' => 'الالتزام والسلوك', 'default_max_score' => 10, 'default_order' => 7],
        ];

        foreach ($items as $item) {
            EvaluationItem::updateOrCreate(
                ['name' => $item['name']],
                $item + ['is_attendance_type' => $item['is_attendance_type'] ?? false, 'is_active' => true]
            );
        }
    }
}
