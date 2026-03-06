<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Contract extends Model
{
    protected $fillable = [
        'employee_id',
        'contract_type',
        'start_date',
        'end_date',
        'status',
        'notes',
        'document_path',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reminder_sent_at' => 'datetime',
    ];

    public const TYPE_FIXED_TERM = 'fixed_term';
    public const TYPE_PERMANENT = 'permanent';
    public const TYPE_TRIAL = 'trial';
    public const TYPE_PROJECT = 'project';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_RENEWED = 'renewed';
    public const STATUS_TERMINATED = 'terminated';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * عقود نشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * عقود تنتهي خلال X يوم من اليوم
     */
    public function scopeExpiringInDays($query, int $days)
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays($days);
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $endDate);
    }

    /**
     * عقود منتهية (end_date في الماضي أو status expired)
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_EXPIRED)
                ->orWhereDate('end_date', '<', Carbon::today());
        });
    }

    /**
     * عدد الأيام المتبقية حتى انتهاء العقد
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }
        return (int) Carbon::today()->diffInDays($this->end_date, false);
    }

    public function getContractTypeLabelAttribute(): string
    {
        return match ($this->contract_type) {
            self::TYPE_FIXED_TERM => 'محدد المدة',
            self::TYPE_PERMANENT => 'دائم',
            self::TYPE_TRIAL => 'تجريبي',
            self::TYPE_PROJECT => 'مشروع',
            default => $this->contract_type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'نشط',
            self::STATUS_EXPIRED => 'منتهي',
            self::STATUS_RENEWED => 'تم تجديده',
            self::STATUS_TERMINATED => 'منهي',
            default => $this->status,
        };
    }
}
