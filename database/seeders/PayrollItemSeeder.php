<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;

class PayrollItemSeeder extends Seeder
{
    public function run(): void
    {
        $payrolls = Payroll::all();
        $components = SalaryComponent::where('is_active', true)->get();

        if ($payrolls->isEmpty() || $components->isEmpty()) {
            $this->command->warn('لا توجد كشوف رواتب أو مكونات راتب!');
            return;
        }

        foreach ($payrolls as $payroll) {
            // حذف البنود القديمة إن وجدت
            PayrollItem::where('payroll_id', $payroll->id)->delete();

            $sortOrder = 1;

            // إضافة البدلات
            $allowanceComponents = $components->where('type', 'allowance')->take(3);
            foreach ($allowanceComponents as $component) {
                $amount = $component->calculation_type === 'percentage' 
                    ? ($payroll->base_salary * $component->percentage / 100)
                    : $component->default_value;

                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'item_type' => 'allowance',
                    'item_name' => $component->name,
                    'item_name_ar' => $component->name_ar,
                    'component_code' => $component->code,
                    'calculation_type' => $component->calculation_type,
                    'amount' => $amount,
                    'percentage' => $component->percentage,
                    'sort_order' => $sortOrder++,
                ]);
            }

            // إضافة المكافآت
            if ($payroll->bonuses > 0) {
                $bonusComponent = $components->where('type', 'bonus')->first();
                if ($bonusComponent) {
                    PayrollItem::create([
                        'payroll_id' => $payroll->id,
                        'item_type' => 'bonus',
                        'item_name' => $bonusComponent->name,
                        'item_name_ar' => $bonusComponent->name_ar,
                        'component_code' => $bonusComponent->code,
                        'calculation_type' => 'fixed',
                        'amount' => $payroll->bonuses,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }

            // إضافة الساعات الإضافية
            if ($payroll->overtime_amount > 0) {
                $overtimeComponent = $components->where('type', 'overtime')->first();
                if ($overtimeComponent) {
                    PayrollItem::create([
                        'payroll_id' => $payroll->id,
                        'item_type' => 'overtime',
                        'item_name' => $overtimeComponent->name,
                        'item_name_ar' => $overtimeComponent->name_ar,
                        'component_code' => $overtimeComponent->code,
                        'calculation_type' => 'fixed',
                        'amount' => $payroll->overtime_amount,
                        'quantity' => $payroll->overtime_hours,
                        'unit_price' => $payroll->overtime_amount / max(1, $payroll->overtime_hours),
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }

            // إضافة الخصومات
            $deductionComponents = $components->where('type', 'deduction')->take(3);
            foreach ($deductionComponents as $component) {
                $amount = $component->calculation_type === 'percentage' 
                    ? ($payroll->base_salary * $component->percentage / 100)
                    : $component->default_value;

                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'item_type' => 'deduction',
                    'item_name' => $component->name,
                    'item_name_ar' => $component->name_ar,
                    'component_code' => $component->code,
                    'calculation_type' => $component->calculation_type,
                    'amount' => $amount,
                    'percentage' => $component->percentage,
                    'sort_order' => $sortOrder++,
                ]);
            }

            // إضافة خصم الإجازات
            if ($payroll->leave_deduction > 0) {
                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'item_type' => 'deduction',
                    'item_name' => 'Leave Deduction',
                    'item_name_ar' => 'خصم الإجازات',
                    'calculation_type' => 'fixed',
                    'amount' => $payroll->leave_deduction,
                    'description' => "خصم {$payroll->leave_days} يوم إجازة",
                    'sort_order' => $sortOrder++,
                ]);
            }

            // إضافة خصم التأخير
            if ($payroll->late_deduction > 0) {
                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'item_type' => 'deduction',
                    'item_name' => 'Late Deduction',
                    'item_name_ar' => 'خصم التأخير',
                    'calculation_type' => 'fixed',
                    'amount' => $payroll->late_deduction,
                    'description' => "خصم {$payroll->late_days} يوم تأخير",
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        $totalItems = PayrollItem::count();
        $this->command->info("✅ تم إنشاء $totalItems بند راتب");
    }
}
