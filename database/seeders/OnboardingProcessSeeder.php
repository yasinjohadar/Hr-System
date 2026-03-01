<?php

namespace Database\Seeders;

use App\Models\OnboardingProcess;
use App\Models\OnboardingTemplate;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OnboardingProcessSeeder extends Seeder
{
    public function run(): void
    {
        $templates = OnboardingTemplate::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)
            ->whereDate('hire_date', '>=', Carbon::now()->subMonths(3))
            ->get();
        $coordinators = Employee::where('is_active', true)->take(5)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($templates->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد قوالب استقبال أو موظفين جدد!');
            return;
        }

        if ($coordinators->isEmpty()) {
            $coordinators = $employees->take(3);
        }

        $statuses = ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'];
        $count = 0;

        foreach ($employees->take(10) as $employee) {
            $template = $templates->random();
            $startDate = $employee->hire_date ?? Carbon::now()->subDays(rand(1, 90));
            $expectedCompletion = $startDate->copy()->addDays(30);
            
            $process = OnboardingProcess::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                ],
                [
                    'template_id' => $template->id,
                    'start_date' => $startDate,
                    'expected_completion_date' => $expectedCompletion,
                    'status' => $statuses[array_rand($statuses)],
                    'completion_percentage' => rand(0, 100),
                    'assigned_to' => $coordinators->random()->id,
                    'created_by' => $createdBy,
                ]
            );

            if ($process->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->command->info("✅ تم إنشاء $count عملية استقبال بنجاح!");
    }
}
