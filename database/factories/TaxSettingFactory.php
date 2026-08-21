<?php

namespace Database\Factories;

use App\Models\TaxSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxSetting>
 */
class TaxSettingFactory extends Factory
{
    protected $model = TaxSetting::class;

    public function definition(): array
    {
        return [
            'name' => 'Income tax ' . $this->faker->unique()->word(),
            'name_ar' => 'ضريبة الدخل',
            'code' => strtoupper($this->faker->unique()->bothify('TAX-###')),
            'type' => 'income_tax',
            'calculation_method' => 'percentage',
            'rate' => 10,
            'exemption_amount' => 0,
            'is_active' => true,
            // تُترك فارغة حتى لا يُبطل calculateTax() الضريبة بسبب النطاق الزمني
            'effective_from' => null,
            'effective_to' => null,
        ];
    }

    public function incomeTax(float $rate): static
    {
        return $this->state(fn () => ['type' => 'income_tax', 'rate' => $rate]);
    }

    public function socialInsurance(float $rate): static
    {
        return $this->state(fn () => ['type' => 'social_insurance', 'rate' => $rate]);
    }

    public function healthInsurance(float $rate): static
    {
        return $this->state(fn () => ['type' => 'health_insurance', 'rate' => $rate]);
    }

    public function withExemption(float $amount): static
    {
        return $this->state(fn () => ['exemption_amount' => $amount]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
