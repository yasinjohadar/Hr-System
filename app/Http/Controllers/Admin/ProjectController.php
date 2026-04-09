<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Currency;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:project-list')->only('index');
        $this->middleware('permission:project-create')->only(['create', 'store']);
        $this->middleware('permission:project-edit')->only(['edit', 'update']);
        $this->middleware('permission:project-delete')->only('destroy');
        $this->middleware('permission:project-show')->only(['show', 'exportTimeEntries']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['department', 'manager', 'currency', 'creator'])->withCount('tasks');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('project_code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $projects = $query->latest()->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.projects.index', compact('projects', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.projects.create', compact('departments', 'employees', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:50|unique:projects,project_code',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success', 'تم إضافة المشروع بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::withCount('tasks')
            ->withSum('timeEntries as total_logged_hours', 'hours')
            ->with([
                'department',
                'manager',
                'currency',
                'creator',
                'members.employee',
                'documents.uploader',
                'timeEntries' => function ($query) {
                    $query->with(['employee', 'task', 'creator'])
                        ->orderByDesc('worked_date')
                        ->orderByDesc('id')
                        ->limit(250);
                },
                'tasks' => function ($query) {
                    $query->withCount('assignments');
                },
            ])
            ->findOrFail($id);

        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();

        return view('admin.pages.projects.show', compact('project', 'employees'));
    }

    /**
     * تصدير سجلات وقت المشروع (CSV)
     */
    public function exportTimeEntries(string $id)
    {
        $project = Project::findOrFail($id);

        $entries = $project->timeEntries()
            ->with(['employee', 'task'])
            ->orderBy('worked_date')
            ->orderBy('id')
            ->get();

        $filename = 'project-'.$project->project_code.'-time.csv';

        return response()->streamDownload(function () use ($entries, $project) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['رمز المشروع', 'الموظف', 'التاريخ', 'الساعات', 'المهمة', 'الوصف']);
            foreach ($entries as $e) {
                $taskLabel = '';
                if ($e->task) {
                    $taskLabel = $e->task->task_code.' — '.($e->task->title_ar ?? $e->task->title);
                }
                fputcsv($out, [
                    $project->project_code,
                    $e->employee->full_name ?? '',
                    $e->worked_date->format('Y-m-d'),
                    (string) $e->hours,
                    $taskLabel,
                    $e->description ?? '',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.projects.edit', compact('project', 'departments', 'employees', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:50|unique:projects,project_code,' . $id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $project->update($request->all());

        return redirect()->route('admin.projects.index')->with('success', 'تم تحديث المشروع بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        if ($project->tasks()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف المشروع لأنه يحتوي على مهام.');
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'تم حذف المشروع بنجاح.');
    }
}
