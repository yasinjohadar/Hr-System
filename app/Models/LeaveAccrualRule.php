<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveAccrualRule extends Model
{
    protected $fillable = [
        'leave_type_id',
        'country_id',
        'branch_id',
        'accrual_days_per_month',
        'max_balance',
        'is_active',
    ];

    protected $casts = [
        'accrual_days_per_month' => 'decimal:2',
        'max_balance' => 'integer',
        'is_active' => 'boolean',
    ];

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
