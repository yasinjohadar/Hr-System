<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectBudgetOverride;
use App\Models\ProjectStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectStageService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Project $project, array $data, ?string $overrideReason = null): ProjectStage
    {
        return DB::transaction(function () use ($project, $data, $overrideReason) {
            $allocated = (float) ($data['allocated_amount'] ?? 0);
            $this->ensureBudgetAllows($project, $allocated, null, $overrideReason);

            $data['project_id'] = $project->id;
            $data['sort_order'] = $data['sort_order'] ?? (($project->stages()->max('sort_order') ?? 0) + 1);

            return ProjectStage::create($data);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ProjectStage $stage, array $data, ?string $overrideReason = null): ProjectStage
    {
        return DB::transaction(function () use ($stage, $data, $overrideReason) {
            $project = $stage->project()->lockForUpdate()->firstOrFail();
            $allocated = array_key_exists('allocated_amount', $data)
                ? (float) $data['allocated_amount']
                : (float) $stage->allocated_amount;

            $this->ensureBudgetAllows($project, $allocated, $stage, $overrideReason);

            $stage->update($data);

            return $stage->fresh();
        });
    }

    public function delete(ProjectStage $stage): void
    {
        if ($stage->members()->exists()) {
            throw ValidationException::withMessages([
                'stage' => 'لا يمكن حذف مرحلة عليها أعضاء. أزل الأعضاء أولاً أو ألغِ المرحلة.',
            ]);
        }

        if ($stage->fundTransfers()->exists()) {
            throw ValidationException::withMessages([
                'stage' => 'لا يمكن حذف مرحلة مرتبطة بتحويلات مالية. يمكن إلغاؤها بدلاً من الحذف.',
            ]);
        }

        $stage->delete();
    }

    protected function ensureBudgetAllows(
        Project $project,
        float $newAllocated,
        ?ProjectStage $excluding = null,
        ?string $overrideReason = null
    ): void {
        $budget = $project->budget;
        if ($budget === null) {
            return;
        }

        $query = $project->stages();
        if ($excluding) {
            $query->where('id', '!=', $excluding->id);
        }

        $others = (float) $query->sum('allocated_amount');
        $total = $others + $newAllocated;

        if ($total <= (float) $budget) {
            return;
        }

        if (! $overrideReason || ! auth()->user()?->can('project-budget-override')) {
            throw ValidationException::withMessages([
                'allocated_amount' => 'مجموع مبالغ المراحل ('.number_format($total, 2).') يتجاوز ميزانية المشروع ('.number_format((float) $budget, 2).'). يلزم سبب وموافقة تجاوز الميزانية.',
                'budget_override_reason' => 'أدخل سبب التجاوز إن كانت لديك صلاحية الموافقة.',
            ]);
        }

        ProjectBudgetOverride::create([
            'project_id' => $project->id,
            'previous_budget' => $budget,
            'requested_stages_total' => $total,
            'reason' => $overrideReason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }
}
