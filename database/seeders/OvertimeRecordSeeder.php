<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\OvertimeRecord;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OvertimeRecordSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $employees = Employee::where('is_active', true)->get();
        $attendances = Attendance::whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->where('attendance_date', '>=', Carbon::now()->subMonths(3))
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn('لا توجد موظفين!');
            return;
        }

        $overtimeTypes = ['regular', 'holiday', 'night', 'weekend'];
        $statuses = ['pending', 'approved', 'rejected'];
        $reasons = [
            'إكمال مشروع عاجل',
            'اجتماعات إضافية',
            'دعم فني',
            'مشروع خاص',
            'طوارئ',
        ];

        // إنشاء سجلات ساعات إضافية من الحضور
        foreach ($attendances as $attendance) {
            $checkIn = Carbon::parse($attendance->check_in);
            $checkOut = Carbon::parse($attendance->check_out);
            $workHours = $checkIn->diffInHours($checkOut);

            // إذا كانت ساعات العمل أكثر من 8 ساعات
            if ($workHours > 8) {
                $overtimeHours = $workHours - 8;
                $overtimeMinutes = ($overtimeHours - floor($overtimeHours)) * 60;

                // 60% من الحضور الطويل يتم تسجيله كساعات إضافية
                if (rand(1, 100) <= 60) {
                    $hourlyRate = ($attendance->employee->salary ?? 10000) / 160; // 160 ساعة شهرياً
                    $rateMultiplier = 1.5;
                    $overtimeAmount = $overtimeHours * $hourlyRate * $rateMultiplier;

                    // تحديد النوع
                    $attendanceDate = Carbon::parse($attendance->attendance_date);
                    $isWeekend = in_array($attendanceDate->dayOfWeek, [5, 6]); // الجمعة والسبت
                    $overtimeType = $isWeekend ? 'weekend' : 'regular';

                    $status = $statuses[array_rand($statuses)];
                    $approvedBy = null;
                    $approvedAt = null;

                    if ($status === 'approved') {
                        $approvedBy = $createdBy;
                        $approvedAt = $attendanceDate->copy()->addDays(rand(1, 3));
                    }

                    // ربط بكشف راتب إذا كان موجود
                    $payroll = Payroll::where('employee_id', $attendance->employee_id)
                        ->where('payroll_month', $attendanceDate->month)
                        ->where('payroll_year', $attendanceDate->year)
                        ->first();

                    OvertimeRecord::firstOrCreate(
                        [
                            'employee_id' => $attendance->employee_id,
                            'attendance_id' => $attendance->id,
                            'overtime_date' => $attendance->attendance_date,
                        ],
                        [
                            'start_time' => $checkOut->format('H:i'),
                            'end_time' => $checkOut->copy()->addHours($overtimeHours)->format('H:i'),
                            'overtime_minutes' => (int)($overtimeHours * 60 + $overtimeMinutes),
                            'overtime_hours' => round($overtimeHours, 2),
                            'overtime_type' => $overtimeType,
                            'rate_multiplier' => $rateMultiplier,
                            'hourly_rate' => $hourlyRate,
                            'overtime_amount' => round($overtimeAmount, 2),
                            'status' => $status,
                            'approved_by' => $approvedBy,
                            'approved_at' => $approvedAt,
                            'approval_notes' => $status === 'approved' ? 'تمت الموافقة' : null,
                            'payroll_id' => $payroll?->id,
                            'reason' => $reasons[array_rand($reasons)],
                            'notes' => "ساعات إضافية من الحضور",
                            'created_by' => $createdBy,
                        ]
                    );
                }
            }
        }

        // إنشاء سجلات ساعات إضافية مستقلة (غير مرتبطة بالحضور)
        foreach ($employees as $employee) {
            // 30% من الموظفين لديهم ساعات إضافية مستقلة
            if (rand(1, 100) <= 30) {
                $numRecords = rand(1, 5);
                for ($i = 0; $i < $numRecords; $i++) {
                    $overtimeDate = Carbon::now()->subDays(rand(1, 90));
                    $hourlyRate = ($employee->salary ?? 10000) / 160;
                    $overtimeHours = rand(1, 8) + (rand(0, 59) / 60);
                    $rateMultiplier = 1.5;
                    $overtimeAmount = $overtimeHours * $hourlyRate * $rateMultiplier;

                    $isWeekend = in_array($overtimeDate->dayOfWeek, [5, 6]);
                    $overtimeType = $isWeekend ? 'weekend' : $overtimeTypes[array_rand($overtimeTypes)];

                    $status = $statuses[array_rand($statuses)];
                    $approvedBy = null;
                    $approvedAt = null;

                    if ($status === 'approved') {
                        $approvedBy = $createdBy;
                        $approvedAt = $overtimeDate->copy()->addDays(rand(1, 3));
                    }

                    OvertimeRecord::create([
                        'employee_id' => $employee->id,
                        'attendance_id' => null,
                        'overtime_date' => $overtimeDate,
                        'start_time' => '17:00',
                        'end_time' => Carbon::parse('17:00')->addHours($overtimeHours)->format('H:i'),
                        'overtime_minutes' => (int)($overtimeHours * 60),
                        'overtime_hours' => round($overtimeHours, 2),
                        'overtime_type' => $overtimeType,
                        'rate_multiplier' => $rateMultiplier,
                        'hourly_rate' => $hourlyRate,
                        'overtime_amount' => round($overtimeAmount, 2),
                        'status' => $status,
                        'approved_by' => $approvedBy,
                        'approved_at' => $approvedAt,
                        'approval_notes' => $status === 'approved' ? 'تمت الموافقة' : null,
                        'reason' => $reasons[array_rand($reasons)],
                        'notes' => 'ساعات إضافية مستقلة',
                        'created_by' => $createdBy,
                    ]);
                }
            }
        }

        $totalOvertimes = OvertimeRecord::count();
        $this->command->info("✅ تم إنشاء $totalOvertimes سجل ساعات إضافية");
    }
}
