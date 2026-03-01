<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $templates = [
            [
                'name' => 'Employment Contract',
                'name_ar' => 'عقد عمل',
                'code' => 'CONTRACT',
                'description' => 'Standard employment contract template',
                'description_ar' => 'قالب عقد عمل قياسي',
                'type' => 'contract',
                'content' => '<h1>Employment Contract</h1><p>This contract is between {{company_name}} and {{employee_name}}...</p>',
                'content_ar' => '<h1>عقد عمل</h1><p>هذا العقد بين {{company_name}} و {{employee_name}}...</p>',
                'variables' => ['company_name', 'employee_name', 'position', 'salary', 'start_date'],
                'file_format' => 'pdf',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Offer Letter',
                'name_ar' => 'خطاب عرض',
                'code' => 'OFFER_LETTER',
                'description' => 'Job offer letter template',
                'description_ar' => 'قالب خطاب عرض عمل',
                'type' => 'letter',
                'content' => '<h1>Job Offer Letter</h1><p>Dear {{candidate_name}}, We are pleased to offer you the position of {{position}}...</p>',
                'content_ar' => '<h1>خطاب عرض عمل</h1><p>عزيزي {{candidate_name}}، يسعدنا أن نعرض عليك منصب {{position}}...</p>',
                'variables' => ['candidate_name', 'position', 'salary', 'start_date'],
                'file_format' => 'pdf',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($templates as $template) {
            DocumentTemplate::firstOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }
}
