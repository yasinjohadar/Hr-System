<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Survey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'survey_code',
        'title',
        'title_ar',
        'description',
        'type',
        'start_date',
        'end_date',
        'status',
        'is_anonymous',
        'target_audience',
        'target_ids',
        'total_responses',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_anonymous' => 'boolean',
        'target_ids' => 'array',
        'total_responses' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($survey) {
            if (empty($survey->survey_code)) {
                $survey->survey_code = 'SRV-' . strtoupper(Str::random(8));
            }
        });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('question_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameArAttribute(): string
    {
        return match($this->type) {
            'satisfaction' => 'رضا',
            'climate' => 'مناخ عمل',
            'engagement' => 'مشاركة',
            'exit' => 'إنهاء خدمة',
            'custom' => 'مخصص',
            default => $this->type,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'active' => 'نشط',
            'closed' => 'مغلق',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}
