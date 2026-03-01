<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSkill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'skill_name',
        'skill_name_ar',
        'proficiency_level',
        'years_of_experience',
        'acquired_date',
        'description',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'acquired_date' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'years_of_experience' => 'integer',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع من تحقق من المهارة
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor لمستوى الكفاءة بالعربية
     */
    public function getProficiencyLevelNameArAttribute(): string
    {
        return match($this->proficiency_level) {
            'beginner' => 'مبتدئ',
            'intermediate' => 'متوسط',
            'advanced' => 'متقدم',
            'expert' => 'خبير',
            default => $this->proficiency_level,
        };
    }
}
