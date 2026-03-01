<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackResponse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'feedback_request_id',
        'respondent_id',
        'relationship_type',
        'ratings',
        'strengths',
        'weaknesses',
        'recommendations',
        'comments',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'ratings' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function feedbackRequest(): BelongsTo
    {
        return $this->belongsTo(FeedbackRequest::class);
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'respondent_id');
    }

    public function getRelationshipTypeNameArAttribute(): string
    {
        return match($this->relationship_type) {
            'manager' => 'مدير',
            'peer' => 'زميل',
            'subordinate' => 'مرؤوس',
            'self' => 'ذاتي',
            'other' => 'آخر',
            default => $this->relationship_type,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'submitted' => 'مقدم',
            'draft' => 'مسودة',
            default => $this->status,
        };
    }
}
