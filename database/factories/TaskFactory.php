<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'task_code' => strtoupper($this->faker->unique()->bothify('TSK-####')),
            'title' => $this->faker->unique()->sentence(4),
            'title_ar' => 'مهمة ' . $this->faker->word(),
            'project_id' => null,
            'department_id' => null,
            'start_date' => now()->toDateString(),
            'due_date' => now()->addWeek()->toDateString(),
            'status' => 'pending',
            'priority' => 'medium',
            'progress' => 0,
            'estimated_hours' => 8,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'progress' => 100,
            'completed_date' => now()->toDateString(),
        ]);
    }
}
