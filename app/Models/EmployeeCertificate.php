<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmployeeCertificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'certificate_name',
        'certificate_name_ar',
        'issuing_organization',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'does_not_expire',
        'file_path',
        'status',
        'description',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'does_not_expire' => 'boolean',
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
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'expired' => 'منتهي',
            'pending' => 'قيد الانتظار',
            default => $this->status,
        };
    }

    /**
     * التحقق من انتهاء الصلاحية
     */
    public function isExpired(): bool
    {
        if ($this->does_not_expire) {
            return false;
        }
        
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Scope للشهادات المنتهية الصلاحية قريباً
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('does_not_expire', false)
                    ->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('expiry_date', '>', Carbon::now())
                    ->where('status', 'active');
    }
}
