<?php

namespace App\Services;

use App\Models\WorkflowInstance;
use App\Support\WorkflowEntityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class WorkflowProgressPresenter
{
    public function __construct(
        protected WorkflowService $workflowService
    ) {}

    /**
     * @return array{
     *   badge_ar: string,
     *   badge_variant: string,
     *   entity_status: string,
     *   instance: WorkflowInstance|null,
     *   timeline: array
     * }|null
     */
    /**
     * @param  Collection<int, Model>  $entities
     * @return array<int, array|null>
     */
    public function mapForEntities(Collection $entities): array
    {
        $map = [];
        foreach ($entities as $entity) {
            $map[(int) $entity->getKey()] = $this->resolveForEntity($entity);
        }

        return $map;
    }

    public function resolveForEntity(Model $entity): ?array
    {
        $workflowType = app(ApprovalService::class)->workflowTypeForEntity($entity);
        if (! $workflowType) {
            return null;
        }

        $instance = WorkflowService::findLatestInstanceForEntity($entity::class, (int) $entity->getKey());
        if (! $instance) {
            return null;
        }

        $statusField = config("approval_workflows.types.{$workflowType}.status_field", 'status');
        $entityStatus = (string) $entity->getAttribute($statusField);

        $timeline = $this->workflowService->getWorkflowTimeline($instance);

        return [
            'badge_ar' => $this->badgeAr($entity, $instance, $entityStatus, $timeline),
            'badge_variant' => $this->badgeVariant($entityStatus, $instance->status),
            'entity_status' => $entityStatus,
            'instance' => $instance,
            'timeline' => $timeline,
        ];
    }

    /**
     * @param  array{timeline: array}  $progress
     */
    public function badgeAr(Model $entity, WorkflowInstance $instance, string $entityStatus, array $progress): string
    {
        if ($entityStatus === 'approved') {
            return $this->entityStatusLabel($entity, 'approved');
        }

        if ($entityStatus === 'rejected') {
            return $this->entityStatusLabel($entity, 'rejected');
        }

        if ($instance->status === 'in_progress' && $instance->currentStep) {
            return $this->pendingStepLabel($instance->currentStep);
        }

        return $this->entityStatusLabel($entity, $entityStatus);
    }

    public function badgeVariant(string $entityStatus, string $instanceStatus): string
    {
        if ($entityStatus === 'approved' || $instanceStatus === 'approved') {
            return 'success';
        }

        if ($entityStatus === 'rejected' || $instanceStatus === 'rejected') {
            return 'danger';
        }

        return 'warning';
    }

    private function pendingStepLabel($step): string
    {
        $stepName = $step->name_ar ?? $step->name;

        if ($step->approver_type === 'department_manager') {
            return 'بانتظار موافقة رئيس القسم';
        }

        if ($step->approver_type === 'role' && $step->role_id) {
            $role = Role::find($step->role_id);
            $roleName = $role?->name ?? '';

            return match ($roleName) {
                'executive_director' => 'بانتظار موافقة المدير التنفيذي',
                'general_manager' => 'بانتظار موافقة المدير العام',
                default => "بانتظار: {$stepName}",
            };
        }

        return "بانتظار: {$stepName}";
    }

    private function entityStatusLabel(Model $entity, string $status): string
    {
        if (method_exists($entity, 'getStatusNameArAttribute') || isset($entity->status_name_ar)) {
            $original = $entity->status;
            $entity->status = $status;

            $label = $entity->status_name_ar;

            $entity->status = $original;

            return $label;
        }

        return match ($status) {
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'pending', 'pending_approval' => 'قيد الانتظار',
            default => $status,
        };
    }
}
