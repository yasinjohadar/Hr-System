<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على الأقسام
        $departments = Department::all();
        
        if ($departments->isEmpty()) {
            $this->command->warn('لا توجد أقسام! يرجى تشغيل DepartmentSeeder أولاً.');
            return;
        }

        // مناصب لكل قسم
        $positions = [
            // قسم الموارد البشرية
            [
                'title' => 'مدير الموارد البشرية',
                'code' => 'HR-MGR',
                'description' => 'إدارة شاملة لقسم الموارد البشرية',
                'department_id' => $departments->where('code', 'HR')->first()?->id ?? $departments->first()->id,
                'min_salary' => 15000,
                'max_salary' => 25000,
                'is_active' => true,
            ],
            [
                'title' => 'أخصائي توظيف',
                'code' => 'HR-REC',
                'description' => 'إدارة عمليات التوظيف والاختيار',
                'department_id' => $departments->where('code', 'HR')->first()?->id ?? $departments->first()->id,
                'min_salary' => 8000,
                'max_salary' => 12000,
                'is_active' => true,
            ],
            [
                'title' => 'أخصائي رواتب',
                'code' => 'HR-PAY',
                'description' => 'إدارة الرواتب والمستحقات',
                'department_id' => $departments->where('code', 'HR')->first()?->id ?? $departments->first()->id,
                'min_salary' => 7000,
                'max_salary' => 11000,
                'is_active' => true,
            ],
            
            // قسم المبيعات
            [
                'title' => 'مدير المبيعات',
                'code' => 'SALES-MGR',
                'description' => 'إدارة فريق المبيعات',
                'department_id' => $departments->where('code', 'SALES')->first()?->id ?? $departments->first()->id,
                'min_salary' => 12000,
                'max_salary' => 20000,
                'is_active' => true,
            ],
            [
                'title' => 'منسق مبيعات',
                'code' => 'SALES-REP',
                'description' => 'تنفيذ عمليات البيع والتسويق',
                'department_id' => $departments->where('code', 'SALES')->first()?->id ?? $departments->first()->id,
                'min_salary' => 5000,
                'max_salary' => 10000,
                'is_active' => true,
            ],
            
            // قسم التسويق
            [
                'title' => 'مدير التسويق',
                'code' => 'MKT-MGR',
                'description' => 'إدارة استراتيجيات التسويق',
                'department_id' => $departments->where('code', 'MARKETING')->first()?->id ?? $departments->first()->id,
                'min_salary' => 13000,
                'max_salary' => 22000,
                'is_active' => true,
            ],
            [
                'title' => 'أخصائي تسويق رقمي',
                'code' => 'MKT-DIG',
                'description' => 'إدارة الحملات التسويقية الرقمية',
                'department_id' => $departments->where('code', 'MARKETING')->first()?->id ?? $departments->first()->id,
                'min_salary' => 7000,
                'max_salary' => 12000,
                'is_active' => true,
            ],
            
            // قسم التطوير
            [
                'title' => 'مدير التطوير',
                'code' => 'DEV-MGR',
                'description' => 'إدارة فريق التطوير',
                'department_id' => $departments->where('code', 'DEV')->first()?->id ?? $departments->first()->id,
                'min_salary' => 18000,
                'max_salary' => 30000,
                'is_active' => true,
            ],
            [
                'title' => 'مطور برمجيات',
                'code' => 'DEV-SWE',
                'description' => 'تطوير البرمجيات والتطبيقات',
                'department_id' => $departments->where('code', 'DEV')->first()?->id ?? $departments->first()->id,
                'min_salary' => 10000,
                'max_salary' => 18000,
                'is_active' => true,
            ],
            [
                'title' => 'مطور ويب',
                'code' => 'DEV-WEB',
                'description' => 'تطوير تطبيقات الويب',
                'department_id' => $departments->where('code', 'DEV')->first()?->id ?? $departments->first()->id,
                'min_salary' => 9000,
                'max_salary' => 16000,
                'is_active' => true,
            ],
            
            // قسم المالية
            [
                'title' => 'مدير مالي',
                'code' => 'FIN-MGR',
                'description' => 'إدارة الشؤون المالية',
                'department_id' => $departments->where('code', 'FINANCE')->first()?->id ?? $departments->first()->id,
                'min_salary' => 15000,
                'max_salary' => 25000,
                'is_active' => true,
            ],
            [
                'title' => 'محاسب',
                'code' => 'FIN-ACC',
                'description' => 'إدارة الحسابات والمحاسبة',
                'department_id' => $departments->where('code', 'FINANCE')->first()?->id ?? $departments->first()->id,
                'min_salary' => 6000,
                'max_salary' => 11000,
                'is_active' => true,
            ],
            
            // قسم الإدارة
            [
                'title' => 'مدير تنفيذي',
                'code' => 'ADMIN-CEO',
                'description' => 'الإدارة التنفيذية',
                'department_id' => $departments->where('code', 'ADMIN')->first()?->id ?? $departments->first()->id,
                'min_salary' => 25000,
                'max_salary' => 50000,
                'is_active' => true,
            ],
            [
                'title' => 'مساعد إداري',
                'code' => 'ADMIN-ASST',
                'description' => 'المساعدة الإدارية',
                'department_id' => $departments->where('code', 'ADMIN')->first()?->id ?? $departments->first()->id,
                'min_salary' => 5000,
                'max_salary' => 9000,
                'is_active' => true,
            ],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['code' => $position['code']],
                $position
            );
        }
    }
}
