<?php

namespace Database\Seeders;

use App\Models\DisciplinaryAction;
use Illuminate\Database\Seeder;

class DisciplinaryActionSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $actions = [
            [
                'name' => 'Verbal Warning',
                'name_ar' => 'إنذار شفهي',
                'code' => 'VERBAL_WARNING',
                'action_type' => 'verbal_warning',
                'description' => 'إنذار شفهي للموظف',
                'severity_level' => 1,
                'deduction_amount' => 0,
                'suspension_days' => 0,
                'requires_approval' => false,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Written Warning',
                'name_ar' => 'إنذار كتابي',
                'code' => 'WRITTEN_WARNING',
                'action_type' => 'written_warning',
                'description' => 'إنذار كتابي للموظف',
                'severity_level' => 2,
                'deduction_amount' => 0,
                'suspension_days' => 0,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Salary Deduction',
                'name_ar' => 'خصم من الراتب',
                'code' => 'SALARY_DEDUCTION',
                'action_type' => 'deduction',
                'description' => 'خصم مبلغ من الراتب',
                'severity_level' => 3,
                'deduction_amount' => 500,
                'suspension_days' => 0,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Suspension',
                'name_ar' => 'تعليق',
                'code' => 'SUSPENSION',
                'action_type' => 'suspension',
                'description' => 'تعليق الموظف عن العمل',
                'severity_level' => 4,
                'deduction_amount' => 0,
                'suspension_days' => 3,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Final Warning',
                'name_ar' => 'إنذار نهائي',
                'code' => 'FINAL_WARNING',
                'action_type' => 'final_warning',
                'description' => 'إنذار نهائي قبل الفصل',
                'severity_level' => 4,
                'deduction_amount' => 0,
                'suspension_days' => 0,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($actions as $action) {
            DisciplinaryAction::firstOrCreate(
                ['code' => $action['code']],
                $action
            );
        }

        $this->command->info('✅ تم إنشاء الإجراءات التأديبية بنجاح!');
    }
}
