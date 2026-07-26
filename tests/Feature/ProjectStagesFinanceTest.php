<?php

namespace Tests\Feature;

use App\Models\CompanyBankAccount;
use App\Models\Employee;
use App\Models\EmployeeBankAccount;
use App\Models\FundTransfer;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\User;
use App\Services\FundTransferService;
use App\Services\ProjectMembershipService;
use App\Services\ProjectStageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectStagesFinanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ([
            'project-budget-override',
            'fund-transfer-create',
            'fund-transfer-approve',
            'fund-transfer-list',
        ] as $perm) {
            Permission::findOrCreate($perm);
        }

        $role = Role::findOrCreate('admin');
        $role->givePermissionTo([
            'project-budget-override',
            'fund-transfer-create',
            'fund-transfer-approve',
            'fund-transfer-list',
        ]);

        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin);
    }

    protected function makeProject(float $budget = 1000): Project
    {
        return Project::create([
            'name' => 'Test Project',
            'name_ar' => 'مشروع اختبار',
            'start_date' => now()->toDateString(),
            'status' => 'active',
            'priority' => 'medium',
            'budget' => $budget,
            'created_by' => $this->admin->id,
        ]);
    }

    protected function makeEmployee(string $code = 'EMP-PF'): Employee
    {
        return Employee::create([
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'employee_code' => $code,
            'first_name' => 'Test',
            'last_name' => 'Emp',
            'full_name' => 'Test Emp',
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'is_active' => true,
        ]);
    }

    public function test_stage_allocation_within_budget_succeeds(): void
    {
        $project = $this->makeProject(1000);
        $stage = app(ProjectStageService::class)->create($project, [
            'name' => 'Phase 1',
            'allocated_amount' => 400,
            'status' => 'planned',
        ]);

        $this->assertEquals(400, (float) $stage->allocated_amount);
        $this->assertEquals(400, $project->fresh()->stagesAllocatedTotal());
    }

    public function test_stage_over_budget_requires_override_permission_and_reason(): void
    {
        $project = $this->makeProject(1000);

        $this->expectException(ValidationException::class);
        app(ProjectStageService::class)->create($project, [
            'name' => 'Phase Big',
            'allocated_amount' => 1500,
            'status' => 'planned',
        ]);
    }

    public function test_stage_over_budget_with_override_records_approval(): void
    {
        $project = $this->makeProject(1000);

        app(ProjectStageService::class)->create($project, [
            'name' => 'Phase Big',
            'allocated_amount' => 1500,
            'status' => 'planned',
        ], 'موافقة استثنائية لتوسيع النطاق');

        $this->assertEquals(1, $project->budgetOverrides()->count());
        $this->assertEquals(1500, $project->fresh()->stagesAllocatedTotal());
    }

    public function test_flexible_membership_stage_only(): void
    {
        $project = $this->makeProject();
        $stage = ProjectStage::create([
            'project_id' => $project->id,
            'name' => 'S1',
            'allocated_amount' => 100,
            'status' => 'active',
            'sort_order' => 1,
        ]);
        $employee = $this->makeEmployee();

        app(ProjectMembershipService::class)->assign(
            $project,
            $employee,
            false,
            [$stage->id],
            'member',
            'lead'
        );

        $this->assertFalse($project->members()->where('employee_id', $employee->id)->exists());
        $this->assertTrue($stage->members()->where('employee_id', $employee->id)->exists());
        $this->assertTrue($project->employeeCanParticipate($employee));
    }

    public function test_internal_transfer_under_threshold_updates_balances(): void
    {
        config(['project_finance.transfer_approval_threshold' => 10000]);

        $from = CompanyBankAccount::create([
            'name' => 'Ops',
            'bank_name' => 'Bank A',
            'account_number' => '111',
            'balance' => 5000,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);
        $to = CompanyBankAccount::create([
            'name' => 'Project',
            'bank_name' => 'Bank B',
            'account_number' => '222',
            'balance' => 100,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $transfer = app(FundTransferService::class)->request([
            'type' => 'internal',
            'from_account_type' => 'company',
            'from_account_id' => $from->id,
            'to_account_type' => 'company',
            'to_account_id' => $to->id,
            'amount' => 250,
        ], $this->admin);

        $this->assertEquals('completed', $transfer->status);
        $this->assertEquals(4750, (float) $from->fresh()->balance);
        $this->assertEquals(350, (float) $to->fresh()->balance);
    }

    public function test_transfer_above_threshold_stays_pending_without_balance_change(): void
    {
        config(['project_finance.transfer_approval_threshold' => 100]);

        $from = CompanyBankAccount::create([
            'name' => 'Ops',
            'bank_name' => 'Bank A',
            'account_number' => '111',
            'balance' => 5000,
            'is_active' => true,
        ]);
        $to = CompanyBankAccount::create([
            'name' => 'Project',
            'bank_name' => 'Bank B',
            'account_number' => '222',
            'balance' => 0,
            'is_active' => true,
        ]);

        $transfer = app(FundTransferService::class)->request([
            'type' => 'internal',
            'from_account_type' => 'company',
            'from_account_id' => $from->id,
            'to_account_type' => 'company',
            'to_account_id' => $to->id,
            'amount' => 500,
        ], $this->admin);

        $this->assertEquals('pending', $transfer->status);
        $this->assertEquals(5000, (float) $from->fresh()->balance);
        $this->assertEquals(0, (float) $to->fresh()->balance);

        app(FundTransferService::class)->approve($transfer, $this->admin);

        $this->assertEquals('completed', $transfer->fresh()->status);
        $this->assertEquals(4500, (float) $from->fresh()->balance);
        $this->assertEquals(500, (float) $to->fresh()->balance);
    }

    public function test_negative_balance_is_rejected(): void
    {
        config(['project_finance.transfer_approval_threshold' => 10000]);

        $from = CompanyBankAccount::create([
            'name' => 'Ops',
            'bank_name' => 'Bank A',
            'account_number' => '111',
            'balance' => 50,
            'is_active' => true,
        ]);
        $to = CompanyBankAccount::create([
            'name' => 'Project',
            'bank_name' => 'Bank B',
            'account_number' => '222',
            'balance' => 0,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);
        app(FundTransferService::class)->request([
            'type' => 'internal',
            'from_account_type' => 'company',
            'from_account_id' => $from->id,
            'to_account_type' => 'company',
            'to_account_id' => $to->id,
            'amount' => 200,
        ], $this->admin);
    }

    public function test_disbursement_to_employee_bank_account(): void
    {
        config(['project_finance.transfer_approval_threshold' => 10000]);

        $from = CompanyBankAccount::create([
            'name' => 'Ops',
            'bank_name' => 'Bank A',
            'account_number' => '111',
            'balance' => 1000,
            'is_active' => true,
        ]);

        $employee = $this->makeEmployee('EMP-BANK');
        $empAccount = EmployeeBankAccount::create([
            'employee_id' => $employee->id,
            'bank_name' => 'Emp Bank',
            'account_number' => '999',
            'account_holder_name' => 'Test Emp',
            'is_primary' => true,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $transfer = app(FundTransferService::class)->request([
            'type' => 'disbursement',
            'from_account_type' => 'company',
            'from_account_id' => $from->id,
            'to_account_type' => 'employee',
            'to_account_id' => $empAccount->id,
            'amount' => 75,
        ], $this->admin);

        $this->assertEquals('completed', $transfer->status);
        $this->assertEquals(925, (float) $from->fresh()->balance);
        $this->assertDatabaseHas('fund_transfers', [
            'id' => $transfer->id,
            'type' => 'disbursement',
            'to_account_type' => FundTransfer::ACCOUNT_EMPLOYEE,
        ]);
    }
}
