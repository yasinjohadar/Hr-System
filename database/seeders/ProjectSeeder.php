<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::where('is_active', true)->get();
        $managers = Employee::where('is_active', true)->whereHas('subordinates')->orWhereHas('department', function($q) {
            $q->whereNotNull('manager_id');
        })->get();
        $employees = Employee::where('is_active', true)->get();
        $currency = Currency::where('code', 'SAR')->first();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($departments->isEmpty() || $managers->isEmpty()) {
            $this->command->warn('لا توجد أقسام أو مديرين!');
            return;
        }

        if ($managers->isEmpty()) {
            $managers = $employees->take(3);
        }

        $projects = [
            [
                'name' => 'Website Redesign',
                'name_ar' => 'إعادة تصميم الموقع',
                'description' => 'Complete website redesign project',
                'description_ar' => 'مشروع إعادة تصميم الموقع بالكامل',
                'project_code' => 'PRJ-001',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(2),
                'status' => 'active',
                'budget' => 50000,
                'progress' => 45,
            ],
            [
                'name' => 'HR System Implementation',
                'name_ar' => 'تنفيذ نظام الموارد البشرية',
                'description' => 'Implement new HR system',
                'description_ar' => 'تنفيذ نظام موارد بشرية جديد',
                'project_code' => 'PRJ-002',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(3),
                'status' => 'active',
                'budget' => 100000,
                'progress' => 30,
            ],
            [
                'name' => 'Mobile App Development',
                'name_ar' => 'تطوير تطبيق موبايل',
                'description' => 'Develop mobile application',
                'description_ar' => 'تطوير تطبيق موبايل',
                'project_code' => 'PRJ-003',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addMonths(6),
                'status' => 'planning',
                'budget' => 150000,
                'progress' => 0,
            ],
        ];

        foreach ($projects as $projectData) {
            $projectData['department_id'] = $departments->random()->id;
            $projectData['manager_id'] = $managers->random()->id;
            $projectData['currency_id'] = $currency->id;
            $projectData['created_by'] = $createdBy;

            $project = Project::firstOrCreate(
                ['project_code' => $projectData['project_code']],
                $projectData
            );

            // إنشاء مهام للمشروع
            if ($project->status == 'active') {
                $taskTitles = [
                    ['title' => 'Design Phase', 'title_ar' => 'مرحلة التصميم'],
                    ['title' => 'Development Phase', 'title_ar' => 'مرحلة التطوير'],
                    ['title' => 'Testing Phase', 'title_ar' => 'مرحلة الاختبار'],
                ];

                foreach ($taskTitles as $index => $taskTitle) {
                    $task = Task::create([
                        'project_id' => $project->id,
                        'title' => $taskTitle['title'],
                        'title_ar' => $taskTitle['title_ar'],
                        'description' => $taskTitle['title'],
                        'description_ar' => $taskTitle['title_ar'],
                        'task_code' => 'TASK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                        'priority' => ['high', 'medium', 'low'][$index],
                        'status' => $index == 0 ? 'in_progress' : 'pending',
                        'start_date' => $project->start_date->copy()->addDays($index * 30),
                        'due_date' => $project->start_date->copy()->addDays(($index + 1) * 30),
                        'created_by' => $createdBy,
                    ]);
                }
            }
        }
    }
}
