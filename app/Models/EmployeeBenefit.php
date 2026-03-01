<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBenefit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'benefit_type_id',
        'value',
        'currency_id',
        'start_date',
        'end_date',
        'status',
        'notes',
        'approval_notes',
        'approval_date',
        'document_path',
        'approved_by',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approval_date' => 'date',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع نوع الميزة
     */
    public function benefitType(): BelongsTo
    {
        return $this->belongsTo(BenefitType::class);
    }

    /**
     * العلاقة مع العملة
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة مع من وافق
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'suspended' => 'معلق',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}
