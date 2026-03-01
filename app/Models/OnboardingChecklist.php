<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingChecklist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'process_id',
        'task_id',
        'status',
        'due_date',
        'completed_date',
        'completed_by',
        'notes',
        'completion_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(OnboardingProcess::class, 'process_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OnboardingTask::class, 'task_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
