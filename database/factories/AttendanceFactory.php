<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'attendance_date' => now()->toDateString(),
            'check_in' => '08:00:00',
            'check_out' => '16:00:00',
            // تنبيه: hours_worked يخزّن الدقائق لا الساعات (راجع migration)
            'hours_worked' => 480,
            'overtime_minutes' => 0,
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'status' => 'present',
        ];
    }

    public function present(): static
    {
        return $this->state(fn () => ['status' => 'present']);
    }

    public function absent(): static
    {
        return $this->state(fn () => [
            'status' => 'absent',
            'check_in' => null,
            'check_out' => null,
            'hours_worked' => 0,
        ]);
    }

    /**
     * حاضر مع تأخير — يُفعّل خصم التأخير في حساب الراتب.
     */
    public function late(int $minutes = 30): static
    {
        return $this->state(fn () => [
            'status' => 'present',
            'late_minutes' => $minutes,
        ]);
    }

    public function onDate(string $date): static
    {
        return $this->state(fn () => ['attendance_date' => $date]);
    }
}
