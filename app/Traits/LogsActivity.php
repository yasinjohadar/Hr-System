<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * تسجيل النشاط تلقائياً
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('create');
        });

        static::updated(function ($model) {
            $model->logActivity('update');
        });

        static::deleted(function ($model) {
            $model->logActivity('delete');
        });
    }

    /**
     * إنشاء سجل التدقيق
     */
    public function logActivity($action)
    {
        if (!Auth::check()) {
            return;
        }

        $oldValues = null;
        $newValues = null;

        if ($action === 'update') {
            $oldValues = $this->getOriginal();
            $newValues = $this->getChanges();
        } elseif ($action === 'create') {
            $newValues = $this->getAttributes();
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'description' => $this->getActivityDescription($action),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'severity' => $this->getActivitySeverity($action),
        ]);
    }

    /**
     * الحصول على وصف النشاط
     */
    protected function getActivityDescription($action)
    {
        $modelName = class_basename($this);
        $actionAr = match($action) {
            'create' => 'إنشاء',
            'update' => 'تحديث',
            'delete' => 'حذف',
            default => $action,
        };

        if (method_exists($this, 'getActivityDescription')) {
            return $this->getActivityDescription($action);
        }

        return "$actionAr $modelName";
    }

    /**
     * الحصول على مستوى الخطورة
     */
    protected function getActivitySeverity($action)
    {
        return match($action) {
            'delete' => 'high',
            'create', 'update' => 'medium',
            default => 'low',
        };
    }
}

