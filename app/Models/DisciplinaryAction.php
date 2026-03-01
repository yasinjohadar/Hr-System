<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryAction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'description',
        'description_ar',
        'action_type',
        'severity_level',
        'deduction_amount',
        'suspension_days',
        'requires_approval',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'severity_level' => 'integer',
        'deduction_amount' => 'decimal:2',
        'suspension_days' => 'integer',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع مخالفات الموظفين
     */
    public function employeeViolations(): HasMany
    {
        return $this->hasMany(EmployeeViolation::class);
    }

    /**
     * العلاقة مع منشئ الإجراء
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor لنوع الإجراء بالعربية
     */
    public function getActionTypeNameArAttribute(): string
    {
        return match($this->action_type) {
            'verbal_warning' => 'تحذير شفهي',
            'written_warning' => 'تحذير كتابي',
            'final_warning' => 'إنذار نهائي',
            'deduction' => 'خصم',
            'suspension' => 'إيقاف',
            'termination' => 'إنهاء خدمة',
            default => $this->action_type,
        };
    }

    /**
     * Accessor لمستوى الخطورة بالعربية
     */
    public function getSeverityLevelNameArAttribute(): string
    {
        return match($this->severity_level) {
            1 => 'منخفض',
            2 => 'متوسط',
            3 => 'عالي',
            4 => 'عالي جداً',
            5 => 'حرج',
            default => $this->severity_level,
        };
    }
}
