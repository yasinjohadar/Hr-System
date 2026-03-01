<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $templates = [
            [
                'name' => 'Welcome Email',
                'name_ar' => 'بريد ترحيبي',
                'code' => 'WELCOME',
                'subject' => 'Welcome to Our Company',
                'subject_ar' => 'مرحباً بك في شركتنا',
                'body' => '<p>Dear {{employee_name}},</p><p>Welcome to our company! We are excited to have you on board.</p>',
                'body_ar' => '<p>عزيزي {{employee_name}}،</p><p>مرحباً بك في شركتنا! نحن متحمسون لانضمامك إلينا.</p>',
                'type' => 'welcome',
                'variables' => ['employee_name', 'company_name', 'start_date'],
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Leave Approved',
                'name_ar' => 'موافقة إجازة',
                'code' => 'LEAVE_APPROVED',
                'subject' => 'Your Leave Request Has Been Approved',
                'subject_ar' => 'تمت الموافقة على طلب إجازتك',
                'body' => '<p>Dear {{employee_name}},</p><p>Your leave request from {{start_date}} to {{end_date}} has been approved.</p>',
                'body_ar' => '<p>عزيزي {{employee_name}}،</p><p>تمت الموافقة على طلب إجازتك من {{start_date}} إلى {{end_date}}.</p>',
                'type' => 'leave_approved',
                'variables' => ['employee_name', 'start_date', 'end_date', 'leave_type'],
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Leave Rejected',
                'name_ar' => 'رفض إجازة',
                'code' => 'LEAVE_REJECTED',
                'subject' => 'Your Leave Request Has Been Rejected',
                'subject_ar' => 'تم رفض طلب إجازتك',
                'body' => '<p>Dear {{employee_name}},</p><p>Unfortunately, your leave request has been rejected. Reason: {{rejection_reason}}</p>',
                'body_ar' => '<p>عزيزي {{employee_name}}،</p><p>للأسف، تم رفض طلب إجازتك. السبب: {{rejection_reason}}</p>',
                'type' => 'leave_rejected',
                'variables' => ['employee_name', 'rejection_reason'],
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }
}
