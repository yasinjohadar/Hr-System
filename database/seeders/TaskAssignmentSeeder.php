<?php

namespace Database\Seeders;

use App\Models\TaskAssignment;
use App\Models\Task;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = Task::all();
        $employees = Employee::where('is_active', true)->get();
        $managers = Employee::where('is_active', true)->take(5)->get();

        if ($tasks->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد مهام أو موظفين!');
            return;
        }

        $statuses = ['assigned', 'in_progress', 'completed', 'cancelled'];

        foreach ($tasks as $task) {
            // 1-3 موظفين لكل مهمة
            $assignedEmployees = $employees->random(rand(1, min(3, $employees->count())));
            $assignedBy = $managers->isNotEmpty() ? $managers->random() : $employees->random();

            foreach ($assignedEmployees as $employee) {
                // تجنب التكرار
                $existing = TaskAssignment::where('task_id', $task->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $assignedDate = Carbon::now()->subDays(rand(1, 30));
                $dueDate = $task->due_date ?? $assignedDate->copy()->addDays(rand(7, 21));
                $status = $statuses[array_rand($statuses)];
                $progress = match($status) {
                    'completed' => 100,
                    'in_progress' => rand(20, 90),
                    'on_hold' => rand(0, 50),
                    default => 0,
                };

                TaskAssignment::create([
                    'task_id' => $task->id,
                    'employee_id' => $employee->id,
                    'assigned_by' => $assignedBy->id,
                    'assigned_date' => $assignedDate,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'progress' => $progress,
                    'notes' => rand(0, 1) ? 'ملاحظات على المهمة' : null,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء تعيينات المهام بنجاح!');
    }
}
