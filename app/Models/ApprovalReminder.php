<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalReminder extends Model
{
    protected $fillable = [
        'workflow_instance_id',
        'reminder_level',
        'sent_at',
        'sent_to',
        'channel',
        'status',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function workflowInstance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_to');
    }
}
