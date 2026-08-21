<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'project_code' => strtoupper($this->faker->unique()->bothify('PRJ-####')),
            'name' => $this->faker->unique()->catchPhrase(),
            'name_ar' => 'مشروع ' . $this->faker->word(),
            'department_id' => null,
            'manager_id' => null,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'status' => 'active',
            'priority' => 'medium',
            'budget' => 100000,
            'progress' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed', 'progress' => 100]);
    }
}
