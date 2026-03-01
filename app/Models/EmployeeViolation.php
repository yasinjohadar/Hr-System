<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmployeeViolation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'violation_code',
        'employee_id',
        'violation_type_id',
        'disciplinary_action_id',
        'violation_date',
        'description',
        'description_ar',
        'witnesses',
        'employee_response',
        'status',
        'severity',
        'reported_by',
        'investigated_by',
        'investigation_date',
        'investigation_notes',
        'action_date',
        'action_notes',
        'approved_by',
        'approval_date',
        'resolution_notes',
        'resolution_date',
        'attendance_id',
        'leave_request_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'investigation_date' => 'date',
        'action_date' => 'date',
        'approval_date' => 'date',
        'resolution_date' => 'date',
    ];

    /**
     * Boot method لتوليد رقم المخالفة تلقائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($violation) {
            if (empty($violation->violation_code)) {
                $violation->violation_code = 'VIO-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع نوع المخالفة
     */
    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    /**
     * العلاقة مع الإجراء التأديبي
     */
    public function disciplinaryAction(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryAction::class);
    }

    /**
     * العلاقة مع من أبلغ عن المخالفة
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * العلاقة مع من قام بالتحقيق
     */
    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }

    /**
     * العلاقة مع من وافق على الإجراء
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * العلاقة مع الحضور (إن وجد)
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * العلاقة مع الإجازة (إن وجد)
     */
    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
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
            'pending' => 'قيد الانتظار',
            'investigating' => 'قيد التحقيق',
            'confirmed' => 'مؤكد',
            'dismissed' => 'مرفوض',
            'resolved' => 'محلول',
            default => $this->status,
        };
    }

    /**
     * Accessor للخطورة بالعربية
     */
    public function getSeverityNameArAttribute(): string
    {
        return match($this->severity) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
            default => $this->severity,
        };
    }
}
