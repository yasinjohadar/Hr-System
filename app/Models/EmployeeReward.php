<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmployeeReward extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reward_code',
        'employee_id',
        'reward_type_id',
        'title',
        'description',
        'reward_date',
        'reason',
        'monetary_value',
        'currency_id',
        'points',
        'status',
        'awarded_by',
        'awarded_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'reward_date' => 'date',
        'monetary_value' => 'decimal:2',
        'points' => 'integer',
        'awarded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($reward) {
            if (empty($reward->reward_code)) {
                $reward->reward_code = 'RWD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function rewardType(): BelongsTo
    {
        return $this->belongsTo(RewardType::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getReasonNameArAttribute(): string
    {
        return match($this->reason) {
            'performance' => 'أداء',
            'achievement' => 'إنجاز',
            'milestone' => 'معلم',
            'recognition' => 'اعتراف',
            'other' => 'أخرى',
            default => $this->reason,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'awarded' => 'ممنوح',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}
