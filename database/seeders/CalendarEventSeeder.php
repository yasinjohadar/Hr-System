<?php

namespace Database\Seeders;

use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CalendarEventSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $employees = Employee::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        if ($employees->isEmpty() || $departments->isEmpty()) {
            $this->command->warn('لا توجد موظفين أو أقسام!');
            return;
        }

        $eventTypes = ['personal', 'public', 'department', 'employee', 'all'];
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        $events = [
            // أحداث عامة
            [
                'title' => 'Company Meeting',
                'title_ar' => 'اجتماع الشركة',
                'description' => 'اجتماع عام لجميع الموظفين',
                'type' => 'public',
                'color' => '#3b82f6',
                'is_all_day' => false,
                'is_reminder' => true,
                'reminder_minutes' => 30,
            ],
            [
                'title' => 'National Day',
                'title_ar' => 'اليوم الوطني',
                'description' => 'عطلة رسمية',
                'type' => 'public',
                'color' => '#10b981',
                'is_all_day' => true,
                'is_reminder' => false,
            ],
            [
                'title' => 'Training Session',
                'title_ar' => 'جلسة تدريبية',
                'description' => 'تدريب على النظام الجديد',
                'type' => 'all',
                'color' => '#f59e0b',
                'is_all_day' => false,
                'is_reminder' => true,
                'reminder_minutes' => 60,
            ],

            // أحداث للأقسام
            [
                'title' => 'Department Meeting',
                'title_ar' => 'اجتماع القسم',
                'description' => 'اجتماع شهري للقسم',
                'type' => 'department',
                'color' => '#8b5cf6',
                'is_all_day' => false,
                'is_reminder' => true,
                'reminder_minutes' => 15,
            ],

            // أحداث شخصية
            [
                'title' => 'Personal Appointment',
                'title_ar' => 'موعد شخصي',
                'description' => 'موعد طبي',
                'type' => 'personal',
                'color' => '#ec4899',
                'is_all_day' => false,
                'is_reminder' => true,
                'reminder_minutes' => 60,
            ],
        ];

        // إنشاء أحداث عامة
        foreach ($events as $eventData) {
            $startDate = Carbon::now()->addDays(rand(-30, 60));
            $endDate = $eventData['is_all_day'] 
                ? $startDate->copy()->endOfDay()
                : $startDate->copy()->addHours(rand(1, 4));

            $event = CalendarEvent::create(array_merge($eventData, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_by' => $createdBy,
                'employee_id' => $eventData['type'] === 'personal' ? $employees->random()->id : null,
                'department_id' => $eventData['type'] === 'department' ? $departments->random()->id : null,
                'is_active' => true,
            ]));
        }

        // إنشاء أحداث متكررة (اجتماعات أسبوعية)
        for ($i = 0; $i < 4; $i++) {
            $department = $departments->random();
            $startDate = Carbon::now()->next(Carbon::MONDAY)->addWeeks($i);

            CalendarEvent::create([
                'title' => 'Weekly Department Meeting',
                'title_ar' => 'اجتماع أسبوعي للقسم',
                'description' => 'اجتماع أسبوعي منتظم',
                'start_date' => $startDate->copy()->setTime(10, 0),
                'end_date' => $startDate->copy()->setTime(11, 30),
                'type' => 'department',
                'department_id' => $department->id,
                'color' => '#3b82f6',
                'is_all_day' => false,
                'is_reminder' => true,
                'reminder_minutes' => 15,
                'is_recurring' => true,
                'recurrence_type' => 'weekly',
                'recurrence_interval' => 1,
                'recurrence_end_date' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'created_by' => $createdBy,
            ]);
        }

        // إنشاء أحداث شخصية للموظفين
        foreach ($employees->take(10) as $employee) {
            $numEvents = rand(2, 5);
            for ($i = 0; $i < $numEvents; $i++) {
                $startDate = Carbon::now()->addDays(rand(-30, 60));
                $endDate = $startDate->copy()->addHours(rand(1, 3));

                CalendarEvent::create([
                    'title' => 'Personal Event',
                    'title_ar' => 'حدث شخصي',
                    'description' => 'موعد أو حدث شخصي',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'type' => 'personal',
                    'employee_id' => $employee->id,
                    'color' => $colors[array_rand($colors)],
                    'is_all_day' => rand(1, 100) <= 30,
                    'is_reminder' => rand(1, 100) <= 70,
                    'reminder_minutes' => rand(15, 120),
                    'is_active' => true,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $totalEvents = CalendarEvent::count();
        $this->command->info("✅ تم إنشاء $totalEvents حدث تقويم");
    }
}
