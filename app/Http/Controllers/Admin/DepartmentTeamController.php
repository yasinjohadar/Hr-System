<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseRequest;
use App\Models\LeaveRequest;
use App\Models\Meeting;
use App\Services\ApprovalService;
use App\Services\DepartmentScopeService;
use App\Services\EmployeeRequestSubmissionService;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class DepartmentTeamController extends Controller
{
    public function __construct(
        protected ApprovalService $approvalService,
        protected DepartmentScopeService $departmentScope,
        protected EmployeeRequestSubmissionService $submissionService
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

        $upcomingMeetings = collect();
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

        $approvedCount = $this->countMonthlyApproved($employeeIds);
        $rejectedCount = $this->countMonthlyRejected($employeeIds);

        $approvalTypeFilters = collect($this->submissionService->allTypes())
            ->map(fn ($config, $key) => [
                'key' => $config['team_filter'],
                'label' => $config['label_ar'],
            ])
            ->values()
            ->all();

        return view('admin.pages.team.approvals', compact(
            'pendingApprovals',
            'approvedCount',
            'rejectedCount',
            'approvalTypeFilters'
        ));
    }

    public function structure()
    {
        $user = Auth::user();
        $departmentIds = $user->getManagedDepartmentIds();

        $departments = Department::whereIn('id', $departmentIds)
            ->with(['manager', 'children', 'employees.user', 'employees.position'])
            ->get();

        $tree = $this->buildDepartmentTree($departments, null);

        // إحصاءات البنر — من نفس المجموعة المُحمَّلة أصلاً، بلا استعلامات إضافية
        $stats = [
            'departments' => $departments->count(),
            'sub_departments' => $departments->whereNotNull('parent_id')->count(),
            'employees' => $departments->sum(fn ($dept) => $dept->employees->count()),
        ];

        return view('admin.pages.team.structure', compact('tree', 'departments', 'stats'));
    }

    protected function getMyPendingApprovalsCount($user, array $employeeIds): int
    {
        return count($this->getPendingApprovals($user, $employeeIds));
    }

    protected function getPendingApprovals($user, array $employeeIds): array
    {
        $approvals = [];

        foreach ($this->submissionService->allTypes() as $workflowType => $config) {
            $query = $this->submissionService->pendingQueryFor($workflowType, $employeeIds);
            if (! empty($config['with'])) {
                $query->with($config['with']);
            }
            $requests = $query->orderByDesc('created_at')->get();

            foreach ($requests as $request) {
                if (! $request->employee) {
                    continue;
                }

                $instance = WorkflowService::findInstanceForEntity($config['model'], (int) $request->getKey());
                if (! $instance?->currentStep) {
                    continue;
                }

                try {
                    if (! $this->approvalService->canUserApprove(
                        $user,
                        $workflowType,
                        $request->employee,
                        $instance->currentStep->step_order
                    )) {
                        continue;
                    }

                    $approvals[] = [
                        'type' => $config['team_filter'],
                        'workflow_type' => $workflowType,
                        'label_ar' => $config['label_ar'],
                        'request' => $request,
                        'instance' => $instance,
                        'step' => $instance->currentStep,
                        'show_route' => $config['show_route'] ?? null,
                        'created_at' => $request->created_at,
                    ];
                } catch (\Exception $e) {
                }
            }
        }

        usort($approvals, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

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
