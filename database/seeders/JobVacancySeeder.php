<?php

namespace Database\Seeders;

use App\Models\JobVacancy;
use App\Models\Department;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JobVacancySeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $currency = Currency::where('code', 'SAR')->first();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($departments->isEmpty() || $positions->isEmpty()) {
            $this->command->warn('لا توجد أقسام أو مناصب!');
            return;
        }

        $vacancies = [
            [
                'title' => 'Senior Software Developer',
                'title_ar' => 'مطور برمجيات أول',
                'code' => 'DEV-001',
                'description' => 'We are looking for an experienced software developer',
                'description_ar' => 'نبحث عن مطور برمجيات ذو خبرة',
                'requirements' => '5+ years experience, PHP, Laravel',
                'responsibilities' => 'Develop and maintain web applications',
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'years_of_experience' => 5,
                'education_level' => 'bachelor',
                'min_salary' => 12000,
                'max_salary' => 18000,
                'status' => 'published',
                'posted_date' => Carbon::now()->subDays(5),
                'closing_date' => Carbon::now()->addDays(30),
                'number_of_positions' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'HR Specialist',
                'title_ar' => 'أخصائي موارد بشرية',
                'code' => 'HR-001',
                'description' => 'Looking for HR specialist',
                'description_ar' => 'نبحث عن أخصائي موارد بشرية',
                'requirements' => '3+ years experience in HR',
                'responsibilities' => 'Manage recruitment and employee relations',
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'years_of_experience' => 3,
                'education_level' => 'bachelor',
                'min_salary' => 8000,
                'max_salary' => 12000,
                'status' => 'published',
                'posted_date' => Carbon::now()->subDays(10),
                'closing_date' => Carbon::now()->addDays(45),
                'number_of_positions' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($vacancies as $vacancy) {
            $vacancy['department_id'] = $departments->random()->id;
            $vacancy['position_id'] = $positions->random()->id;
            $vacancy['branch_id'] = $branches->random()->id;
            $vacancy['currency_id'] = $currency->id;
            $vacancy['created_by'] = $createdBy;

            JobVacancy::firstOrCreate(
                ['code' => $vacancy['code']],
                $vacancy
            );
        }
    }
}
