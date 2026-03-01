<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceBreakSeeder extends Seeder
{
    public function run(): void
    {
        $attendances = Attendance::whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->get();

        if ($attendances->isEmpty()) {
            $this->command->warn('لا توجد سجلات حضور!');
            return;
        }

        $breakTypes = ['lunch', 'coffee', 'prayer', 'other'];
        $breakDurations = [
            'lunch' => [30, 60, 90],
            'coffee' => [10, 15, 20],
            'prayer' => [15, 20, 30],
            'other' => [5, 10, 15],
        ];

        foreach ($attendances as $attendance) {
            // 70% من سجلات الحضور لديها استراحات
            if (rand(1, 100) > 70) {
                continue;
            }

            $checkIn = Carbon::parse($attendance->check_in);
            $checkOut = Carbon::parse($attendance->check_out);
            $workDuration = $checkIn->diffInHours($checkOut);

            // استراحة غداء (إذا كانت مدة العمل أكثر من 6 ساعات)
            if ($workDuration >= 6) {
                $lunchStart = $checkIn->copy()->addHours(4)->addMinutes(rand(0, 60));
                $lunchDuration = $breakDurations['lunch'][array_rand($breakDurations['lunch'])];
                $lunchEnd = $lunchStart->copy()->addMinutes($lunchDuration);

                if ($lunchEnd->lt($checkOut)) {
                    AttendanceBreak::create([
                        'attendance_id' => $attendance->id,
                        'break_type' => 'lunch',
                        'break_start' => $lunchStart->format('H:i'),
                        'break_end' => $lunchEnd->format('H:i'),
                        'duration_minutes' => $lunchDuration,
                        'notes' => 'استراحة غداء',
                    ]);
                }
            }

            // استراحة صلاة (مرتين في اليوم)
            if ($workDuration >= 4) {
                $prayerTimes = [
                    $checkIn->copy()->addHours(2)->addMinutes(rand(0, 30)),
                    $checkIn->copy()->addHours(6)->addMinutes(rand(0, 30)),
                ];

                foreach ($prayerTimes as $prayerTime) {
                    if ($prayerTime->lt($checkOut)) {
                        $prayerDuration = $breakDurations['prayer'][array_rand($breakDurations['prayer'])];
                        $prayerEnd = $prayerTime->copy()->addMinutes($prayerDuration);

                        if ($prayerEnd->lt($checkOut)) {
                            AttendanceBreak::create([
                                'attendance_id' => $attendance->id,
                                'break_type' => 'prayer',
                                'break_start' => $prayerTime->format('H:i'),
                                'break_end' => $prayerEnd->format('H:i'),
                                'duration_minutes' => $prayerDuration,
                                'notes' => 'استراحة صلاة',
                            ]);
                        }
                    }
                }
            }

            // استراحة قهوة (30% من الحالات)
            if (rand(1, 100) <= 30 && $workDuration >= 3) {
                $coffeeTime = $checkIn->copy()->addHours(rand(1, 3))->addMinutes(rand(0, 30));
                if ($coffeeTime->lt($checkOut)) {
                    $coffeeDuration = $breakDurations['coffee'][array_rand($breakDurations['coffee'])];
                    $coffeeEnd = $coffeeTime->copy()->addMinutes($coffeeDuration);

                    if ($coffeeEnd->lt($checkOut)) {
                        AttendanceBreak::create([
                            'attendance_id' => $attendance->id,
                            'break_type' => 'coffee',
                            'break_start' => $coffeeTime->format('H:i'),
                            'break_end' => $coffeeEnd->format('H:i'),
                            'duration_minutes' => $coffeeDuration,
                            'notes' => 'استراحة قهوة',
                        ]);
                    }
                }
            }
        }

        $totalBreaks = AttendanceBreak::count();
        $this->command->info("✅ تم إنشاء $totalBreaks استراحة حضور");
    }
}
