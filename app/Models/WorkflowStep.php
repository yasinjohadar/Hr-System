<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workflow_id',
        'name',
        'name_ar',
        'step_order',
        'approver_type',
        'approver_id',
        'role_id',
        'is_required',
        'can_reject',
        'timeout_hours',
        'conditions',
    ];

    protected $casts = [
        'step_order' => 'integer',
        'is_required' => 'boolean',
        'can_reject' => 'boolean',
        'timeout_hours' => 'integer',
        'conditions' => 'array',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    public function getApproverTypeNameArAttribute(): string
    {
        return match($this->approver_type) {
            'user' => 'مستخدم',
            'role' => 'دور',
            'department_manager' => 'مدير القسم',
            'employee_manager' => 'مدير الموظف',
            'custom' => 'مخصص',
            default => $this->approver_type,
        };
    }
}
