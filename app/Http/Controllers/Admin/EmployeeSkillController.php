<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSkill;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeSkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-skill-list')->only(['index', 'show']);
        $this->middleware('permission:employee-skill-create')->only(['create', 'store']);
        $this->middleware('permission:employee-skill-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-skill-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeSkill::with(['employee', 'verifier']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('proficiency_level')) {
            $query->where('proficiency_level', $request->input('proficiency_level'));
        }

        $skills = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.employee-skills.index', compact('skills', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.employee-skills.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'skill_name' => 'required|string|max:255',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0',
            'acquired_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        EmployeeSkill::create($data);

        return redirect()->route('admin.employee-skills.index')->with('success', 'تم إضافة المهارة بنجاح');
    }

    public function show(string $id)
    {
        $skill = EmployeeSkill::with(['employee', 'verifier'])->findOrFail($id);
        return view('admin.pages.employee-skills.show', compact('skill'));
    }

    public function edit(string $id)
    {
        $skill = EmployeeSkill::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.employee-skills.edit', compact('skill', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $skill = EmployeeSkill::findOrFail($id);

        $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0',
            'acquired_date' => 'nullable|date',
        ]);

        $skill->update($request->all());

        return redirect()->route('admin.employee-skills.index')->with('success', 'تم تحديث المهارة بنجاح');
    }

    public function destroy(Request $request)
    {
        $skill = EmployeeSkill::findOrFail($request->id);
        $skill->delete();

        return redirect()->route('admin.employee-skills.index')->with('success', 'تم حذف المهارة بنجاح');
    }

    public function verify(Request $request, string $id)
    {
        $skill = EmployeeSkill::findOrFail($id);
        $skill->update([
            'is_verified' => true,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تم التحقق من المهارة بنجاح');
    }
}
