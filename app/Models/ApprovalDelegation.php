<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalDelegation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delegator_id',
        'delegate_id',
        'workflow_types',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'workflow_types' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function delegator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegator_id');
    }

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegate_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->start_date <= now()
            && $this->end_date >= now();
    }

    public function canDelegate(string $workflowType): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if (empty($this->workflow_types)) {
            return true; // جميع الأنواع
        }

        return in_array($workflowType, $this->workflow_types);
    }

    public static function getActiveDelegationForUser(User $delegator, string $workflowType): ?self
    {
        return self::where('delegator_id', $delegator->id)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query) use ($workflowType) {
                $query->whereNull('workflow_types')
                    ->orWhereJsonContains('workflow_types', $workflowType);
            })
            ->first();
    }
}
