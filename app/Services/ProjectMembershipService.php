<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\ProjectStageMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectMembershipService
{
    /**
     * Flexible assignment: project and/or one or more stages.
     *
     * @param  array<int>  $stageIds
     */
    public function assign(
        Project $project,
        Employee $employee,
        bool $assignToProject = true,
        array $stageIds = [],
        string $projectRole = 'member',
        ?string $stageRole = 'member'
    ): void {
        if (! $assignToProject && $stageIds === []) {
            throw ValidationException::withMessages([
                'scope' => 'يجب اختيار العضوية في المشروع و/أو مرحلة واحدة على الأقل.',
            ]);
        }

        DB::transaction(function () use ($project, $employee, $assignToProject, $stageIds, $projectRole, $stageRole) {
            if ($assignToProject) {
                $project->members()->updateOrCreate(
                    ['employee_id' => $employee->id],
                    ['role' => $projectRole]
                );
            }

            if ($stageIds === []) {
                return;
            }

            $validStageIds = $project->stages()->whereIn('id', $stageIds)->pluck('id')->all();
            if (count($validStageIds) !== count(array_unique($stageIds))) {
                throw ValidationException::withMessages([
                    'stage_ids' => 'بعض المراحل المحددة لا تتبع هذا المشروع.',
                ]);
            }

            foreach ($validStageIds as $stageId) {
                ProjectStageMember::updateOrCreate(
                    [
                        'project_stage_id' => $stageId,
                        'employee_id' => $employee->id,
                    ],
                    ['role' => $stageRole]
                );
            }
        });
    }

    public function removeFromStage(ProjectStage $stage, Employee $employee): void
    {
        ProjectStageMember::where('project_stage_id', $stage->id)
            ->where('employee_id', $employee->id)
            ->delete();
    }

    public function removeFromProject(Project $project, Employee $employee, bool $alsoStages = false): void
    {
        $project->members()->where('employee_id', $employee->id)->delete();

        if ($alsoStages) {
            ProjectStageMember::whereIn(
                'project_stage_id',
                $project->stages()->pluck('id')
            )->where('employee_id', $employee->id)->delete();
        }
    }
}
