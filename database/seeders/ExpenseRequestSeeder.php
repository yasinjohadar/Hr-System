<?php

namespace Database\Seeders;

use App\Models\ExpenseRequest;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpenseRequestSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        $currency = Currency::where('code', 'SAR')->first();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('لا توجد موظفين أو تصنيفات مصروفات!');
            return;
        }

        $statuses = ['pending', 'approved', 'rejected', 'paid'];
        $descriptions = [
            'مصروفات سفر', 'مصروفات طعام', 'مصروفات نقل', 'مصروفات إقامة',
            'مصروفات معدات', 'مصروفات تدريب', 'مصروفات أخرى'
        ];

        foreach ($employees as $employee) {
            // 2-5 طلبات مصروفات لكل موظف
            $numRequests = rand(2, 5);

            for ($i = 0; $i < $numRequests; $i++) {
                $category = $categories->random();
                $expenseDate = Carbon::now()->subDays(rand(1, 90));
                $status = $statuses[array_rand($statuses)];

                ExpenseRequest::create([
                    'request_code' => 'EXP-' . strtoupper(uniqid()),
                    'employee_id' => $employee->id,
                    'expense_category_id' => $category->id,
                    'description' => $descriptions[array_rand($descriptions)],
                    'amount' => rand(100, 5000),
                    'currency_id' => $currency ? $currency->id : null,
                    'expense_date' => $expenseDate,
                    'status' => $status,
                    'rejection_reason' => $status === 'rejected' && rand(0, 1) ? 'المبلغ غير مدعوم' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء طلبات المصروفات بنجاح!');
    }
}
