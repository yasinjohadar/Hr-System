<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetLifecycleAttachment;
use App\Models\AssetLifecycleEvent;
use App\Models\AssetMaintenance;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class AssetLifecycleRecorder
{
    public function recordCreated(Asset $asset, ?int $userId = null, bool $forBackfill = false): void
    {
        $meta = [
            'asset_code' => $asset->asset_code,
            'status' => $asset->status,
        ];
        if ($forBackfill) {
            $meta['backfill'] = true;
        }

        AssetLifecycleEvent::create([
            'asset_id' => $asset->id,
            'event_type' => 'created',
            'occurred_at' => $asset->created_at ?? now(),
            'user_id' => $userId ?? $asset->created_by,
            'summary' => 'إنشاء الأصل: '.($asset->name_ar ?? $asset->name),
            'meta' => $meta,
        ]);
    }

    /**
     * @param  array<string, mixed>  $before  قيم status, branch_id, department_id, photo قبل التحديث
     */
    public function recordAfterUpdate(Asset $asset, array $before, ?int $userId = null): void
    {
        $userId = $userId ?? auth()->id();

        if (($before['status'] ?? null) !== $asset->status) {
            AssetLifecycleEvent::create([
                'asset_id' => $asset->id,
                'event_type' => 'status_changed',
                'occurred_at' => now(),
                'user_id' => $userId,
                'summary' => 'تغيير الحالة',
                'meta' => [
                    'from' => $before['status'] ?? null,
                    'to' => $asset->status,
                ],
            ]);
        }

        if (($before['branch_id'] ?? null) != $asset->branch_id) {
            AssetLifecycleEvent::create([
                'asset_id' => $asset->id,
                'event_type' => 'branch_changed',
                'occurred_at' => now(),
                'user_id' => $userId,
                'summary' => 'تغيير الفرع',
                'meta' => [
                    'from_branch_id' => $before['branch_id'] ?? null,
                    'to_branch_id' => $asset->branch_id,
                ],
            ]);
        }

        if (($before['department_id'] ?? null) != $asset->department_id) {
            AssetLifecycleEvent::create([
                'asset_id' => $asset->id,
                'event_type' => 'department_changed',
                'occurred_at' => now(),
                'user_id' => $userId,
                'summary' => 'تغيير القسم',
                'meta' => [
                    'from_department_id' => $before['department_id'] ?? null,
                    'to_department_id' => $asset->department_id,
                ],
            ]);
        }

        if (($before['photo'] ?? null) !== $asset->photo && filled($asset->photo)) {
            AssetLifecycleEvent::create([
                'asset_id' => $asset->id,
                'event_type' => 'photo_updated',
                'occurred_at' => now(),
                'user_id' => $userId,
                'summary' => 'تحديث صورة الأصل',
                'meta' => [],
            ]);
        }
    }

    public function recordAssignmentStarted(AssetAssignment $assignment, ?Carbon $occurredAt = null, bool $forBackfill = false): void
    {
        $assignment->loadMissing('employee');

        $name = $assignment->employee?->full_name ?? 'موظف #'.$assignment->employee_id;

        $at = $occurredAt ?? Carbon::parse($assignment->assigned_date)->startOfDay();

        $meta = [
            'condition_on_assignment' => $assignment->condition_on_assignment,
            'assignment_notes' => $assignment->assignment_notes,
        ];
        if ($forBackfill) {
            $meta['backfill'] = true;
        }

        AssetLifecycleEvent::create([
            'asset_id' => $assignment->asset_id,
            'event_type' => 'assignment_started',
            'occurred_at' => $at,
            'user_id' => $assignment->assigned_by,
            'employee_id' => $assignment->employee_id,
            'related_assignment_id' => $assignment->id,
            'summary' => 'تسليم الأصل إلى: '.$name,
            'meta' => $meta,
        ]);
    }

    public function recordAssignmentReturned(AssetAssignment $assignment, ?Carbon $occurredAt = null, bool $forBackfill = false): void
    {
        $assignment->loadMissing('employee');

        $name = $assignment->employee?->full_name ?? 'موظف #'.$assignment->employee_id;
        $at = $occurredAt ?? (
            $assignment->actual_return_date
                ? Carbon::parse($assignment->actual_return_date)->startOfDay()
                : now()
        );

        AssetLifecycleEvent::create([
            'asset_id' => $assignment->asset_id,
            'event_type' => 'assignment_returned',
            'occurred_at' => $at,
            'user_id' => $assignment->returned_by,
            'employee_id' => $assignment->employee_id,
            'related_assignment_id' => $assignment->id,
            'summary' => 'استرجاع من: '.$name,
            'meta' => array_merge([
                'condition_on_return' => $assignment->condition_on_return,
                'return_notes' => $assignment->return_notes,
                'assignment_status' => $assignment->assignment_status,
            ], $forBackfill ? ['backfill' => true] : []),
        ]);
    }

    public function recordMaintenanceCreated(AssetMaintenance $maintenance, ?Carbon $occurredAt = null, bool $forBackfill = false): void
    {
        $meta = [
            'status' => $maintenance->status,
            'maintenance_type' => $maintenance->maintenance_type,
        ];
        if ($forBackfill) {
            $meta['backfill'] = true;
        }

        AssetLifecycleEvent::create([
            'asset_id' => $maintenance->asset_id,
            'event_type' => 'maintenance_recorded',
            'occurred_at' => $occurredAt ?? ($maintenance->created_at ?? now()),
            'user_id' => $maintenance->created_by,
            'related_maintenance_id' => $maintenance->id,
            'summary' => 'تسجيل صيانة: '.$maintenance->title,
            'meta' => $meta,
        ]);
    }

    public function recordMaintenanceStatusChanged(AssetMaintenance $maintenance, string $oldStatus): void
    {
        if ($oldStatus === $maintenance->status) {
            return;
        }

        AssetLifecycleEvent::create([
            'asset_id' => $maintenance->asset_id,
            'event_type' => 'maintenance_status_changed',
            'occurred_at' => now(),
            'user_id' => auth()->id(),
            'related_maintenance_id' => $maintenance->id,
            'summary' => 'تغيير حالة الصيانة',
            'meta' => [
                'from' => $oldStatus,
                'to' => $maintenance->status,
            ],
        ]);
    }

    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function recordManualNote(Asset $asset, string $summary, ?string $notes, array $files): void
    {
        $event = AssetLifecycleEvent::create([
            'asset_id' => $asset->id,
            'event_type' => 'manual_note',
            'occurred_at' => now(),
            'user_id' => auth()->id(),
            'summary' => $summary,
            'meta' => array_filter(['notes' => $notes]),
        ]);

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('asset_lifecycle/'.$asset->id, 'public');

            AssetLifecycleAttachment::create([
                'asset_lifecycle_event_id' => $event->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }
}
