<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\LeaveRequest;
use App\Models\ExpenseRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WorkflowInstanceSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $initiatedBy = $adminUser ? $adminUser->id : 1;

        $workflows = Workflow::where('is_active', true)->get();

        if ($workflows->isEmpty()) {
            $this->command->warn('لا توجد سير عمل!');
            return;
        }

        // ربط سير العمل بطلبات الإجازات
        $leaveRequests = LeaveRequest::whereIn('status', ['pending', 'approved'])->get();
        foreach ($leaveRequests->take(10) as $request) {
            $workflow = $workflows->where('type', 'leave_request')->first() ?? $workflows->random();
            $firstStep = WorkflowStep::where('workflow_id', $workflow->id)->orderBy('step_order')->first();

            if (!$firstStep) {
                continue;
            }

            WorkflowInstance::firstOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'entity_type' => 'LeaveRequest',
                    'entity_id' => $request->id,
                ],
                [
                    'workflow_step_id' => $firstStep->id,
                    'status' => $request->status === 'approved' ? 'approved' : 'in_progress',
                    'initiated_by' => $initiatedBy,
                    'started_at' => $request->created_at,
                    'completed_at' => $request->status === 'approved' ? $request->updated_at : null,
                    'notes' => 'حالة سير العمل لطلب الإجازة',
                ]
            );
        }

        // ربط سير العمل بطلبات المصروفات
        $expenseRequests = ExpenseRequest::whereIn('status', ['pending', 'approved'])->get();
        foreach ($expenseRequests->take(10) as $request) {
            $workflow = $workflows->where('type', 'expense_request')->first() ?? $workflows->random();
            $firstStep = WorkflowStep::where('workflow_id', $workflow->id)->orderBy('step_order')->first();

            if (!$firstStep) {
                continue;
            }

            WorkflowInstance::firstOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'entity_type' => 'ExpenseRequest',
                    'entity_id' => $request->id,
                ],
                [
                    'workflow_step_id' => $firstStep->id,
                    'status' => $request->status === 'approved' ? 'approved' : 'in_progress',
                    'initiated_by' => $initiatedBy,
                    'started_at' => $request->created_at,
                    'completed_at' => $request->status === 'approved' ? $request->updated_at : null,
                    'notes' => 'حالة سير العمل لطلب المصروف',
                ]
            );
        }

        $totalInstances = WorkflowInstance::count();
        $this->command->info("✅ تم إنشاء $totalInstances حالة سير عمل");
    }
}
