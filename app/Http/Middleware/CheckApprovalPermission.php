<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ApprovalService;

class CheckApprovalPermission
{
    protected ApprovalService $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $workflowType): Response
    {
        // الحصول على معرف الكيان من route parameter
        $entityId = $request->route('id') ?? $request->route('leaveRequest') ?? $request->route('expenseRequest');
        
        if (!$entityId) {
            abort(404, 'Entity not found');
        }

        $modelClass = match ($workflowType) {
            'leave_request' => \App\Models\LeaveRequest::class,
            'expense_request' => \App\Models\ExpenseRequest::class,
            'employee_job_change' => \App\Models\EmployeeJobChange::class,
            'payroll' => \App\Models\Payroll::class,
            default => config("approval_workflows.types.{$workflowType}.model"),
        };

        if (! $modelClass || ! class_exists($modelClass)) {
            abort(400, 'Invalid workflow type');
        }

        $entity = $modelClass::find($entityId);
        if (!$entity) {
            abort(404, 'Entity not found');
        }

        // الحصول على الموظف
        $employee = $this->getEmployeeFromEntity($entity);
        if (!$employee) {
            abort(404, 'Employee not found');
        }

        if (! $this->approvalService->canActOnEntity(auth()->user(), $entity)) {
            abort(403, 'ليس لديك صلاحية الموافقة على هذا الطلب في المرحلة الحالية');
        }

        return $next($request);
    }

    private function getEmployeeFromEntity($entity): ?\App\Models\Employee
    {
        if (method_exists($entity, 'employee')) {
            return $entity->employee;
        }

        if (isset($entity->employee_id)) {
            return \App\Models\Employee::find($entity->employee_id);
        }

        return null;
    }
}
