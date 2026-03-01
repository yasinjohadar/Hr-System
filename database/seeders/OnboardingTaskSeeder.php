<?php

namespace Database\Seeders;

use App\Models\OnboardingTemplate;
use App\Models\OnboardingTask;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class OnboardingTaskSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $templates = OnboardingTemplate::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();

        if ($templates->isEmpty()) {
            $this->command->warn('لا توجد قوالب استقبال!');
            return;
        }

        $taskTypes = ['document', 'training', 'meeting', 'system_access', 'other'];
        $roles = ['hr', 'it', 'manager', 'admin'];

        $standardTasks = [
            [
                'title' => 'Complete Employment Documents',
                'title_ar' => 'إكمال مستندات التوظيف',
                'description' => 'تقديم جميع المستندات المطلوبة',
                'task_type' => 'document',
                'is_required' => true,
                'estimated_duration_minutes' => 60,
                'instructions' => 'يرجى تقديم نسخ من الهوية، الشهادة، والعقد',
                'assigned_to_role' => 'hr',
            ],
            [
                'title' => 'IT Account Setup',
                'title_ar' => 'إعداد حساب IT',
                'description' => 'إنشاء حساب البريد الإلكتروني والأنظمة',
                'task_type' => 'system_access',
                'is_required' => true,
                'estimated_duration_minutes' => 30,
                'instructions' => 'سيتم إنشاء الحساب تلقائياً',
                'assigned_to_role' => 'it',
            ],
            [
                'title' => 'Orientation Meeting',
                'title_ar' => 'اجتماع التعريف',
                'description' => 'اجتماع تعريف بالشركة والسياسات',
                'task_type' => 'meeting',
                'is_required' => true,
                'estimated_duration_minutes' => 120,
                'instructions' => 'حضور اجتماع التعريف في اليوم الأول',
                'assigned_to_role' => 'hr',
            ],
            [
                'title' => 'Safety Training',
                'title_ar' => 'تدريب السلامة',
                'description' => 'دورة تدريبية على السلامة المهنية',
                'task_type' => 'training',
                'is_required' => true,
                'estimated_duration_minutes' => 180,
                'instructions' => 'إكمال دورة السلامة عبر المنصة',
                'assigned_to_role' => 'hr',
            ],
            [
                'title' => 'Equipment Assignment',
                'title_ar' => 'تسليم المعدات',
                'description' => 'استلام اللابتوب والهاتف',
                'task_type' => 'other',
                'is_required' => false,
                'estimated_duration_minutes' => 15,
                'instructions' => 'استلام المعدات من قسم IT',
                'assigned_to_role' => 'it',
            ],
            [
                'title' => 'Manager Introduction',
                'title_ar' => 'مقابلة المدير',
                'description' => 'مقابلة مع المدير المباشر',
                'task_type' => 'meeting',
                'is_required' => true,
                'estimated_duration_minutes' => 60,
                'instructions' => 'ترتيب موعد مع المدير المباشر',
                'assigned_to_role' => 'manager',
            ],
        ];

        foreach ($templates as $template) {
            $taskOrder = 1;

            foreach ($standardTasks as $taskData) {
                OnboardingTask::firstOrCreate(
                    [
                        'template_id' => $template->id,
                        'title' => $taskData['title'],
                    ],
                    array_merge($taskData, [
                        'task_order' => $taskOrder++,
                        'assigned_to_employee' => rand(1, 100) <= 30 ? $employees->random()->id : null,
                        'is_active' => true,
                        'created_by' => $createdBy,
                    ])
                );
            }
        }

        $totalTasks = OnboardingTask::count();
        $this->command->info("✅ تم إنشاء $totalTasks مهمة استقبال");
    }
}
