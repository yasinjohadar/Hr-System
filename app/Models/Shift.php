<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shift_code',
        'name',
        'name_ar',
        'start_time',
        'end_time',
        'duration_hours',
        'grace_period_minutes',
        'break_duration_minutes',
        'has_night_shift',
        'night_shift_start',
        'night_shift_end',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'overtime_rate',
        'overtime_threshold_minutes',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'night_shift_start' => 'string',
        'night_shift_end' => 'string',
        'duration_hours' => 'integer',
        'grace_period_minutes' => 'integer',
        'break_duration_minutes' => 'integer',
        'has_night_shift' => 'boolean',
        'monday' => 'boolean',
        'tuesday' => 'boolean',
        'wednesday' => 'boolean',
        'thursday' => 'boolean',
        'friday' => 'boolean',
        'saturday' => 'boolean',
        'sunday' => 'boolean',
        'overtime_rate' => 'decimal:2',
        'overtime_threshold_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(ShiftAssignment::class)->where('is_active', true);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الحصول على أيام العمل
     */
    public function getWorkingDaysAttribute(): array
    {
        $days = [];
        if ($this->monday) $days[] = 'monday';
        if ($this->tuesday) $days[] = 'tuesday';
        if ($this->wednesday) $days[] = 'wednesday';
        if ($this->thursday) $days[] = 'thursday';
        if ($this->friday) $days[] = 'friday';
        if ($this->saturday) $days[] = 'saturday';
        if ($this->sunday) $days[] = 'sunday';
        return $days;
    }

    /**
     * التحقق من أن اليوم هو يوم عمل
     */
    public function isWorkingDay(string $dayName): bool
    {
        return match($dayName) {
            'monday' => $this->monday,
            'tuesday' => $this->tuesday,
            'wednesday' => $this->wednesday,
            'thursday' => $this->thursday,
            'friday' => $this->friday,
            'saturday' => $this->saturday,
            'sunday' => $this->sunday,
            default => false,
        };
    }
}
