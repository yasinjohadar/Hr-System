<?php

namespace Database\Seeders;

use App\Models\SalaryComponent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalaryComponentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $components = [
            // البدلات (Allowances)
            [
                'code' => 'HOUSING_ALLOWANCE',
                'name' => 'Housing Allowance',
                'name_ar' => 'بدل السكن',
                'type' => 'allowance',
                'calculation_type' => 'fixed',
                'default_value' => 3000.00,
                'is_taxable' => false,
                'is_required' => false,
                'apply_to_all' => false,
                'is_active' => true,
                'sort_order' => 1,
                'description' => 'بدل السكن الشهري',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'TRANSPORT_ALLOWANCE',
                'name' => 'Transport Allowance',
                'name_ar' => 'بدل النقل',
                'type' => 'allowance',
                'calculation_type' => 'fixed',
                'default_value' => 800.00,
                'is_taxable' => false,
                'is_required' => false,
                'apply_to_all' => false,
                'is_active' => true,
                'sort_order' => 2,
                'description' => 'بدل النقل الشهري',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'FOOD_ALLOWANCE',
                'name' => 'Food Allowance',
                'name_ar' => 'بدل الطعام',
                'type' => 'allowance',
                'calculation_type' => 'fixed',
                'default_value' => 500.00,
                'is_taxable' => false,
                'is_required' => false,
                'apply_to_all' => true,
                'is_active' => true,
                'sort_order' => 3,
                'description' => 'بدل الطعام الشهري',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'COMMUNICATION_ALLOWANCE',
                'name' => 'Communication Allowance',
                'name_ar' => 'بدل الاتصالات',
                'type' => 'allowance',
                'calculation_type' => 'fixed',
                'default_value' => 200.00,
                'is_taxable' => false,
                'is_required' => false,
                'apply_to_all' => false,
                'is_active' => true,
                'sort_order' => 4,
                'description' => 'بدل الاتصالات الشهري',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'MEDICAL_ALLOWANCE',
                'name' => 'Medical Allowance',
                'name_ar' => 'بدل طبي',
                'type' => 'allowance',
                'calculation_type' => 'percentage',
                'default_value' => 0,
                'percentage' => 5.00,
                'is_taxable' => false,
                'is_required' => false,
                'apply_to_all' => false,
                'is_active' => true,
                'sort_order' => 5,
                'description' => 'بدل طبي (5% من الراتب الأساسي)',
                'created_by' => $createdBy,
            ],

            // الخصومات (Deductions)
            [
                'code' => 'INCOME_TAX',
                'name' => 'Income Tax',
                'name_ar' => 'ضريبة الدخل',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 0,
                'percentage' => 2.50,
                'is_taxable' => false,
                'is_required' => true,
                'apply_to_all' => true,
                'is_active' => true,
                'sort_order' => 10,
                'description' => 'ضريبة الدخل (2.5% من الراتب الإجمالي)',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'SOCIAL_INSURANCE',
                'name' => 'Social Insurance',
                'name_ar' => 'التأمينات الاجتماعية',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 0,
                'percentage' => 9.00,
                'is_taxable' => false,
                'is_required' => true,
                'apply_to_all' => true,
                'is_active' => true,
                'sort_order' => 11,
                'description' => 'التأمينات الاجتماعية (9% من الراتب الأساسي)',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'HEALTH_INSURANCE',
                'name' => 'Health Insurance',
                'name_ar' => 'التأمين الصحي',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'default_value' => 200.00,
                'is_taxable' => false,
                'is_required' => true,
                'apply_to_all' => true,
                'is_active' => true,
                'sort_order' => 12,
                'description' => 'التأمين الصحي الشهري',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'LOAN_DEDUCTION',
                'name' => 'Loan Deduction',
                'name_ar' => 'خصم قرض',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'default_value' => 0,
                'is_taxable' => false,
                'is_required' => false,
                'apply_to_all' => false,
                'is_active' => true,
                'sort_order' => 13,
                'description' => 'خصم القرض الشهري',
                'created_by' => $createdBy,
            ],

            // المكافآت (Bonuses)
            [
                'code' => 'PERFORMANCE_BONUS',
                'name' => 'Performance Bonus',
                'name_ar' => 'مكافأة الأداء',
                'type' => 'bonus',
                'calculation_type' => 'percentage',
                'default_value' => 0,
                'percentage' => 10.00,
                'is_taxable' => true,
                'is_required' => false,
                'apply_to_all' => false,
                'is_active' => true,
                'sort_order' => 20,
                'description' => 'مكافأة الأداء (10% من الراتب الأساسي)',
                'created_by' => $createdBy,
            ],
            [
                'code' => 'ANNUAL_BONUS',
                'name' => 'Annual Bonus',
                'name_ar' => 'مكافأة سنوية',
                'type' => 'bonus',
                'calculation_type' => 'fixed',
                'default_value' => 5000.00,
                'is_taxable' => true,
                'is_required' => false,
                'apply_to_all' => false,
                'is_active' => true,
                'sort_order' => 21,
                'description' => 'مكافأة سنوية',
                'created_by' => $createdBy,
            ],

            // الساعات الإضافية (Overtime)
            [
                'code' => 'OVERTIME_RATE',
                'name' => 'Overtime Rate',
                'name_ar' => 'معدل الساعات الإضافية',
                'type' => 'overtime',
                'calculation_type' => 'formula',
                'default_value' => 0,
                'formula' => 'hourly_rate * hours * 1.5',
                'is_taxable' => true,
                'is_required' => false,
                'apply_to_all' => true,
                'is_active' => true,
                'sort_order' => 30,
                'description' => 'حساب الساعات الإضافية (1.5 ضعف السعر)',
                'created_by' => $createdBy,
            ],
        ];

        foreach ($components as $component) {
            SalaryComponent::firstOrCreate(
                ['code' => $component['code']],
                $component
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($components) . ' مكون راتب');
    }
}
