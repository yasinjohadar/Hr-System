<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'name_ar',
        'sort_order',
        'start_date',
        'end_date',
        'allocated_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'allocated_amount' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectStageMember::class);
    }

    public function memberEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_stage_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function fundTransfers(): HasMany
    {
        return $this->hasMany(FundTransfer::class);
    }

    public function getStatusNameArAttribute(): string
    {
        return match ($this->status) {
            'planned' => 'مخططة',
            'active' => 'نشطة',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
            default => $this->status,
        };
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_ar ?: $this->name;
    }
}
