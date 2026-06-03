<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\WorkflowStepAction;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Services\ApprovalService;
use App\Support\WorkflowEntityType;
use App\Notifications\ApprovalRequestNotification;
use App\Events\ApprovalRequestSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class WorkflowService
{
    protected ApprovalService $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * بدء سير عمل جديد لطلب معين
     * 
     * @param string $workflowType نوع سير العمل
     * @param Employee $employee الموظف صاحب الطلب
     * @param string $entityType نوع الكيان (LeaveRequest, ExpenseRequest, etc.)
     * @param int $entityId معرف الكيان
     * @return WorkflowInstance|null
     */
    public function startWorkflow(string $workflowType, Employee $employee, string $entityType, int $entityId): ?WorkflowInstance
    {
        $workflow = Workflow::where('type', $workflowType)
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            Log::warning("Workflow not found for type: {$workflowType}");
            return null;
        }

        // الحصول على الخطوة الأولى
        $firstStep = $workflow->steps()
            ->where('is_required', true)
            ->orderBy('step_order')
            ->first();

        if (!$firstStep) {
            Log::warning("No required steps found for workflow: {$workflow->id}");
            return null;
        }

        $entityType = WorkflowEntityType::normalize($entityType);

        // إنشاء instance جديد
        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'workflow_step_id' => $firstStep->id,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'status' => 'in_progress',
            'initiated_by' => auth()->id(),
            'started_at' => now(),
        ]);

        // إرسال إشعار للموافق الأول
        $this->notifyApprover($firstStep, $employee, $instance);

        return $instance;
    }

    /**
     * Skip workflow steps that have no resolvable approver (e.g. backfill when department has no manager).
     */
    public function advanceToFirstStaffedStep(WorkflowInstance $instance, Employee $employee, $entity = null): WorkflowInstance
    {
        $instance->load(['workflow', 'currentStep']);
        $entity ??= $this->getEntity($instance);
        $maxHops = $instance->workflow?->steps()->where('is_required', true)->count() ?? 10;

        for ($i = 0; $i < $maxHops; $i++) {
            $instance->refresh();
            $currentStep = $instance->currentStep;
            if (! $currentStep || $instance->status !== 'in_progress') {
                break;
            }

            $approver = $this->approvalService->getApproverForStep($currentStep, $employee, $entity);
            if ($approver) {
                $this->notifyApprover($currentStep, $employee, $instance);
                break;
            }

            Log::warning('Skipping workflow step without approver', [
                'instance_id' => $instance->id,
                'step_id' => $currentStep->id,
                'step_order' => $currentStep->step_order,
            ]);

            $nextStep = $this->getNextStep($instance->workflow, $currentStep, $entity, $employee);
            if (! $nextStep) {
                break;
            }

            $instance->update(['workflow_step_id' => $nextStep->id]);
        }

        return $instance->refresh();
    }

    /**
     * معالجة الموافقة على خطوة معينة
     * 
     * @param WorkflowInstance $instance
     * @param User $approver الموافق
     * @param bool $approved true للموافقة، false للرفض
     * @param string|null $comments تعليقات
     * @return bool
     */
    public function processApproval(WorkflowInstance $instance, User $approver, bool $approved, ?string $comments = null): bool
    {
        DB::beginTransaction();
        try {
            $currentStep = $instance->currentStep;
            if (!$currentStep) {
                throw new \Exception("Current step not found");
            }

            // التحقق من أن المستخدم يمكنه الموافقة
            $entity = $this->getEntity($instance);
            if (!$entity) {
                throw new \Exception("Entity not found");
            }

            $employee = $this->getEmployeeFromEntity($entity);
            if (!$employee) {
                throw new \Exception("Employee not found");
            }

            $canApprove = $this->approvalService->canUserApprove(
                $approver,
                $instance->workflow->type,
                $employee,
                $currentStep->step_order
            );

            if (!$canApprove) {
                throw new \Exception("User cannot approve this step");
            }

            $this->recordStepAction($instance, $currentStep, $approver, $approved ? 'approved' : 'rejected', $comments);

            if ($approved) {
                // الموافقة - الانتقال للخطوة التالية
                $nextStep = $this->getNextStep($instance->workflow, $currentStep, $entity, $employee);
                
                if ($nextStep) {
                    // تحديث instance للخطوة التالية
                    $instance->update([
                        'workflow_step_id' => $nextStep->id,
                        'status' => 'in_progress',
                    ]);

                    // إرسال إشعار للموافق التالي
                    $this->notifyApprover($nextStep, $employee, $instance);
                } else {
                    // لا توجد خطوات أخرى - اكتمل سير العمل
                    $instance->update([
                        'status' => 'approved',
                        'completed_at' => now(),
                    ]);

                    // تحديث حالة الكيان
                    $this->updateEntityStatus($entity, 'approved');

                    // إرسال إشعار للموظف بالموافقة النهائية
                    $this->notifyEmployeeApproval($employee, $entity, $instance);
                }
            } else {
                // الرفض - إنهاء سير العمل
                $instance->update([
                    'status' => 'rejected',
                    'completed_at' => now(),
                ]);

                // تحديث حالة الكيان
                $this->updateEntityStatus($entity, 'rejected', $comments);

                // إرسال إشعار للموظف بالرفض
                $this->notifyEmployeeRejection($employee, $entity, $instance, $comments);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Workflow approval error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * الحصول على الخطوة التالية (مع دعم الشروط الديناميكية)
     */
    private function getNextStep(Workflow $workflow, WorkflowStep $currentStep, $entity, Employee $employee): ?WorkflowStep
    {
        $steps = $workflow->steps()
            ->where('is_required', true)
            ->where('step_order', '>', $currentStep->step_order)
            ->orderBy('step_order')
            ->get();

        foreach ($steps as $step) {
            // تقييم الشروط الديناميكية
            if ($this->approvalService->evaluateStepConditions($step, $entity)) {
                return $step;
            }
        }

        return null;
    }

    /**
     * الحصول على الكيان من instance
     */
    private function getEntity(WorkflowInstance $instance)
    {
        $modelClass = $this->getModelClass($instance->entity_type);
        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($instance->entity_id);
    }

    /**
     * الحصول على class name للكيان
     */
    public function getModelClass(string $entityType): ?string
    {
        return WorkflowEntityType::resolveModelClass($entityType);
    }

    public static function findInstanceForEntity(string $entityType, int $entityId, ?string $status = 'in_progress'): ?WorkflowInstance
    {
        $query = WorkflowInstance::where('entity_type', WorkflowEntityType::normalize($entityType))
            ->where('entity_id', $entityId);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->with('currentStep')->latest('id')->first();
    }

    /**
     * الحصول على الموظف من الكيان
     */
    private function getEmployeeFromEntity($entity): ?Employee
    {
        if (method_exists($entity, 'employee')) {
            return $entity->employee;
        }

        if (isset($entity->employee_id)) {
            return Employee::find($entity->employee_id);
        }

        return null;
    }

    /**
     * تحديث حالة الكيان
     */
    private function updateEntityStatus($entity, string $status, ?string $rejectionReason = null): void
    {
        if (! method_exists($entity, 'update')) {
            return;
        }

        if ($entity instanceof \App\Models\Ticket) {
            $status = $status === 'approved' ? 'open' : ($status === 'rejected' ? 'rejected' : $status);
        }

        $updateData = ['status' => $status];
        $fillable = $entity->getFillable();

        if ($status === 'approved') {
            if (in_array('approved_by', $fillable, true)) {
                $updateData['approved_by'] = auth()->id();
            }
            if (in_array('approved_at', $fillable, true)) {
                $updateData['approved_at'] = now();
            }
        }

        if ($status === 'rejected' && $rejectionReason && in_array('rejection_reason', $fillable, true)) {
            $updateData['rejection_reason'] = $rejectionReason;
        }

        $entity->update($updateData);
    }

    /**
     * إرسال إشعار للموافق
     */
    private function notifyApprover(WorkflowStep $step, Employee $employee, WorkflowInstance $instance): void
    {
        $approver = $this->approvalService->getApproverForStep($step, $employee);
        
        if (!$approver) {
            Log::warning("No approver found for workflow instance: {$instance->id}");
            return;
        }

        try {
            // الحصول على معلومات الكيان
            $entity = $this->getEntity($instance);
            if (!$entity) {
                Log::warning("Entity not found for workflow instance: {$instance->id}");
                return;
            }

            $entityType = $instance->entity_type;
            $entityName = $this->getEntityName($entity, $entityType);
            $employeeName = $employee->full_name ?? $employee->user->name ?? 'موظف';

            // إرسال Notification (Database + Broadcast)
            $approver->notify(new ApprovalRequestNotification(
                $instance,
                $entityType,
                $entityName,
                $employeeName
            ));

            // إرسال Event للبث المباشر (Real-time)
            event(new ApprovalRequestSent(
                $instance,
                $approver,
                $entityType,
                $entityName,
                $employeeName
            ));

            Log::info("Approval request notification sent to user: {$approver->id} for workflow instance: {$instance->id}");
        } catch (\Exception $e) {
            Log::error("Error sending approval notification: " . $e->getMessage());
        }
    }

    /**
     * الحصول على اسم الكيان
     */
    private function getEntityName($entity, string $entityType): string
    {
        $short = WorkflowEntityType::shortName($entityType);

        try {
            return match ($short) {
                'LeaveRequest' => "إجازة من {$entity->start_date->format('Y-m-d')} إلى {$entity->end_date->format('Y-m-d')}",
                'ExpenseRequest' => "مصروف: {$entity->amount} " . ($entity->currency?->code ?? $entity->currency_id ?? 'SAR'),
                'EmployeeJobChange' => 'تغيير وظيفي: ' . ($entity->change_type_label ?? $entity->change_type ?? ''),
                'OvertimeRecord' => 'ساعات إضافية: ' . ($entity->overtime_hours ?? $entity->hours ?? 0) . ' ساعة',
                'PerformanceReview' => 'تقييم أداء: ' . ($entity->review_period ?? 'غير محدد'),
                'Ticket' => 'تذكرة: ' . ($entity->title ?? ''),
                'ProjectTimeEntry' => 'وقت مشروع: ' . ($entity->hours ?? 0) . ' ساعة',
                default => 'طلب موافقة',
            };
        } catch (\Exception $e) {
            Log::warning("Error getting entity name: " . $e->getMessage());
            return 'طلب موافقة';
        }
    }

    /**
     * Record an approval/rejection action for the current workflow step.
     */
    public function recordStepAction(
        WorkflowInstance $instance,
        WorkflowStep $step,
        User $user,
        string $action,
        ?string $comments = null
    ): WorkflowStepAction {
        return WorkflowStepAction::create([
            'workflow_instance_id' => $instance->id,
            'workflow_step_id' => $step->id,
            'user_id' => $user->id,
            'action' => $action,
            'comments' => $comments,
            'acted_at' => now(),
        ]);
    }

    /**
     * Build timeline data for UI (all workflow types).
     *
     * @return array{instance: WorkflowInstance, steps: array<int, array>}
     */
    public function getWorkflowTimeline(WorkflowInstance $instance): array
    {
        $instance->load([
            'workflow.steps' => fn ($q) => $q->orderBy('step_order'),
            'currentStep',
            'stepActions.user',
            'stepActions.step',
        ]);

        $entity = $this->getEntity($instance);
        $employee = $this->getEmployeeFromEntity($entity);
        $actionsByStepId = $instance->stepActions->keyBy('workflow_step_id');
        $currentStep = $instance->currentStep;

        $steps = [];
        foreach ($instance->workflow->steps as $step) {
            $action = $actionsByStepId->get($step->id);
            $expectedApprover = $employee
                ? $this->approvalService->getApproverForStep($step, $employee, $entity)
                : null;

            $status = $this->resolveStepUiStatus($instance, $step, $currentStep, $action);

            $steps[] = [
                'step' => $step,
                'status' => $status,
                'expected_approver' => $expectedApprover,
                'action' => $action,
                'action_user' => $action?->user,
                'acted_at' => $action?->acted_at,
                'comments' => $action?->comments,
            ];
        }

        return [
            'instance' => $instance,
            'steps' => $steps,
        ];
    }

    /**
     * Flash message after approve/reject reflecting intermediate vs final state.
     */
    public function approvalFlashMessage(WorkflowInstance $instance, bool $approved): string
    {
        $instance->refresh();
        $instance->load('currentStep');

        if (! $approved || $instance->status === 'rejected') {
            return 'تم رفض الطلب.';
        }

        if ($instance->status === 'approved') {
            return 'تم اعتماد الطلب نهائياً.';
        }

        $nextLabel = $instance->currentStep?->name_ar
            ?? $instance->currentStep?->name
            ?? 'الموافق التالي';

        return "تمت موافقتك. الطلب بانتظار: {$nextLabel}";
    }

    /**
     * Find the latest workflow instance for an entity (in progress preferred).
     */
    public static function findLatestInstanceForEntity(string $entityType, int $entityId): ?WorkflowInstance
    {
        $normalized = WorkflowEntityType::normalize($entityType);

        $inProgress = WorkflowInstance::where('entity_type', $normalized)
            ->where('entity_id', $entityId)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if ($inProgress) {
            return $inProgress;
        }

        return WorkflowInstance::where('entity_type', $normalized)
            ->where('entity_id', $entityId)
            ->latest('id')
            ->first();
    }

    /**
     * الحصول على حالة سير العمل الحالية
     */
    public function getWorkflowStatus(WorkflowInstance $instance): array
    {
        $timeline = $this->getWorkflowTimeline($instance);
        $currentStep = $instance->currentStep;
        $entity = $this->getEntity($instance);
        $employee = $this->getEmployeeFromEntity($entity);

        $status = [
            'instance' => $instance,
            'current_step' => $currentStep,
            'next_approver' => null,
            'completed_steps' => [],
            'pending_steps' => [],
            'all_steps' => [],
        ];

        if ($employee && $currentStep && $instance->status === 'in_progress') {
            $status['next_approver'] = $this->approvalService->getApproverForStep($currentStep, $employee, $entity);
        }

        foreach ($timeline['steps'] as $stepData) {
            $legacy = [
                'step' => $stepData['step'],
                'approver' => $stepData['expected_approver'],
                'status' => $stepData['status'],
                'action' => $stepData['action'],
                'action_user' => $stepData['action_user'],
                'acted_at' => $stepData['acted_at'],
                'comments' => $stepData['comments'],
            ];
            $status['all_steps'][] = $legacy;

            if ($stepData['status'] === 'completed') {
                $status['completed_steps'][] = $legacy;
            } elseif ($stepData['status'] === 'pending') {
                $status['pending_steps'][] = $legacy;
            }
        }

        return $status;
    }

    private function resolveStepUiStatus(
        WorkflowInstance $instance,
        WorkflowStep $step,
        ?WorkflowStep $currentStep,
        ?WorkflowStepAction $action
    ): string {
        if ($action) {
            return 'completed';
        }

        if ($instance->status === 'in_progress' && $currentStep && $currentStep->id === $step->id) {
            return 'current';
        }

        if ($instance->status === 'in_progress' && $currentStep && $step->step_order < $currentStep->step_order) {
            return 'completed';
        }

        return 'pending';
    }

    /**
     * إرسال إشعار للموظف عند الموافقة النهائية
     */
    private function notifyEmployeeApproval(Employee $employee, $entity, WorkflowInstance $instance): void
    {
        $user = $employee->user;
        if (!$user) {
            Log::warning("No user found for employee: {$employee->id}");
            return;
        }

        try {
            $entityType = $instance->entity_type;
            $entityName = $this->getEntityName($entity, $entityType);

            $user->notify(new \App\Notifications\ApprovalCompletedNotification(
                $entityType,
                $entityName,
                $instance->initiated_by,
                $instance->completed_at ?? now()
            ));

            Log::info("Approval completed notification sent to employee: {$employee->id} for instance: {$instance->id}");
        } catch (\Exception $e) {
            Log::error("Error sending approval completed notification: " . $e->getMessage());
        }
    }

    /**
     * إرسال إشعار للموظف عند الرفض
     */
    private function notifyEmployeeRejection(Employee $employee, $entity, WorkflowInstance $instance, ?string $reason = null): void
    {
        $user = $employee->user;
        if (!$user) {
            Log::warning("No user found for employee: {$employee->id}");
            return;
        }

        try {
            $entityType = $instance->entity_type;
            $entityName = $this->getEntityName($entity, $entityType);

            $user->notify(new \App\Notifications\ApprovalRejectedNotification(
                $entityType,
                $entityName,
                $reason,
                $instance->initiated_by,
                $instance->completed_at ?? now()
            ));

            Log::info("Approval rejected notification sent to employee: {$employee->id} for instance: {$instance->id}");
        } catch (\Exception $e) {
            Log::error("Error sending approval rejected notification: " . $e->getMessage());
        }
    }
}
