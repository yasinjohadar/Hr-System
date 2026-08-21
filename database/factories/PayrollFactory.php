<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition(): array
    {
        $period = now()->startOfMonth();

        return [
            'payroll_code' => strtoupper($this->faker->unique()->bothify('PR-######')),
            'employee_id' => Employee::factory(),
            'payroll_month' => (int) $period->month,
            'payroll_year' => (int) $period->year,
            'period_start' => $period->toDateString(),
            'period_end' => $period->copy()->endOfMonth()->toDateString(),
            'status' => 'draft',
        ];
    }

    /**
     * كشف راتب لفترة محددة — يضبط الشهر والسنة معاً حتى لا يخالف
     * قيد unique(employee_id, payroll_month, payroll_year).
     */
    public function forPeriod(string $start, string $end): static
    {
        return $this->state(function () use ($start, $end) {
            $from = \Carbon\Carbon::parse($start);

            return [
                'period_start' => $from->toDateString(),
                'period_end' => \Carbon\Carbon::parse($end)->toDateString(),
                'payroll_month' => (int) $from->month,
                'payroll_year' => (int) $from->year,
            ];
        });
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'payment_date' => now()->toDateString(),
        ]);
    }

    public function calculated(): static
    {
        return $this->state(fn () => ['status' => 'calculated']);
    }
}
