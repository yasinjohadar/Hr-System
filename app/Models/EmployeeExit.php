<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeExit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'resignation_date',
        'last_working_day',
        'exit_type',
        'reason',
        'reason_ar',
        'status',
        'exit_interview_rating',
        'exit_interview_feedback',
        'suggestions',
        'exit_interview_completed',
        'assets_returned',
        'assets_notes',
        'handover_completed',
        'handover_notes',
        'handover_to',
        'documents_returned',
        'documents_notes',
        'final_settlement_completed',
        'final_settlement_amount',
        'final_settlement_date',
        'approved_by',
        'approved_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'resignation_date' => 'date',
        'last_working_day' => 'date',
        'final_settlement_date' => 'date',
        'exit_interview_completed' => 'boolean',
        'assets_returned' => 'boolean',
        'handover_completed' => 'boolean',
        'documents_returned' => 'boolean',
        'final_settlement_completed' => 'boolean',
        'final_settlement_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'exit_interview_rating' => 'integer',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع من تسلم المهام
     */
    public function handoverTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'handover_to');
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
     * Accessor لنوع إنهاء الخدمة بالعربية
     */
    public function getExitTypeNameArAttribute(): string
    {
        return match($this->exit_type) {
            'resignation' => 'استقالة',
            'termination' => 'إنهاء خدمة',
            'retirement' => 'تقاعد',
            'end_of_contract' => 'انتهاء عقد',
            'other' => 'أخرى',
            default => $this->exit_type,
        };
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'in_process' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * التحقق من اكتمال العملية
     */
    public function isComplete(): bool
    {
        return $this->exit_interview_completed &&
               $this->assets_returned &&
               $this->handover_completed &&
               $this->documents_returned &&
               $this->final_settlement_completed;
    }
}
