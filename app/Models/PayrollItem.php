<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    protected $fillable = [
        'payroll_id',
        'item_type',
        'item_name',
        'item_name_ar',
        'component_code',
        'calculation_type',
        'amount',
        'percentage',
        'formula',
        'quantity',
        'unit_price',
        'description',
        'metadata',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'metadata' => 'array',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function salaryComponent(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class, 'component_code', 'code');
    }

    public function getItemTypeNameArAttribute(): string
    {
        return match($this->item_type) {
            'allowance' => 'بدل',
            'deduction' => 'خصم',
            'bonus' => 'مكافأة',
            'overtime' => 'ساعات إضافية',
            default => $this->item_type,
        };
    }
}
