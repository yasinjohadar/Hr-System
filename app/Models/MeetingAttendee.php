<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'meeting_id',
        'employee_id',
        'status',
        'response_notes',
        'is_required',
        'notes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'invited' => 'مدعو',
            'accepted' => 'قبل',
            'declined' => 'رفض',
            'tentative' => 'مشكوك',
            'attended' => 'حضر',
            'absent' => 'غائب',
            default => $this->status,
        };
    }
}
