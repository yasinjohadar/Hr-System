<?php

namespace Database\Seeders;

use App\Models\AssetAssignment;
use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssetAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $assets = Asset::where('status', 'available')->get();
        $employees = Employee::where('is_active', true)->get();
        $managers = Employee::where('is_active', true)->take(5)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($assets->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد أصول أو موظفين!');
            return;
        }

        $conditions = ['excellent', 'good', 'fair', 'poor'];
        $statuses = ['active', 'returned', 'lost', 'damaged'];

        // تعيين 30-50% من الأصول
        $assetsToAssign = $assets->random(rand((int)($assets->count() * 0.3), (int)($assets->count() * 0.5)));

        foreach ($assetsToAssign as $asset) {
            $employee = $employees->random();
            $assignedBy = $managers->isNotEmpty() ? $managers->random() : $employees->random();
            $assignedDate = Carbon::now()->subDays(rand(1, 180));
            $expectedReturnDate = rand(0, 1) ? $assignedDate->copy()->addMonths(rand(6, 24)) : null;
            $status = $statuses[array_rand($statuses)];
            $actualReturnDate = $status === 'returned' ? $assignedDate->copy()->addDays(rand(30, 180)) : null;

            AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $employee->id,
                'assigned_date' => $assignedDate,
                'expected_return_date' => $expectedReturnDate,
                'actual_return_date' => $actualReturnDate,
                'assignment_status' => $status,
                'condition_on_assignment' => $conditions[array_rand($conditions)],
                'condition_on_return' => $status === 'returned' ? $conditions[array_rand($conditions)] : null,
                'assignment_notes' => rand(0, 1) ? 'تعيين عادي' : null,
                'return_notes' => $status === 'returned' && rand(0, 1) ? 'تم الإرجاع بنجاح' : null,
                'assigned_by' => $assignedBy->id,
                'returned_by' => $status === 'returned' && rand(0, 1) ? $assignedBy->id : null,
                'created_by' => $createdBy,
            ]);

            // تحديث حالة الأصل
            if ($status === 'active') {
                $asset->update(['status' => 'assigned']);
            }
        }

        $this->command->info('✅ تم إنشاء تعيينات الأصول بنجاح!');
    }
}
