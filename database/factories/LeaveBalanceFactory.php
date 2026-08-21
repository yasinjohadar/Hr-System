<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveBalance>
 */
class LeaveBalanceFactory extends Factory
{
    protected $model = LeaveBalance::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'year' => (int) now()->year,
            'total_days' => 21,
            'used_days' => 0,
            'remaining_days' => 21,
            'carried_forward' => 0,
        ];
    }

    public function used(int $days): static
    {
        return $this->state(fn (array $attributes) => [
            'used_days' => $days,
            'remaining_days' => ($attributes['total_days'] ?? 21) - $days,
        ]);
    }
}
