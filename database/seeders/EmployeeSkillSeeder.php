<?php

namespace Database\Seeders;

use App\Models\EmployeeSkill;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeSkillSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty()) {
            $this->command->warn('لا توجد موظفين!');
            return;
        }

        $skills = [
            'Microsoft Office', 'Excel', 'Word', 'PowerPoint', 'Project Management',
            'Communication', 'Leadership', 'Teamwork', 'Problem Solving', 'Time Management',
            'PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React', 'MySQL', 'PostgreSQL',
            'HTML', 'CSS', 'Bootstrap', 'Git', 'Docker', 'Linux', 'AWS', 'Azure',
            'Customer Service', 'Sales', 'Marketing', 'Accounting', 'Finance',
            'Arabic', 'English', 'French', 'German', 'Spanish'
        ];

        $proficiencyLevels = ['beginner', 'intermediate', 'advanced', 'expert'];

        foreach ($employees as $employee) {
            // 5-12 مهارة لكل موظف
            $selectedSkills = array_rand($skills, rand(5, min(12, count($skills))));
            if (!is_array($selectedSkills)) {
                $selectedSkills = [$selectedSkills];
            }

            foreach ($selectedSkills as $skillIndex) {
                $skillName = $skills[$skillIndex];
                $proficiency = $proficiencyLevels[array_rand($proficiencyLevels)];
                $yearsOfExperience = match($proficiency) {
                    'beginner' => rand(0, 1),
                    'intermediate' => rand(2, 4),
                    'advanced' => rand(5, 8),
                    'expert' => rand(9, 15),
                    default => rand(0, 5),
                };

                EmployeeSkill::create([
                    'employee_id' => $employee->id,
                    'skill_name' => $skillName,
                    'proficiency_level' => $proficiency,
                    'years_of_experience' => $yearsOfExperience,
                    'acquired_date' => Carbon::now()->subYears($yearsOfExperience)->subMonths(rand(0, 11)),
                    'notes' => rand(0, 1) ? 'مهارة مهمة في العمل' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء مهارات الموظفين بنجاح!');
    }
}
