<?php

namespace Tests\Feature;

use App\Models\CompanyBankAccount;
use App\Models\FundTransfer;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectFinancePagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $perms = [
            'project-list', 'project-show', 'project-edit', 'project-create',
            'project-stage-list', 'project-stage-create', 'project-stage-edit', 'project-stage-delete',
            'project-budget-override',
            'company-bank-account-list', 'company-bank-account-create', 'company-bank-account-edit',
            'company-bank-account-delete', 'company-bank-account-show',
            'fund-transfer-list', 'fund-transfer-create', 'fund-transfer-approve',
        ];

        foreach ($perms as $perm) {
            Permission::findOrCreate($perm);
        }

        $role = Role::findOrCreate('admin');
        $role->syncPermissions($perms);

        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');
    }

    protected function seedFixtures(): array
    {
        $project = Project::create([
            'name' => 'Smoke Project',
            'name_ar' => 'مشروع فحص',
            'start_date' => now()->toDateString(),
            'status' => 'active',
            'priority' => 'medium',
            'budget' => 5000,
            'created_by' => $this->admin->id,
        ]);

        $stage = ProjectStage::create([
            'project_id' => $project->id,
            'name' => 'Stage 1',
            'allocated_amount' => 1000,
            'status' => 'planned',
            'sort_order' => 1,
        ]);

        $account = CompanyBankAccount::create([
            'name' => 'Main',
            'bank_name' => 'Test Bank',
            'account_number' => 'ACC-1',
            'balance' => 2000,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $account2 = CompanyBankAccount::create([
            'name' => 'Ops',
            'bank_name' => 'Test Bank',
            'account_number' => 'ACC-2',
            'balance' => 500,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $transfer = FundTransfer::create([
            'type' => 'internal',
            'from_account_type' => 'company',
            'from_account_id' => $account->id,
            'to_account_type' => 'company',
            'to_account_id' => $account2->id,
            'amount' => 50,
            'status' => 'completed',
            'requested_by' => $this->admin->id,
            'executed_at' => now(),
            'project_id' => $project->id,
            'project_stage_id' => $stage->id,
        ]);

        return compact('project', 'stage', 'account', 'account2', 'transfer');
    }

    public function test_all_finance_and_project_pages_render(): void
    {
        $f = $this->seedFixtures();

        $this->actingAs($this->admin);

        $pages = [
            ['GET', route('admin.company-bank-accounts.index')],
            ['GET', route('admin.company-bank-accounts.create')],
            ['GET', route('admin.company-bank-accounts.show', $f['account'])],
            ['GET', route('admin.company-bank-accounts.edit', $f['account'])],
            ['GET', route('admin.fund-transfers.index')],
            ['GET', route('admin.fund-transfers.create')],
            ['GET', route('admin.fund-transfers.show', $f['transfer'])],
            ['GET', route('admin.projects.show', $f['project'])],
            ['GET', route('admin.projects.show', [$f['project'], 'tab' => 'stages'])],
            ['GET', route('admin.projects.show', [$f['project'], 'tab' => 'team'])],
            ['GET', route('admin.projects.show', [$f['project'], 'tab' => 'finance'])],
        ];

        foreach ($pages as [$method, $url]) {
            $response = $this->call($method, $url);
            $this->assertTrue(
                $response->isSuccessful(),
                "Failed {$method} {$url} with status {$response->getStatusCode()}: ".$response->exception?->getMessage()
            );
        }
    }

    public function test_store_stage_and_company_account_and_transfer(): void
    {
        $f = $this->seedFixtures();
        $this->actingAs($this->admin);

        $this->post(route('admin.projects.stages.store', $f['project']), [
            'name' => 'Phase 2',
            'allocated_amount' => 200,
            'status' => 'planned',
        ])->assertRedirect();

        $this->assertDatabaseHas('project_stages', [
            'project_id' => $f['project']->id,
            'name' => 'Phase 2',
        ]);

        $this->post(route('admin.company-bank-accounts.store'), [
            'name' => 'New Acc',
            'bank_name' => 'Bank',
            'account_number' => '999',
            'balance' => 100,
            'is_active' => 1,
        ])->assertRedirect(route('admin.company-bank-accounts.index'));

        config(['project_finance.transfer_approval_threshold' => 10000]);

        $response = $this->post(route('admin.fund-transfers.store'), [
            'type' => 'internal',
            'from_account_id' => $f['account']->id,
            'to_account_id' => $f['account2']->id,
            'amount' => 25,
            'project_id' => $f['project']->id,
        ]);

        if ($response->exception) {
            $this->fail($response->exception->getMessage());
        }

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('fund_transfers', [
            'amount' => 25.00,
            'status' => 'completed',
            'project_id' => $f['project']->id,
        ]);
    }
}
