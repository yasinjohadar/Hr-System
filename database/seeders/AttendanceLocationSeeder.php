<?php

namespace Database\Seeders;

use App\Models\AttendanceLocation;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttendanceLocationSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        // إحداثيات مواقع حقيقية في السعودية
        $locations = [
            [
                'name' => 'Main Office - Riyadh',
                'name_ar' => 'المكتب الرئيسي - الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'radius_meters' => 100,
                'address' => 'الرياض، المملكة العربية السعودية',
                'description' => 'المكتب الرئيسي في الرياض',
                'is_active' => true,
                'require_location' => true,
            ],
            [
                'name' => 'Branch Office - Jeddah',
                'name_ar' => 'فرع جدة',
                'latitude' => 21.4858,
                'longitude' => 39.1925,
                'radius_meters' => 150,
                'address' => 'جدة، المملكة العربية السعودية',
                'description' => 'فرع جدة',
                'is_active' => true,
                'require_location' => true,
            ],
            [
                'name' => 'Branch Office - Dammam',
                'name_ar' => 'فرع الدمام',
                'latitude' => 26.4207,
                'longitude' => 50.0888,
                'radius_meters' => 120,
                'address' => 'الدمام، المملكة العربية السعودية',
                'description' => 'فرع الدمام',
                'is_active' => true,
                'require_location' => true,
            ],
            [
                'name' => 'Remote Work Location',
                'name_ar' => 'موقع العمل عن بُعد',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'radius_meters' => 500,
                'address' => 'موقع مرن للعمل عن بُعد',
                'description' => 'موقع للعمل عن بُعد (نطاق واسع)',
                'is_active' => true,
                'require_location' => false,
            ],
        ];

        $departments = Department::where('is_active', true)->pluck('id')->toArray();
        $positions = Position::where('is_active', true)->pluck('id')->toArray();

        foreach ($locations as $index => $location) {
            // تعيين أقسام ومناصب لبعض المواقع
            if ($index < 3 && !empty($departments) && !empty($positions)) {
                $location['allowed_departments'] = array_slice($departments, 0, rand(1, 3));
                $location['allowed_positions'] = array_slice($positions, 0, rand(1, 2));
            }

            AttendanceLocation::firstOrCreate(
                ['code' => 'LOC-' . strtoupper(Str::random(8))],
                array_merge($location, ['created_by' => $createdBy])
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($locations) . ' موقع حضور');
    }
}
