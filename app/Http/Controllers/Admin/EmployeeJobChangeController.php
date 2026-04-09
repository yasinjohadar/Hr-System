<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobChange;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Branch;
use App\Models\WorkflowInstance;
use App\Services\ApprovalService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeJobChangeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-job-change-list')->only('index');
        $this->middleware('permission:employee-job-change-create')->only(['create', 'store']);
        $this->middleware('permission:employee-job-change-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-job-change-show')->only('show');
        $this->middleware('permission:employee-job-change-approve')->only('approve');
        $this->middleware('permission:employee-job-change-reject')->only('reject');
    }

    /**
     * عرض قائمة طلبات التغيير الوظيفي
     */
    public function index(Request $request)
    {
        $query = EmployeeJobChange::with(['employee', 'requestedBy', 'approvedBy']);

        // الفلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // الفلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // الفلترة حسب نوع التغيير
        if ($request->filled('change_type')) {
            $query->where('change_type', $request->change_type);
        }

        // الفلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->where('effective_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('effective_date', '<=', $request->date_to);
        }

        $jobChanges = $query->orderBy('created_at', 'desc')->paginate(15);

        // جلب الموظفين النشطين للفلتر
        $employees = Employee::where('is_active', true)->get(['id', 'first_name', 'last_name', 'full_name']);

        return view('admin.pages.employee-job-changes.index', compact('jobChanges', 'employees'));
    }

    /**
     * عرض صفحة إنشاء طلب تغيير وظيفي
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->get(['id', 'first_name', 'last_name', 'full_name', 'department_id', 'position_id', 'branch_id', 'salary']);
        $departments = Department::all(['id', 'name']);
        $positions = Position::all(['id', 'title']);
        $branches = Branch::all(['id', 'name']);

        return view('admin.pages.employee-job-changes.create', compact('employees', 'departments', 'positions', 'branches'));
    }

    /**
     * حفظ طلب تغيير وظيفي جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'change_type' => 'required|in:transfer,promotion,salary_change,demotion',
            'effective_date' => 'required|date',
            'reason' => 'nullable|string',
            // حقول النقل
            'new_department_id' => 'nullable|exists:departments,id',
            'new_position_id' => 'nullable|exists:positions,id',
            'new_branch_id' => 'nullable|exists:branches,id',
            'new_manager_id' => 'nullable|exists:employees,id',
            // حقول الراتب
            'new_salary' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        DB::beginTransaction();
        try {
            $jobChange = EmployeeJobChange::create([
                'employee_id' => $validated['employee_id'],
                'change_type' => $validated['change_type'],
                'status' => EmployeeJobChange::STATUS_PENDING,
                'effective_date' => $validated['effective_date'],
                'reason' => $validated['reason'] ?? null,
                'requested_by' => auth()->id(),
                // القيم القديمة
                'old_department_id' => $employee->department_id,
                'old_position_id' => $employee->position_id,
                'old_branch_id' => $employee->branch_id,
                'old_manager_id' => $employee->manager_id,
                'old_salary' => $employee->salary,
                // القيم الجديدة
                'new_department_id' => $validated['new_department_id'] ?? null,
                'new_position_id' => $validated['new_position_id'] ?? null,
                'new_branch_id' => $validated['new_branch_id'] ?? null,
                'new_manager_id' => $validated['new_manager_id'] ?? null,
                'new_salary' => $validated['new_salary'] ?? null,
            ]);

            app(WorkflowService::class)->startWorkflow(
                'employee_job_change',
                $employee,
                'EmployeeJobChange',
                $jobChange->id
            );

            DB::commit();
            return redirect()->route('admin.employee-job-changes.index')
                ->with('success', 'تم إنشاء طلب التغيير الوظيفي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل طلب تغيير وظيفي
     */
    public function show(EmployeeJobChange $employeeJobChange)
    {
        $employeeJobChange->load([
            'employee',
            'requestedBy',
            'approvedBy',
            'oldDepartment',
            'newDepartment',
            'oldPosition',
            'newPosition',
            'oldBranch',
            'newBranch',
            'oldManager',
            'newManager'
        ]);

        return view('admin.pages.employee-job-changes.show', compact('employeeJobChange'));
    }

    /**
     * عرض صفحة تعديل طلب تغيير وظيفي
     */
    public function edit(EmployeeJobChange $employeeJobChange)
    {
        if (!$employeeJobChange->canBeEdited()) {
            return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                ->with('error', 'لا يمكن تعديل هذا الطلب');
        }

        $employees = Employee::where('is_active', true)->get(['id', 'first_name', 'last_name', 'full_name']);
        $departments = Department::all(['id', 'name']);
        $positions = Position::all(['id', 'title']);
        $branches = Branch::all(['id', 'name']);

        return view('admin.pages.employee-job-changes.edit', compact('employeeJobChange', 'employees', 'departments', 'positions', 'branches'));
    }

    /**
     * تحديث طلب تغيير وظيفي
     */
    public function update(Request $request, EmployeeJobChange $employeeJobChange)
    {
        if (!$employeeJobChange->canBeEdited()) {
            return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                ->with('error', 'لا يمكن تعديل هذا الطلب');
        }

        $validated = $request->validate([
            'change_type' => 'required|in:transfer,promotion,salary_change,demotion',
            'effective_date' => 'required|date',
            'reason' => 'nullable|string',
            'new_department_id' => 'nullable|exists:departments,id',
            'new_position_id' => 'nullable|exists:positions,id',
            'new_branch_id' => 'nullable|exists:branches,id',
            'new_manager_id' => 'nullable|exists:employees,id',
            'new_salary' => 'nullable|numeric|min:0',
        ]);

        $employeeJobChange->update($validated);

        return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
            ->with('success', 'تم تحديث طلب التغيير الوظيفي بنجاح');
    }

    /**
     * الموافقة على طلب تغيير وظيفي
     */
    public function approve(Request $request, EmployeeJobChange $employeeJobChange)
    {
        if (!$employeeJobChange->canBeApproved()) {
            return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                ->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        $workflowService = app(WorkflowService::class);
        $approvalService = app(ApprovalService::class);
        $employee = $employeeJobChange->employee;

        $instance = WorkflowInstance::where('entity_type', 'EmployeeJobChange')
            ->where('entity_id', $employeeJobChange->id)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->first();

        if ($instance && $employee) {
            $currentStep = $instance->currentStep;
            if ($currentStep) {
                if (! $approvalService->canUserApprove(
                    auth()->user(),
                    'employee_job_change',
                    $employee,
                    $currentStep->step_order
                )) {
                    return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                        ->with('error', 'ليس لديك صلاحية الموافقة على هذه المرحلة من الطلب');
                }

                $ok = $workflowService->processApproval(
                    $instance,
                    auth()->user(),
                    true,
                    $request->comments ?? null
                );

                if ($ok) {
                    $instance->refresh();
                    if ($instance->status === 'approved') {
                        DB::beginTransaction();
                        try {
                            $this->applyApprovedJobChangeToEmployee($employeeJobChange->fresh());
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                                ->with('error', 'تم اعتماد سير العمل لكن فشل تطبيق التغييرات: '.$e->getMessage());
                        }
                    }

                    return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                        ->with('success', 'تم تسجيل الموافقة بنجاح');
                }

                return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                    ->with('error', 'حدث خطأ أثناء معالجة الموافقة');
            }
        }

        DB::beginTransaction();
        try {
            $employeeJobChange->update([
                'status' => EmployeeJobChange::STATUS_APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->applyApprovedJobChangeToEmployee($employeeJobChange->fresh());

            DB::commit();
            return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                ->with('success', 'تمت الموافقة على الطلب وتطبيق التغييرات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                ->with('error', 'حدث خطأ أثناء الموافقة على الطلب: ' . $e->getMessage());
        }
    }

    /**
     * رفض طلب تغيير وظيفي
     */
    public function reject(Request $request, EmployeeJobChange $employeeJobChange)
    {
        if (!$employeeJobChange->canBeRejected()) {
            return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                ->with('error', 'لا يمكن رفض هذا الطلب');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $workflowService = app(WorkflowService::class);
        $approvalService = app(ApprovalService::class);
        $employee = $employeeJobChange->employee;

        $instance = WorkflowInstance::where('entity_type', 'EmployeeJobChange')
            ->where('entity_id', $employeeJobChange->id)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->first();

        if ($instance && $employee) {
            $currentStep = $instance->currentStep;
            if ($currentStep) {
                if (! $approvalService->canUserApprove(
                    auth()->user(),
                    'employee_job_change',
                    $employee,
                    $currentStep->step_order
                )) {
                    return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                        ->with('error', 'ليس لديك صلاحية رفض هذه المرحلة من الطلب');
                }

                $ok = $workflowService->processApproval(
                    $instance,
                    auth()->user(),
                    false,
                    $validated['rejection_reason']
                );

                if ($ok) {
                    return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                        ->with('success', 'تم رفض الطلب بنجاح');
                }

                return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
                    ->with('error', 'حدث خطأ أثناء رفض الطلب عبر سير العمل');
            }
        }

        $employeeJobChange->update([
            'status' => EmployeeJobChange::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('admin.employee-job-changes.show', $employeeJobChange)
            ->with('success', 'تم رفض الطلب بنجاح');
    }

    /**
     * بعد اعتماد الطلب نهائياً: تطبيق الحقول الجديدة على سجل الموظف.
     */
    private function applyApprovedJobChangeToEmployee(EmployeeJobChange $employeeJobChange): void
    {
        $employee = $employeeJobChange->employee;
        $updateData = [];

        if ($employeeJobChange->new_department_id) {
            $updateData['department_id'] = $employeeJobChange->new_department_id;
        }
        if ($employeeJobChange->new_position_id) {
            $updateData['position_id'] = $employeeJobChange->new_position_id;
        }
        if ($employeeJobChange->new_branch_id) {
            $updateData['branch_id'] = $employeeJobChange->new_branch_id;
        }
        if ($employeeJobChange->new_manager_id) {
            $updateData['manager_id'] = $employeeJobChange->new_manager_id;
        }
        if ($employeeJobChange->new_salary !== null) {
            $updateData['salary'] = $employeeJobChange->new_salary;
        }

        if ($updateData !== []) {
            $employee->update($updateData);
        }
    }
}
