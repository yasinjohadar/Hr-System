<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectTimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'employee_id',
        'task_id',
        'worked_date',
        'hours',
        'description',
        'status',
        'approved_at',
        'approved_by',
        'created_by',
    ];

    protected $casts = [
        'worked_date' => 'date',
        'hours' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
