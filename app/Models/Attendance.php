<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in',
        'check_out',
        'expected_check_in',
        'expected_check_out',
        'hours_worked',
        'overtime_minutes',
        'late_minutes',
        'early_leave_minutes',
        'status',
        'notes',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_location',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_location',
        'attendance_location_id',
        'location_verified',
        'created_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'hours_worked' => 'integer',
        'overtime_minutes' => 'integer',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'location_verified' => 'boolean',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع موقع الحضور
     */
    public function attendanceLocation(): BelongsTo
    {
        return $this->belongsTo(AttendanceLocation::class);
    }

    /**
     * العلاقة مع الاستراحات
     */
    public function breaks(): HasMany
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    /**
     * حساب ساعات العمل تلقائياً
     */
    public function calculateHours(): void
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->check_in);
            $checkOut = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->check_out);
            
            // حساب ساعات العمل بالدقائق
            $this->hours_worked = $checkIn->diffInMinutes($checkOut);
            
            // حساب التأخير
            if ($this->expected_check_in) {
                $expectedCheckIn = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->expected_check_in);
                if ($checkIn->gt($expectedCheckIn)) {
                    $this->late_minutes = $checkIn->diffInMinutes($expectedCheckIn);
                } else {
                    $this->late_minutes = 0;
                }
            }
            
            // حساب الانصراف المبكر
            if ($this->expected_check_out) {
                $expectedCheckOut = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->expected_check_out);
                if ($checkOut->lt($expectedCheckOut)) {
                    $this->early_leave_minutes = $checkOut->diffInMinutes($expectedCheckOut);
                } else {
                    $this->early_leave_minutes = 0;
                }
            }
            
            // حساب الساعات الإضافية (إذا تجاوزت ساعات العمل المتوقعة)
            if ($this->expected_check_in && $this->expected_check_out) {
                $expectedHours = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->expected_check_in)
                    ->diffInMinutes(Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->expected_check_out));
                
                if ($this->hours_worked > $expectedHours) {
                    $this->overtime_minutes = $this->hours_worked - $expectedHours;
                } else {
                    $this->overtime_minutes = 0;
                }
            }
        } else {
            // إعادة تعيين القيم إذا لم يكن هناك دخول أو خروج
            $this->hours_worked = 0;
            $this->late_minutes = 0;
            $this->early_leave_minutes = 0;
            $this->overtime_minutes = 0;
        }
    }

    /**
     * الحصول على ساعات العمل بصيغة ساعة:دقيقة
     */
    public function getHoursWorkedFormattedAttribute(): string
    {
        $hours = floor($this->hours_worked / 60);
        $minutes = $this->hours_worked % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * الحصول على حالة الحضور بالعربية
     */
    public function getStatusArAttribute(): string
    {
        return match($this->status) {
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'half_day' => 'نصف يوم',
            'on_leave' => 'في إجازة',
            'holiday' => 'عطلة',
            default => $this->status
        };
    }
}
