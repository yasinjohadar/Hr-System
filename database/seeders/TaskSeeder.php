<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $employees = Employee::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($projects->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد مشاريع أو موظفين!');
            return;
        }

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'];
        $taskTitles = [
            'تحليل المتطلبات', 'تصميم الواجهة', 'تطوير الوظائف', 'اختبار النظام',
            'مراجعة الكود', 'توثيق المشروع', 'اجتماع مع العميل', 'تحديث قاعدة البيانات'
        ];

        foreach ($projects as $project) {
            // 5-15 مهمة لكل مشروع
            $numTasks = rand(5, 15);

            for ($i = 0; $i < $numTasks; $i++) {
                $startDate = Carbon::now()->subDays(rand(0, 60));
                $dueDate = $startDate->copy()->addDays(rand(3, 30));
                $status = $statuses[array_rand($statuses)];
                $completedDate = $status === 'completed' ? $dueDate->copy()->subDays(rand(0, 5)) : null;

                Task::create([
                    'project_id' => $project->id,
                    'title' => $taskTitles[array_rand($taskTitles)],
                    'title_ar' => $taskTitles[array_rand($taskTitles)],
                    'description' => 'وصف المهمة',
                    'description_ar' => 'وصف المهمة بالعربية',
                    'priority' => $priorities[array_rand($priorities)],
                    'status' => $status,
                    'start_date' => $startDate,
                    'due_date' => $dueDate,
                    'completed_date' => $completedDate,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء المهام بنجاح!');
    }
}
