<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyAcknowledgment extends Model
{
    protected $fillable = [
        'policy_id',
        'employee_id',
        'acknowledged_at',
        'ip_address',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
