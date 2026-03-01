<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryComponent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'type',
        'calculation_type',
        'default_value',
        'percentage',
        'formula',
        'min_value',
        'max_value',
        'is_taxable',
        'is_required',
        'apply_to_all',
        'applicable_positions',
        'applicable_departments',
        'is_active',
        'sort_order',
        'description',
        'created_by',
    ];

    protected $casts = [
        'default_value' => 'decimal:2',
        'percentage' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_required' => 'boolean',
        'apply_to_all' => 'boolean',
        'applicable_positions' => 'array',
        'applicable_departments' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'allowance' => 'بدل',
            'deduction' => 'خصم',
            'bonus' => 'مكافأة',
            'overtime' => 'ساعات إضافية',
            default => $this->type,
        };
    }

    public function getCalculationTypeNameArAttribute(): string
    {
        return match($this->calculation_type) {
            'fixed' => 'ثابت',
            'percentage' => 'نسبة مئوية',
            'formula' => 'صيغة',
            'attendance_based' => 'بناءً على الحضور',
            'leave_based' => 'بناءً على الإجازات',
            default => $this->calculation_type,
        };
    }
}
