<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Workflow extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'description',
        'type',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($workflow) {
            if (empty($workflow->code)) {
                $workflow->code = 'WF-' . strtoupper(Str::random(8));
            }
        });
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('step_order');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameArAttribute(): string
    {
        $fromConfig = config("approval_workflows.types.{$this->type}.label_ar");

        return $fromConfig ?? match ($this->type) {
            'leave_request' => 'طلب إجازة',
            'expense_request' => 'طلب مصروف',
            'employee_job_change' => 'تغيير وظيفي',
            'ticket_request' => 'تذكرة',
            'project_time_entry' => 'وقت مشروع',
            'fund_transfer' => 'تحويل مالي',
            'task_approval' => 'موافقة مهمة',
            'performance_review' => 'تقييم الأداء',
            'overtime_request' => 'عمل إضافي',
            'custom' => 'مخصص',
            default => $this->type,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function allowedTypes(): array
    {
        $fromConfig = array_keys(config('approval_workflows.types', []));

        return array_values(array_unique(array_merge($fromConfig, [
            'task_approval',
            'performance_review',
            'custom',
        ])));
    }
}
