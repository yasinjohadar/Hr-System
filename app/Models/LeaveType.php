<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'description',
        'max_days',
        'is_paid',
        'requires_approval',
        'carry_forward',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'max_days' => 'integer',
        'is_paid' => 'boolean',
        'requires_approval' => 'boolean',
        'carry_forward' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع طلبات الإجازات
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * العلاقة مع أرصدة الإجازات
     */
    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }
}
