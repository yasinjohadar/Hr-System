<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\OvertimeRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OvertimeRecord>
 */
class OvertimeRecordFactory extends Factory
{
    protected $model = OvertimeRecord::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'overtime_date' => now()->toDateString(),
            // إلزامية في المخطط
            'start_time' => '17:00:00',
            'end_time' => '19:00:00',
            'overtime_minutes' => 120,
            'overtime_hours' => 2,
            'overtime_type' => 'regular',
            'rate_multiplier' => 1.5,
            'hourly_rate' => 50,
            'overtime_amount' => 150,
            'status' => 'pending',
            // payroll_id فارغ: حساب الراتب لا يحتسب إلا السجلات غير المرتبطة بكشف
            'payroll_id' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function hours(float $hours, float $amount): static
    {
        return $this->state(fn () => [
            'overtime_hours' => $hours,
            'overtime_minutes' => (int) round($hours * 60),
            'overtime_amount' => $amount,
        ]);
    }

    public function onDate(string $date): static
    {
        return $this->state(fn () => ['overtime_date' => $date]);
    }
}
