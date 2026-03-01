<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Currency;
use App\Models\Salary;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $employees = Employee::where('is_active', true)->get();
        $currency = Currency::where('code', 'SAR')->first();

        if ($employees->isEmpty() || !$currency) {
            $this->command->warn('لا توجد موظفين أو عملة!');
            return;
        }

        // إنشاء كشوف رواتب لآخر 6 أشهر
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i);
        }

        $statuses = ['calculated', 'approved', 'paid'];
        $paymentMethods = ['bank_transfer', 'cash', 'cheque'];

        foreach ($employees as $employee) {
            $salary = Salary::where('employee_id', $employee->id)
                ->first();

            $baseSalary = $salary ? ($salary->base_salary ?? rand(5000, 20000)) : rand(5000, 20000);

            foreach ($months as $month) {
                $payrollMonth = $month->month;
                $payrollYear = $month->year;

                // التحقق من عدم وجود كشف مسبق
                $existing = Payroll::where('employee_id', $employee->id)
                    ->where('payroll_month', $payrollMonth)
                    ->where('payroll_year', $payrollYear)
                    ->first();

                if ($existing) {
                    continue;
                }

                // حساب أيام العمل والحضور
                $workingDays = 22; // متوسط أيام العمل في الشهر
                $presentDays = rand(18, $workingDays);
                $absentDays = $workingDays - $presentDays;
                $lateDays = rand(0, 5);
                $leaveDays = rand(0, 3);

                // حساب البدلات
                $housingAllowance = rand(2000, 5000);
                $transportAllowance = rand(500, 1500);
                $foodAllowance = 500;
                $totalAllowances = $housingAllowance + $transportAllowance + $foodAllowance;

                // حساب المكافآت
                $bonuses = rand(0, 2000);

                // حساب الساعات الإضافية
                $overtimeHours = rand(0, 20);
                $hourlyRate = $baseSalary / 160; // 160 ساعة عمل شهرياً
                $overtimeAmount = $overtimeHours * $hourlyRate * 1.5;

                // حساب الخصومات
                $leaveDeduction = $leaveDays > 0 ? ($baseSalary / $workingDays) * $leaveDays : 0;
                $lateDeduction = $lateDays > 0 ? ($baseSalary / $workingDays / 8) * $lateDays * 0.5 : 0;
                $totalDeductions = $leaveDeduction + $lateDeduction;

                // حساب الراتب الإجمالي
                $grossSalary = $baseSalary + $totalAllowances + $bonuses + $overtimeAmount;

                // حساب الضرائب
                $incomeTax = ($grossSalary - 4000) * 0.025; // 2.5% ضريبة
                $socialInsuranceEmployee = $baseSalary * 0.09; // 9% تأمين
                $socialInsuranceEmployer = $baseSalary * 0.11; // 11% صاحب العمل
                $healthInsuranceEmployee = 200;
                $healthInsuranceEmployer = 300;
                $otherTaxes = 0;
                $totalTaxes = $incomeTax + $socialInsuranceEmployee + $healthInsuranceEmployee + $otherTaxes;

                // حساب الراتب الصافي
                $netSalary = $grossSalary - $totalDeductions - $totalTaxes;
                $totalEmployerCost = $grossSalary + $socialInsuranceEmployer + $healthInsuranceEmployer;

                // تحديد الحالة
                $status = $statuses[array_rand($statuses)];
                $paymentDate = null;
                $paymentMethod = null;
                $approvedBy = null;
                $approvedAt = null;

                if ($status === 'approved' || $status === 'paid') {
                    $approvedBy = $createdBy;
                    $approvedAt = $month->copy()->endOfMonth()->subDays(rand(0, 5));
                }

                if ($status === 'paid') {
                    $paymentDate = $month->copy()->endOfMonth()->addDays(rand(1, 5));
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                }

                $periodStart = $month->copy()->startOfMonth();
                $periodEnd = $month->copy()->endOfMonth();

                $payroll = Payroll::create([
                    'employee_id' => $employee->id,
                    'payroll_month' => $payrollMonth,
                    'payroll_year' => $payrollYear,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'base_salary' => $baseSalary,
                    'total_allowances' => $totalAllowances,
                    'total_deductions' => $totalDeductions,
                    'bonuses' => $bonuses,
                    'overtime_amount' => $overtimeAmount,
                    'overtime_hours' => $overtimeHours,
                    'leave_days' => $leaveDays,
                    'leave_deduction' => $leaveDeduction,
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'late_days' => $lateDays,
                    'late_deduction' => $lateDeduction,
                    'gross_salary' => $grossSalary,
                    'income_tax' => max(0, $incomeTax),
                    'social_insurance_employee' => $socialInsuranceEmployee,
                    'social_insurance_employer' => $socialInsuranceEmployer,
                    'health_insurance_employee' => $healthInsuranceEmployee,
                    'health_insurance_employer' => $healthInsuranceEmployer,
                    'other_taxes' => $otherTaxes,
                    'total_taxes' => $totalTaxes,
                    'net_salary' => max(0, $netSalary),
                    'total_employer_cost' => $totalEmployerCost,
                    'currency_id' => $currency->id,
                    'status' => $status,
                    'payment_date' => $paymentDate,
                    'payment_method' => $paymentMethod,
                    'payment_reference' => $paymentMethod ? 'REF-' . strtoupper(uniqid()) : null,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt,
                    'created_by' => $createdBy,
                    'notes' => "كشف راتب لشهر {$payrollMonth}/{$payrollYear}",
                ]);
            }
        }

        $totalPayrolls = Payroll::count();
        $this->command->info("✅ تم إنشاء $totalPayrolls كشف راتب");
    }
}
