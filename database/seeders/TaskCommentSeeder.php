<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskCommentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $tasks = Task::all();
        $employees = Employee::where('is_active', true)->get();

        if ($tasks->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد مهام أو موظفين!');
            return;
        }

        $comments = [
            'تم إكمال المهمة بنجاح',
            'يحتاج إلى مراجعة',
            'في انتظار الموافقة',
            'تم التعديل حسب الملاحظات',
            'ممتاز، عمل رائع',
            'يحتاج إلى تحسينات',
            'تم حل المشكلة',
            'في التقدم',
            'مكتمل',
            'يحتاج إلى معلومات إضافية',
        ];

        foreach ($tasks as $task) {
            // 2-8 تعليقات لكل مهمة
            $numComments = rand(2, 8);

            for ($i = 0; $i < $numComments; $i++) {
                $commenter = $employees->random();
                $isInternal = rand(1, 100) <= 20; // 20% تعليقات داخلية

                TaskComment::create([
                    'task_id' => $task->id,
                    'employee_id' => $commenter->id,
                    'user_id' => $commenter->user_id ?? $createdBy,
                    'comment' => $comments[array_rand($comments)] . ' - ' . ($i + 1),
                    'is_internal' => $isInternal,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $totalComments = TaskComment::count();
        $this->command->info("✅ تم إنشاء $totalComments تعليق مهمة");
    }
}
