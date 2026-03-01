<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $categories = [
            [
                'name' => 'Travel',
                'name_ar' => 'سفر',
                'description' => 'Travel expenses',
                'description_ar' => 'مصروفات السفر',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Meals',
                'name_ar' => 'وجبات',
                'description' => 'Meal expenses',
                'description_ar' => 'مصروفات الوجبات',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Office Supplies',
                'name_ar' => 'مستلزمات مكتبية',
                'description' => 'Office supplies expenses',
                'description_ar' => 'مصروفات المستلزمات المكتبية',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Transportation',
                'name_ar' => 'مواصلات',
                'description' => 'Transportation expenses',
                'description_ar' => 'مصروفات المواصلات',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Communication',
                'name_ar' => 'اتصالات',
                'description' => 'Communication expenses',
                'description_ar' => 'مصروفات الاتصالات',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
