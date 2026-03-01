<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Country;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $saudiArabia = Country::where('code', 'SA')->first();
        
        $branches = [
            [
                'name' => 'Head Office',
                'code' => 'HO',
                'address' => 'King Fahd Road, Riyadh',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'phone' => '+966112345678',
                'email' => 'headoffice@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Jeddah Branch',
                'code' => 'JED',
                'address' => 'Corniche Road, Jeddah',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'phone' => '+966122345678',
                'email' => 'jeddah@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Dammam Branch',
                'code' => 'DAM',
                'address' => 'King Faisal Road, Dammam',
                'city' => 'Dammam',
                'country' => 'Saudi Arabia',
                'phone' => '+966133456789',
                'email' => 'dammam@company.com',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            $created = Branch::firstOrCreate(
                ['code' => $branch['code']],
                $branch
            );
            
            // تعيين مدير للفرع
            if ($created && !$created->manager_id) {
                $manager = Employee::where('branch_id', $created->id)->first();
                if ($manager) {
                    $created->update(['manager_id' => $manager->id]);
                }
            }
        }
    }
}
