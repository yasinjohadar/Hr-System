<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobVacancy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_ar',
        'code',
        'department_id',
        'position_id',
        'branch_id',
        'description',
        'description_ar',
        'requirements',
        'responsibilities',
        'employment_type',
        'experience_level',
        'years_of_experience',
        'education_level',
        'min_salary',
        'max_salary',
        'currency_id',
        'posted_date',
        'closing_date',
        'start_date',
        'status',
        'number_of_positions',
        'applications_count',
        'location',
        'is_remote',
        'benefits',
        'notes',
        'created_by',
        'hiring_manager_id',
        'is_active',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'posted_date' => 'date',
        'closing_date' => 'date',
        'start_date' => 'date',
        'number_of_positions' => 'integer',
        'applications_count' => 'integer',
        'years_of_experience' => 'integer',
        'is_remote' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع القسم
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * العلاقة مع المنصب
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * العلاقة مع الفرع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * العلاقة مع العملة
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة مع مدير التوظيف
     */
    public function hiringManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'hiring_manager_id');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع طلبات التوظيف
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * العلاقة مع المقابلات
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'published' => 'منشور',
            'closed' => 'مغلق',
            'filled' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * Accessor لنوع التوظيف بالعربية
     */
    public function getEmploymentTypeArAttribute(): string
    {
        return match($this->employment_type) {
            'full_time' => 'دوام كامل',
            'part_time' => 'دوام جزئي',
            'contract' => 'عقد',
            'intern' => 'تدريب',
            'freelance' => 'عمل حر',
            default => $this->employment_type,
        };
    }
}
