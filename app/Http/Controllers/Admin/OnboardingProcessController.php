<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingProcess;
use App\Models\OnboardingTemplate;
use App\Models\OnboardingTask;
use App\Models\OnboardingChecklist;
use App\Models\Employee;
use Illuminate\Http\Request;

class OnboardingProcessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:onboarding-process-list')->only('index');
        $this->middleware('permission:onboarding-process-create')->only(['create', 'store']);
        $this->middleware('permission:onboarding-process-edit')->only(['edit', 'update']);
        $this->middleware('permission:onboarding-process-delete')->only('destroy');
        $this->middleware('permission:onboarding-process-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = OnboardingProcess::with(['employee', 'template', 'assignedTo', 'creator'])
            ->withCount('checklists');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('process_code', 'like', "%$search%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('full_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $processes = $query->latest()->paginate(15);
        $employees = Employee::where('is_active', true)->get();
        $templates = OnboardingTemplate::where('is_active', true)->get();

        return view('admin.pages.onboarding-processes.index', compact('processes', 'employees', 'templates'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $templates = OnboardingTemplate::where('is_active', true)->get();
        $coordinators = Employee::where('is_active', true)->get();

        return view('admin.pages.onboarding-processes.create', compact('employees', 'templates', 'coordinators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'nullable|exists:onboarding_templates,id',
            'start_date' => 'required|date',
            'expected_completion_date' => 'nullable|date|after:start_date',
            'assigned_to' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'not_started';
        $data['completion_percentage'] = 0;

        $process = OnboardingProcess::create($data);

        // إنشاء checklist items من المهام في القالب إذا كان موجوداً
        if ($process->template) {
            $templateTasks = OnboardingTask::where('template_id', $process->template_id)
                ->where('is_active', true)
                ->orderBy('task_order')
                ->get();
            
            foreach ($templateTasks as $index => $task) {
                OnboardingChecklist::create([
                    'process_id' => $process->id,
                    'task_id' => $task->id,
                    'status' => 'pending',
                    'due_date' => $process->start_date->copy()->addDays($index + 1),
                ]);
            }
        }

        return redirect()->route('admin.onboarding-processes.index')->with('success', 'تم إنشاء عملية الاستقبال بنجاح.');
    }

    public function show(string $id)
    {
        $process = OnboardingProcess::with([
            'employee',
            'template',
            'assignedTo',
            'checklists.task',
            'checklists.completedBy',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.onboarding-processes.show', compact('process'));
    }

    public function edit(string $id)
    {
        $process = OnboardingProcess::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        $templates = OnboardingTemplate::where('is_active', true)->get();
        $coordinators = Employee::where('is_active', true)->get();

        return view('admin.pages.onboarding-processes.edit', compact('process', 'employees', 'templates', 'coordinators'));
    }

    public function update(Request $request, string $id)
    {
        $process = OnboardingProcess::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'nullable|exists:onboarding_templates,id',
            'start_date' => 'required|date',
            'expected_completion_date' => 'nullable|date|after:start_date',
            'actual_completion_date' => 'nullable|date',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'assigned_to' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $process->update($request->all());

        return redirect()->route('admin.onboarding-processes.index')->with('success', 'تم تحديث عملية الاستقبال بنجاح.');
    }

    public function destroy(string $id)
    {
        $process = OnboardingProcess::findOrFail($id);
        $process->delete();

        return redirect()->route('admin.onboarding-processes.index')->with('success', 'تم حذف عملية الاستقبال بنجاح.');
    }
}
