<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_code',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'employee_id',
        'assigned_to',
        'resolved_at',
        'resolution_notes',
        'satisfaction_rating',
        'satisfaction_feedback',
        'created_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_code)) {
                $ticket->ticket_code = 'TKT-' . strtoupper(Str::random(8));
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryNameArAttribute(): string
    {
        return match($this->category) {
            'technical' => 'تقني',
            'hr' => 'موارد بشرية',
            'it' => 'تقنية معلومات',
            'facilities' => 'مرافق',
            'other' => 'أخرى',
            default => $this->category,
        };
    }

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

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'open' => 'مفتوح',
            'in_progress' => 'قيد التنفيذ',
            'resolved' => 'محلول',
            'closed' => 'مغلق',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}
