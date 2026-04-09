<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AssetLifecycleAttachment extends Model
{
    protected $fillable = [
        'asset_lifecycle_event_id',
        'file_path',
        'original_name',
        'mime',
        'uploaded_by',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(AssetLifecycleEvent::class, 'asset_lifecycle_event_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDiskUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }
}
