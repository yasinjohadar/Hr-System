<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_code',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'project_id',
        'department_id',
        'created_by',
        'start_date',
        'due_date',
        'completed_date',
        'status',
        'priority',
        'progress',
        'estimated_hours',
        'actual_hours',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_date' => 'date',
        'progress' => 'integer',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
    ];

    /**
     * Boot method لتوليد رقم المهمة تلقائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->task_code)) {
                $task->task_code = 'TASK-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * العلاقة مع المشروع
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * العلاقة مع القسم
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * العلاقة مع منشئ المهمة
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع تعيينات المهام
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function projectTimeEntries(): HasMany
    {
        return $this->hasMany(ProjectTimeEntry::class);
    }

    /**
     * العلاقة مع الموظفين المعينين (Many-to-Many عبر TaskAssignment)
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'task_assignments')
                    ->withPivot('assigned_date', 'due_date', 'status', 'progress', 'notes')
                    ->withTimestamps();
    }

    /**
     * العلاقة مع التعليقات
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * العلاقة مع المرفقات
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'in_review' => 'قيد المراجعة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'on_hold' => 'معلق',
            default => $this->status,
        };
    }

    /**
     * Accessor للأولوية بالعربية
     */
    public function getPriorityNameArAttribute(): string
    {
        return match($this->priority) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
            default => $this->priority,
        };
    }
}
