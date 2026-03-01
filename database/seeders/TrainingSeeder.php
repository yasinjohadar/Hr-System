<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\TrainingRecord;
use App\Models\Employee;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        $currency = Currency::where('code', 'SAR')->first();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $trainings = [
            [
                'title' => 'Leadership Skills',
                'title_ar' => 'مهارات القيادة',
                'code' => 'TRN-001',
                'description' => 'تدريب على مهارات القيادة والإدارة',
                'type' => 'internal',
                'provider' => 'Internal Training Department',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(32),
                'duration_hours' => 16,
                'cost' => 0,
                'status' => 'planned',
                'max_participants' => 20,
                'location' => 'Training Room A',
                'created_by' => $createdBy,
            ],
            [
                'title' => 'Project Management',
                'title_ar' => 'إدارة المشاريع',
                'code' => 'TRN-002',
                'description' => 'تدريب على إدارة المشاريع',
                'type' => 'external',
                'provider' => 'PMI Institute',
                'start_date' => Carbon::now()->addDays(60),
                'end_date' => Carbon::now()->addDays(65),
                'duration_hours' => 40,
                'cost' => 5000,
                'status' => 'planned',
                'max_participants' => 15,
                'location' => 'External Venue',
                'created_by' => $createdBy,
            ],
            [
                'title' => 'Communication Skills',
                'title_ar' => 'مهارات التواصل',
                'code' => 'TRN-003',
                'description' => 'تدريب على مهارات التواصل الفعال',
                'type' => 'internal',
                'provider' => 'Internal Training Department',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->subDays(8),
                'duration_hours' => 12,
                'cost' => 0,
                'status' => 'completed',
                'max_participants' => 25,
                'location' => 'Training Room B',
                'created_by' => $createdBy,
            ],
        ];

        foreach ($trainings as $training) {
            $training['currency_id'] = $currency->id;
            $created = Training::firstOrCreate(
                ['code' => $training['code']],
                $training
            );

            // إضافة موظفين للتدريب المكتمل
            if ($created->status == 'completed') {
                $employees = Employee::where('is_active', true)->take(rand(10, 15))->get();
                foreach ($employees as $employee) {
                    TrainingRecord::firstOrCreate(
                        [
                            'training_id' => $created->id,
                            'employee_id' => $employee->id,
                        ],
                        [
                            'registration_date' => $created->start_date->copy()->subDays(5),
                            'completion_date' => $created->end_date,
                            'status' => 'completed',
                            'score' => rand(75, 100) + (rand(0, 99) / 100),
                            'created_by' => $createdBy,
                        ]
                    );
                }
            }
        }
    }
}
