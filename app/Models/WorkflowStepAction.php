<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStepAction extends Model
{
    protected $fillable = [
        'workflow_instance_id',
        'workflow_step_id',
        'user_id',
        'action',
        'comments',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionNameArAttribute(): string
    {
        return match ($this->action) {
            'approved' => 'موافقة',
            'rejected' => 'رفض',
            default => $this->action,
        };
    }
}
