<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxSetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'type',
        'calculation_method',
        'rate',
        'min_amount',
        'max_amount',
        'slabs',
        'exemption_amount',
        'is_active',
        'effective_from',
        'effective_to',
        'description',
        'created_by',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'slabs' => 'array',
        'exemption_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * حساب الضريبة بناءً على الراتب
     */
    public function calculateTax(float $salary): float
    {
        if (!$this->is_active) {
            return 0;
        }

        // التحقق من التاريخ
        $now = now();
        if ($this->effective_from && $now->lt($this->effective_from)) {
            return 0;
        }
        if ($this->effective_to && $now->gt($this->effective_to)) {
            return 0;
        }

        // خصم مبلغ الإعفاء
        $taxableAmount = max(0, $salary - $this->exemption_amount);

        if ($taxableAmount <= 0) {
            return 0;
        }

        return match($this->calculation_method) {
            'percentage' => ($taxableAmount * $this->rate) / 100,
            'slab' => $this->calculateSlabTax($taxableAmount),
            'fixed' => $this->rate,
            default => 0,
        };
    }

    /**
     * حساب الضريبة بناءً على الشرائح
     */
    private function calculateSlabTax(float $amount): float
    {
        if (!$this->slabs || !is_array($this->slabs)) {
            return 0;
        }

        $totalTax = 0;
        foreach ($this->slabs as $slab) {
            $min = $slab['min'] ?? 0;
            $max = $slab['max'] ?? PHP_FLOAT_MAX;
            $rate = $slab['rate'] ?? 0;

            if ($amount > $min) {
                $taxableInSlab = min($amount, $max) - $min;
                $totalTax += ($taxableInSlab * $rate) / 100;
            }
        }

        return $totalTax;
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'income_tax' => 'ضريبة الدخل',
            'social_insurance' => 'التأمينات الاجتماعية',
            'health_insurance' => 'التأمين الصحي',
            'other' => 'أخرى',
            default => $this->type,
        };
    }
}
