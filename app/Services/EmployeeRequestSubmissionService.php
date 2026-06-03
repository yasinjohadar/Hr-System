<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Workflow;
use App\Support\WorkflowEntityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class EmployeeRequestSubmissionService
{
    public function __construct(
        protected WorkflowService $workflowService,
        protected ApprovalService $approvalService
    ) {}

    /**
     * Start approval workflow after a pending employee request is created.
     *
     * @throws RuntimeException when workflow or department manager is missing
     */
    public function afterRequestCreated(
        string $workflowType,
        Employee $employee,
        Model $entity,
        bool $requireDepartmentManager = true,
        bool $advancePastUnstaffedSteps = false
    ): void {
        $config = $this->getTypeConfig($workflowType);
        $entityClass = $config['model'];

        if (! $entity instanceof $entityClass) {
            throw new RuntimeException('نوع الطلب لا يطابق إعدادات سير العمل.');
        }

        $mustRequireDeptManager = $requireDepartmentManager
            && config('approval_workflows.require_department_manager', true);

        if ($mustRequireDeptManager) {
            $deptManager = $this->approvalService->getApproverForStep(
                $this->firstDepartmentManagerStep($workflowType),
                $employee,
                $entity
            );

            if (! $deptManager) {
                throw new RuntimeException(
                    'لا يمكن إرسال الطلب: لم يُعيَّن رئيس قسم لقسمك. يرجى التواصل مع إدارة الموارد البشرية.'
                );
            }
        }

        $workflow = Workflow::where('type', $workflowType)->where('is_active', true)->first();
        if (! $workflow) {
            throw new RuntimeException(
                'لا يمكن إرسال الطلب: سير العمل غير مُفعَّل لهذا النوع. يرجى التواصل مع الإدارة.'
            );
        }

        $instance = $this->workflowService->startWorkflow(
            $workflowType,
            $employee,
            $entityClass,
            (int) $entity->getKey()
        );

        if (! $instance) {
            Log::error('Failed to start workflow', [
                'workflow_type' => $workflowType,
                'entity_id' => $entity->getKey(),
            ]);
            throw new RuntimeException('تعذّر بدء مسار الموافقة. يرجى المحاولة لاحقاً.');
        }

        if ($advancePastUnstaffedSteps) {
            $this->workflowService->advanceToFirstStaffedStep($instance, $employee, $entity);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getTypeConfig(string $workflowType): array
    {
        $types = config('approval_workflows.types', []);
        if (! isset($types[$workflowType])) {
            throw new RuntimeException("نوع سير العمل غير معرّف: {$workflowType}");
        }

        return $types[$workflowType];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function allTypes(): array
    {
        return config('approval_workflows.types', []);
    }

    public function entityClassFor(string $workflowType): string
    {
        return $this->getTypeConfig($workflowType)['model'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Model>
     */
    public function pendingQueryFor(string $workflowType, array $employeeIds)
    {
        $config = $this->getTypeConfig($workflowType);
        $modelClass = $config['model'];
        $statusField = $config['status_field'];
        $pendingValues = $config['pending_values'];

        return $modelClass::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereIn($statusField, $pendingValues);
    }

    protected function firstDepartmentManagerStep(string $workflowType): \App\Models\WorkflowStep
    {
        $workflow = Workflow::where('type', $workflowType)->where('is_active', true)->first();
        if (! $workflow) {
            throw new RuntimeException('سير العمل غير موجود.');
        }

        $step = $workflow->steps()
            ->where('is_required', true)
            ->where('approver_type', 'department_manager')
            ->orderBy('step_order')
            ->first();

        if (! $step) {
            $step = $workflow->steps()->where('is_required', true)->orderBy('step_order')->first();
        }

        if (! $step) {
            throw new RuntimeException('لا توجد خطوات موافقة في سير العمل.');
        }

        return $step;
    }
}
