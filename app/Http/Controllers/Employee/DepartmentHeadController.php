<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\ExpenseRequest;
use App\Models\Attendance;
use App\Models\WorkflowInstance;
use App\Models\Meeting;
use App\Models\Task;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentHeadController extends Controller
{
    public function __construct(
        protected ApprovalService $approvalService
    ) {}

    public function dashboard()
    {
        $user = Auth::user();
        if (! $user->isDepartmentHead()) {
            abort(403, 'ليس لديك صلاحية الوصول');
        }

        $employee = $user->employee;
        $departmentIds = $this->getAllManagedDepartmentIds($user);
        $employeeIds = Employee::whereIn('department_id', $departmentIds)
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $today = today();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // إحصائيات سريعة
        $stats = [
            'total_employees' => count($employeeIds),
            'present_today' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)
                ->where('status', 'present')->count(),
            'absent_today' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)
                ->where('status', 'absent')->count(),
            'late_today' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)
                ->where('status', 'late')->count(),
            'on_leave_today' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)
                ->where('status', 'on_leave')->count(),
            'pending_leaves' => LeaveRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'pending')->count(),
            'pending_expenses' => ExpenseRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'pending')->count(),
            'my_pending_approvals' => $this->getMyPendingApprovalsCount($user, $employeeIds),
            'month_attendance' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereMonth('attendance_date', $currentMonth)
                ->where('status', 'present')->count(),
        ];

        // الأقسام المُدارة
        $departments = Department::whereIn('id', $departmentIds)
            ->withCount(['employees' => fn($q) => $q->where('is_active', true)])
            ->get();

        // الطلبات المعلقة للموافقة
        $pendingApprovals = $this->getPendingApprovals($user, $employeeIds);

        // حضور اليوم
        $todayAttendance = Attendance::whereIn('employee_id', $employeeIds)
            ->whereDate('attendance_date', $today)
            ->with(['employee.user', 'employee.department', 'employee.position'])
            ->get();

        // الاجتماعات القادمة
        $upcomingMeetings = collect();
        if ($employee) {
            $upcomingMeetings = Meeting::where(function($q) use ($employee) {
                    $q->where('organizer_id', $employee->id)
                      ->orWhereHas('attendees', fn($q2) => $q2->where('employee_id', $employee->id));
                })
                ->where('start_time', '>', now())
                ->orderBy('start_time')
                ->limit(5)
                ->get();
        }

        // آخر النشاطات
        $recentLeaves = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->with(['employee', 'leaveType'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('employee.pages.department-head.dashboard', compact(
            'stats', 'departments', 'pendingApprovals', 'todayAttendance',
            'upcomingMeetings', 'recentLeaves', 'employee'
        ));
    }

    public function team()
    {
        $user = Auth::user();
        if (! $user->isDepartmentHead()) {
            abort(403, 'ليس لديك صلاحية الوصول');
        }

        $departmentIds = $this->getAllManagedDepartmentIds($user);

        $employees = Employee::whereIn('department_id', $departmentIds)
            ->with(['department', 'position', 'user', 'manager.department'])
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        // إحصائيات الفريق
        $today = today();
        $employeeIds = $employees->pluck('id')->all();

        $teamStats = [
            'total' => count($employeeIds),
            'present' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)->where('status', 'present')->count(),
            'absent' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)->where('status', 'absent')->count(),
            'late' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)->where('status', 'late')->count(),
            'on_leave' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', $today)->where('status', 'on_leave')->count(),
        ];

        // الأقسام
        $departments = Department::whereIn('id', $departmentIds)
            ->withCount(['employees' => fn($q) => $q->where('is_active', true)])
            ->get();

        return view('employee.pages.department-head.team', compact('employees', 'teamStats', 'departments'));
    }

    public function approvals()
    {
        $user = Auth::user();
        if (! $user->isDepartmentHead()) {
            abort(403, 'ليس لديك صلاحية الوصول');
        }

        $employeeIds = $user->getManagedEmployeeIds();

        $pendingApprovals = $this->getPendingApprovals($user, $employeeIds);

        $approvedCount = $this->countMonthlyApproved($employeeIds);
        $rejectedCount = $this->countMonthlyRejected($employeeIds);

        return view('employee.pages.department-head.approvals', compact('pendingApprovals', 'approvedCount', 'rejectedCount'));
    }

    public function structure()
    {
        $user = Auth::user();
        if (! $user->isDepartmentHead()) {
            abort(403, 'ليس لديك صلاحية الوصول');
        }

        $departmentIds = $this->getAllManagedDepartmentIds($user);

        $departments = Department::whereIn('id', $departmentIds)
            ->with(['manager', 'children', 'employees.user', 'employees.position'])
            ->get();

        $tree = $this->buildDepartmentTree($departments, null);

        return view('employee.pages.department-head.structure', compact('tree', 'departments'));
    }

    protected function getAllManagedDepartmentIds($user): array
    {
        $directIds = Department::where('manager_id', $user->id)->pluck('id')->all();

        $allIds = $directIds;
        $queue = $directIds;

        while (!empty($queue)) {
            $parentId = array_shift($queue);
            $childIds = Department::where('parent_id', $parentId)->pluck('id')->all();
            foreach ($childIds as $childId) {
                if (!in_array($childId, $allIds)) {
                    $allIds[] = $childId;
                    $queue[] = $childId;
                }
            }
        }

        return array_unique($allIds);
    }

    protected function getMyPendingApprovalsCount($user, array $employeeIds): int
    {
        $count = 0;

        $leaveRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->where('status', 'pending')
            ->with('employee')
            ->get();

        foreach ($leaveRequests as $request) {
            $instance = WorkflowInstance::where('entity_type', LeaveRequest::class)
                ->where('entity_id', $request->id)
                ->where('status', 'in_progress')
                ->with('currentStep')
                ->first();

            if ($instance && $instance->currentStep) {
                try {
                    if ($this->approvalService->canUserApprove(
                        $user, 'leave_request', $request->employee, $instance->currentStep->step_order
                    )) {
                        $count++;
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
        }

        $expenseRequests = ExpenseRequest::whereIn('employee_id', $employeeIds)
            ->where('status', 'pending')
            ->with('employee')
            ->get();

        foreach ($expenseRequests as $request) {
            $instance = WorkflowInstance::where('entity_type', ExpenseRequest::class)
                ->where('entity_id', $request->id)
                ->where('status', 'in_progress')
                ->with('currentStep')
                ->first();

            if ($instance && $instance->currentStep) {
                try {
                    if ($this->approvalService->canUserApprove(
                        $user, 'expense_request', $request->employee, $instance->currentStep->step_order
                    )) {
                        $count++;
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
        }

        return $count;
    }

    protected function getPendingApprovals($user, array $employeeIds)
    {
        $approvals = [];

        // طلبات الإجازة المعلقة
        $leaveRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->where('status', 'pending')
            ->with(['employee.user', 'employee.department', 'employee.position', 'leaveType'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($leaveRequests as $request) {
            $instance = WorkflowInstance::where('entity_type', LeaveRequest::class)
                ->where('entity_id', $request->id)
                ->where('status', 'in_progress')
                ->with('currentStep')
                ->first();

            if ($instance && $instance->currentStep) {
                try {
                    if ($this->approvalService->canUserApprove(
                        $user, 'leave_request', $request->employee, $instance->currentStep->step_order
                    )) {
                        $approvals[] = [
                            'type' => 'leave',
                            'request' => $request,
                            'instance' => $instance,
                            'step' => $instance->currentStep,
                            'created_at' => $request->created_at,
                        ];
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
        }

        // طلبات المصروفات المعلقة
        $expenseRequests = ExpenseRequest::whereIn('employee_id', $employeeIds)
            ->where('status', 'pending')
            ->with(['employee.user', 'employee.department', 'employee.position', 'category', 'currency'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($expenseRequests as $request) {
            $instance = WorkflowInstance::where('entity_type', ExpenseRequest::class)
                ->where('entity_id', $request->id)
                ->where('status', 'in_progress')
                ->with('currentStep')
                ->first();

            if ($instance && $instance->currentStep) {
                try {
                    if ($this->approvalService->canUserApprove(
                        $user, 'expense_request', $request->employee, $instance->currentStep->step_order
                    )) {
                        $approvals[] = [
                            'type' => 'expense',
                            'request' => $request,
                            'instance' => $instance,
                            'step' => $instance->currentStep,
                            'created_at' => $request->created_at,
                        ];
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
        }

        // ترتيب حسب الأقدم
        usort($approvals, fn($a, $b) => $a['created_at']->cmp($b['created_at']));

        return $approvals;
    }

    protected function buildDepartmentTree($departments, $parentId)
    {
        $tree = [];
        foreach ($departments as $dept) {
            if ($dept->parent_id == $parentId) {
                $children = $this->buildDepartmentTree($departments, $dept->id);
                $tree[] = [
                    'department' => $dept,
                    'children' => $children,
                ];
            }
        }
        return $tree;
    }

    protected function countMonthlyApproved(array $employeeIds): int
    {
        if ($employeeIds === []) {
            return 0;
        }

        $month = now()->month;
        $year = now()->year;

        return LeaveRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'approved')
                ->whereMonth('approved_at', $month)
                ->whereYear('approved_at', $year)
                ->count()
            + ExpenseRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'approved')
                ->whereMonth('updated_at', $month)
                ->whereYear('updated_at', $year)
                ->count();
    }

    protected function countMonthlyRejected(array $employeeIds): int
    {
        if ($employeeIds === []) {
            return 0;
        }

        $month = now()->month;
        $year = now()->year;

        return LeaveRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'rejected')
                ->whereMonth('updated_at', $month)
                ->whereYear('updated_at', $year)
                ->count()
            + ExpenseRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'rejected')
                ->whereMonth('updated_at', $month)
                ->whereYear('updated_at', $year)
                ->count();
    }
}
