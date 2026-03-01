<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AttendanceBreak extends Model
{
    protected $fillable = [
        'attendance_id',
        'break_type',
        'break_start',
        'break_end',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'break_start' => 'string',
        'break_end' => 'string',
        'duration_minutes' => 'integer',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * حساب مدة الاستراحة تلقائياً
     */
    public function calculateDuration(): void
    {
        if ($this->break_start && $this->break_end) {
            $start = Carbon::parse($this->break_start);
            $end = Carbon::parse($this->break_end);
            $this->duration_minutes = $start->diffInMinutes($end);
        } else {
            $this->duration_minutes = 0;
        }
    }

    public function getBreakTypeNameArAttribute(): string
    {
        return match($this->break_type) {
            'lunch' => 'غداء',
            'coffee' => 'قهوة',
            'prayer' => 'صلاة',
            'other' => 'أخرى',
            default => $this->break_type,
        };
    }
}
