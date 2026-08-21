<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            // ملاحظة: العمود اسمه title لا name — راجع migration positions
            'title' => $this->faker->unique()->jobTitle(),
            'code' => strtoupper($this->faker->unique()->bothify('POS-###')),
            'description' => $this->faker->optional()->sentence(),
            'department_id' => null,
            'min_salary' => 3000,
            'max_salary' => 12000,
            'is_active' => true,
        ];
    }
}
