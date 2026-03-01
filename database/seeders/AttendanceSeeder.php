<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على 20 موظف
        $employees = Employee::where('is_active', true)->take(20)->get();
        
        if ($employees->isEmpty()) {
            $this->command->warn('لا توجد موظفين! يرجى تشغيل EmployeeSeeder أولاً.');
            return;
        }

        // الحصول على مستخدم admin لاستخدامه كـ created_by
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : null;

        // تاريخ بداية الأسبوع (من يوم الاثنين)
        // استخدام الأسبوع الماضي ليكون لدينا بيانات واقعية للاختبار
        $startDate = Carbon::now()->subWeek()->startOfWeek(); // يوم الاثنين من الأسبوع الماضي
        $endDate = $startDate->copy()->addDays(6); // يوم الأحد
        
        // أو استخدام الأسبوع الحالي (قم بإلغاء التعليق إذا أردت)
        // $startDate = Carbon::now()->startOfWeek();
        // $endDate = $startDate->copy()->addDays(6);

        $this->command->info("إنشاء سجلات حضور من {$startDate->format('Y-m-d')} إلى {$endDate->format('Y-m-d')}");

        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // تخطي عطلة نهاية الأسبوع (الجمعة والسبت) - يمكن تعديلها حسب النظام
                $dayOfWeek = $currentDate->dayOfWeek; // 0 = الأحد, 5 = الجمعة, 6 = السبت
                
                // في هذا المثال سننشئ حضور لجميع الأيام
                // إذا أردت تخطي نهاية الأسبوع، قم بإلغاء التعليق:
                // if ($dayOfWeek == 5 || $dayOfWeek == 6) {
                //     $currentDate->addDay();
                //     continue;
                // }
                
                // احتمال 85% أن يكون الموظف حاضر (15% غائب)
                $isPresent = rand(1, 100) <= 85;
                
                if ($isPresent) {
                    // أوقات الدخول المتوقعة
                    $expectedCheckIn = '09:00';
                    $expectedCheckOut = '17:00';
                    
                    // وقت الدخول الفعلي (قد يكون متأخر)
                    $lateChance = rand(1, 100);
                    if ($lateChance <= 30) {
                        // 30% احتمال تأخير (5-30 دقيقة)
                        $lateMinutes = rand(5, 30);
                        $checkIn = Carbon::parse($expectedCheckIn)->addMinutes($lateMinutes)->format('H:i');
                        $status = 'late';
                    } else {
                        // 70% في الوقت المحدد أو مبكر (5 دقائق قبل أو بعد)
                        $checkInMinutes = rand(-5, 5);
                        $checkIn = Carbon::parse($expectedCheckIn)->addMinutes($checkInMinutes)->format('H:i');
                        $status = 'present';
                    }
                    
                    // وقت الخروج (16:30 - 18:00)
                    $checkOutVariation = rand(-30, 60); // من 30 دقيقة قبل إلى ساعة بعد
                    $checkOut = Carbon::parse($expectedCheckOut)->addMinutes($checkOutVariation)->format('H:i');
                    
                    // بعض الأيام نصف يوم (5% احتمال)
                    if (rand(1, 100) <= 5) {
                        $checkOut = Carbon::parse($checkIn)->addHours(4)->format('H:i');
                        $status = 'half_day';
                    }
                } else {
                    // موظف غائب
                    $checkIn = null;
                    $checkOut = null;
                    $expectedCheckIn = '09:00';
                    $expectedCheckOut = '17:00';
                    $status = 'absent';
                }
                
                // التحقق من وجود سجل حضور لنفس الموظف في نفس اليوم
                $attendance = Attendance::firstOrNew([
                    'employee_id' => $employee->id,
                    'attendance_date' => $currentDate->format('Y-m-d'),
                ]);
                
                // تحديث البيانات فقط إذا كان السجل جديداً
                if (!$attendance->exists) {
                    $attendance->check_in = $checkIn;
                    $attendance->check_out = $checkOut;
                    $attendance->expected_check_in = $expectedCheckIn;
                    $attendance->expected_check_out = $expectedCheckOut;
                    $attendance->status = $status;
                    $attendance->created_by = $createdBy;
                    $attendance->save();
                    
                    // حساب ساعات العمل تلقائياً
                    if ($checkIn && $checkOut) {
                        $attendance->calculateHours();
                        $attendance->save();
                    }
                }
                
                $currentDate->addDay();
            }
        }

        $this->command->info("تم إنشاء سجلات حضور لـ " . $employees->count() . " موظف لمدة أسبوع كامل!");
    }
}
