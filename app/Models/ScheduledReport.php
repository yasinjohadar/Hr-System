<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReport extends Model
{
    protected $fillable = [
        'name',
        'report_type',
        'frequency',
        'recipients',
        'filters',
        'last_run_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
        'filters' => 'array',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
