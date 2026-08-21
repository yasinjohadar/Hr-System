<?php

namespace Database\Seeders;

use App\Models\Department;
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

        // تعيين مديرين للأقسام لا يحدث هنا.
        //
        // هذا السيدر يُشغَّل في "المرحلة 2: الهيكل التنظيمي" — قبل
        // EmployeeSeeder ("المرحلة 3") في DatabaseSeeder — فلا يوجد أي
        // موظف بعد عند تنفيذه على قاعدة جديدة. الكود القديم هنا كان
        // يستعلم Employee::where(...)->first() في هذه اللحظة فيرجع دائماً
        // null على تنفيذ نظيف، ثم — الأخطر — كان يمرّر $employee->id
        // مباشرة كـ manager_id رغم أن العمود مفتاح أجنبي على users.id لا
        // employees.id (انظر plan/department-head-runtime.md)، فيُخزَّن رقم
        // سجل موظف عشوائي وكأنه معرّف مستخدم. تحقّقتُ: على القاعدة الحالية
        // (المبنية بتشغيلات متكرّرة لا تشغيلة واحدة نظيفة) هذا أدّى فعلاً
        // إلى تعيين مستخدمين حقيقيين لا علاقة لهم بالقسم كمديرين له.
        //
        // التعيين الصحيح — بعد وجود الموظفين، وعبر DepartmentHeadRoleService
        // الذي يضبط manager_id بالمعرّف الصحيح ويمنح دور department_head
        // تلقائياً — انتقل إلى DepartmentStructureSeeder (يُشغَّل بعد
        // EmployeeSeeder في DatabaseSeeder).
    }
}
