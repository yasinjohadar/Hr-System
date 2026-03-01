<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Meeting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'meeting_code',
        'title',
        'title_ar',
        'description',
        'start_time',
        'end_time',
        'location',
        'meeting_link',
        'type',
        'status',
        'organizer_id',
        'agenda',
        'minutes',
        'action_items',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($meeting) {
            if (empty($meeting->meeting_code)) {
                $meeting->meeting_code = 'MTG-' . strtoupper(Str::random(8));
            }
        });
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'organizer_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'in_person' => 'حضوري',
            'virtual' => 'افتراضي',
            'hybrid' => 'مختلط',
            default => $this->type,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'مجدول',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'postponed' => 'مؤجل',
            default => $this->status,
        };
    }
}
