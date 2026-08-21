<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' leave',
            'name_ar' => 'إجازة ' . $this->faker->word(),
            'code' => strtoupper($this->faker->unique()->bothify('LT-###')),
            'max_days' => 21,
            'is_paid' => true,
            'requires_approval' => true,
            'carry_forward' => false,
            'is_active' => true,
        ];
    }

    /**
     * إجازة غير مدفوعة — تُفعّل خصم الإجازات في حساب الراتب.
     */
    public function unpaid(): static
    {
        return $this->state(fn () => ['is_paid' => false]);
    }

    public function paid(): static
    {
        return $this->state(fn () => ['is_paid' => true]);
    }
}
