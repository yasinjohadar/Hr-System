<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class WorkflowStepSyncService
{
    public const APPROVER_TYPES = [
        'department_manager',
        'employee_manager',
        'role',
        'user',
        'custom',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function stepValidationRules(): array
    {
        return [
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.name_ar' => ['required', 'string', 'max:255'],
            'steps.*.name' => ['nullable', 'string', 'max:255'],
            'steps.*.approver_type' => ['required', 'in:' . implode(',', self::APPROVER_TYPES)],
            'steps.*.role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'steps.*.approver_id' => ['nullable', 'integer', 'exists:users,id'],
            'steps.*.is_required' => ['nullable', 'boolean'],
            'steps.*.can_reject' => ['nullable', 'boolean'],
            'steps.*.id' => ['nullable', 'integer', 'exists:workflow_steps,id'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     */
    public function syncSteps(Workflow $workflow, array $steps): void
    {
        $normalized = $this->normalizeSteps($steps);
        $this->validateStepPayloads($normalized);
        $this->guardDeletingActiveSteps($workflow, $normalized);

        DB::transaction(function () use ($workflow, $normalized) {
            $keptIds = [];

            foreach ($normalized as $index => $stepData) {
                $order = $index + 1;
                $payload = $this->buildStepPayload($workflow->id, $order, $stepData);

                if (! empty($stepData['id'])) {
                    $step = WorkflowStep::where('workflow_id', $workflow->id)
                        ->where('id', $stepData['id'])
                        ->firstOrFail();
                    $step->update($payload);
                    $keptIds[] = $step->id;
                } else {
                    $step = WorkflowStep::create($payload);
                    $keptIds[] = $step->id;
                }
            }

            WorkflowStep::where('workflow_id', $workflow->id)
                ->whereNotIn('id', $keptIds)
                ->delete();
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     * @return array<int, array<string, mixed>>
     */
    public function normalizeSteps(array $steps): array
    {
        $list = array_values($steps);

        return array_map(function (array $step) {
            $step['is_required'] = filter_var($step['is_required'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $step['can_reject'] = filter_var($step['can_reject'] ?? true, FILTER_VALIDATE_BOOLEAN);

            if (($step['approver_type'] ?? '') !== 'role') {
                $step['role_id'] = null;
            }
            if (($step['approver_type'] ?? '') !== 'user') {
                $step['approver_id'] = null;
            }

            return $step;
        }, $list);
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     */
    protected function validateStepPayloads(array $steps): void
    {
        $errors = [];

        foreach ($steps as $i => $step) {
            $type = $step['approver_type'] ?? '';

            if ($type === 'role' && empty($step['role_id'])) {
                $errors["steps.{$i}.role_id"] = ['يجب اختيار الدور عندما يكون نوع الموافق «دور».'];
            }
            if ($type === 'user' && empty($step['approver_id'])) {
                $errors["steps.{$i}.approver_id"] = ['يجب اختيار المستخدم عندما يكون نوع الموافق «مستخدم».'];
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     */
    protected function guardDeletingActiveSteps(Workflow $workflow, array $steps): void
    {
        $submittedIds = collect($steps)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        $removedIds = WorkflowStep::where('workflow_id', $workflow->id)
            ->when($submittedIds !== [], fn ($q) => $q->whereNotIn('id', $submittedIds))
            ->pluck('id')
            ->all();

        if ($removedIds === []) {
            return;
        }

        $inUse = WorkflowInstance::where('workflow_id', $workflow->id)
            ->where('status', 'in_progress')
            ->whereIn('workflow_step_id', $removedIds)
            ->exists();

        if ($inUse) {
            throw new RuntimeException(
                'لا يمكن حذف خطوة مستخدمة في طلبات قيد الموافقة. أكمل الطلبات الجارية أو أضف خطوات جديدة فقط.'
            );
        }
    }

    /**
     * @param  array<string, mixed>  $stepData
     * @return array<string, mixed>
     */
    protected function buildStepPayload(int $workflowId, int $order, array $stepData): array
    {
        $nameAr = $stepData['name_ar'];
        $name = $stepData['name'] ?? $nameAr;

        return [
            'workflow_id' => $workflowId,
            'name' => $name,
            'name_ar' => $nameAr,
            'step_order' => $order,
            'approver_type' => $stepData['approver_type'],
            'approver_id' => $stepData['approver_id'] ?? null,
            'role_id' => $stepData['role_id'] ?? null,
            'is_required' => $stepData['is_required'],
            'can_reject' => $stepData['can_reject'],
        ];
    }

    /**
     * Map existing workflow steps for the editor.
     *
     * @return array<int, array<string, mixed>>
     */
    public function stepsForEditor(Workflow $workflow): array
    {
        return $workflow->steps()
            ->orderBy('step_order')
            ->get()
            ->map(fn (WorkflowStep $step) => [
                'id' => $step->id,
                'name' => $step->name,
                'name_ar' => $step->name_ar,
                'approver_type' => $step->approver_type,
                'role_id' => $step->role_id,
                'approver_id' => $step->approver_id,
                'is_required' => $step->is_required,
                'can_reject' => $step->can_reject,
            ])
            ->values()
            ->all();
    }
}
