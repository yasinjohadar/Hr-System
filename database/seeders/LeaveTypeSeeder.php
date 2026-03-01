<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'name_ar' => 'إجازة سنوية',
                'code' => 'ANNUAL',
                'max_days' => 30,
                'is_paid' => true,
                'requires_approval' => true,
                'carry_forward' => true,
                'description' => 'إجازة سنوية مدفوعة',
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'name_ar' => 'إجازة مرضية',
                'code' => 'SICK',
                'max_days' => 30,
                'is_paid' => true,
                'requires_approval' => true,
                'carry_forward' => false,
                'description' => 'إجازة مرضية مدفوعة',
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Leave',
                'name_ar' => 'إجازة طارئة',
                'code' => 'EMERGENCY',
                'max_days' => 5,
                'is_paid' => true,
                'requires_approval' => true,
                'carry_forward' => false,
                'description' => 'إجازة طارئة',
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'name_ar' => 'إجازة أمومة',
                'code' => 'MATERNITY',
                'max_days' => 90,
                'is_paid' => true,
                'requires_approval' => true,
                'carry_forward' => false,
                'description' => 'إجازة أمومة',
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'name_ar' => 'إجازة أبوة',
                'code' => 'PATERNITY',
                'max_days' => 5,
                'is_paid' => true,
                'requires_approval' => true,
                'carry_forward' => false,
                'description' => 'إجازة أبوة',
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'name_ar' => 'إجازة غير مدفوعة',
                'code' => 'UNPAID',
                'max_days' => 30,
                'is_paid' => false,
                'requires_approval' => true,
                'carry_forward' => false,
                'description' => 'إجازة غير مدفوعة',
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
