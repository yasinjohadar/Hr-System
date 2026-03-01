<?php

namespace Database\Seeders;

use App\Models\SuccessionPlan;
use App\Models\SuccessionCandidate;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuccessionCandidateSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $plans = SuccessionPlan::all();
        $employees = Employee::where('is_active', true)->get();

        if ($plans->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد خطط تعاقب أو موظفين!');
            return;
        }

        $readinessLevels = ['ready_now', 'ready_1_year', 'ready_2_years', 'ready_3_years', 'not_ready'];
        $statuses = ['potential', 'identified', 'developing', 'ready', 'selected', 'rejected'];

        foreach ($plans as $plan) {
            // 2-5 مرشحين لكل خطة
            $numCandidates = rand(2, min(5, $employees->count()));
            $candidates = $employees->random($numCandidates);

            foreach ($candidates as $candidate) {
                // تجنب التكرار
                $existing = SuccessionCandidate::where('succession_plan_id', $plan->id)
                    ->where('employee_id', $candidate->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $readinessLevel = $readinessLevels[array_rand($readinessLevels)];
                $readinessScore = match($readinessLevel) {
                    'ready_now' => rand(80, 100),
                    'ready_1_year' => rand(60, 79),
                    'ready_2_years' => rand(40, 59),
                    'ready_3_years' => rand(20, 39),
                    'not_ready' => rand(0, 19),
                    default => rand(0, 100),
                };

                $status = $statuses[array_rand($statuses)];

                SuccessionCandidate::create([
                    'succession_plan_id' => $plan->id,
                    'employee_id' => $candidate->id,
                    'readiness_level' => $readinessLevel,
                    'readiness_score' => $readinessScore,
                    'strengths' => 'نقاط القوة: الخبرة، المهارات القيادية، المعرفة التقنية',
                    'development_needs' => 'احتياجات التطوير: دورات تدريبية في الإدارة',
                    'action_plan' => 'خطة العمل: حضور برنامج قيادي، تدريب عملي',
                    'status' => $status,
                    'notes' => 'مرشح محتمل للتعاقب',
                    'created_by' => $createdBy,
                ]);
            }
        }

        $totalCandidates = SuccessionCandidate::count();
        $this->command->info("✅ تم إنشاء $totalCandidates مرشح تعاقب");
    }
}
