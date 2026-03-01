<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BenefitType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'description',
        'description_ar',
        'type',
        'default_value',
        'currency_id',
        'is_taxable',
        'is_mandatory',
        'requires_approval',
        'max_employees',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'default_value' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_mandatory' => 'boolean',
        'requires_approval' => 'boolean',
        'max_employees' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * العلاقة مع العملة
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع مزايا الموظفين
     */
    public function employeeBenefits(): HasMany
    {
        return $this->hasMany(EmployeeBenefit::class);
    }

    /**
     * Accessor لنوع الميزة بالعربية
     */
    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'monetary' => 'نقدي',
            'in_kind' => 'عيني',
            'service' => 'خدمة',
            'insurance' => 'تأمين',
            'allowance' => 'بدل',
            default => $this->type,
        };
    }
}
