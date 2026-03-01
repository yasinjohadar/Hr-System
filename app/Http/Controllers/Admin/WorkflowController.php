<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:workflow-list')->only('index');
        $this->middleware('permission:workflow-create')->only(['create', 'store']);
        $this->middleware('permission:workflow-edit')->only(['edit', 'update']);
        $this->middleware('permission:workflow-delete')->only('destroy');
        $this->middleware('permission:workflow-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Workflow::with('creator')->withCount(['steps', 'instances']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $workflows = $query->latest()->paginate(15);

        return view('admin.pages.workflows.index', compact('workflows'));
    }

    public function create()
    {
        return view('admin.pages.workflows.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:workflows,code',
            'description' => 'nullable|string',
            'type' => 'required|in:leave_request,expense_request,task_approval,performance_review,custom',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        Workflow::create($data);

        return redirect()->route('admin.workflows.index')->with('success', 'تم إضافة سير العمل بنجاح.');
    }

    public function show(string $id)
    {
        $workflow = Workflow::with(['steps', 'instances', 'creator'])->findOrFail($id);
        return view('admin.pages.workflows.show', compact('workflow'));
    }

    public function edit(string $id)
    {
        $workflow = Workflow::findOrFail($id);
        return view('admin.pages.workflows.edit', compact('workflow'));
    }

    public function update(Request $request, string $id)
    {
        $workflow = Workflow::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:workflows,code,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:leave_request,expense_request,task_approval,performance_review,custom',
            'is_active' => 'boolean',
        ]);

        $workflow->update($request->all());

        return redirect()->route('admin.workflows.index')->with('success', 'تم تحديث سير العمل بنجاح.');
    }

    public function destroy(string $id)
    {
        $workflow = Workflow::findOrFail($id);

        if ($workflow->instances()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف سير العمل لأنه مستخدم في طلبات.');
        }

        $workflow->delete();

        return redirect()->route('admin.workflows.index')->with('success', 'تم حذف سير العمل بنجاح.');
    }
}
