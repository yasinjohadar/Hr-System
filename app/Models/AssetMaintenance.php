<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'maintenance_type',
        'title',
        'scheduled_date',
        'actual_date',
        'cost',
        'description',
        'status',
        'next_maintenance_date',
        'service_provider',
        'service_provider_contact',
        'performed_by',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'actual_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * العلاقة مع الأصل
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor للحالة بالعربية
     */
    public function getStatusNameArAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'مجدولة',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
            'postponed' => 'مؤجلة',
            default => $this->status,
        };
    }

    /**
     * Accessor لنوع الصيانة بالعربية
     */
    public function getMaintenanceTypeNameArAttribute(): string
    {
        return match($this->maintenance_type) {
            'preventive' => 'وقائية',
            'corrective' => 'تصحيحية',
            'cleaning' => 'تنظيف',
            'upgrade' => 'ترقية',
            'inspection' => 'فحص',
            default => $this->maintenance_type,
        };
    }
}
