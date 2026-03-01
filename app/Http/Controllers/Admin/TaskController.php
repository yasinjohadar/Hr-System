<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Department;
use App\Models\Employee;
use App\Models\TaskAssignment;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:task-list')->only('index');
        $this->middleware('permission:task-create')->only(['create', 'store']);
        $this->middleware('permission:task-edit')->only(['edit', 'update']);
        $this->middleware('permission:task-delete')->only('destroy');
        $this->middleware('permission:task-show')->only('show');
        $this->middleware('permission:task-assign')->only(['assign', 'storeAssignment']);
        $this->middleware('permission:task-comment')->only(['addComment', 'uploadAttachment']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with(['project', 'department', 'creator'])->withCount('assignments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('title_ar', 'like', "%$search%")
                  ->orWhere('task_code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $tasks = $query->latest()->paginate(15);
        $projects = Project::all();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.tasks.index', compact('tasks', 'projects', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.tasks.create', compact('projects', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'task_code' => 'nullable|string|max:50|unique:tasks,task_code',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:pending,in_progress,in_review,completed,cancelled,on_hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'progress' => 'nullable|integer|min:0|max:100',
            'estimated_hours' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        $task = Task::create($data);

        return redirect()->route('admin.tasks.show', $task->id)->with('success', 'تم إضافة المهمة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with([
            'project',
            'department',
            'creator',
            'assignments.employee',
            'assignments.assigner',
            'comments.employee',
            'comments.user',
            'comments.creator',
            'attachments.uploader'
        ])->findOrFail($id);

        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.tasks.show', compact('task', 'employees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        $projects = Project::all();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.tasks.edit', compact('task', 'projects', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'task_code' => 'nullable|string|max:50|unique:tasks,task_code,' . $id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:pending,in_progress,in_review,completed,cancelled,on_hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'progress' => 'nullable|integer|min:0|max:100',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();

        // إذا تم تغيير الحالة إلى completed، قم بتحديث تاريخ الإكمال
        if ($data['status'] == 'completed' && $task->status != 'completed') {
            $data['completed_date'] = now();
        } elseif ($data['status'] != 'completed') {
            $data['completed_date'] = null;
        }

        $task->update($data);

        return redirect()->route('admin.tasks.show', $task->id)->with('success', 'تم تحديث المهمة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);

        // حذف المرفقات
        foreach ($task->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $task->delete();

        return redirect()->route('admin.tasks.index')->with('success', 'تم حذف المهمة بنجاح.');
    }

    /**
     * تعيين مهمة لموظف
     */
    public function assign(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'due_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // التحقق من عدم تكرار التعيين
        $existing = TaskAssignment::where('task_id', $id)
            ->where('employee_id', $request->employee_id)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'هذا الموظف معين بالفعل على هذه المهمة.');
        }

        TaskAssignment::create([
            'task_id' => $id,
            'employee_id' => $request->employee_id,
            'assigned_by' => auth()->id(),
            'assigned_date' => now(),
            'due_date' => $request->due_date,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'تم تعيين المهمة للموظف بنجاح.');
    }

    /**
     * تحديث حالة تعيين مهمة
     */
    public function updateAssignment(Request $request, string $taskId, string $assignmentId)
    {
        $assignment = TaskAssignment::where('task_id', $taskId)
            ->where('id', $assignmentId)
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:assigned,in_progress,completed,cancelled',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $assignment->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث حالة التعيين بنجاح.');
    }

    /**
     * إضافة تعليق على مهمة
     */
    public function addComment(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'comment' => 'required|string',
            'employee_id' => 'nullable|exists:employees,id',
            'is_internal' => 'boolean',
        ]);

        TaskComment::create([
            'task_id' => $id,
            'employee_id' => $request->employee_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'is_internal' => $request->has('is_internal'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة التعليق بنجاح.');
    }

    /**
     * رفع مرفق لمهمة
     */
    public function uploadAttachment(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB Max
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('task_attachments', 'public');

            TaskAttachment::create([
                'task_id' => $id,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'description' => $request->description,
                'uploaded_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'تم رفع المرفق بنجاح.');
        }

        return redirect()->back()->with('error', 'حدث خطأ أثناء رفع الملف.');
    }

    /**
     * حذف مرفق
     */
    public function deleteAttachment(string $taskId, string $attachmentId)
    {
        $attachment = TaskAttachment::where('task_id', $taskId)
            ->where('id', $attachmentId)
            ->firstOrFail();

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return redirect()->back()->with('success', 'تم حذف المرفق بنجاح.');
    }
}
