<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LeaveRequestSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->take(15)->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty() || $leaveTypes->isEmpty()) {
            $this->command->warn('لا توجد موظفين أو أنواع إجازات!');
            return;
        }

        $statuses = ['pending', 'approved', 'rejected'];
        
        foreach ($employees as $employee) {
            // إنشاء 2-3 طلبات إجازة لكل موظف
            $numRequests = rand(2, 3);
            
            for ($i = 0; $i < $numRequests; $i++) {
                $leaveType = $leaveTypes->random();
                $startDate = Carbon::now()->subMonths(rand(1, 6))->addDays(rand(1, 20));
                $endDate = $startDate->copy()->addDays(rand(1, 5));
                $numberOfDays = $startDate->diffInDays($endDate) + 1;
                $status = $statuses[array_rand($statuses)];

                LeaveRequest::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'days_count' => $numberOfDays,
                        'reason' => 'طلب إجازة ' . $leaveType->name_ar,
                        'status' => $status,
                        'approved_by' => $status == 'approved' ? $createdBy : null,
                        'approved_at' => $status == 'approved' ? Carbon::now() : null,
                        'created_by' => $createdBy,
                    ]
                );
            }
        }
    }
}
