<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'bank_name',
        'bank_name_ar',
        'account_number',
        'iban',
        'swift_code',
        'account_holder_name',
        'branch_name',
        'branch_address',
        'account_type',
        'currency_code',
        'is_primary',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // عند تعيين حساب كأساسي، إلغاء الأساسية من الحسابات الأخرى
        static::saving(function ($account) {
            if ($account->is_primary) {
                static::where('employee_id', $account->employee_id)
                    ->where('id', '!=', $account->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAccountTypeNameArAttribute(): string
    {
        return match($this->account_type) {
            'savings' => 'توفير',
            'current' => 'جاري',
            'salary' => 'راتب',
            default => $this->account_type,
        };
    }
}
