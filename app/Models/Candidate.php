<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'candidate_code',
        'first_name',
        'last_name',
        'full_name',
        'national_id',
        'date_of_birth',
        'gender',
        'marital_status',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'country_id',
        'postal_code',
        'current_position',
        'current_company',
        'years_of_experience',
        'education_level',
        'university',
        'major',
        'graduation_year',
        'cv_path',
        'cover_letter_path',
        'photo',
        'skills',
        'languages',
        'certifications',
        'notes',
        'status',
        'rating',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'years_of_experience' => 'integer',
        'graduation_year' => 'integer',
        'rating' => 'integer',
        'is_active' => 'boolean',
        'skills' => 'array',
        'languages' => 'array',
    ];

    /**
     * العلاقة مع الدولة
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع طلبات التوظيف
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * العلاقة مع المقابلات
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'new' => 'جديد',
            'contacted' => 'تم التواصل',
            'screening' => 'قيد الفحص',
            'interviewed' => 'تمت المقابلة',
            'offered' => 'تم العرض',
            'hired' => 'تم التوظيف',
            'rejected' => 'مرفوض',
            'withdrawn' => 'انسحب',
            default => $this->status,
        };
    }
}
