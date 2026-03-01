<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\PayrollPayment;
use App\Models\EmployeeBankAccount;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PayrollPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $currency = Currency::where('code', 'SAR')->first();
        $payrolls = Payroll::where('status', 'paid')->get();

        if ($payrolls->isEmpty() || !$currency) {
            $this->command->warn('لا توجد كشوف رواتب مدفوعة!');
            return;
        }

        $paymentMethods = ['bank_transfer', 'cash', 'cheque', 'card'];
        $statuses = ['completed', 'processing', 'pending'];

        foreach ($payrolls as $payroll) {
            // التحقق من عدم وجود دفعة مسبقة
            $existing = PayrollPayment::where('payroll_id', $payroll->id)->first();
            if ($existing) {
                continue;
            }

            // الحصول على الحساب البنكي الأساسي للموظف
            $bankAccount = EmployeeBankAccount::where('employee_id', $payroll->employee_id)
                ->where('is_primary', true)
                ->where('is_active', true)
                ->first();

            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $status = $statuses[array_rand($statuses)];
            $paymentDate = $payroll->payment_date ?? Carbon::now()->subDays(rand(1, 10));

            $payment = PayrollPayment::create([
                'payroll_id' => $payroll->id,
                'payment_code' => 'PAY-' . strtoupper(Str::random(10)),
                'amount' => $payroll->net_salary,
                'currency_id' => $currency->id,
                'payment_method' => $paymentMethod,
                'payment_date' => $paymentDate,
                'reference_number' => $paymentMethod === 'bank_transfer' ? 'TRF-' . strtoupper(Str::random(12)) : null,
                'bank_account_id' => $bankAccount?->id,
                'status' => $status,
                'payment_notes' => "دفعة راتب لشهر {$payroll->payroll_month}/{$payroll->payroll_year}",
                'processed_at' => $status === 'completed' ? $paymentDate->copy()->addHours(rand(1, 24)) : null,
                'processed_by' => $status === 'completed' ? $createdBy : null,
                'created_by' => $createdBy,
            ]);
        }

        $totalPayments = PayrollPayment::count();
        $this->command->info("✅ تم إنشاء $totalPayments سجل دفع");
    }
}
