<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkflowStepsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser(): User
    {
        $guard = 'web';
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);

        foreach (['workflow-create', 'workflow-edit', 'workflow-show', 'workflow-list'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
        }

        $role->syncPermissions([
            'workflow-create',
            'workflow-edit',
            'workflow-show',
            'workflow-list',
        ]);

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    public function test_store_workflow_with_steps_creates_ordered_steps(): void
    {
        $admin = $this->adminUser();
        Role::firstOrCreate(['name' => 'executive_director', 'guard_name' => 'web']);

        $response = $this->actingAs($admin)->post(route('admin.workflows.store'), [
            'name' => 'Test Workflow',
            'name_ar' => 'سير تجريبي',
            'type' => 'leave_request',
            'is_active' => '1',
            'steps' => [
                [
                    'name_ar' => 'موافقة رئيس القسم',
                    'approver_type' => 'department_manager',
                    'is_required' => '1',
                    'can_reject' => '1',
                ],
                [
                    'name_ar' => 'موافقة المدير التنفيذي',
                    'approver_type' => 'role',
                    'role_id' => Role::where('name', 'executive_director')->value('id'),
                    'is_required' => '1',
                    'can_reject' => '1',
                ],
            ],
        ]);

        $response->assertRedirect();
        $workflow = Workflow::where('type', 'leave_request')->latest('id')->first();
        $this->assertNotNull($workflow);

        $steps = WorkflowStep::where('workflow_id', $workflow->id)->orderBy('step_order')->get();
        $this->assertCount(2, $steps);
        $this->assertSame(1, $steps[0]->step_order);
        $this->assertSame('department_manager', $steps[0]->approver_type);
        $this->assertSame(2, $steps[1]->step_order);
        $this->assertSame('role', $steps[1]->approver_type);
    }

    public function test_store_without_steps_returns_validation_error(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post(route('admin.workflows.store'), [
            'name' => 'No Steps',
            'type' => 'leave_request',
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('steps');
        $this->assertSame(0, Workflow::count());
    }

    public function test_update_changes_approver_type_on_step(): void
    {
        $admin = $this->adminUser();

        $workflow = Workflow::create([
            'name' => 'WF',
            'type' => 'expense_request',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'name' => 'Step',
            'name_ar' => 'خطوة',
            'step_order' => 1,
            'approver_type' => 'role',
            'role_id' => Role::firstOrCreate(['name' => 'general_manager', 'guard_name' => 'web'])->id,
            'is_required' => true,
            'can_reject' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.workflows.update', $workflow->id), [
            'name' => 'WF',
            'type' => 'expense_request',
            'is_active' => '1',
            'steps' => [
                [
                    'id' => $step->id,
                    'name_ar' => 'خطوة محدثة',
                    'approver_type' => 'department_manager',
                    'is_required' => '1',
                    'can_reject' => '1',
                ],
            ],
        ]);

        $response->assertRedirect();
        $step->refresh();
        $this->assertSame('department_manager', $step->approver_type);
        $this->assertNull($step->role_id);
    }
}
