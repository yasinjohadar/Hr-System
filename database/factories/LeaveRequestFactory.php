<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $start = now()->startOfMonth()->addDays(5);

        return [
            'employee_id' => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(2)->toDateString(),
            'days_count' => 3,
            'reason' => $this->faker->sentence(),
            'status' => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'rejection_reason' => 'غير مستوفٍ للشروط',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }

    /**
     * تحديد فترة الإجازة صريحاً مع ضبط days_count تلقائياً.
     */
    public function between(string $start, string $end): static
    {
        return $this->state(function () use ($start, $end) {
            $from = \Carbon\Carbon::parse($start);
            $to = \Carbon\Carbon::parse($end);

            return [
                'start_date' => $from->toDateString(),
                'end_date' => $to->toDateString(),
                'days_count' => $from->diffInDays($to) + 1,
            ];
        });
    }
}
