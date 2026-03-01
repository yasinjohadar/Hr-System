<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'إدارة الموارد البشرية',
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'قسم تقنية المعلومات',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'قسم المالية والمحاسبة',
                'is_active' => true,
            ],
            [
                'name' => 'Sales',
                'code' => 'SALES',
                'description' => 'قسم المبيعات والتسويق',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'قسم العمليات',
                'is_active' => true,
            ],
            [
                'name' => 'Customer Service',
                'code' => 'CS',
                'description' => 'قسم خدمة العملاء',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'قسم التسويق',
                'is_active' => true,
            ],
            [
                'name' => 'Legal',
                'code' => 'LEGAL',
                'description' => 'قسم الشؤون القانونية',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                $dept
            );
        }

        // تعيين مديرين للأقسام
        $hrDept = Department::where('code', 'HR')->first();
        $itDept = Department::where('code', 'IT')->first();
        
        if ($hrDept) {
            $hrManager = Employee::where('department_id', $hrDept->id)->first();
            if ($hrManager) {
                $hrDept->update(['manager_id' => $hrManager->id]);
            }
        }
        
        if ($itDept) {
            $itManager = Employee::where('department_id', $itDept->id)->first();
            if ($itManager) {
                $itDept->update(['manager_id' => $itManager->id]);
            }
        }
    }
}
