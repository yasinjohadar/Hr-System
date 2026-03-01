<?php

namespace Database\Seeders;

use App\Models\EmployeeBenefit;
use App\Models\Employee;
use App\Models\BenefitType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeBenefitSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $benefitTypes = BenefitType::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty() || $benefitTypes->isEmpty()) {
            $this->command->warn('لا توجد موظفين أو أنواع مزايا!');
            return;
        }

        $statuses = ['active', 'suspended', 'expired', 'cancelled'];

        foreach ($employees as $employee) {
            // 2-5 مزايا لكل موظف
            $selectedBenefits = $benefitTypes->random(rand(2, min(5, $benefitTypes->count())));

            foreach ($selectedBenefits as $benefitType) {
                $startDate = Carbon::now()->subMonths(rand(1, 12))->startOfMonth();
                
                // التحقق من عدم وجود ميزة بنفس start_date
                $existing = EmployeeBenefit::where('employee_id', $employee->id)
                    ->where('benefit_type_id', $benefitType->id)
                    ->where('start_date', $startDate->format('Y-m-d'))
                    ->first();

                if ($existing) {
                    continue;
                }

                $endDate = rand(1, 100) <= 20 
                    ? $startDate->copy()->addMonths(rand(6, 24))->endOfMonth()
                    : null;

                $value = $benefitType->default_value ?? rand(100, 5000);

                EmployeeBenefit::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'benefit_type_id' => $benefitType->id,
                        'start_date' => $startDate,
                    ],
                    [
                        'end_date' => $endDate,
                        'value' => $value,
                        'status' => $statuses[array_rand($statuses)],
                        'notes' => rand(0, 1) ? 'ميزة ممنوحة للموظف' : null,
                        'created_by' => $createdBy,
                    ]
                );
            }
        }

        $this->command->info('✅ تم إنشاء مزايا الموظفين بنجاح!');
    }
}
