<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Candidate;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $vacancies = JobVacancy::where('status', 'published')->get();
        $candidates = Candidate::all();
        $currency = Currency::where('code', 'SAR')->first();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($vacancies->isEmpty() || $candidates->isEmpty()) {
            $this->command->warn('لا توجد وظائف شاغرة أو مرشحين!');
            return;
        }

        $statuses = ['pending', 'reviewing', 'shortlisted', 'interviewed', 'offered', 'accepted', 'rejected', 'withdrawn'];

        foreach ($vacancies as $vacancy) {
            // 3-8 طلبات لكل وظيفة
            $selectedCandidates = $candidates->random(rand(3, min(8, $candidates->count())));

            foreach ($selectedCandidates as $candidate) {
                // تجنب التكرار
                $existing = JobApplication::where('job_vacancy_id', $vacancy->id)
                    ->where('candidate_id', $candidate->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $expectedSalary = $vacancy->salary_min 
                    ? rand($vacancy->salary_min, $vacancy->salary_max ?? $vacancy->salary_min * 2)
                    : null;

                JobApplication::create([
                    'job_vacancy_id' => $vacancy->id,
                    'candidate_id' => $candidate->id,
                    'application_date' => Carbon::now()->subDays(rand(1, 90)),
                    'status' => $status,
                    'source' => ['website', 'linkedin', 'referral', 'indeed', 'other'][array_rand(['website', 'linkedin', 'referral', 'indeed', 'other'])],
                    'cover_letter_path' => rand(0, 1) ? 'documents/covers/cover_' . uniqid() . '.pdf' : null,
                    'cv_path' => 'documents/cvs/cv_' . uniqid() . '.pdf',
                    'expected_salary' => $expectedSalary,
                    'available_start_date' => Carbon::now()->addDays(rand(7, 30)),
                    'notes' => rand(0, 1) ? 'ملاحظات على الطلب' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء طلبات التوظيف بنجاح!');
    }
}
