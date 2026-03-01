<?php

namespace Database\Seeders;

use App\Models\EmployeeDocument;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->take(20)->get();
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($employees->isEmpty()) {
            return;
        }

        $documentTypes = [
            'هوية وطنية',
            'جواز سفر',
            'عقد عمل',
            'شهادة تأمين',
            'شهادة جامعية',
            'رخصة قيادة',
        ];

        foreach ($employees as $employee) {
            // إنشاء 2-4 مستندات لكل موظف
            $numDocs = rand(2, 4);
            
            for ($i = 0; $i < $numDocs; $i++) {
                $docType = $documentTypes[array_rand($documentTypes)];
                $issueDate = Carbon::now()->subYears(rand(1, 5))->subMonths(rand(0, 11));
                $expiryDate = $issueDate->copy()->addYears(rand(1, 5));

                EmployeeDocument::create([
                    'employee_id' => $employee->id,
                    'document_type' => $docType,
                    'title' => $docType,
                    'file_path' => 'employee_documents/sample.pdf',
                    'file_name' => $docType . '.pdf',
                    'file_size' => (string)rand(100000, 500000),
                    'mime_type' => 'application/pdf',
                    'issue_date' => $issueDate,
                    'expiry_date' => $expiryDate,
                    'is_required' => rand(0, 1),
                    'status' => rand(0, 1) ? 'active' : 'expired',
                    'notes' => 'مستند ' . $docType,
                    'uploaded_by' => $createdBy,
                ]);
            }
        }
    }
}
