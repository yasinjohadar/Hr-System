<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\DepartmentHeadCapabilitiesService;
use App\Services\DepartmentHeadRoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class DepartmentHeadController extends Controller
{
    public function __construct(
        protected DepartmentHeadRoleService $departmentHeadRole,
        protected DepartmentHeadCapabilitiesService $capabilitiesService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:department-head-list')->only(['index', 'show', 'capabilities']);
        $this->middleware('permission:department-head-manage')->only(['create', 'store', 'edit', 'update', 'destroy', 'removeDepartment', 'applyRoleTemplate']);
    }

    public function index(Request $request)
    {
        $query = User::query()
            ->where(function ($q) {
                $q->role('department_head')
                    ->orWhereIn('id', Department::whereNotNull('manager_id')->pluck('manager_id'));
            })
            ->with(['employee.department', 'roles']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('employee_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $heads = $query->orderBy('name')->paginate(15)->withQueryString();

        $headIds = $heads->pluck('id');
        $departmentsByManager = Department::whereIn('manager_id', $headIds)
            ->get()
            ->groupBy('manager_id');

        foreach ($heads as $head) {
            $head->managed_departments_list = $departmentsByManager->get($head->id, collect());
            $head->managed_team_count = count($head->getManagedEmployeeIds());
        }

        $stats = [
            'total' => User::role('department_head')->count(),
            'with_departments' => Department::whereNotNull('manager_id')->distinct('manager_id')->count('manager_id'),
            'unassigned_departments' => Department::where('is_active', true)->whereNull('manager_id')->count(),
        ];

        return view('admin.pages.department-heads.index', compact('heads', 'stats'));
    }

    public function create()
    {
        $users = User::where('is_active', true)
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))
            ->with('employee')
            ->orderBy('name')
            ->get();

        $departments = Department::where('is_active', true)
            ->with('manager')
            ->orderBy('name')
            ->get();

        return view('admin.pages.department-heads.create', compact('users', 'departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
        ], [
            'department_ids.required' => 'اختر قسماً واحداً على الأقل.',
        ]);

        $user = User::findOrFail($data['user_id']);

        if ($user->hasRole('admin')) {
            return back()->with('error', 'لا يمكن تعيين مدير النظام كرئيس قسم.');
        }

        DB::transaction(function () use ($user, $data) {
            $this->departmentHeadRole->assignDepartments($user, $data['department_ids']);
        });

        return redirect()
            ->route('admin.department-heads.show', $user->id)
            ->with('success', 'تم تعيين رئيس القسم بنجاح.');
    }

    public function capabilities(string $id)
    {
        $head = User::with(['employee', 'roles'])->findOrFail($id);

        if (! $head->hasRole('department_head') && ! Department::where('manager_id', $head->id)->exists()) {
            abort(404);
        }

        $capabilities = $this->capabilitiesService->build($head);

        return view('admin.pages.department-heads.capabilities', compact('head', 'capabilities'));
    }

    public function show(string $id)
    {
        $head = User::with(['employee.department', 'roles.permissions'])->findOrFail($id);

        if (! $head->hasRole('department_head') && ! Department::where('manager_id', $head->id)->exists()) {
            abort(404);
        }

        $managedDepartments = Department::where('manager_id', $head->id)
            ->withCount(['employees' => fn ($q) => $q->where('is_active', true)])
            ->with('parent')
            ->get();

        $managedEmployeeIds = $head->getManagedEmployeeIds();
        $pendingLeaves = LeaveRequest::where('status', 'pending')
            ->whereIn('employee_id', $managedEmployeeIds)
            ->count();

        $permissions = $head->getAllPermissions()->pluck('name')->sort()->values();

        $roleTemplate = config('role-templates.department_head');

        return view('admin.pages.department-heads.show', compact(
            'head',
            'managedDepartments',
            'managedEmployeeIds',
            'pendingLeaves',
            'permissions',
            'roleTemplate'
        ));
    }

    public function edit(string $id)
    {
        $head = User::with('employee')->findOrFail($id);

        $managedDepartmentIds = Department::where('manager_id', $head->id)->pluck('id')->all();

        $departments = Department::where('is_active', true)
            ->with('manager')
            ->orderBy('name')
            ->get();

        return view('admin.pages.department-heads.edit', compact('head', 'managedDepartmentIds', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        $head = User::findOrFail($id);

        $data = $request->validate([
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
        ]);

        if ($head->hasRole('admin')) {
            return back()->with('error', 'لا يمكن تعديل تعيين مدير النظام.');
        }

        DB::transaction(function () use ($head, $data) {
            Department::where('manager_id', $head->id)->update(['manager_id' => null]);
            $ids = $data['department_ids'] ?? [];
            if (! empty($ids)) {
                $this->departmentHeadRole->assignDepartments($head, $ids);
            } else {
                $this->departmentHeadRole->revokeRoleIfNotManagingAnyDepartment($head);
            }
        });

        return redirect()
            ->route('admin.department-heads.show', $head->id)
            ->with('success', 'تم تحديث أقسام رئيس القسم.');
    }

    public function destroy(string $id)
    {
        $head = User::findOrFail($id);

        if ($head->hasRole('admin')) {
            return back()->with('error', 'لا يمكن إزالة مدير النظام.');
        }

        $this->departmentHeadRole->removeFromAllDepartments($head);

        return redirect()
            ->route('admin.department-heads.index')
            ->with('success', 'تم إلغاء تعيين رئيس القسم من جميع الأقسام.');
    }

    public function removeDepartment(string $userId, string $departmentId)
    {
        $head = User::findOrFail($userId);
        $department = Department::findOrFail($departmentId);

        $this->departmentHeadRole->removeFromDepartment($head, $department);

        return back()->with('success', 'تم إزالة رئيس القسم من القسم «' . $department->name . '».');
    }

    public function applyRoleTemplate(string $id)
    {
        $head = User::findOrFail($id);

        if ($head->hasRole('admin')) {
            return back()->with('error', 'غير متاح لمدير النظام.');
        }

        $template = config('role-templates.department_head');
        if (! $template || empty($template['permissions'])) {
            return back()->with('error', 'قالب رئيس القسم غير معرّف.');
        }

        $this->departmentHeadRole->ensureRole($head);
        $head->syncPermissions(
            Permission::whereIn('name', $template['permissions'])->pluck('name')
        );

        return back()->with('success', 'تم تطبيق صلاحيات قالب رئيس القسم على المستخدم.');
    }
}
