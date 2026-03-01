<?php

namespace Database\Seeders;

use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InterviewSeeder extends Seeder
{
    public function run(): void
    {
        $applications = JobApplication::whereIn('status', ['shortlisted', 'interviewed'])->get();
        $interviewers = Employee::where('is_active', true)->take(10)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($applications->isEmpty() || $interviewers->isEmpty()) {
            $this->command->warn('لا توجد طلبات توظيف أو مقابلات!');
            return;
        }

        $types = ['phone', 'video', 'in_person'];
        $statuses = ['scheduled', 'completed', 'cancelled', 'no_show'];
        $ratings = [1, 2, 3, 4, 5, null];

        foreach ($applications as $application) {
            // 50% فرصة لمقابلة
            if (rand(0, 1)) {
                $interviewDate = Carbon::now()->addDays(rand(-30, 30));
                $interviewTime = Carbon::createFromTime(rand(9, 17), rand(0, 59), 0);
                $selectedInterviewers = $interviewers->random(rand(1, 2))->pluck('id')->toArray();

                Interview::create([
                    'job_application_id' => $application->id,
                    'candidate_id' => $application->candidate_id,
                    'job_vacancy_id' => $application->job_vacancy_id,
                    'title' => 'مقابلة توظيف',
                    'interview_date' => $interviewDate,
                    'interview_time' => $interviewTime,
                    'type' => $types[array_rand($types)],
                    'round' => ['first', 'second', 'third', 'final'][array_rand(['first', 'second', 'third', 'final'])],
                    'location' => rand(0, 1) ? 'قاعة المقابلات الرئيسية' : null,
                    'status' => $statuses[array_rand($statuses)],
                    'interviewers' => $selectedInterviewers,
                    'overall_rating' => $ratings[array_rand($ratings)],
                    'interview_notes' => rand(0, 1) ? 'أداء جيد في المقابلة' : null,
                    'scheduled_by' => $createdBy,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء المقابلات بنجاح!');
    }
}
