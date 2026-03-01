<?php

namespace Database\Seeders;

use App\Models\OnboardingProcess;
use App\Models\OnboardingChecklist;
use App\Models\OnboardingTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OnboardingChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $completedBy = $adminUser ? $adminUser->id : 1;

        $processes = OnboardingProcess::all();

        if ($processes->isEmpty()) {
            $this->command->warn('لا توجد عمليات استقبال!');
            return;
        }

        foreach ($processes as $process) {
            // الحصول على المهام من القالب
            $template = $process->template;
            if (!$template) {
                continue;
            }

            $tasks = OnboardingTask::where('template_id', $template->id)
                ->where('is_active', true)
                ->orderBy('task_order')
                ->get();

            if ($tasks->isEmpty()) {
                continue;
            }

            foreach ($tasks as $task) {
                // التحقق من عدم وجود عنصر مسبق
                $existing = OnboardingChecklist::where('process_id', $process->id)
                    ->where('task_id', $task->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                // تحديد الحالة بناءً على حالة العملية
                $status = match($process->status) {
                    'completed' => rand(1, 100) <= 90 ? 'completed' : 'in_progress',
                    'in_progress' => ['pending', 'in_progress', 'completed'][array_rand(['pending', 'in_progress', 'completed'])],
                    default => 'pending',
                };

                $dueDate = $process->start_date 
                    ? Carbon::parse($process->start_date)->addDays(rand(1, 7))
                    : Carbon::now()->addDays(rand(1, 7));

                $completedDate = $status === 'completed' 
                    ? Carbon::parse($dueDate)->addDays(rand(0, 3))
                    : null;

                OnboardingChecklist::create([
                    'process_id' => $process->id,
                    'task_id' => $task->id,
                    'status' => $status,
                    'due_date' => $dueDate,
                    'completed_date' => $completedDate,
                    'completed_by' => $status === 'completed' ? $completedBy : null,
                    'notes' => $status === 'completed' ? 'تم إكمال المهمة' : null,
                    'completion_notes' => $status === 'completed' ? 'تم إكمال المهمة بنجاح' : null,
                ]);
            }
        }

        $totalChecklists = OnboardingChecklist::count();
        $this->command->info("✅ تم إنشاء $totalChecklists عنصر قائمة استقبال");
    }
}
