<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBudgetOverride extends Model
{
    protected $fillable = [
        'project_id',
        'previous_budget',
        'requested_stages_total',
        'reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'previous_budget' => 'decimal:2',
        'requested_stages_total' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
