<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'iban',
        'currency_id',
        'balance',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisplayLabelAttribute(): string
    {
        return "{$this->name} — {$this->bank_name} ({$this->account_number})";
    }
}
