<?php

namespace Database\Seeders;

use App\Models\AttendanceRule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttendanceRuleSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $rules = [
            // قواعد التأخير
            [
                'name' => 'Late Arrival - Minor',
                'name_ar' => 'تأخير بسيط',
                'rule_type' => 'late',
                'threshold_minutes' => 15,
                'action_type' => 'warning',
                'deduction_amount' => null,
                'deduction_percentage' => null,
                'apply_to_all' => true,
                'send_notification' => true,
                'notification_delay_minutes' => 0,
                'is_active' => true,
                'priority' => 1,
                'description' => 'تأخير أقل من 15 دقيقة - تحذير فقط',
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Late Arrival - Moderate',
                'name_ar' => 'تأخير متوسط',
                'rule_type' => 'late',
                'threshold_minutes' => 30,
                'action_type' => 'deduction',
                'deduction_amount' => 50.00,
                'deduction_percentage' => null,
                'apply_to_all' => true,
                'send_notification' => true,
                'notification_delay_minutes' => 0,
                'is_active' => true,
                'priority' => 2,
                'description' => 'تأخير من 15 إلى 30 دقيقة - خصم 50 ريال',
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Late Arrival - Severe',
                'name_ar' => 'تأخير شديد',
                'rule_type' => 'late',
                'threshold_minutes' => 60,
                'action_type' => 'deduction',
                'deduction_amount' => null,
                'deduction_percentage' => 5,
                'apply_to_all' => true,
                'send_notification' => true,
                'notification_delay_minutes' => 0,
                'is_active' => true,
                'priority' => 3,
                'description' => 'تأخير أكثر من 60 دقيقة - خصم 5% من الراتب اليومي',
                'created_by' => $createdBy,
            ],

            // قواعد الغياب
            [
                'name' => 'Absence Without Notice',
                'name_ar' => 'غياب بدون إشعار',
                'rule_type' => 'absent',
                'threshold_minutes' => 0,
                'action_type' => 'deduction',
                'deduction_amount' => null,
                'deduction_percentage' => 100,
                'apply_to_all' => true,
                'send_notification' => true,
                'notification_delay_minutes' => 60,
                'is_active' => true,
                'priority' => 10,
                'description' => 'غياب بدون إشعار - خصم الراتب اليومي كاملاً',
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Absence With Notice',
                'name_ar' => 'غياب بإشعار',
                'rule_type' => 'absent',
                'threshold_minutes' => 0,
                'action_type' => 'notification',
                'deduction_amount' => null,
                'deduction_percentage' => null,
                'apply_to_all' => true,
                'send_notification' => true,
                'notification_delay_minutes' => 0,
                'is_active' => true,
                'priority' => 5,
                'description' => 'غياب بإشعار - إشعار فقط',
                'created_by' => $createdBy,
            ],

            // قواعد الانصراف المبكر
            [
                'name' => 'Early Leave',
                'name_ar' => 'انصراف مبكر',
                'rule_type' => 'early_leave',
                'threshold_minutes' => 30,
                'action_type' => 'deduction',
                'deduction_amount' => 30.00,
                'deduction_percentage' => null,
                'apply_to_all' => true,
                'send_notification' => true,
                'notification_delay_minutes' => 0,
                'is_active' => true,
                'priority' => 4,
                'description' => 'انصراف مبكر أكثر من 30 دقيقة - خصم 30 ريال',
                'created_by' => $createdBy,
            ],

            // قواعد الساعات الإضافية
            [
                'name' => 'Overtime Approval Required',
                'name_ar' => 'الساعات الإضافية تتطلب موافقة',
                'rule_type' => 'overtime',
                'threshold_minutes' => 60,
                'action_type' => 'notification',
                'deduction_amount' => null,
                'deduction_percentage' => null,
                'apply_to_all' => true,
                'send_notification' => true,
                'notification_delay_minutes' => 0,
                'is_active' => true,
                'priority' => 6,
                'description' => 'الساعات الإضافية أكثر من ساعة تتطلب موافقة',
                'created_by' => $createdBy,
            ],
        ];

        foreach ($rules as $rule) {
            AttendanceRule::firstOrCreate(
                ['rule_code' => 'AR-' . strtoupper(Str::random(8))],
                $rule
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($rules) . ' قاعدة حضور');
    }
}
