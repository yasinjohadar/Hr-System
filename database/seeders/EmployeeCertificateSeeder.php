<?php

namespace Database\Seeders;

use App\Models\EmployeeCertificate;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty()) {
            $this->command->warn('لا توجد موظفين!');
            return;
        }

        $certificates = [
            'شهادة PMP', 'شهادة ITIL', 'شهادة CISSP', 'شهادة AWS', 'شهادة Microsoft',
            'شهادة Google Analytics', 'شهادة Salesforce', 'شهادة Oracle', 'شهادة Cisco',
            'شهادة اللغة الإنجليزية', 'شهادة اللغة العربية', 'شهادة المحاسبة', 'شهادة التسويق',
            'شهادة إدارة المشاريع', 'شهادة الموارد البشرية', 'شهادة المبيعات'
        ];

        $organizations = [
            'Microsoft', 'Google', 'Amazon', 'Oracle', 'Cisco', 'PMI', 'ITIL',
            'جامعة الملك سعود', 'جامعة الملك فهد', 'معهد الإدارة', 'المعهد العربي'
        ];

        foreach ($employees as $employee) {
            // 2-6 شهادات لكل موظف
            $numCertificates = rand(2, 6);

            for ($i = 0; $i < $numCertificates; $i++) {
                $issueDate = Carbon::now()->subYears(rand(1, 5));
                $expiryDate = rand(0, 1) ? $issueDate->copy()->addYears(rand(2, 5)) : null;

                EmployeeCertificate::create([
                    'employee_id' => $employee->id,
                    'certificate_name' => $certificates[array_rand($certificates)],
                    'issuing_organization' => $organizations[array_rand($organizations)],
                    'certificate_number' => 'CERT-' . strtoupper(uniqid()),
                    'issue_date' => $issueDate,
                    'expiry_date' => $expiryDate,
                    'does_not_expire' => $expiryDate === null,
                    'status' => $expiryDate && $expiryDate->lt(Carbon::now()) ? 'expired' : 'active',
                    'notes' => rand(0, 1) ? 'شهادة معتمدة' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء شهادات الموظفين بنجاح!');
    }
}
