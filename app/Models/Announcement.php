<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'publish_date',
        'expiry_date',
        'status',
        'target_type',
        'department_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date' => 'date',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const TARGET_ALL = 'all';
    public const TARGET_DEPARTMENT = 'department';
    public const TARGET_BRANCH = 'branch';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * هل الإعلان ظاهر حالياً للموظفين (منشور ولم ينتهِ)
     */
    public function scopeVisible($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('publish_date')->orWhere('publish_date', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString());
            });
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_PUBLISHED => 'منشور',
            self::STATUS_ARCHIVED => 'مؤرشف',
            default => $this->status,
        };
    }

    public function getTargetTypeLabelAttribute(): string
    {
        return match ($this->target_type) {
            self::TARGET_ALL => 'الجميع',
            self::TARGET_DEPARTMENT => 'قسم محدد',
            self::TARGET_BRANCH => 'فرع محدد',
            default => $this->target_type,
        };
    }
}
