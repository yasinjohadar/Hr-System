<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Services\DepartmentScopeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DepartmentScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_head_only_sees_managed_department_employees(): void
    {
        Role::firstOrCreate(['name' => 'department_head']);
        Role::firstOrCreate(['name' => 'employee']);

        $headUser = User::factory()->create(['is_active' => true]);
        $headUser->assignRole(['department_head', 'employee']);

        $managedDept = Department::create(['name' => 'Managed', 'code' => 'M1', 'is_active' => true]);
        $otherDept = Department::create(['name' => 'Other', 'code' => 'O1', 'is_active' => true]);

        $managedDept->update(['manager_id' => $headUser->id]);

        $managedEmpUser = User::factory()->create(['is_active' => true]);
        $managedEmpUser->assignRole('employee');
        Employee::create([
            'user_id' => $managedEmpUser->id,
            'employee_code' => 'EMP-M1',
            'first_name' => 'Managed',
            'last_name' => 'User',
            'full_name' => 'Managed User',
            'department_id' => $managedDept->id,
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $otherEmpUser = User::factory()->create(['is_active' => true]);
        $otherEmpUser->assignRole('employee');
        Employee::create([
            'user_id' => $otherEmpUser->id,
            'employee_code' => 'EMP-O1',
            'first_name' => 'Other',
            'last_name' => 'User',
            'full_name' => 'Other User',
            'department_id' => $otherDept->id,
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $scope = app(DepartmentScopeService::class)->forUser($headUser);
        $ids = $scope->scopeEmployees(Employee::query())->pluck('id')->all();

        $this->assertCount(1, $ids);
        $this->assertTrue($headUser->isDepartmentHead());
    }

    public function test_admin_user_is_not_scoped(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'employee']);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $empUser = User::factory()->create(['is_active' => true]);
        $empUser->assignRole('employee');
        Employee::create([
            'user_id' => $empUser->id,
            'employee_code' => 'EMP-A1',
            'first_name' => 'A',
            'last_name' => 'B',
            'full_name' => 'A B',
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $count = app(DepartmentScopeService::class)->forUser($admin)->scopeEmployees(Employee::query())->count();

        $this->assertGreaterThanOrEqual(1, $count);
        $this->assertFalse($admin->isDepartmentHead());
    }
}
