<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTimeEntry;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectTimeEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:project-edit');
    }

    public function store(Request $request, Project $project)
    {
        if (! $project->allowsTimeLogging()) {
            return redirect()
                ->route('admin.projects.show', $project)
                ->with('error', 'لا يمكن تسجيل وقت على مشروع مكتمل أو ملغى.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'task_id' => 'nullable|exists:tasks,id',
            'worked_date' => 'required|date',
            'hours' => 'required|numeric|min:0.01|max:24',
            'description' => 'nullable|string|max:2000',
        ]);

        if (! empty($validated['task_id'])) {
            $task = Task::find($validated['task_id']);
            if (! $task || (int) $task->project_id !== (int) $project->id) {
                return redirect()
                    ->route('admin.projects.show', $project)
                    ->with('error', 'المهمة المحددة لا تنتمي لهذا المشروع.');
            }
        }

        ProjectTimeEntry::create([
            'project_id' => $project->id,
            'employee_id' => $validated['employee_id'],
            'task_id' => $validated['task_id'] ?? null,
            'worked_date' => $validated['worked_date'],
            'hours' => $validated['hours'],
            'description' => $validated['description'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'تم تسجيل ساعات العمل.');
    }

    public function destroy(Project $project, ProjectTimeEntry $timeEntry)
    {
        abort_unless((int) $timeEntry->project_id === (int) $project->id, 404);

        $timeEntry->delete();

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'تم حذف سجل الوقت.');
    }
}
