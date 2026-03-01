<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViolationType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'description',
        'description_ar',
        'severity_level',
        'requires_warning',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'severity_level' => 'integer',
        'requires_warning' => 'boolean',
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
     * العلاقة مع منشئ النوع
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
