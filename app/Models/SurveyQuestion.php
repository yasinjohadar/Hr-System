<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'survey_id',
        'question_text',
        'question_text_ar',
        'question_type',
        'options',
        'question_order',
        'is_required',
        'help_text',
    ];

    protected $casts = [
        'options' => 'array',
        'question_order' => 'integer',
        'is_required' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function getQuestionTypeNameArAttribute(): string
    {
        return match($this->question_type) {
            'text' => 'نص',
            'textarea' => 'نص طويل',
            'radio' => 'اختيار واحد',
            'checkbox' => 'اختيار متعدد',
            'rating' => 'تقييم',
            'date' => 'تاريخ',
            'number' => 'رقم',
            default => $this->question_type,
        };
    }
}
