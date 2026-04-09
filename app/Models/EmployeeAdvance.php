<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeAdvance extends Model
{
    protected $fillable = [
        'employee_id',
        'principal_amount',
        'remaining_balance',
        'description',
        'granted_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'granted_at' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ledgerLines(): HasMany
    {
        return $this->hasMany(SalaryLedgerLine::class, 'employee_advance_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
