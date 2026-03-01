<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'template_id',
        'title',
        'title_ar',
        'description',
        'task_type',
        'task_order',
        'is_required',
        'estimated_duration_minutes',
        'instructions',
        'assigned_to_role',
        'assigned_to_employee',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'task_order' => 'integer',
        'estimated_duration_minutes' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function assignedToEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTaskTypeNameArAttribute(): string
    {
        return match($this->task_type) {
            'document' => 'مستند',
            'training' => 'تدريب',
            'meeting' => 'اجتماع',
            'equipment' => 'معدات',
            'access' => 'وصول',
            'other' => 'أخرى',
            default => $this->task_type,
        };
    }

}
