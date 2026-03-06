<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class EmployeeJobChange extends Model
{

    /**
     * أنواع التغييرات الوظيفية
     */
    public const CHANGE_TYPE_TRANSFER = 'transfer';
    public const CHANGE_TYPE_PROMOTION = 'promotion';
    public const CHANGE_TYPE_SALARY_CHANGE = 'salary_change';
    public const CHANGE_TYPE_DEMOTION = 'demotion';

    /**
     * حالات الطلب
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'employee_id',
        'change_type',
        'status',
        'effective_date',
        'reason',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'old_department_id',
        'new_department_id',
        'old_position_id',
        'new_position_id',
        'old_branch_id',
        'new_branch_id',
        'old_manager_id',
        'new_manager_id',
        'old_salary',
        'new_salary',
    ];

    /**
     * التحويلات
     */
    protected $casts = [
        'effective_date' => 'date',
        'approved_at' => 'datetime',
        'old_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع المستخدم الذي طلب التغيير
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * العلاقة مع المستخدم الذي وافق على التغيير
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * العلاقة مع القسم القديم
     */
    public function oldDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'old_department_id');
    }

    /**
     * العلاقة مع القسم الجديد
     */
    public function newDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'new_department_id');
    }

    /**
     * العلاقة مع المنصب القديم
     */
    public function oldPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'old_position_id');
    }

    /**
     * العلاقة مع المنصب الجديد
     */
    public function newPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'new_position_id');
    }

    /**
     * العلاقة مع الفرع القديم
     */
    public function oldBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'old_branch_id');
    }

    /**
     * العلاقة مع الفرع الجديد
     */
    public function newBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'new_branch_id');
    }

    /**
     * العلاقة مع المدير القديم
     */
    public function oldManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'old_manager_id');
    }

    /**
     * العلاقة مع المدير الجديد
     */
    public function newManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'new_manager_id');
    }

    /**
     * Scope للطلبات المعلقة
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope للطلبات المعتمدة
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope للطلبات المرفوضة
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope لطلبات موظف معين
     */
    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Accessor للحصول على اسم نوع التغيير بالعربية
     */
    public function getChangeTypeLabelAttribute(): string
    {
        return match($this->change_type) {
            self::CHANGE_TYPE_TRANSFER => 'نقل',
            self::CHANGE_TYPE_PROMOTION => 'ترقية',
            self::CHANGE_TYPE_SALARY_CHANGE => 'تعديل راتب',
            self::CHANGE_TYPE_DEMOTION => 'تنزيل',
            default => $this->change_type,
        };
    }

    /**
     * Accessor للحصول على اسم الحالة بالعربية
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_APPROVED => 'تمت الموافقة',
            self::STATUS_REJECTED => 'مرفوض',
            default => $this->status,
        };
    }

    /**
     * Accessor للحصول على لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * التحقق من أن الطلب يمكن تعديله
     */
    public function canBeEdited(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * التحقق من أن الطلب يمكن الموافقة عليه
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * التحقق من أن الطلب يمكن رفضه
     */
    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
