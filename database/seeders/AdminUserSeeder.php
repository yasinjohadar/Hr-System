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
            'user-show', // يمكنه رؤية ملفه الشخصي فقط
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

        // إنشاء دور رئيس القسم (نفس لوحة الإدارة مع صلاحيات محدودة بنطاق القسم)
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
            'report-view',
            'dashboard-view',
        ];
        $departmentHeadRole->syncPermissions(
            Permission::whereIn('name', $departmentHeadPermissions)->pluck('name')
        );
    }
}
