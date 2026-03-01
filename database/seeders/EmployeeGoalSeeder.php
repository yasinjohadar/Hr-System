<?php

namespace Database\Seeders;

use App\Models\EmployeeGoal;
use App\Models\Employee;
use App\Models\PerformanceReview;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeGoalSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $reviews = PerformanceReview::all();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty()) {
            $this->command->warn('لا توجد موظفين!');
            return;
        }

        $goals = [
            'زيادة الإنتاجية بنسبة 20%', 'تحسين مهارات التواصل', 'إكمال مشروع رئيسي',
            'الحصول على شهادة جديدة', 'تحسين رضا العملاء', 'زيادة المبيعات',
            'تطوير مهارات القيادة', 'إكمال التدريب المطلوب', 'تحسين الجودة',
            'تقليل التكاليف', 'زيادة الكفاءة', 'تحسين الأداء'
        ];

        $statuses = ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'];

        foreach ($employees as $employee) {
            // 3-6 أهداف لكل موظف
            $numGoals = rand(3, 6);

            for ($i = 0; $i < $numGoals; $i++) {
                $startDate = Carbon::now()->subMonths(rand(0, 6));
                $targetDate = $startDate->copy()->addMonths(rand(3, 12));
                $status = $statuses[array_rand($statuses)];
                $progressPercentage = match($status) {
                    'completed' => 100,
                    'in_progress' => rand(20, 90),
                    'on_hold' => rand(0, 50),
                    default => 0,
                };
                $completionDate = $status === 'completed' ? $targetDate->copy()->subDays(rand(0, 30)) : null;

                $review = $reviews->where('employee_id', $employee->id)->first();

                EmployeeGoal::create([
                    'employee_id' => $employee->id,
                    'title' => $goals[array_rand($goals)],
                    'description' => 'هدف تطويري للموظف',
                    'type' => ['personal', 'team', 'department', 'company'][array_rand(['personal', 'team', 'department', 'company'])],
                    'priority' => ['low', 'medium', 'high', 'critical'][array_rand(['low', 'medium', 'high', 'critical'])],
                    'start_date' => $startDate,
                    'target_date' => $targetDate,
                    'completion_date' => $completionDate,
                    'status' => $status,
                    'progress_percentage' => $progressPercentage,
                    'performance_review_id' => $review ? $review->id : null,
                    'notes' => rand(0, 1) ? 'ملاحظات على الهدف' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء أهداف الموظفين بنجاح!');
    }
}
