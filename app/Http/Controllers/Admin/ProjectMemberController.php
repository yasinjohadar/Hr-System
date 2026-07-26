<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectStage;
use App\Models\ProjectStageMember;
use App\Services\ProjectMembershipService;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    public function __construct(
        protected ProjectMembershipService $membershipService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:project-edit');
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|in:member,lead,sponsor',
            'assign_to_project' => 'nullable|boolean',
            'stage_ids' => 'nullable|array',
            'stage_ids.*' => 'integer|exists:project_stages,id',
            'stage_role' => 'nullable|in:member,lead,sponsor',
        ]);

        $stageIds = $validated['stage_ids'] ?? [];
        // Checkbox omitted when unchecked — treat missing as false only when stages were chosen
        $assignToProject = $request->has('assign_to_project')
            ? $request->boolean('assign_to_project')
            : ($stageIds === []);

        $this->membershipService->assign(
            $project,
            Employee::findOrFail($validated['employee_id']),
            $assignToProject,
            $stageIds,
            $validated['role'],
            $validated['stage_role'] ?? $validated['role']
        );

        return redirect()
            ->route('admin.projects.show', [$project, 'tab' => 'team'])
            ->with('success', 'تمت إضافة / تحديث عضوية الفريق.');
    }

    public function destroy(Project $project, ProjectMember $member)
    {
        abort_unless((int) $member->project_id === (int) $project->id, 404);

        $member->delete();

        return redirect()
            ->route('admin.projects.show', [$project, 'tab' => 'team'])
            ->with('success', 'تمت إزالة العضو من المشروع.');
    }

    public function destroyStageMember(Project $project, ProjectStage $stage, ProjectStageMember $stageMember)
    {
        abort_unless((int) $stage->project_id === (int) $project->id, 404);
        abort_unless((int) $stageMember->project_stage_id === (int) $stage->id, 404);

        $stageMember->delete();

        return redirect()
            ->route('admin.projects.show', [$project, 'tab' => 'stages'])
            ->with('success', 'تمت إزالة العضو من المرحلة.');
    }
}
