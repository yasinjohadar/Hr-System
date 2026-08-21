<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => 'قسم ' . $this->faker->unique()->word(),
            'code' => strtoupper($this->faker->unique()->bothify('DEP-###')),
            'description' => $this->faker->optional()->sentence(),
            'manager_id' => null,
            'parent_id' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
