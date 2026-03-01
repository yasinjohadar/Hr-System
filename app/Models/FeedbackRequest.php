<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FeedbackRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_code',
        'employee_id',
        'feedback_type',
        'start_date',
        'end_date',
        'status',
        'instructions',
        'is_anonymous',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_anonymous' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($request) {
            if (empty($request->request_code)) {
                $request->request_code = 'FBR-' . strtoupper(Str::random(8));
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFeedbackTypeNameArAttribute(): string
    {
        return match($this->feedback_type) {
            '360_degree' => '360 درجة',
            'peer' => 'زملاء',
            'subordinate' => 'مرؤوسين',
            'self' => 'ذاتي',
            'custom' => 'مخصص',
            default => $this->feedback_type,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'active' => 'نشط',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}
