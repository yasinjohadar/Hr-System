<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'rule_code',
        'name',
        'name_ar',
        'rule_type',
        'threshold_minutes',
        'action_type',
        'deduction_amount',
        'deduction_percentage',
        'apply_to_all',
        'applicable_positions',
        'applicable_departments',
        'send_notification',
        'notification_delay_minutes',
        'is_active',
        'priority',
        'description',
        'created_by',
    ];

    protected $casts = [
        'threshold_minutes' => 'integer',
        'deduction_amount' => 'decimal:2',
        'deduction_percentage' => 'integer',
        'apply_to_all' => 'boolean',
        'applicable_positions' => 'array',
        'applicable_departments' => 'array',
        'send_notification' => 'boolean',
        'notification_delay_minutes' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($rule) {
            if (empty($rule->rule_code)) {
                $rule->rule_code = 'AR-' . strtoupper(\Illuminate\Support\Str::random(8));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRuleTypeNameArAttribute(): string
    {
        return match($this->rule_type) {
            'late' => 'تأخير',
            'absent' => 'غياب',
            'early_leave' => 'انصراف مبكر',
            'overtime' => 'ساعات إضافية',
            'break' => 'استراحة',
            'holiday' => 'عطلة',
            default => $this->rule_type,
        };
    }

    public function getActionTypeNameArAttribute(): string
    {
        return match($this->action_type) {
            'warning' => 'تحذير',
            'deduction' => 'خصم',
            'notification' => 'إشعار',
            'block' => 'حظر',
            default => $this->action_type,
        };
    }

    /**
     * التحقق من أن القاعدة تنطبق على موظف معين
     */
    public function appliesToEmployee(Employee $employee): bool
    {
        if ($this->apply_to_all) {
            return true;
        }

        if ($this->applicable_positions && in_array($employee->position_id, $this->applicable_positions)) {
            return true;
        }

        if ($this->applicable_departments && in_array($employee->department_id, $this->applicable_departments)) {
            return true;
        }

        return false;
    }
}
