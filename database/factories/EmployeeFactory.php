<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();

        return [
            'employee_code' => strtoupper($this->faker->unique()->bothify('EMP-#####')),
            // user_id إلزامي في المخطط (foreignId غير nullable)
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'position_id' => Position::factory(),
            'manager_id' => null,
            'first_name' => $first,
            'last_name' => $last,
            'full_name' => $first . ' ' . $last,
            'national_id' => $this->faker->unique()->numerify('##########'),
            'date_of_birth' => $this->faker->date('Y-m-d', '-25 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'personal_email' => $this->faker->unique()->safeEmail(),
            'personal_phone' => $this->faker->numerify('05########'),
            'hire_date' => now()->subYears(2)->toDateString(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'salary' => 6000,
            'is_active' => true,
        ];
    }

    /**
     * موظف بلا راتب مُسجَّل — لاختبار حالة salary = null.
     */
    public function withoutSalary(): static
    {
        return $this->state(fn () => ['salary' => null]);
    }

    public function salary(float $amount): static
    {
        return $this->state(fn () => ['salary' => $amount]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
            'employment_status' => 'terminated',
        ]);
    }

    public function inDepartment(Department $department): static
    {
        return $this->state(fn () => ['department_id' => $department->id]);
    }
}
