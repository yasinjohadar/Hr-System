<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\ScopesByDepartment;
use App\Models\LeaveRequest;
use App\Models\ExpenseRequest;
use App\Models\WorkflowInstance;
use App\Services\WorkflowService;
use App\Services\ApprovalService;
use App\Services\WorkflowProgressPresenter;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    use ScopesByDepartment;

    protected WorkflowService $workflowService;
    protected ApprovalService $approvalService;

    public function __construct(WorkflowService $workflowService, ApprovalService $approvalService)
    {
        $this->middleware('auth');
        $this->middleware('permission:approval-list')->only('index');
        $this->middleware('permission:approval-show')->only('show');
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
        $leaveQuery = LeaveRequest::where('status', 'pending')->with(['employee', 'leaveType']);
        $this->scopeByEmployeeQuery($leaveQuery);
        $leaveRequests = $leaveQuery->get()
            ->filter(fn ($leaveRequest) => $leaveRequest->employee
                && $this->approvalService->canActOnEntity($user, $leaveRequest));

        // طلبات المصروفات المعلقة
        $expenseQuery = ExpenseRequest::where('status', 'pending')->with(['employee', 'category']);
        $this->scopeByEmployeeQuery($expenseQuery);
        $expenseRequests = $expenseQuery->get()
            ->filter(fn ($expenseRequest) => $expenseRequest->employee
                && $this->approvalService->canActOnEntity($user, $expenseRequest));

        return view('admin.pages.approvals.index', compact('leaveRequests', 'expenseRequests'));
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

        $entityClass = match ($type) {
            'leave' => LeaveRequest::class,
            'expense' => ExpenseRequest::class,
            default => null,
        };

        $instance = null;
        if ($entityClass) {
            $instance = WorkflowInstance::where('entity_type', $entityClass)
                ->where('entity_id', $entity->id)
                ->with(['workflow', 'currentStep'])
                ->latest('id')
                ->first();
        }

        $workflowStatus = null;
        if ($instance) {
            $workflowStatus = $this->workflowService->getWorkflowStatus($instance);
        }

        $canApproveNow = $this->approvalService->canActOnEntity(auth()->user(), $entity);
        $workflowProgress = app(WorkflowProgressPresenter::class)->resolveForEntity($entity);

        return view('admin.pages.approvals.show', compact('entity', 'type', 'instance', 'workflowStatus', 'canApproveNow', 'workflowProgress'));
    }
}
