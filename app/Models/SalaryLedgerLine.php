<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryLedgerLine extends Model
{
    public const DEDUCTION_SIDE_TYPES = ['deduction', 'advance_recovery', 'loan_installment'];

    public const POSITIVE_SIDE_TYPES = ['allowance', 'bonus', 'overtime'];

    protected $fillable = [
        'salary_id',
        'line_type',
        'label',
        'label_ar',
        'amount',
        'employee_advance_id',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function salary(): BelongsTo
    {
        return $this->belongsTo(Salary::class);
    }

    public function employeeAdvance(): BelongsTo
    {
        return $this->belongsTo(EmployeeAdvance::class, 'employee_advance_id');
    }

    public function isDeductionSide(): bool
    {
        return in_array($this->line_type, self::DEDUCTION_SIDE_TYPES, true);
    }

    public function getLineTypeNameArAttribute(): string
    {
        return match ($this->line_type) {
            'allowance' => 'بدل',
            'bonus' => 'مكافأة',
            'deduction' => 'خصم',
            'advance_recovery' => 'استرداد سلفة',
            'loan_installment' => 'قسط قرض',
            'overtime' => 'ساعات إضافية',
            'other' => 'أخرى',
            default => $this->line_type,
        };
    }
}
