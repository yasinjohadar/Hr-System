<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\WorkflowStepAction;
use App\Services\ApprovalService;
use App\Services\WorkflowProgressPresenter;
use App\Services\EmployeeRequestSubmissionService;
use App\Services\WorkflowService;
use App\Support\WorkflowEntityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function seedLeaveWorkflowWithDeptHeadStep(User $deptHead): void
    {
        $workflow = Workflow::create([
            'name' => 'Leave Test',
            'name_ar' => 'إجازة تجريبية',
            'type' => 'leave_request',
            'is_active' => true,
            'created_by' => $deptHead->id,
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'name' => 'Dept Head',
            'name_ar' => 'رئيس القسم',
            'approver_type' => 'department_manager',
            'step_order' => 1,
            'is_required' => true,
            'can_reject' => true,
        ]);
    }

    /**
     * @return array{workflow: Workflow, employee: Employee, leaveRequest: LeaveRequest, instance: WorkflowInstance}
     */
    protected function createPendingLeaveWithThreeStepWorkflow(
        User $deptHeadUser,
        User $employeeUser,
        User $execUser,
    ): array {
        Role::firstOrCreate(['name' => 'employee']);
        $execRole = Role::firstOrCreate(['name' => 'executive_director']);
        $execUser->assignRole($execRole);

        $department = Department::create([
            'name' => 'IT',
            'code' => 'IT-SEQ',
            'is_active' => true,
            'manager_id' => $deptHeadUser->id,
        ]);

        $employee = Employee::create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'EMP-SEQ',
            'first_name' => 'Seq',
            'last_name' => 'Employee',
            'full_name' => 'Seq Employee',
            'department_id' => $department->id,
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $leaveType = LeaveType::create([
            'name' => 'Annual',
            'name_ar' => 'سنوية',
            'code' => 'ANNUAL-SEQ',
            'max_days' => 30,
            'is_active' => true,
            'requires_approval' => true,
        ]);

        $workflow = Workflow::create([
            'name' => 'Leave Sequential',
            'name_ar' => 'إجازة تسلسلية',
            'type' => 'leave_request',
            'is_active' => true,
            'created_by' => $deptHeadUser->id,
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'name' => 'Dept Head',
            'name_ar' => 'رئيس القسم',
            'approver_type' => 'department_manager',
            'step_order' => 1,
            'is_required' => true,
            'can_reject' => true,
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'name' => 'Executive',
            'name_ar' => 'مدير تنفيذي',
            'approver_type' => 'role',
            'role_id' => $execRole->id,
            'step_order' => 2,
            'is_required' => true,
            'can_reject' => true,
        ]);

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'days_count' => 3,
            'status' => 'pending',
            'created_by' => $employeeUser->id,
        ]);

        app(EmployeeRequestSubmissionService::class)->afterRequestCreated(
            'leave_request',
            $employee,
            $leaveRequest
        );

        $instance = WorkflowService::findInstanceForEntity(LeaveRequest::class, $leaveRequest->id);
        $this->assertNotNull($instance);

        return compact('workflow', 'employee', 'leaveRequest', 'instance');
    }

    public function test_entity_type_normalizes_to_fqcn(): void
    {
        $this->assertSame(
            LeaveRequest::class,
            WorkflowEntityType::normalize('LeaveRequest')
        );
        $this->assertSame(
            LeaveRequest::class,
            WorkflowEntityType::normalize(LeaveRequest::class)
        );
    }

    public function test_submission_starts_workflow_for_department_head(): void
    {
        Role::firstOrCreate(['name' => 'employee']);

        $deptHeadUser = User::factory()->create(['is_active' => true]);
        $employeeUser = User::factory()->create(['is_active' => true]);
        $employeeUser->assignRole('employee');

        $department = Department::create([
            'name' => 'IT',
            'code' => 'IT-1',
            'is_active' => true,
            'manager_id' => $deptHeadUser->id,
        ]);

        $employee = Employee::create([
            'user_id' => $employeeUser->id,
            'employee_code' => 'EMP-001',
            'first_name' => 'Test',
            'last_name' => 'Employee',
            'full_name' => 'Test Employee',
            'department_id' => $department->id,
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);

        $leaveType = LeaveType::create([
            'name' => 'Annual',
            'name_ar' => 'سنوية',
            'code' => 'ANNUAL',
            'max_days' => 30,
            'is_active' => true,
            'requires_approval' => true,
        ]);

        $this->seedLeaveWorkflowWithDeptHeadStep($deptHeadUser);

        $this->actingAs($employeeUser);

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'days_count' => 3,
            'status' => 'pending',
            'created_by' => $employeeUser->id,
        ]);

        app(EmployeeRequestSubmissionService::class)->afterRequestCreated(
            'leave_request',
            $employee,
            $leaveRequest
        );

        $instance = WorkflowService::findInstanceForEntity(LeaveRequest::class, $leaveRequest->id);

        $this->assertNotNull($instance);
        $this->assertSame(LeaveRequest::class, $instance->entity_type);
        $this->assertSame('in_progress', $instance->status);

        $approvalService = app(ApprovalService::class);
        $this->assertTrue($approvalService->canUserApprove(
            $deptHeadUser,
            'leave_request',
            $employee,
            $instance->currentStep->step_order
        ));
    }

    public function test_workflow_service_get_model_class_resolves_fqcn(): void
    {
        $service = app(WorkflowService::class);
        $this->assertSame(LeaveRequest::class, $service->getModelClass(LeaveRequest::class));
        $this->assertSame(LeaveRequest::class, $service->getModelClass('LeaveRequest'));
    }

    public function test_only_department_head_can_act_on_step_one(): void
    {
        $deptHeadUser = User::factory()->create(['is_active' => true]);
        $execUser = User::factory()->create(['is_active' => true]);
        $employeeUser = User::factory()->create(['is_active' => true]);

        ['leaveRequest' => $leaveRequest] = $this->createPendingLeaveWithThreeStepWorkflow(
            $deptHeadUser,
            $employeeUser,
            $execUser
        );

        $approvalService = app(ApprovalService::class);

        $this->assertTrue($approvalService->canActOnEntity($deptHeadUser, $leaveRequest));
        $this->assertFalse($approvalService->canActOnEntity($execUser, $leaveRequest));
    }

    public function test_executive_director_can_act_after_department_head_approves(): void
    {
        $deptHeadUser = User::factory()->create(['is_active' => true]);
        $execUser = User::factory()->create(['is_active' => true]);
        $employeeUser = User::factory()->create(['is_active' => true]);

        ['leaveRequest' => $leaveRequest, 'instance' => $instance] = $this->createPendingLeaveWithThreeStepWorkflow(
            $deptHeadUser,
            $employeeUser,
            $execUser
        );

        $workflowService = app(WorkflowService::class);
        $approvalService = app(ApprovalService::class);

        $this->actingAs($deptHeadUser);
        $this->assertTrue($workflowService->processApproval($instance, $deptHeadUser, true));

        $leaveRequest->refresh();
        $instance->refresh();

        $this->assertFalse($approvalService->canActOnEntity($deptHeadUser, $leaveRequest));
        $this->assertTrue($approvalService->canActOnEntity($execUser, $leaveRequest));
    }

    public function test_approve_all_permission_does_not_bypass_current_step(): void
    {
        $deptHeadUser = User::factory()->create(['is_active' => true]);
        $execUser = User::factory()->create(['is_active' => true]);
        $employeeUser = User::factory()->create(['is_active' => true]);

        $superUser = User::factory()->create(['is_active' => true]);
        Permission::findOrCreate('leave-request-approve-all', 'web');
        $superUser->givePermissionTo('leave-request-approve-all');

        ['leaveRequest' => $leaveRequest] = $this->createPendingLeaveWithThreeStepWorkflow(
            $deptHeadUser,
            $employeeUser,
            $execUser
        );

        $approvalService = app(ApprovalService::class);

        $this->assertFalse($approvalService->canActOnEntity($superUser, $leaveRequest));
        $this->assertTrue($approvalService->canActOnEntity($deptHeadUser, $leaveRequest));
    }

    public function test_intermediate_approval_records_action_and_display_status(): void
    {
        $deptHeadUser = User::factory()->create(['is_active' => true]);
        $execUser = User::factory()->create(['is_active' => true]);
        $employeeUser = User::factory()->create(['is_active' => true]);

        ['leaveRequest' => $leaveRequest, 'instance' => $instance] = $this->createPendingLeaveWithThreeStepWorkflow(
            $deptHeadUser,
            $employeeUser,
            $execUser
        );

        $workflowService = app(WorkflowService::class);
        $this->actingAs($deptHeadUser);
        $this->assertTrue($workflowService->processApproval($instance, $deptHeadUser, true, 'موافقة رئيس القسم'));

        $leaveRequest->refresh();
        $instance->refresh();

        $this->assertSame('pending', $leaveRequest->status);
        $this->assertDatabaseHas('workflow_step_actions', [
            'workflow_instance_id' => $instance->id,
            'user_id' => $deptHeadUser->id,
            'action' => 'approved',
            'comments' => 'موافقة رئيس القسم',
        ]);

        $progress = app(WorkflowProgressPresenter::class)->resolveForEntity($leaveRequest);
        $this->assertNotNull($progress);
        $this->assertStringContainsString('المدير التنفيذي', $progress['badge_ar']);

        $timeline = $workflowService->getWorkflowTimeline($instance);
        $completed = collect($timeline['steps'])->where('status', 'completed')->count();
        $this->assertSame(1, $completed);
    }

    public function test_approval_flash_message_differentiates_intermediate_and_final(): void
    {
        $deptHeadUser = User::factory()->create(['is_active' => true]);
        $execUser = User::factory()->create(['is_active' => true]);
        $employeeUser = User::factory()->create(['is_active' => true]);

        ['instance' => $instance] = $this->createPendingLeaveWithThreeStepWorkflow(
            $deptHeadUser,
            $employeeUser,
            $execUser
        );

        $workflowService = app(WorkflowService::class);
        $this->actingAs($deptHeadUser);
        $workflowService->processApproval($instance, $deptHeadUser, true);
        $instance->refresh();

        $intermediate = $workflowService->approvalFlashMessage($instance, true);
        $this->assertStringContainsString('تمت موافقتك', $intermediate);
        $this->assertStringNotContainsString('نهائياً', $intermediate);
    }
}
