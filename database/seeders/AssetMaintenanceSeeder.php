<?php

namespace Database\Seeders;

use App\Models\AssetMaintenance;
use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssetMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $assets = Asset::all();
        $employees = Employee::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($assets->isEmpty()) {
            $this->command->warn('لا توجد أصول!');
            return;
        }

        $types = ['preventive', 'corrective', 'upgrade', 'inspection'];
        $statuses = ['scheduled', 'in_progress', 'completed', 'cancelled'];

        foreach ($assets as $asset) {
            // 1-3 صيانة لكل أصل
            $numMaintenances = rand(1, 3);

            for ($i = 0; $i < $numMaintenances; $i++) {
                $scheduledDate = Carbon::now()->subMonths(rand(0, 12));
                $actualDate = rand(0, 1) ? $scheduledDate->copy()->addDays(rand(0, 7)) : null;
                $status = $statuses[array_rand($statuses)];

                AssetMaintenance::create([
                    'asset_id' => $asset->id,
                    'maintenance_type' => $types[array_rand($types)],
                    'scheduled_date' => $scheduledDate,
                    'actual_date' => $actualDate,
                    'cost' => rand(100, 5000),
                    'description' => 'صيانة دورية للأصل',
                    'work_done' => $status === 'completed' ? 'تم إصلاح المشكلة' : null,
                    'status' => $status,
                    'next_maintenance_date' => $status === 'completed' ? Carbon::now()->addMonths(rand(3, 12)) : null,
                    'service_provider' => rand(0, 1) ? 'شركة الصيانة المتخصصة' : null,
                    'notes' => rand(0, 1) ? 'ملاحظات الصيانة' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء سجلات الصيانة بنجاح!');
    }
}
