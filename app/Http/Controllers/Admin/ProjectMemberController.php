<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:project-edit');
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|in:member,lead,sponsor',
        ]);

        $project->members()->updateOrCreate(
            ['employee_id' => $validated['employee_id']],
            ['role' => $validated['role']]
        );

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'تمت إضافة عضو الفريق.');
    }

    public function destroy(Project $project, ProjectMember $member)
    {
        abort_unless((int) $member->project_id === (int) $project->id, 404);

        $member->delete();

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'تمت إزالة العضو من المشروع.');
    }
}
