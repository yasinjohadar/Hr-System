<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    protected $fillable = [
        'project_id',
        'employee_id',
        'role',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getRoleNameArAttribute(): string
    {
        return match ($this->role) {
            'member' => 'عضو فريق',
            'lead' => 'قائد فريق',
            'sponsor' => 'راعي / داعم',
            default => $this->role,
        };
    }
}
