<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRequest;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\WorkflowStepAction;
use App\Services\EmployeeRequestSubmissionService;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseRequestWorkflowActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_expense_approval_records_workflow_step_action(): void
    {
        $deptHeadUser = User::factory()->create(['is_active' => true]);

        $department = Department::create([
            'name' => 'Finance',
            'code' => 'FIN-1',
            'is_active' => true,
            'manager_id' => $deptHeadUser->id,
        ]);

        $employeeUser = User::factory()->create(['is_active' => true]);
        $employee = Employee::create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'EMP-EXP',
            'first_name' => 'Exp',
            'last_name' => 'Employee',
            'full_name' => 'Exp Employee',
            'department_id' => $department->id,
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $category = ExpenseCategory::create([
            'name' => 'Travel',
            'name_ar' => 'سفر',
            'code' => 'TRAVEL',
            'is_active' => true,
        ]);

        $workflow = Workflow::create([
            'name' => 'Expense',
            'name_ar' => 'مصروف',
            'type' => 'expense_request',
            'is_active' => true,
            'created_by' => $deptHeadUser->id,
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'name' => 'Dept',
            'name_ar' => 'رئيس قسم',
            'approver_type' => 'department_manager',
            'step_order' => 1,
            'is_required' => true,
            'can_reject' => true,
        ]);

        $expense = ExpenseRequest::create([
            'request_code' => 'EXP-TEST-001',
            'employee_id' => $employee->id,
            'expense_category_id' => $category->id,
            'amount' => 100,
            'expense_date' => now()->toDateString(),
            'description' => 'Test',
            'status' => 'pending',
            'created_by' => $employeeUser->id,
        ]);

        app(EmployeeRequestSubmissionService::class)->afterRequestCreated(
            'expense_request',
            $employee,
            $expense
        );

        $instance = WorkflowService::findInstanceForEntity(ExpenseRequest::class, $expense->id);
        $this->assertNotNull($instance);

        $this->actingAs($deptHeadUser);
        $ok = app(WorkflowService::class)->processApproval($instance, $deptHeadUser, true, 'ملاحظة مصروف');
        $this->assertTrue($ok);

        $this->assertDatabaseHas('workflow_step_actions', [
            'workflow_instance_id' => $instance->id,
            'user_id' => $deptHeadUser->id,
            'comments' => 'ملاحظة مصروف',
        ]);

        $instance->refresh();
        $expense->refresh();
        $this->assertSame('approved', $instance->status);
        $this->assertSame('approved', $expense->status);
    }
}
