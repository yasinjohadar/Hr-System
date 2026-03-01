<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'assigned_date',
        'expected_return_date',
        'actual_return_date',
        'assignment_status',
        'condition_on_assignment',
        'condition_on_return',
        'assignment_notes',
        'return_notes',
        'assigned_by',
        'returned_by',
        'created_by',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    /**
     * العلاقة مع الأصل
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع من وزع
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * العلاقة مع من استرجع
     */
    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor لحالة التوزيع بالعربية
     */
    public function getAssignmentStatusNameArAttribute(): string
    {
        return match($this->assignment_status) {
            'active' => 'نشط',
            'returned' => 'مسترجع',
            'lost' => 'مفقود',
            'damaged' => 'معطل',
            default => $this->assignment_status,
        };
    }

    /**
     * Accessor لحالة الأصل عند التوزيع بالعربية
     */
    public function getConditionOnAssignmentNameArAttribute(): string
    {
        return match($this->condition_on_assignment) {
            'excellent' => 'ممتاز',
            'good' => 'جيد',
            'fair' => 'متوسط',
            'poor' => 'ضعيف',
            default => $this->condition_on_assignment,
        };
    }

    /**
     * Accessor لحالة الأصل عند الاسترجاع بالعربية
     */
    public function getConditionOnReturnNameArAttribute(): string
    {
        return match($this->condition_on_return) {
            'excellent' => 'ممتاز',
            'good' => 'جيد',
            'fair' => 'متوسط',
            'poor' => 'ضعيف',
            'damaged' => 'معطل',
            default => $this->condition_on_return ?? '-',
        };
    }
}
