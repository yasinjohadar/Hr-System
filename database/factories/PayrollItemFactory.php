<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PayrollItem>
 */
class PayrollItemFactory extends Factory
{
    protected $model = PayrollItem::class;

    public function definition(): array
    {
        return [
            'payroll_id' => Payroll::factory(),
            'item_type' => 'allowance',
            'item_name' => $this->faker->word() . ' allowance',
            'calculation_type' => 'fixed',
            'amount' => 500,
            'quantity' => 1,
            'sort_order' => 1,
        ];
    }
}
