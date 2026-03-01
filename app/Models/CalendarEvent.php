<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'start_date',
        'end_date',
        'type',
        'created_by',
        'employee_id',
        'department_id',
        'color',
        'is_all_day',
        'is_reminder',
        'reminder_minutes',
        'reminder_sent_at',
        'is_recurring',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'recurrence_end_date' => 'date',
        'is_all_day' => 'boolean',
        'is_reminder' => 'boolean',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'reminder_minutes' => 'integer',
        'recurrence_interval' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'personal' => 'شخصي',
            'public' => 'عام',
            'department' => 'قسم',
            'employee' => 'موظف',
            'all' => 'للجميع',
            default => $this->type,
        };
    }

    /**
     * التحقق من أن المستخدم يمكنه رؤية هذا الحدث
     */
    public function canViewBy(User $user): bool
    {
        // إذا كان منشئ الحدث
        if ($this->created_by === $user->id) {
            return true;
        }

        // إذا كان الحدث عام أو للجميع
        if (in_array($this->type, ['public', 'all'])) {
            return true;
        }

        // إذا كان الحدث لقسم معين
        if ($this->type === 'department' && $this->department_id) {
            $employee = $user->employee;
            if ($employee && $employee->department_id === $this->department_id) {
                return true;
            }
        }

        // إذا كان الحدث لموظف معين
        if ($this->type === 'employee' && $this->employee_id) {
            $employee = $user->employee;
            if ($employee && $employee->id === $this->employee_id) {
                return true;
            }
        }

        // إذا كان الحدث شخصي
        if ($this->type === 'personal') {
            $employee = $user->employee;
            if ($employee && $employee->id === $this->employee_id) {
                return true;
            }
        }

        return false;
    }
}
