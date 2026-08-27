<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LevelSeeder::class,          // 6 مستويات ثابتة -- لازم قبل أي حاجة تانية
            GradeScaleSeeder::class,     // سلم الدرجات الافتراضي
            EvaluationItemSeeder::class, // كتالوج عناصر التقييم
            AdminUserSeeder::class,      // مستخدم admin@academy.test / password
        ]);
    }
}
