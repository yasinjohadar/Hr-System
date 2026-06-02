<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditableAction
{
    public static function log(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        array $meta = [],
        string $severity = 'info'
    ): void {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? $model::class : null,
            'model_id' => $model?->getKey(),
            'description' => $description ?? $action,
            'new_values' => $meta ?: null,
            'ip_address' => request()?->ip(),
            'user_agent' => (string) request()?->userAgent(),
            'url' => request()?->fullUrl(),
            'severity' => $severity,
        ]);
    }
}
