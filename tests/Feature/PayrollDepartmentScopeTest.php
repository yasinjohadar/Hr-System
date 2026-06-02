<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use App\Services\DepartmentScopeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayrollDepartmentScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_head_payroll_query_is_scoped(): void
    {
        Role::firstOrCreate(['name' => 'department_head']);
        Role::firstOrCreate(['name' => 'employee']);

        $head = User::factory()->create(['is_active' => true]);
        $head->assignRole(['department_head', 'employee']);

        $dept = Department::create(['name' => 'D1', 'code' => 'D1', 'is_active' => true]);
        $dept->update(['manager_id' => $head->id]);

        $otherDept = Department::create(['name' => 'D2', 'code' => 'D2', 'is_active' => true]);

        $empUser = User::factory()->create(['is_active' => true]);
        $empUser->assignRole('employee');
        $emp = Employee::create([
            'user_id' => $empUser->id,
            'employee_code' => 'E1',
            'first_name' => 'A',
            'last_name' => 'B',
            'full_name' => 'A B',
            'department_id' => $dept->id,
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $otherUser = User::factory()->create(['is_active' => true]);
        $otherUser->assignRole('employee');
        $otherEmp = Employee::create([
            'user_id' => $otherUser->id,
            'employee_code' => 'E2',
            'first_name' => 'C',
            'last_name' => 'D',
            'full_name' => 'C D',
            'department_id' => $otherDept->id,
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();
        Payroll::create([
            'employee_id' => $emp->id,
            'payroll_code' => 'P1',
            'payroll_month' => 6,
            'payroll_year' => 2026,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'status' => 'draft',
            'created_by' => $head->id,
        ]);
        Payroll::create([
            'employee_id' => $otherEmp->id,
            'payroll_code' => 'P2',
            'payroll_month' => 6,
            'payroll_year' => 2026,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'status' => 'draft',
            'created_by' => $head->id,
        ]);

        $count = app(DepartmentScopeService::class)->forUser($head)
            ->scopeByEmployeeId(Payroll::query())
            ->count();

        $this->assertEquals(1, $count);
    }
}
