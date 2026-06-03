<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workflow;
use App\Services\WorkflowStepSyncService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class WorkflowController extends Controller
{
    public function __construct(
        protected WorkflowStepSyncService $stepSync
    ) {
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
        return view('admin.pages.workflows.create', array_merge($this->formSharedData(), [
            'editorSteps' => old('steps') ? array_values(old('steps')) : [],
            'hasActiveInstances' => false,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $this->validateWorkflow($request);

        $workflow = Workflow::create([
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active'),
            'created_by' => auth()->id(),
        ]);

        try {
            $this->stepSync->syncSteps($workflow, $validated['steps']);
        } catch (\RuntimeException $e) {
            $workflow->delete();

            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.workflows.show', $workflow->id)
            ->with('success', 'تم إضافة سير العمل والخطوات بنجاح.');
    }

    public function show(string $id)
    {
        $workflow = Workflow::with(['steps.role', 'steps.approver', 'creator'])
            ->withCount(['steps', 'instances'])
            ->findOrFail($id);

        return view('admin.pages.workflows.show', compact('workflow'));
    }

    public function edit(string $id)
    {
        $workflow = Workflow::withCount('instances')->findOrFail($id);

        return view('admin.pages.workflows.edit', array_merge(
            $this->formSharedData(),
            [
                'workflow' => $workflow,
                'editorSteps' => $this->editorStepsFromRequestOrWorkflow($workflow),
                'hasActiveInstances' => $workflow->instances_count > 0,
            ]
        ));
    }

    public function update(Request $request, string $id)
    {
        $workflow = Workflow::withCount('instances')->findOrFail($id);
        $validated = $this->validateWorkflow($request);

        $workflow->update([
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active'),
        ]);

        try {
            $this->stepSync->syncSteps($workflow, $validated['steps']);
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $message = 'تم تحديث سير العمل بنجاح.';
        if ($workflow->instances_count > 0) {
            $message .= ' التعديلات على الخطوات تطبّق على الطلبات الجديدة فقط.';
        }

        return redirect()
            ->route('admin.workflows.show', $workflow->id)
            ->with('success', $message);
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

    /**
     * @return array<string, mixed>
     */
    protected function validateWorkflow(Request $request): array
    {
        $workflowId = $request->route('workflow');

        return $request->validate(array_merge([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:workflows,code' . ($workflowId ? ',' . $workflowId : ''),
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', Workflow::allowedTypes()),
            'is_active' => 'boolean',
        ], WorkflowStepSyncService::stepValidationRules()));
    }

    /**
     * @return array<string, mixed>
     */
    protected function formSharedData(): array
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $defaultTemplate = [
            [
                'name_ar' => 'موافقة رئيس القسم',
                'name' => 'Department Head Approval',
                'approver_type' => 'department_manager',
                'role_id' => null,
                'approver_id' => null,
                'is_required' => true,
                'can_reject' => true,
            ],
            [
                'name_ar' => 'موافقة المدير التنفيذي',
                'name' => 'Executive Director Approval',
                'approver_type' => 'role',
                'role_id' => $roles->firstWhere('name', 'executive_director')?->id,
                'approver_id' => null,
                'is_required' => true,
                'can_reject' => true,
            ],
            [
                'name_ar' => 'موافقة المدير العام',
                'name' => 'General Manager Approval',
                'approver_type' => 'role',
                'role_id' => $roles->firstWhere('name', 'general_manager')?->id,
                'approver_id' => null,
                'is_required' => true,
                'can_reject' => true,
            ],
        ];

        return compact('roles', 'users', 'defaultTemplate');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function editorStepsFromRequestOrWorkflow(Workflow $workflow): array
    {
        if (old('steps')) {
            return array_values(old('steps'));
        }

        return $this->stepSync->stepsForEditor($workflow);
    }
}
