<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $currentYear = Carbon::now()->year;

        if ($employees->isEmpty() || $leaveTypes->isEmpty()) {
            $this->command->warn('لا توجد موظفين أو أنواع إجازات!');
            return;
        }

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                // التحقق من عدم وجود رصيد موجود
                $existingBalance = LeaveBalance::where('employee_id', $employee->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', $currentYear)
                    ->first();

                if (!$existingBalance) {
                    $allocatedDays = $leaveType->max_days ?? 30;
                    $usedDays = rand(0, min($allocatedDays, 15)); // استخدام عشوائي
                    $remainingDays = $allocatedDays - $usedDays;

                LeaveBalance::create([
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $currentYear,
                    'total_days' => $allocatedDays,
                    'used_days' => $usedDays,
                    'remaining_days' => $remainingDays,
                ]);
                }
            }
        }

        $this->command->info('✅ تم إنشاء أرصدة الإجازات بنجاح!');
    }
}
