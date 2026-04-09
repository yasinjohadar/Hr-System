<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_code',
        'name',
        'name_ar',
        'category',
        'type',
        'manufacturer',
        'model',
        'serial_number',
        'barcode',
        'purchase_date',
        'purchase_cost',
        'current_value',
        'status',
        'branch_id',
        'department_id',
        'warranty_expiry',
        'description',
        'notes',
        'photo',
        'location',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    /**
     * العلاقة مع الفرع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * العلاقة مع القسم
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع توزيعات الأصول
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    /**
     * العلاقة مع التوزيع النشط الحالي
     */
    public function currentAssignment(): HasMany
    {
        return $this->hasMany(AssetAssignment::class)->where('assignment_status', 'active')->latest();
    }

    /**
     * العلاقة مع صيانة الأصول
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function lifecycleEvents(): HasMany
    {
        return $this->hasMany(AssetLifecycleEvent::class)->orderByDesc('occurred_at')->orderByDesc('id');
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'available' => 'متاح',
            'assigned' => 'موزع',
            'maintenance' => 'قيد الصيانة',
            'damaged' => 'معطل',
            'lost' => 'مفقود',
            'disposed' => 'مستبعد',
            default => $this->status,
        };
    }

    /**
     * Accessor للفئة بالعربية
     */
    public function getCategoryNameArAttribute(): string
    {
        return match($this->category) {
            'technical' => 'تقني',
            'office' => 'مكتبي',
            'mobile' => 'متنقل',
            'other' => 'أخرى',
            default => $this->category,
        };
    }

    /**
     * التحقق من أن الأصل متاح للتوزيع
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && 
               !$this->assignments()->where('assignment_status', 'active')->exists();
    }

    /**
     * الحصول على الموظف الحالي الذي يستخدم الأصل
     */
    public function currentEmployee()
    {
        $assignment = $this->assignments()
            ->where('assignment_status', 'active')
            ->with('employee')
            ->latest()
            ->first();
        
        return $assignment?->employee;
    }
}
