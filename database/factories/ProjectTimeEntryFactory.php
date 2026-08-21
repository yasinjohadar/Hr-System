<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectTimeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectTimeEntry>
 */
class ProjectTimeEntryFactory extends Factory
{
    protected $model = ProjectTimeEntry::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'employee_id' => Employee::factory(),
            'task_id' => null,
            'worked_date' => now()->toDateString(),
            'hours' => 8,
            'description' => $this->faker->sentence(),
        ];
    }

    public function hours(float $hours): static
    {
        return $this->state(fn () => ['hours' => $hours]);
    }

    public function onDate(string $date): static
    {
        return $this->state(fn () => ['worked_date' => $date]);
    }
}
