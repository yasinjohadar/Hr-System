<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceLocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'latitude',
        'longitude',
        'radius_meters',
        'address',
        'description',
        'is_active',
        'allowed_employees',
        'allowed_departments',
        'allowed_positions',
        'require_location',
        'created_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
        'allowed_employees' => 'array',
        'allowed_departments' => 'array',
        'allowed_positions' => 'array',
        'require_location' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($location) {
            if (empty($location->code)) {
                $location->code = 'LOC-' . strtoupper(Str::random(8));
            }
        });
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * التحقق من أن الموظف مسموح له بالحضور في هذا الموقع
     */
    public function isEmployeeAllowed(int $employeeId, ?int $departmentId = null, ?int $positionId = null): bool
    {
        // إذا كان الموقع غير نشط
        if (!$this->is_active) {
            return false;
        }

        // إذا كان الموقع مفتوح للجميع
        if (empty($this->allowed_employees) && empty($this->allowed_departments) && empty($this->allowed_positions)) {
            return true;
        }

        // التحقق من الموظفين المسموح لهم
        if (!empty($this->allowed_employees) && in_array($employeeId, $this->allowed_employees)) {
            return true;
        }

        // التحقق من الأقسام
        if (!empty($this->allowed_departments) && $departmentId && in_array($departmentId, $this->allowed_departments)) {
            return true;
        }

        // التحقق من المناصب
        if (!empty($this->allowed_positions) && $positionId && in_array($positionId, $this->allowed_positions)) {
            return true;
        }

        return false;
    }

    /**
     * التحقق من أن الإحداثيات ضمن نطاق الموقع
     */
    public function isWithinRadius(float $latitude, float $longitude): bool
    {
        $distance = $this->calculateDistance($this->latitude, $this->longitude, $latitude, $longitude);
        return $distance <= $this->radius_meters;
    }

    /**
     * حساب المسافة بين نقطتين (بالمتر)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // نصف قطر الأرض بالمتر

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
