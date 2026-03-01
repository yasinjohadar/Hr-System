<?php

namespace Database\Seeders;

use App\Models\TaxSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaxSettingSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $taxSettings = [
            // ضريبة الدخل (شرائح)
            [
                'name' => 'Income Tax',
                'name_ar' => 'ضريبة الدخل',
                'code' => 'INCOME_TAX_2025',
                'type' => 'income_tax',
                'calculation_method' => 'slab',
                'rate' => 0,
                'min_amount' => 0,
                'max_amount' => null,
                'slabs' => [
                    ['min' => 0, 'max' => 5000, 'rate' => 0],
                    ['min' => 5000, 'max' => 10000, 'rate' => 2.5],
                    ['min' => 10000, 'max' => 20000, 'rate' => 5],
                    ['min' => 20000, 'max' => 50000, 'rate' => 10],
                    ['min' => 50000, 'max' => null, 'rate' => 20],
                ],
                'exemption_amount' => 4000.00,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_to' => null,
                'description' => 'ضريبة الدخل بشرائح متدرجة',
                'created_by' => $createdBy,
            ],

            // التأمينات الاجتماعية
            [
                'name' => 'Social Insurance',
                'name_ar' => 'التأمينات الاجتماعية',
                'code' => 'SOCIAL_INSURANCE',
                'type' => 'social_insurance',
                'calculation_method' => 'percentage',
                'rate' => 9.00,
                'min_amount' => 0,
                'max_amount' => 45000.00,
                'slabs' => null,
                'exemption_amount' => 0,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_to' => null,
                'description' => 'التأمينات الاجتماعية (9% من الراتب الأساسي حتى 45000)',
                'created_by' => $createdBy,
            ],

            // التأمين الصحي
            [
                'name' => 'Health Insurance',
                'name_ar' => 'التأمين الصحي',
                'code' => 'HEALTH_INSURANCE',
                'type' => 'health_insurance',
                'calculation_method' => 'fixed',
                'rate' => 200.00,
                'min_amount' => 0,
                'max_amount' => null,
                'slabs' => null,
                'exemption_amount' => 0,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'effective_to' => null,
                'description' => 'التأمين الصحي الشهري',
                'created_by' => $createdBy,
            ],

            // ضريبة القيمة المضافة (إن وجدت)
            [
                'name' => 'VAT',
                'name_ar' => 'ضريبة القيمة المضافة',
                'code' => 'VAT',
                'type' => 'other',
                'calculation_method' => 'percentage',
                'rate' => 15.00,
                'min_amount' => 0,
                'max_amount' => null,
                'slabs' => null,
                'exemption_amount' => 0,
                'is_active' => false,
                'effective_from' => null,
                'effective_to' => null,
                'description' => 'ضريبة القيمة المضافة (غير مفعلة)',
                'created_by' => $createdBy,
            ],
        ];

        foreach ($taxSettings as $tax) {
            TaxSetting::firstOrCreate(
                ['code' => $tax['code']],
                $tax
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($taxSettings) . ' إعداد ضريبي');
    }
}
