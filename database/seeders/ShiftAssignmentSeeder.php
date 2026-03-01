<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShiftAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $assignedBy = $adminUser ? $adminUser->id : 1;

        $employees = Employee::where('is_active', true)->get();
        $shifts = Shift::where('is_active', true)->get();

        if ($employees->isEmpty() || $shifts->isEmpty()) {
            $this->command->warn('لا توجد موظفين أو مناوبات!');
            return;
        }

        $shiftTypes = [
            'morning' => 'SHIFT-MORNING-001',
            'evening' => 'SHIFT-EVENING-001',
            'night' => 'SHIFT-NIGHT-001',
            'flexible' => 'SHIFT-FLEXIBLE-001',
        ];

        foreach ($employees as $employee) {
            // تعيين مناوبة لكل موظف
            $shiftType = array_rand($shiftTypes);
            $shift = Shift::where('shift_code', $shiftTypes[$shiftType])->first();

            if (!$shift) {
                $shift = $shifts->random();
            }

            // تاريخ البدء: قبل 3 أشهر
            $startDate = Carbon::now()->subMonths(3)->startOfMonth();
            // 70% من الموظفين لديهم مناوبة دائمة (بدون end_date)
            $endDate = rand(1, 100) <= 30 ? Carbon::now()->addMonths(rand(1, 6))->endOfMonth() : null;

            ShiftAssignment::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'shift_id' => $shift->id,
                    'start_date' => $startDate->format('Y-m-d'),
                ],
                [
                    'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                    'is_active' => true,
                    'notes' => 'تعيين تلقائي للمناوبة',
                    'assigned_by' => $assignedBy,
                ]
            );

            // بعض الموظفين لديهم مناوبات متعددة (تاريخية)
            if (rand(1, 100) <= 20) {
                $oldShift = $shifts->random();
                $oldStartDate = Carbon::now()->subMonths(rand(6, 12))->startOfMonth();
                $oldEndDate = Carbon::now()->subMonths(3)->endOfMonth();

                ShiftAssignment::create([
                    'employee_id' => $employee->id,
                    'shift_id' => $oldShift->id,
                    'start_date' => $oldStartDate->format('Y-m-d'),
                    'end_date' => $oldEndDate->format('Y-m-d'),
                    'is_active' => false,
                    'notes' => 'مناوبة سابقة',
                    'assigned_by' => $assignedBy,
                ]);
            }
        }

        $totalAssignments = ShiftAssignment::count();
        $this->command->info("✅ تم إنشاء $totalAssignments تعيين مناوبة");
    }
}
