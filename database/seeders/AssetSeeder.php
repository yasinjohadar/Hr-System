<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $assets = [
            [
                'name' => 'Laptop Dell XPS 15',
                'name_ar' => 'لابتوب ديل XPS 15',
                'category' => 'IT Equipment',
                'type' => 'Laptop',
                'manufacturer' => 'Dell',
                'model' => 'XPS 15',
                'serial_number' => 'DL-' . rand(100000, 999999),
                'purchase_date' => Carbon::now()->subMonths(rand(6, 24)),
                'purchase_cost' => rand(4000, 6000),
                'current_value' => rand(2000, 4000),
                'status' => 'assigned',
            ],
            [
                'name' => 'Desktop Computer',
                'name_ar' => 'جهاز كمبيوتر مكتبي',
                'category' => 'IT Equipment',
                'type' => 'Desktop',
                'manufacturer' => 'HP',
                'model' => 'EliteDesk',
                'serial_number' => 'HP-' . rand(100000, 999999),
                'purchase_date' => Carbon::now()->subMonths(rand(6, 24)),
                'purchase_cost' => rand(3000, 5000),
                'current_value' => rand(1500, 3000),
                'status' => 'assigned',
            ],
            [
                'name' => 'Office Chair',
                'name_ar' => 'كرسي مكتب',
                'category' => 'Furniture',
                'type' => 'Chair',
                'manufacturer' => 'IKEA',
                'model' => 'Ergonomic',
                'serial_number' => 'IK-' . rand(100000, 999999),
                'purchase_date' => Carbon::now()->subMonths(rand(3, 12)),
                'purchase_cost' => rand(500, 1000),
                'current_value' => rand(300, 700),
                'status' => 'assigned',
            ],
            [
                'name' => 'Printer HP LaserJet',
                'name_ar' => 'طابعة HP LaserJet',
                'category' => 'IT Equipment',
                'type' => 'Printer',
                'manufacturer' => 'HP',
                'model' => 'LaserJet Pro',
                'serial_number' => 'HP-PR-' . rand(100000, 999999),
                'purchase_date' => Carbon::now()->subMonths(rand(6, 18)),
                'purchase_cost' => rand(2000, 4000),
                'current_value' => rand(1000, 2500),
                'status' => 'available',
            ],
        ];

        foreach ($assets as $assetData) {
            $assetData['branch_id'] = $branches->random()->id;
            $assetData['department_id'] = $departments->random()->id;
            $assetData['created_by'] = $createdBy;
            $assetData['asset_code'] = 'AST-' . strtoupper(substr(md5(uniqid()), 0, 8));

            $asset = Asset::create($assetData);

            // تعيين الأصل لموظف إذا كان assigned
            if ($asset->status == 'assigned' && $employees->isNotEmpty()) {
                $employee = $employees->random();
                AssetAssignment::create([
                    'asset_id' => $asset->id,
                    'employee_id' => $employee->id,
                    'assigned_date' => Carbon::now()->subDays(rand(1, 90)),
                    'expected_return_date' => Carbon::now()->addDays(rand(30, 365)),
                    'assignment_status' => 'active',
                    'condition_on_assignment' => 'excellent',
                    'assigned_by' => $createdBy,
                    'created_by' => $createdBy,
                ]);
            }
        }
    }
}
