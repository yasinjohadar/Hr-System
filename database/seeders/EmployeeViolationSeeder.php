<?php

namespace Database\Seeders;

use App\Models\EmployeeViolation;
use App\Models\Employee;
use App\Models\ViolationType;
use App\Models\DisciplinaryAction;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeViolationSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $violationTypes = ViolationType::where('is_active', true)->get();
        $disciplinaryActions = DisciplinaryAction::where('is_active', true)->get();
        $managers = Employee::where('is_active', true)->take(5)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty() || $violationTypes->isEmpty()) {
            $this->command->warn('لا توجد موظفين أو أنواع مخالفات!');
            return;
        }

        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['pending', 'investigating', 'confirmed', 'dismissed', 'resolved'];

        // 10-20% من الموظفين لديهم مخالفات
        $employeesWithViolations = $employees->random(rand((int)($employees->count() * 0.1), (int)($employees->count() * 0.2)));

        foreach ($employeesWithViolations as $employee) {
            // 1-3 مخالفات لكل موظف
            $numViolations = rand(1, 3);

            for ($i = 0; $i < $numViolations; $i++) {
                $violationType = $violationTypes->random();
                $severity = $severities[array_rand($severities)];
                $status = $statuses[array_rand($statuses)];
                $violationDate = Carbon::now()->subDays(rand(1, 180));
                $reportedBy = $managers->isNotEmpty() ? $managers->random() : $employees->random();

                $reportedByUser = \App\Models\User::where('id', $reportedBy->user_id ?? $createdBy)->first() ?? \App\Models\User::find($createdBy);
                $reportedById = $reportedByUser ? $reportedByUser->id : $createdBy;

                $violation = EmployeeViolation::create([
                    'employee_id' => $employee->id,
                    'violation_type_id' => $violationType->id,
                    'disciplinary_action_id' => $disciplinaryActions->isNotEmpty() && rand(0, 1) ? $disciplinaryActions->random()->id : null,
                    'violation_date' => $violationDate,
                    'description' => 'وصف المخالفة',
                    'description_ar' => 'وصف المخالفة بالعربية',
                    'status' => $status,
                    'severity' => $severity,
                    'reported_by' => $reportedById,
                    'investigated_by' => ($status === 'investigating' || $status === 'confirmed') ? $reportedById : null,
                    'investigation_date' => ($status === 'investigating' || $status === 'confirmed') ? $violationDate->copy()->addDays(rand(1, 7)) : null,
                    'investigation_notes' => ($status === 'investigating' || $status === 'confirmed') && rand(0, 1) ? 'ملاحظات التحقيق' : null,
                    'action_date' => ($status === 'approved' || $status === 'resolved') ? $violationDate->copy()->addDays(rand(1, 14)) : null,
                    'action_notes' => ($status === 'approved' || $status === 'resolved') && rand(0, 1) ? 'ملاحظات الإجراء' : null,
                    'approved_by' => $status === 'approved' ? $reportedById : null,
                    'approval_date' => $status === 'approved' ? $violationDate->copy()->addDays(rand(1, 14)) : null,
                    'resolution_date' => $status === 'resolved' ? $violationDate->copy()->addDays(rand(15, 30)) : null,
                    'resolution_notes' => $status === 'resolved' && rand(0, 1) ? 'تم حل المخالفة' : null,
                    'notes' => rand(0, 1) ? 'ملاحظات إضافية' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء مخالفات الموظفين بنجاح!');
    }
}
