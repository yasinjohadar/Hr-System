<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $shifts = [
            [
                'shift_code' => 'SHIFT-MORNING-001',
                'name' => 'Morning Shift',
                'name_ar' => 'مناوبة صباحية',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'duration_hours' => 8,
                'grace_period_minutes' => 15,
                'break_duration_minutes' => 60,
                'has_night_shift' => false,
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
                'sunday' => false,
                'overtime_rate' => 1.50,
                'overtime_threshold_minutes' => 0,
                'is_active' => true,
                'description' => 'مناوبة صباحية من 8 صباحاً إلى 4 مساءً',
                'created_by' => $createdBy,
            ],
            [
                'shift_code' => 'SHIFT-EVENING-001',
                'name' => 'Evening Shift',
                'name_ar' => 'مناوبة مسائية',
                'start_time' => '16:00',
                'end_time' => '00:00',
                'duration_hours' => 8,
                'grace_period_minutes' => 15,
                'break_duration_minutes' => 60,
                'has_night_shift' => false,
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
                'sunday' => false,
                'overtime_rate' => 1.50,
                'overtime_threshold_minutes' => 0,
                'is_active' => true,
                'description' => 'مناوبة مسائية من 4 مساءً إلى 12 منتصف الليل',
                'created_by' => $createdBy,
            ],
            [
                'shift_code' => 'SHIFT-NIGHT-001',
                'name' => 'Night Shift',
                'name_ar' => 'مناوبة ليلية',
                'start_time' => '00:00',
                'end_time' => '08:00',
                'duration_hours' => 8,
                'grace_period_minutes' => 15,
                'break_duration_minutes' => 60,
                'has_night_shift' => true,
                'night_shift_start' => '00:00',
                'night_shift_end' => '08:00',
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
                'sunday' => false,
                'overtime_rate' => 2.00,
                'overtime_threshold_minutes' => 0,
                'is_active' => true,
                'description' => 'مناوبة ليلية من 12 منتصف الليل إلى 8 صباحاً',
                'created_by' => $createdBy,
            ],
            [
                'shift_code' => 'SHIFT-FLEXIBLE-001',
                'name' => 'Flexible Shift',
                'name_ar' => 'مناوبة مرنة',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'duration_hours' => 8,
                'grace_period_minutes' => 30,
                'break_duration_minutes' => 60,
                'has_night_shift' => false,
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
                'sunday' => false,
                'overtime_rate' => 1.50,
                'overtime_threshold_minutes' => 0,
                'is_active' => true,
                'description' => 'مناوبة مرنة من 9 صباحاً إلى 6 مساءً',
                'created_by' => $createdBy,
            ],
            [
                'shift_code' => 'SHIFT-WEEKEND-001',
                'name' => 'Weekend Shift',
                'name_ar' => 'مناوبة نهاية الأسبوع',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'duration_hours' => 8,
                'grace_period_minutes' => 15,
                'break_duration_minutes' => 60,
                'has_night_shift' => false,
                'monday' => false,
                'tuesday' => false,
                'wednesday' => false,
                'thursday' => false,
                'friday' => false,
                'saturday' => true,
                'sunday' => true,
                'overtime_rate' => 2.00,
                'overtime_threshold_minutes' => 0,
                'is_active' => true,
                'description' => 'مناوبة نهاية الأسبوع (السبت والأحد)',
                'created_by' => $createdBy,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['shift_code' => $shift['shift_code']],
                $shift
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($shifts) . ' مناوبة');
    }
}
