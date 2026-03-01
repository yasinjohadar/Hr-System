<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeBankAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeBankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $employees = Employee::where('is_active', true)->get();
        $banks = [
            ['name' => 'Al Rajhi Bank', 'name_ar' => 'البنك الأهلي السعودي', 'swift' => 'RJHISARI'],
            ['name' => 'Saudi National Bank', 'name_ar' => 'البنك الأهلي السعودي', 'swift' => 'NCBKSAJE'],
            ['name' => 'Riyad Bank', 'name_ar' => 'بنك الرياض', 'swift' => 'RIBLSAJE'],
            ['name' => 'SABB', 'name_ar' => 'البنك السعودي البريطاني', 'swift' => 'SABBSARI'],
            ['name' => 'Alinma Bank', 'name_ar' => 'بنك الإنماء', 'swift' => 'ALIMSARI'],
        ];

        if ($employees->isEmpty()) {
            $this->command->warn('لا توجد موظفين!');
            return;
        }

        $accountTypes = ['salary', 'savings', 'current'];
        $branches = ['الرياض', 'جدة', 'الدمام', 'المدينة المنورة', 'الخبر'];

        foreach ($employees as $index => $employee) {
            // حساب أساسي واحد لكل موظف
            $bank = $banks[array_rand($banks)];
            $accountType = $accountTypes[array_rand($accountTypes)];
            
            // إنشاء IBAN (SA + 22 رقم)
            $ibanDigits = '';
            for ($i = 0; $i < 22; $i++) {
                $ibanDigits .= rand(0, 9);
            }
            $iban = 'SA' . $ibanDigits;
            $accountNumber = str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);

            EmployeeBankAccount::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'is_primary' => true,
                ],
                [
                    'bank_name' => $bank['name'],
                    'bank_name_ar' => $bank['name_ar'],
                    'account_number' => $accountNumber,
                    'iban' => $iban,
                    'swift_code' => $bank['swift'],
                    'account_holder_name' => $employee->full_name,
                    'branch_name' => $branches[array_rand($branches)],
                    'branch_address' => 'فرع ' . $branches[array_rand($branches)],
                    'account_type' => $accountType,
                    'currency_code' => 'SAR',
                    'is_primary' => true,
                    'is_active' => true,
                    'notes' => 'حساب الراتب الأساسي',
                    'created_by' => $createdBy,
                ]
            );

            // حساب إضافي لبعض الموظفين (30% من الموظفين)
            if (rand(1, 100) <= 30) {
                $bank2 = $banks[array_rand($banks)];
                // إنشاء IBAN (SA + 22 رقم)
                $ibanDigits2 = '';
                for ($i = 0; $i < 22; $i++) {
                    $ibanDigits2 .= rand(0, 9);
                }
                $iban2 = 'SA' . $ibanDigits2;
                $accountNumber2 = str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);

                // التحقق من عدم وجود حساب ثانوي مسبق
                $existingSecondary = EmployeeBankAccount::where('employee_id', $employee->id)
                    ->where('is_primary', false)
                    ->first();

                if (!$existingSecondary) {
                    EmployeeBankAccount::create([
                        'employee_id' => $employee->id,
                        'bank_name' => $bank2['name'],
                        'bank_name_ar' => $bank2['name_ar'],
                        'account_number' => $accountNumber2,
                        'iban' => $iban2,
                        'swift_code' => $bank2['swift'],
                        'account_holder_name' => $employee->full_name,
                        'branch_name' => $branches[array_rand($branches)],
                        'branch_address' => 'فرع ' . $branches[array_rand($branches)],
                        'account_type' => 'savings',
                        'currency_code' => 'SAR',
                        'is_primary' => false,
                        'is_active' => true,
                        'notes' => 'حساب توفير',
                        'created_by' => $createdBy,
                    ]);
                }
            }
        }

        $totalAccounts = EmployeeBankAccount::count();
        $this->command->info("✅ تم إنشاء $totalAccounts حساب بنكي للموظفين");
    }
}
