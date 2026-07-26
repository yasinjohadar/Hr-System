<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Services\ProjectStageService;
use Illuminate\Http\Request;

class ProjectStageController extends Controller
{
    public function __construct(
        protected ProjectStageService $stageService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:project-stage-list')->only('index');
        $this->middleware('permission:project-stage-create')->only(['create', 'store']);
        $this->middleware('permission:project-stage-edit')->only(['edit', 'update']);
        $this->middleware('permission:project-stage-delete')->only('destroy');
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'allocated_amount' => 'required|numeric|min:0',
            'status' => 'required|in:planned,active,completed,cancelled',
            'notes' => 'nullable|string',
            'budget_override_reason' => 'nullable|string|max:2000',
        ]);

        $reason = $validated['budget_override_reason'] ?? null;
        unset($validated['budget_override_reason']);

        $this->stageService->create($project, $validated, $reason);

        return redirect()
            ->route('admin.projects.show', [$project, 'tab' => 'stages'])
            ->with('success', 'تمت إضافة المرحلة.');
    }

    public function update(Request $request, Project $project, ProjectStage $stage)
    {
        abort_unless((int) $stage->project_id === (int) $project->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'allocated_amount' => 'required|numeric|min:0',
            'status' => 'required|in:planned,active,completed,cancelled',
            'notes' => 'nullable|string',
            'budget_override_reason' => 'nullable|string|max:2000',
        ]);

        $reason = $validated['budget_override_reason'] ?? null;
        unset($validated['budget_override_reason']);

        $this->stageService->update($stage, $validated, $reason);

        return redirect()
            ->route('admin.projects.show', [$project, 'tab' => 'stages'])
            ->with('success', 'تم تحديث المرحلة.');
    }

    public function destroy(Project $project, ProjectStage $stage)
    {
        abort_unless((int) $stage->project_id === (int) $project->id, 404);

        $this->stageService->delete($stage);

        return redirect()
            ->route('admin.projects.show', [$project, 'tab' => 'stages'])
            ->with('success', 'تم حذف المرحلة.');
    }
}
