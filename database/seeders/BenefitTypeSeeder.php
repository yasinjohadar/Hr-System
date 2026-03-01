<?php

namespace Database\Seeders;

use App\Models\BenefitType;
use App\Models\User;
use Illuminate\Database\Seeder;

class BenefitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $benefitTypes = [
            [
                'name' => 'Health Insurance',
                'name_ar' => 'تأمين صحي',
                'code' => 'HEALTH_INS',
                'description' => 'Health insurance coverage',
                'description_ar' => 'تغطية تأمين صحي',
                'type' => 'insurance',
                'default_value' => 100,
                'is_taxable' => false,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Transportation Allowance',
                'name_ar' => 'بدل مواصلات',
                'code' => 'TRANS_ALLOW',
                'description' => 'Monthly transportation allowance',
                'description_ar' => 'بدل مواصلات شهري',
                'type' => 'allowance',
                'default_value' => 500,
                'is_taxable' => false,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Housing Allowance',
                'name_ar' => 'بدل سكن',
                'code' => 'HOUSING_ALLOW',
                'description' => 'Monthly housing allowance',
                'description_ar' => 'بدل سكن شهري',
                'type' => 'allowance',
                'default_value' => 2000,
                'is_taxable' => false,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Mobile Allowance',
                'name_ar' => 'بدل جوال',
                'code' => 'MOBILE_ALLOW',
                'description' => 'Monthly mobile phone allowance',
                'description_ar' => 'بدل جوال شهري',
                'type' => 'allowance',
                'default_value' => 200,
                'is_taxable' => false,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($benefitTypes as $type) {
            BenefitType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
