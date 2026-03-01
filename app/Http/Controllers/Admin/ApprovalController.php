<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\ExpenseRequest;
use App\Models\WorkflowInstance;
use App\Services\WorkflowService;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected WorkflowService $workflowService;
    protected ApprovalService $approvalService;

    public function __construct(WorkflowService $workflowService, ApprovalService $approvalService)
    {
        $this->middleware('auth');
        $this->workflowService = $workflowService;
        $this->approvalService = $approvalService;
    }

    /**
     * عرض جميع طلبات الموافقة المعلقة للمستخدم الحالي
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $pendingApprovals = [];

        // طلبات الإجازات المعلقة
        $leaveRequests = LeaveRequest::where('status', 'pending')
            ->with(['employee', 'leaveType'])
            ->get()
            ->filter(function ($leaveRequest) use ($user) {
                if (!$leaveRequest->employee) {
                    return false;
                }

                $instance = WorkflowInstance::where('entity_type', 'LeaveRequest')
                    ->where('entity_id', $leaveRequest->id)
                    ->where('status', 'in_progress')
                    ->first();

                if ($instance) {
                    $currentStep = $instance->currentStep;
                    if ($currentStep) {
                        return $this->approvalService->canUserApprove(
                            $user,
                            'leave_request',
                            $leaveRequest->employee,
                            $currentStep->step_order
                        );
                    }
                }

                // Fallback: التحقق من التسلسل الهرمي
                return $this->isUserApprover($user, $leaveRequest->employee);
            });

        // طلبات المصروفات المعلقة
        $expenseRequests = ExpenseRequest::where('status', 'pending')
            ->with(['employee', 'category'])
            ->get()
            ->filter(function ($expenseRequest) use ($user) {
                if (!$expenseRequest->employee) {
                    return false;
                }

                $instance = WorkflowInstance::where('entity_type', 'ExpenseRequest')
                    ->where('entity_id', $expenseRequest->id)
                    ->where('status', 'in_progress')
                    ->first();

                if ($instance) {
                    $currentStep = $instance->currentStep;
                    if ($currentStep) {
                        return $this->approvalService->canUserApprove(
                            $user,
                            'expense_request',
                            $expenseRequest->employee,
                            $currentStep->step_order
                        );
                    }
                }

                // Fallback: التحقق من التسلسل الهرمي
                return $this->isUserApprover($user, $expenseRequest->employee);
            });

        return view('admin.pages.approvals.index', compact('leaveRequests', 'expenseRequests'));
    }

    /**
     * التحقق من أن المستخدم يمكنه الموافقة على طلب موظف معين
     */
    private function isUserApprover(\App\Models\User $user, Employee $employee): bool
    {
        // المدير المباشر
        $directManager = $employee->getDirectManager();
        if ($directManager && $directManager->user_id === $user->id) {
            return true;
        }

        // مدير القسم
        $deptManager = $employee->getDepartmentManager();
        if ($deptManager && $deptManager->id === $user->id) {
            return true;
        }

        // صلاحيات عامة
        if ($user->hasPermissionTo('leave-request-approve-all') || 
            $user->hasPermissionTo('expense-request-approve-all')) {
            return true;
        }

        return false;
    }

    /**
     * عرض تفاصيل طلب الموافقة
     */
    public function show(string $type, string $id)
    {
        $entity = match($type) {
            'leave' => LeaveRequest::with(['employee', 'leaveType'])->findOrFail($id),
            'expense' => ExpenseRequest::with(['employee', 'category'])->findOrFail($id),
            default => abort(404),
        };

        $entityType = match($type) {
            'leave' => 'LeaveRequest',
            'expense' => 'ExpenseRequest',
            default => null,
        };

        $instance = null;
        if ($entityType) {
            $instance = WorkflowInstance::where('entity_type', $entityType)
                ->where('entity_id', $entity->id)
                ->with(['workflow', 'currentStep'])
                ->first();
        }

        $workflowStatus = null;
        if ($instance) {
            $workflowStatus = $this->workflowService->getWorkflowStatus($instance);
        }

        return view('admin.pages.approvals.show', compact('entity', 'type', 'instance', 'workflowStatus'));
    }
}
