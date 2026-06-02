<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseRequest;
use App\Models\LeaveRequest;
use App\Models\Meeting;
use App\Models\WorkflowInstance;
use App\Services\ApprovalService;
use App\Services\DepartmentScopeService;
use Illuminate\Support\Facades\Auth;

class DepartmentTeamController extends Controller
{
    public function __construct(
        protected ApprovalService $approvalService,
        protected DepartmentScopeService $departmentScope
    ) {
        $this->middleware('auth');
        $this->middleware('ensure.department.head.or.admin');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $departmentIds = $user->getManagedDepartmentIds();
        $employeeIds = Employee::whereIn('department_id', $departmentIds)
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $today = today();
        $currentMonth = now()->month;

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

        $departments = Department::whereIn('id', $departmentIds)
            ->withCount(['employees' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $pendingApprovals = $this->getPendingApprovals($user, $employeeIds);

        $todayAttendance = Attendance::whereIn('employee_id', $employeeIds)
            ->whereDate('attendance_date', $today)
            ->with(['employee.user', 'employee.department', 'employee.position'])
            ->get();

        $upcomingMeetings = [];
        if ($employee) {
            $upcomingMeetings = Meeting::where(function ($q) use ($employee) {
                $q->where('organizer_id', $employee->id)
                    ->orWhereHas('attendees', fn ($q2) => $q2->where('employee_id', $employee->id));
            })
                ->where('start_time', '>', now())
                ->orderBy('start_time')
                ->limit(5)
                ->get();
        }

        $recentLeaves = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->with(['employee', 'leaveType'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.pages.team.dashboard', compact(
            'stats', 'departments', 'pendingApprovals', 'todayAttendance',
            'upcomingMeetings', 'recentLeaves', 'employee'
        ));
    }

    public function members()
    {
        $user = Auth::user();
        $departmentIds = $user->getManagedDepartmentIds();

        $employees = Employee::whereIn('department_id', $departmentIds)
            ->with(['department', 'position', 'user', 'manager.department'])
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

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

        $departments = Department::whereIn('id', $departmentIds)
            ->withCount(['employees' => fn ($q) => $q->where('is_active', true)])
            ->get();

        return view('admin.pages.team.team', compact('employees', 'teamStats', 'departments'));
    }

    public function approvals()
    {
        $user = Auth::user();
        $employeeIds = $user->getManagedEmployeeIds();

        $pendingApprovals = $this->getPendingApprovals($user, $employeeIds);

        $approvedCount = LeaveRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'approved')
                ->whereMonth('approved_at', now()->month)
                ->count()
            + ExpenseRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'approved')
                ->whereMonth('approved_at', now()->month)
                ->count();

        $rejectedCount = LeaveRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'rejected')
                ->whereMonth('updated_at', now()->month)
                ->count()
            + ExpenseRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'rejected')
                ->whereMonth('updated_at', now()->month)
                ->count();

        return view('admin.pages.team.approvals', compact('pendingApprovals', 'approvedCount', 'rejectedCount'));
    }

    public function structure()
    {
        $user = Auth::user();
        $departmentIds = $user->getManagedDepartmentIds();

        $departments = Department::whereIn('id', $departmentIds)
            ->with(['manager', 'children', 'employees.user', 'employees.position'])
            ->get();

        $tree = $this->buildDepartmentTree($departments, null);

        return view('admin.pages.team.structure', compact('tree', 'departments'));
    }

    protected function getMyPendingApprovalsCount($user, array $employeeIds): int
    {
        return count($this->getPendingApprovals($user, $employeeIds));
    }

    protected function getPendingApprovals($user, array $employeeIds): array
    {
        $approvals = [];

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
                }
            }
        }

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
                }
            }
        }

        usort($approvals, fn ($a, $b) => $a['created_at']->cmp($b['created_at']));

        return $approvals;
    }

    protected function buildDepartmentTree($departments, $parentId): array
    {
        $tree = [];
        foreach ($departments as $dept) {
            if ($dept->parent_id == $parentId) {
                $tree[] = [
                    'department' => $dept,
                    'children' => $this->buildDepartmentTree($departments, $dept->id),
                ];
            }
        }

        return $tree;
    }
}
