<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class OvertimeRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'attendance_id',
        'overtime_date',
        'start_time',
        'end_time',
        'overtime_minutes',
        'overtime_hours',
        'overtime_type',
        'rate_multiplier',
        'hourly_rate',
        'overtime_amount',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'payroll_id',
        'reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
        'overtime_minutes' => 'integer',
        'overtime_hours' => 'decimal:2',
        'rate_multiplier' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getOvertimeTypeNameArAttribute(): string
    {
        return match($this->overtime_type) {
            'regular' => 'عادي',
            'holiday' => 'عطلة',
            'night' => 'ليلي',
            'weekend' => 'عطلة نهاية الأسبوع',
            default => $this->overtime_type,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'paid' => 'مدفوع',
            default => $this->status,
        };
    }

    /**
     * حساب مبلغ الساعات الإضافية
     */
    public function calculateAmount(): void
    {
        if ($this->hourly_rate && $this->overtime_hours) {
            $this->overtime_amount = $this->hourly_rate * $this->overtime_hours * $this->rate_multiplier;
        }
    }
}
