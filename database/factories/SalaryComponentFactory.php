<?php

namespace Database\Factories;

use App\Models\SalaryComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryComponent>
 */
class SalaryComponentFactory extends Factory
{
    protected $model = SalaryComponent::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('SC-###')),
            'name' => $this->faker->unique()->word() . ' component',
            'name_ar' => 'مكون ' . $this->faker->word(),
            'type' => 'allowance',
            'calculation_type' => 'fixed',
            'default_value' => 500,
            'apply_to_all' => true,
            'is_active' => true,
        ];
    }

    public function allowance(): static
    {
        return $this->state(fn () => ['type' => 'allowance']);
    }

    public function deduction(): static
    {
        return $this->state(fn () => ['type' => 'deduction']);
    }

    public function bonus(): static
    {
        return $this->state(fn () => ['type' => 'bonus']);
    }

    public function fixed(float $value): static
    {
        return $this->state(fn () => [
            'calculation_type' => 'fixed',
            'default_value' => $value,
        ]);
    }

    public function percentage(float $percent): static
    {
        return $this->state(fn () => [
            'calculation_type' => 'percentage',
            'percentage' => $percent,
        ]);
    }

    public function formula(string $formula): static
    {
        return $this->state(fn () => [
            'calculation_type' => 'formula',
            'formula' => $formula,
        ]);
    }

    public function attendanceBased(float $perDay): static
    {
        return $this->state(fn () => [
            'calculation_type' => 'attendance_based',
            'default_value' => $perDay,
        ]);
    }

    public function leaveBased(float $perDay): static
    {
        return $this->state(fn () => [
            'calculation_type' => 'leave_based',
            'default_value' => $perDay,
        ]);
    }
}
