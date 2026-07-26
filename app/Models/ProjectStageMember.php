<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectStageMember extends Model
{
    protected $fillable = [
        'project_stage_id',
        'employee_id',
        'role',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class, 'project_stage_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getRoleNameArAttribute(): string
    {
        return match ($this->role) {
            'member' => 'عضو',
            'lead' => 'قائد',
            'sponsor' => 'راعي',
            default => $this->role ?: 'عضو',
        };
    }
}
