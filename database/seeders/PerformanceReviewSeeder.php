<?php

namespace Database\Seeders;

use App\Models\PerformanceReview;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PerformanceReviewSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->take(20)->get();
        $reviewers = Employee::where('is_active', true)->whereHas('subordinates')->get();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty()) {
            $this->command->warn('لا توجد موظفين!');
            return;
        }

        if ($reviewers->isEmpty()) {
            $reviewers = $employees->take(3);
        }

        foreach ($employees as $employee) {
            $reviewer = $reviewers->random();
            $reviewDate = Carbon::now()->subMonths(rand(1, 6));
            $periodStart = $reviewDate->copy()->subMonths(6);
            $periodEnd = $reviewDate->copy()->subDay();
            
            $year = $reviewDate->year;
            $quarter = ceil($reviewDate->month / 3);
            $reviewPeriod = "Q{$quarter} {$year}";

            $ratings = [];
            for ($i = 0; $i < 8; $i++) {
                $ratings[] = rand(3, 5);
            }
            
            $overallRating = round(array_sum($ratings) / count($ratings), 2);

            PerformanceReview::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'review_period' => $reviewPeriod,
                    'period_start_date' => $periodStart,
                    'period_end_date' => $periodEnd,
                ],
                [
                    'reviewer_id' => $reviewer->id,
                    'review_date' => $reviewDate,
                    'job_knowledge' => $ratings[0],
                    'work_quality' => $ratings[1],
                    'productivity' => $ratings[2],
                    'communication' => $ratings[3],
                    'teamwork' => $ratings[4],
                    'initiative' => $ratings[5],
                    'problem_solving' => $ratings[6],
                    'attendance_punctuality' => $ratings[7],
                    'overall_rating' => $overallRating,
                    'strengths' => 'أداء ممتاز في العمل الجماعي والتواصل',
                    'weaknesses' => 'يحتاج لتحسين في إدارة الوقت',
                    'goals_achieved' => 'تحسين الإنتاجية بنسبة 20%',
                    'future_goals' => 'تحسين المهارات القيادية',
                    'status' => rand(0, 1) ? 'approved' : 'draft',
                    'comments' => 'موظف متميز يحتاج لمتابعة',
                    'created_by' => $createdBy,
                ]
            );
        }
    }
}
