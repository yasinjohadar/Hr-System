<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\Employee;
use App\Services\ApprovalService;
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

        // إنشاء instance جديد
        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'workflow_step_id' => $firstStep->id,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'status' => 'pending',
            'initiated_by' => auth()->id(),
            'started_at' => now(),
        ]);

        // إرسال إشعار للموافق الأول
        $this->notifyApprover($firstStep, $employee, $instance);

        return $instance;
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

            if ($approved) {
                // الموافقة - الانتقال للخطوة التالية
                $nextStep = $this->getNextStep($instance->workflow, $currentStep);
                
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
                }
            } else {
                // الرفض - إنهاء سير العمل
                $instance->update([
                    'status' => 'rejected',
                    'completed_at' => now(),
                ]);

                // تحديث حالة الكيان
                $this->updateEntityStatus($entity, 'rejected', $comments);
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
     * الحصول على الخطوة التالية
     */
    private function getNextStep(Workflow $workflow, WorkflowStep $currentStep): ?WorkflowStep
    {
        return $workflow->steps()
            ->where('is_required', true)
            ->where('step_order', '>', $currentStep->step_order)
            ->orderBy('step_order')
            ->first();
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
    private function getModelClass(string $entityType): ?string
    {
        return match($entityType) {
            'LeaveRequest' => \App\Models\LeaveRequest::class,
            'ExpenseRequest' => \App\Models\ExpenseRequest::class,
            'OvertimeRecord' => \App\Models\OvertimeRecord::class,
            'Payroll' => \App\Models\Payroll::class,
            'PerformanceReview' => \App\Models\PerformanceReview::class,
            default => null,
        };
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
        if (method_exists($entity, 'update')) {
            $updateData = ['status' => $status];
            
            if ($status === 'approved' && method_exists($entity, 'approved_by')) {
                $updateData['approved_by'] = auth()->id();
                $updateData['approved_at'] = now();
            }
            
            if ($status === 'rejected' && $rejectionReason) {
                if (method_exists($entity, 'rejection_reason')) {
                    $updateData['rejection_reason'] = $rejectionReason;
                }
            }

            $entity->update($updateData);
        }
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
        try {
            return match($entityType) {
                'LeaveRequest' => "إجازة من {$entity->start_date->format('Y-m-d')} إلى {$entity->end_date->format('Y-m-d')}",
                'ExpenseRequest' => "مصروف: {$entity->amount} " . ($entity->currency?->code ?? $entity->currency_id ?? 'SAR'),
                'OvertimeRecord' => "ساعات إضافية: " . ($entity->overtime_hours ?? $entity->hours ?? 0) . " ساعة",
                'PerformanceReview' => "تقييم أداء: " . ($entity->review_period ?? 'غير محدد'),
                default => 'طلب موافقة',
            };
        } catch (\Exception $e) {
            Log::warning("Error getting entity name: " . $e->getMessage());
            return 'طلب موافقة';
        }
    }

    /**
     * الحصول على حالة سير العمل الحالية
     */
    public function getWorkflowStatus(WorkflowInstance $instance): array
    {
        $workflow = $instance->workflow;
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

        if ($currentStep && $employee) {
            $nextApprover = $this->approvalService->getApproverForStep($currentStep, $employee);
            $status['next_approver'] = $nextApprover;

            // الحصول على جميع الخطوات
            $allSteps = $workflow->steps()->orderBy('step_order')->get();
            foreach ($allSteps as $step) {
                $stepData = [
                    'step' => $step,
                    'approver' => $this->approvalService->getApproverForStep($step, $employee),
                    'status' => $step->step_order < $currentStep->step_order ? 'completed' : 
                               ($step->step_order == $currentStep->step_order ? 'current' : 'pending'),
                ];

                $status['all_steps'][] = $stepData;

                if ($stepData['status'] === 'completed') {
                    $status['completed_steps'][] = $stepData;
                } elseif ($stepData['status'] === 'pending') {
                    $status['pending_steps'][] = $stepData;
                }
            }
        }

        return $status;
    }
}
