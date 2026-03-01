<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $violationTypes = [
            [
                'name' => 'Late Arrival',
                'name_ar' => 'التأخر عن العمل',
                'code' => 'LATE_ARRIVAL',
                'description' => 'التأخر عن موعد الحضور',
                'severity_level' => 1,
                'requires_warning' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Early Departure',
                'name_ar' => 'الانصراف المبكر',
                'code' => 'EARLY_DEPARTURE',
                'description' => 'الانصراف قبل موعد الانتهاء',
                'severity_level' => 1,
                'requires_warning' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Absence Without Permission',
                'name_ar' => 'الغياب بدون إذن',
                'code' => 'ABSENCE_NO_PERM',
                'description' => 'عدم الحضور بدون إذن',
                'severity_level' => 3,
                'requires_warning' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Misconduct',
                'name_ar' => 'سوء السلوك',
                'code' => 'MISCONDUCT',
                'description' => 'سلوك غير لائق في مكان العمل',
                'severity_level' => 5,
                'requires_warning' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Violation of Company Policy',
                'name_ar' => 'مخالفة سياسة الشركة',
                'code' => 'POLICY_VIOLATION',
                'description' => 'عدم الالتزام بسياسات الشركة',
                'severity_level' => 3,
                'requires_warning' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Poor Performance',
                'name_ar' => 'ضعف الأداء',
                'code' => 'POOR_PERFORMANCE',
                'description' => 'عدم تحقيق الأهداف المطلوبة',
                'severity_level' => 2,
                'requires_warning' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($violationTypes as $type) {
            ViolationType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }

        $this->command->info('✅ تم إنشاء أنواع المخالفات بنجاح!');
    }
}
