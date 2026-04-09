<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetLifecycleEvent;
use App\Models\AssetMaintenance;
use App\Services\AssetLifecycleRecorder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class BackfillAssetLifecycleEvents extends Command
{
    protected $signature = 'assets:backfill-lifecycle-events {--force : حذف أحداث الاستيراد السابقة ثم إعادة التوليد}';

    protected $description = 'استيراد أحداث السجل الزمني للأصول القديمة (إنشاء، توزيع، استرجاع، صيانة)';

    public function handle(AssetLifecycleRecorder $recorder): int
    {
        if (! Schema::hasTable('asset_lifecycle_events')) {
            $this->error('جدول asset_lifecycle_events غير موجود. نفّذ php artisan migrate أولاً.');

            return self::FAILURE;
        }

        if ($this->option('force')) {
            $deleted = AssetLifecycleEvent::where('meta->backfill', true)->delete();
            $this->info("تم حذف {$deleted} حدث استيراد سابق.");
        }

        $createdAssets = 0;
        foreach (Asset::withTrashed()->cursor() as $asset) {
            $has = AssetLifecycleEvent::where('asset_id', $asset->id)
                ->where('event_type', 'created')
                ->exists();
            if (! $has) {
                $recorder->recordCreated($asset, $asset->created_by, true);
                $createdAssets++;
            }
        }
        $this->info("أحداث إنشاء أصل: {$createdAssets}");

        $started = 0;
        $returned = 0;
        foreach (AssetAssignment::withTrashed()->cursor() as $assignment) {
            $hasStart = AssetLifecycleEvent::where('related_assignment_id', $assignment->id)
                ->where('event_type', 'assignment_started')
                ->exists();
            if (! $hasStart) {
                $recorder->recordAssignmentStarted($assignment, null, true);
                $started++;
            }

            if (in_array($assignment->assignment_status, ['returned', 'lost', 'damaged'], true)
                && $assignment->actual_return_date) {
                $hasRet = AssetLifecycleEvent::where('related_assignment_id', $assignment->id)
                    ->where('event_type', 'assignment_returned')
                    ->exists();
                if (! $hasRet) {
                    $recorder->recordAssignmentReturned($assignment, null, true);
                    $returned++;
                }
            }
        }
        $this->info("أحداث بدء توزيع: {$started}، أحداث استرجاع: {$returned}");

        $maint = 0;
        foreach (AssetMaintenance::withTrashed()->cursor() as $maintenance) {
            $has = AssetLifecycleEvent::where('related_maintenance_id', $maintenance->id)
                ->where('event_type', 'maintenance_recorded')
                ->exists();
            if (! $has) {
                $recorder->recordMaintenanceCreated($maintenance, null, true);
                $maint++;
            }
        }
        $this->info("أحداث تسجيل صيانة: {$maint}");

        $this->info('اكتمل الاستيراد.');

        return self::SUCCESS;
    }
}
