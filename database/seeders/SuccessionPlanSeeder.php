<?php

namespace Database\Seeders;

use App\Models\SuccessionPlan;
use App\Models\SuccessionCandidate;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SuccessionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $managers = Employee::where('is_active', true)->take(5)->get();
        $positions = Position::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($managers->isEmpty() || $positions->isEmpty()) {
            $this->command->warn('لا توجد مديرين أو مناصب!');
            return;
        }

        $statuses = ['planning', 'in_progress', 'completed', 'cancelled'];
        $readinessLevels = ['ready_now', 'ready_1_year', 'ready_2_years', 'ready_3_years', 'not_ready'];

        // 5-10 خطط تعاقب
        $selectedManagers = $managers->random(rand(5, min(10, $managers->count())));

        foreach ($selectedManagers as $manager) {
            $position = $positions->random();
            $planDate = Carbon::now()->subMonths(rand(0, 6));
            $status = $statuses[array_rand($statuses)];

            $plan = SuccessionPlan::create([
                'current_employee_id' => $manager->id,
                'position_id' => $position->id,
                'target_date' => $planDate,
                'status' => $status,
                'urgency' => ['low', 'medium', 'high', 'critical'][array_rand(['low', 'medium', 'high', 'critical'])],
                'description' => 'خطة تعاقب للمنصب',
                'notes' => rand(0, 1) ? 'ملاحظات إضافية' : null,
                'created_by' => $createdBy,
            ]);

            // 2-4 مرشحين لكل خطة
            $candidates = $employees->where('id', '!=', $manager->id)->random(rand(2, min(4, $employees->count() - 1)));

            foreach ($candidates as $candidate) {
                SuccessionCandidate::create([
                    'succession_plan_id' => $plan->id,
                    'employee_id' => $candidate->id,
                    'readiness_level' => $readinessLevels[array_rand($readinessLevels)],
                    'readiness_score' => rand(0, 100),
                    'strengths' => 'نقاط القوة: الخبرة، المهارات القيادية',
                    'development_needs' => rand(0, 1) ? 'احتياجات التطوير: دورات تدريبية' : null,
                    'action_plan' => rand(0, 1) ? 'خطة تطوير للمرشح' : null,
                    'status' => ['potential', 'identified', 'developing', 'ready'][array_rand(['potential', 'identified', 'developing', 'ready'])],
                    'notes' => rand(0, 1) ? 'ملاحظات على المرشح' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء خطط التعاقب بنجاح!');
    }
}
