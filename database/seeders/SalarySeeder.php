<?php

namespace Database\Seeders;

use App\Models\Salary;
use App\Models\Employee;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalarySeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $currency = Currency::where('code', 'SAR')->first();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty() || !$currency) {
            $this->command->warn('لا توجد موظفين أو عملة!');
            return;
        }

        // إنشاء رواتب للشهرين الماضيين والحالي
        $months = [Carbon::now()->subMonths(2), Carbon::now()->subMonth(), Carbon::now()];

        foreach ($employees as $employee) {
            foreach ($months as $month) {
                $baseSalary = rand(5000, 15000);
                $allowances = rand(500, 2000);
                $bonuses = rand(0, 1000);
                $deductions = rand(0, 500);
                $overtime = rand(0, 500);
                $grossSalary = $baseSalary + $allowances + $bonuses + $overtime - $deductions;

                Salary::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'salary_month' => $month->month,
                        'salary_year' => $month->year,
                    ],
                    [
                        'base_salary' => $baseSalary,
                        'allowances' => $allowances,
                        'bonuses' => $bonuses,
                        'deductions' => $deductions,
                        'overtime' => $overtime,
                        'total_salary' => $grossSalary,
                        'payment_status' => rand(0, 1) ? 'paid' : 'pending',
                        'payment_date' => $grossSalary > 0 ? $month->copy()->endOfMonth() : null,
                        'currency_id' => $currency->id,
                        'notes' => 'راتب شهر ' . $month->format('F Y'),
                        'created_by' => $createdBy,
                    ]
                );
            }
        }
    }
}
