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

        // تحديد نوع الكيان بناءً على workflowType
        $entityType = match($workflowType) {
            'leave_request' => 'LeaveRequest',
            'expense_request' => 'ExpenseRequest',
            'payroll' => 'Payroll',
            default => null,
        };

        if (!$entityType) {
            abort(400, 'Invalid workflow type');
        }

        // الحصول على الكيان
        $modelClass = $this->getModelClass($entityType);
        if (!$modelClass) {
            abort(404, 'Model not found');
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

        // البحث عن workflow instance
        $instance = \App\Models\WorkflowInstance::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'approved')
            ->first();

        if ($instance) {
            $currentStep = $instance->currentStep;
            if ($currentStep) {
                $canApprove = $this->approvalService->canUserApprove(
                    auth()->user(),
                    $workflowType,
                    $employee,
                    $currentStep->step_order
                );

                if (!$canApprove) {
                    abort(403, 'ليس لديك صلاحية الموافقة على هذا الطلب');
                }
            }
        } else {
            // إذا لم يكن هناك workflow، التحقق من الصلاحيات العامة
            $permission = $workflowType . '-approve';
            if (!auth()->user()->hasPermissionTo($permission) && !auth()->user()->hasPermissionTo($permission . '-all')) {
                abort(403, 'ليس لديك صلاحية الموافقة');
            }
        }

        return $next($request);
    }

    private function getModelClass(string $entityType): ?string
    {
        return match($entityType) {
            'LeaveRequest' => \App\Models\LeaveRequest::class,
            'ExpenseRequest' => \App\Models\ExpenseRequest::class,
            'Payroll' => \App\Models\Payroll::class,
            default => null,
        };
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
