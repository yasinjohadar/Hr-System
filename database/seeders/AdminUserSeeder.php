<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  public function run(): void
    {
        // إنشاء دور المدير
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // منح جميع الصلاحيات لدور المدير
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        // إنشاء مستخدم مدير افتراضي
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('123456789'),
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // تعيين دور المدير للمستخدم
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole($adminRole);
        }

        // إنشاء دور مستخدم عادي
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // منح صلاحيات محدودة للمستخدم العادي
        $userPermissions = [
            'dashboard-view',
            'user-show',
            'notification-list',
            'notification-mark-read',
        ];

        $userRole->syncPermissions($userPermissions);

        // إنشاء دور الموظف (لوحة التحكم الذاتية فقط، بدون صلاحيات الإدارة)
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeePermissions = [
            'dashboard-view',
        ];
        $employeeRole->syncPermissions(
            Permission::whereIn('name', $employeePermissions)->pluck('name')
        );

        // إنشاء دور رئيس القسم (لوحة الإدارة مع صلاحيات محدودة بنطاق القسم؛ يُدمج غالباً مع employee للبوابة الذاتية)
        // انظر plan/department-head-runtime.md لتعيين manager_id على الأقسام والأدوار الموصى بها.
        $departmentHeadRole = Role::firstOrCreate(['name' => 'department_head']);
        $departmentHeadPermissions = [
            'employee-list',
            'employee-show',
            'leave-request-list',
            'leave-request-show',
            'leave-request-approve',
            'attendance-list',
            'attendance-show',
            'expense-request-list',
            'expense-request-show',
            'expense-request-approve',
            'performance-review-list',
            'performance-review-show',
            'performance-review-approve',
            'approval-list',
            'approval-show',
            'report-view',
            'report-employees',
            'report-attendance',
            'report-salaries',
            'report-leaves',
            'report-performance',
            'report-training',
            'report-recruitment',
            'report-benefits',
            'report-dashboard',
            'report-turnover',
            'report-training-effectiveness',
            'report-kpis',
            'notification-list',
            'notification-mark-read',
            'dashboard-view',
        ];
        $departmentHeadRole->syncPermissions(
            Permission::whereIn('name', $departmentHeadPermissions)->pluck('name')
        );

        // المدير التنفيذي والمدير العام — خطوات سير العمل (Workflow) للموافقة التسلسلية بعد رئيس القسم
        $hierarchyApprovalPermissions = [
            'dashboard-view',
            'approval-list',
            'approval-show',
            'leave-request-list',
            'leave-request-show',
            'leave-request-approve',
            'expense-request-list',
            'expense-request-show',
            'expense-request-approve',
            'employee-job-change-list',
            'employee-job-change-show',
            'employee-job-change-approve',
            'employee-job-change-reject',
            'notification-list',
            'notification-mark-read',
        ];
        $hierarchyPermNames = Permission::whereIn('name', $hierarchyApprovalPermissions)->pluck('name');

        $executiveRole = Role::firstOrCreate(['name' => 'executive_director']);
        $executiveRole->syncPermissions($hierarchyPermNames);

        $generalManagerRole = Role::firstOrCreate(['name' => 'general_manager']);
        $generalManagerRole->syncPermissions($hierarchyPermNames);

        // للتطوير: ربط المدير الافتراضي بهذين الدورين حتى يعمل getRoleApprover (يُفضّل لاحقاً مستخدمون منفصلون)
        if (! $adminUser->hasRole($executiveRole)) {
            $adminUser->assignRole($executiveRole);
        }
        if (! $adminUser->hasRole($generalManagerRole)) {
            $adminUser->assignRole($generalManagerRole);
        }
    }
}
