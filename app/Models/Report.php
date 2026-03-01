<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'report_type',
        'description',
        'criteria',
        'format',
        'file_path',
        'total_records',
        'summary',
        'status',
        'generated_date',
        'period_start',
        'period_end',
        'notes',
        'is_public',
        'is_scheduled',
        'schedule_frequency',
        'created_by',
    ];

    protected $casts = [
        'criteria' => 'array',
        'summary' => 'array',
        'generated_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'total_records' => 'integer',
        'is_public' => 'boolean',
        'is_scheduled' => 'boolean',
    ];

    /**
     * العلاقة مع منشئ التقرير
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor لنوع التقرير بالعربية
     */
    public function getReportTypeNameArAttribute(): string
    {
        return match($this->report_type) {
            'employees' => 'تقارير الموظفين',
            'attendance' => 'تقارير الحضور',
            'salaries' => 'تقارير الرواتب',
            'leaves' => 'تقارير الإجازات',
            'performance' => 'تقارير التقييمات',
            'training' => 'تقارير التدريب',
            'recruitment' => 'تقارير التوظيف',
            'benefits' => 'تقارير المزايا',
            'dashboard' => 'التقارير الشاملة',
            default => $this->report_type,
        };
    }
}
