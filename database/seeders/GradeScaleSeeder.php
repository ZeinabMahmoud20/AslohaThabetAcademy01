<?php

namespace Database\Seeders;

use App\Models\GradeScale;
use Illuminate\Database\Seeder;

class GradeScaleSeeder extends Seeder
{
    public function run(): void
    {
        // Default bands per Section O / BR-031. Editable later by Administration --
        // never hardcode these thresholds anywhere in application code.
        $scales = [
            ['label' => 'ممتاز',   'min_score' => 90, 'max_score' => 100],
            ['label' => 'جيد جداً', 'min_score' => 75, 'max_score' => 89.99],
            ['label' => 'جيد',     'min_score' => 65, 'max_score' => 74.99],
            ['label' => 'مقبول',   'min_score' => 50, 'max_score' => 64.99],
            ['label' => 'راسب',    'min_score' => 0,  'max_score' => 49.99],
        ];

        foreach ($scales as $scale) {
            GradeScale::updateOrCreate(
                ['label' => $scale['label']],
                $scale + ['is_active' => true]
            );
        }
    }
}
