<?php

namespace Database\Factories;

use App\Models\LeaveAccrualRule;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveAccrualRule>
 */
class LeaveAccrualRuleFactory extends Factory
{
    protected $model = LeaveAccrualRule::class;

    public function definition(): array
    {
        return [
            'leave_type_id' => LeaveType::factory(),
            'country_id' => null,
            'branch_id' => null,
            'accrual_days_per_month' => 1.75,
            'max_balance' => null,
            'is_active' => true,
        ];
    }

    public function perMonth(float $days): static
    {
        return $this->state(fn () => ['accrual_days_per_month' => $days]);
    }

    public function maxBalance(int $days): static
    {
        return $this->state(fn () => ['max_balance' => $days]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
