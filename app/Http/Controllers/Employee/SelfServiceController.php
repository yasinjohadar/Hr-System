<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\EmployeeDocument;
use App\Models\EmployeeSkill;
use App\Models\EmployeeCertificate;
use App\Models\EmployeeGoal;
use App\Models\PerformanceReview;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\EmployeeBenefit;
use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectTimeEntry;
use App\Models\Ticket;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\ExpenseRequest;
use App\Models\AssetAssignment;
use App\Models\EmployeeViolation;
use App\Models\Announcement;
use App\Models\Policy;
use App\Models\PolicyAcknowledgment;
use App\Models\Contract;
use App\Services\EmployeeRequestSubmissionService;
use Illuminate\Support\Facades\DB;
use App\Models\Payroll;
use App\Models\ExpenseCategory;
use App\Models\Currency;
use App\Models\TrainingRecord;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SelfServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * لوحة تحكم الموظف
     */
    public function dashboard()
    {
        $employee = $this->resolveDashboardEmployee();
        if ($employee instanceof \Illuminate\Http\RedirectResponse) {
            return $employee;
        }

        return view('employee.pages.self-service.dashboard.index', $this->buildDashboardViewData($employee));
    }

    /**
     * تحديث جزئي لبيانات لوحة التحكم (JSON)
     */
    public function dashboardRefresh()
    {
        $employee = $this->resolveDashboardEmployee();
        if ($employee instanceof \Illuminate\Http\RedirectResponse) {
            return response()->json(['error' => 'لا يوجد ملف موظف'], 403);
        }

        $data = $this->buildDashboardViewData($employee);

        return response()->json([
            'stats' => $data['stats'],
            'absentDays' => $data['absentDays'],
            'lateDays' => $data['lateDays'],
            'latestPayroll' => $data['latestPayroll'] ? [
                'net_salary' => number_format($data['latestPayroll']->net_salary, 2),
                'currency' => $data['latestPayroll']->currency->code ?? 'ر.س',
                'month_label' => $data['latestPayroll']->month_name . ' ' . $data['latestPayroll']->payroll_year,
                'status' => $data['latestPayroll']->status,
                'status_name_ar' => $data['latestPayroll']->status_name_ar,
            ] : null,
            'attendanceChart' => $this->buildAttendanceChartPayload($data['attendanceByDay']),
            'refreshedAt' => now()->format('Y/m/d H:i'),
        ]);
    }

    /**
     * HTML جزئي لقائمة ويدجت
     */
    public function dashboardWidget(string $widget)
    {
        $employee = $this->resolveDashboardEmployee();
        if ($employee instanceof \Illuminate\Http\RedirectResponse) {
            return response('', 403);
        }

        $allowed = ['meetings', 'tasks', 'announcements', 'leaves', 'payroll', 'violations', 'assets'];
        if (! in_array($widget, $allowed, true)) {
            abort(404);
        }

        $data = $this->buildDashboardViewData($employee);

        return view('employee.pages.self-service.dashboard.partials.widgets.' . $widget, $data);
    }

    private function resolveDashboardEmployee(): Employee|\Illuminate\Http\RedirectResponse
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $employee->load(['position', 'department', 'branch']);

        return $employee;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDashboardViewData(Employee $employee): array
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $stats = $this->buildDashboardStats($employee, $currentMonth);

        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $currentYear)
            ->with('leaveType')
            ->get();

        $recentLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentAttendance = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $currentMonth)
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->get();

        $attendanceByDay = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', now()->year)
            ->orderBy('attendance_date')
            ->get()
            ->mapWithKeys(fn ($record) => [
                $record->attendance_date->format('Y-m-d') => [
                    'status' => $record->status,
                    'check_in' => $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i') : null,
                    'check_out' => $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('H:i') : null,
                    'late_minutes' => $record->late_minutes ?? 0,
                ],
            ]);

        $latestPayroll = Payroll::where('employee_id', $employee->id)
            ->with('currency')
            ->orderByDesc('payroll_year')
            ->orderByDesc('payroll_month')
            ->first();

        $recentPayrolls = Payroll::where('employee_id', $employee->id)
            ->with('currency')
            ->orderByDesc('payroll_year')
            ->orderByDesc('payroll_month')
            ->limit(3)
            ->get();

        $upcomingMeetings = Meeting::where(function ($q) use ($employee) {
            $q->where('organizer_id', $employee->id)
                ->orWhereHas('attendees', fn ($q2) => $q2->where('employee_id', $employee->id));
        })
            ->with(['organizer'])
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $upcomingTasks = Task::whereHas('assignments', fn ($q) => $q->where('employee_id', $employee->id))
            ->with(['project'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $recentViolations = EmployeeViolation::where('employee_id', $employee->id)
            ->with('violationType')
            ->orderByDesc('violation_date')
            ->limit(3)
            ->get();

        $assignedAssets = AssetAssignment::where('employee_id', $employee->id)
            ->where('assignment_status', 'active')
            ->with('asset')
            ->limit(3)
            ->get();

        $announcements = Announcement::visible()
            ->where(function ($q) use ($employee) {
                $q->where('target_type', 'all')
                    ->orWhere(function ($q2) use ($employee) {
                        $q2->where('target_type', 'department')->where('department_id', $employee->department_id);
                    })
                    ->orWhere(function ($q2) use ($employee) {
                        $q2->where('target_type', 'branch')->where('branch_id', $employee->branch_id);
                    });
            })
            ->orderByDesc('publish_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $yearsOfService = (int) $employee->hire_date->diffInYears(now());
        $monthsOfService = (int) ($employee->hire_date->diffInMonths(now()) % 12);

        $absentDays = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $currentMonth)
            ->where('status', 'absent')->count();

        $lateDays = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $currentMonth)
            ->where('status', 'late')->count();

        return compact(
            'employee', 'stats', 'recentLeaves', 'recentAttendance', 'announcements',
            'leaveBalances', 'latestPayroll', 'recentPayrolls', 'upcomingMeetings',
            'upcomingTasks', 'attendanceByDay', 'recentViolations', 'assignedAssets',
            'yearsOfService', 'monthsOfService', 'absentDays', 'lateDays'
        );
    }

    /**
     * @return array<string, int>
     */
    private function buildDashboardStats(Employee $employee, int $currentMonth): array
    {
        return [
            'pending_leaves' => LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'pending')->count(),
            'approved_leaves' => LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')->count(),
            'total_attendance' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('attendance_date', $currentMonth)
                ->where('status', 'present')->count(),
            'pending_goals' => EmployeeGoal::where('employee_id', $employee->id)
                ->where('status', 'in_progress')->count(),
            'pending_tasks' => Task::whereHas('assignments', fn ($q) => $q->where('employee_id', $employee->id))
                ->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'upcoming_meetings' => Meeting::where(function ($q) use ($employee) {
                $q->where('organizer_id', $employee->id)
                    ->orWhereHas('attendees', fn ($q2) => $q2->where('employee_id', $employee->id));
            })
                ->where('start_time', '>', now())
                ->count(),
            'pending_expenses' => ExpenseRequest::where('employee_id', $employee->id)
                ->where('status', 'pending')->count(),
            'open_tickets' => Ticket::where('employee_id', $employee->id)
                ->where('status', 'open')->count(),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<string, array<string, mixed>>  $attendanceByDay
     * @return array<string, mixed>
     */
    private function buildAttendanceChartPayload($attendanceByDay): array
    {
        $daysInMonth = now()->daysInMonth;
        $year = now()->year;
        $month = now()->month;

        $categories = [];
        $data = [];
        $colors = [];

        $colorMap = [
            'present' => '#22c55e',
            'late' => '#f59e0b',
            'absent' => '#ef4444',
            'none' => '#94a3b8',
        ];

        $valueMap = [
            'present' => 3,
            'late' => 2,
            'absent' => 1,
            'none' => 0,
        ];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $categories[] = (string) $day;

            $record = $attendanceByDay->get($dateKey);
            $status = $record['status'] ?? 'none';
            if (! isset($valueMap[$status])) {
                $status = 'none';
            }

            $data[] = $valueMap[$status];
            $colors[] = $colorMap[$status];
        }

        return [
            'categories' => $categories,
            'series' => [['name' => 'الحضور', 'data' => $data]],
            'colors' => $colors,
            'monthLabel' => now()->translatedFormat('F Y'),
        ];
    }

    /**
     * عرض الملف الشخصي
     */
    public function profile()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $employee->load(['position', 'department', 'branch']);

        $yearsOfService = (int) $employee->hire_date->diffInYears(now());
        $monthsOfService = (int) ($employee->hire_date->diffInMonths(now()) % 12);

        return view('employee.pages.self-service.profile', compact('employee', 'yearsOfService', 'monthsOfService'));
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $request->validate([
            'personal_email' => 'nullable|email',
            'personal_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
        ]);

        $employee->update($request->only([
            'personal_email',
            'personal_phone',
            'address',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relation',
        ]));

        return redirect()->back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض الإجازات
     */
    public function leaves()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $currentYear = (int) now()->year;
        $employeeId = $employee->id;

        $leaves = LeaveRequest::where('employee_id', $employeeId)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $leaveTypes = LeaveType::where('is_active', true)->orderBy('sort_order')->get();

        $leaveBalances = LeaveBalance::where('employee_id', $employeeId)
            ->where('year', $currentYear)
            ->with('leaveType')
            ->get();

        $stats = [
            'total' => LeaveRequest::where('employee_id', $employeeId)->count(),
            'pending' => LeaveRequest::where('employee_id', $employeeId)->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('employee_id', $employeeId)->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('employee_id', $employeeId)->where('status', 'rejected')->count(),
            'days_approved_year' => (int) LeaveRequest::where('employee_id', $employeeId)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->sum('days_count'),
        ];

        $workflowProgressById = app(\App\Services\WorkflowProgressPresenter::class)->mapForEntities(
            $leaves->getCollection()
        );

        return view('employee.pages.self-service.leaves', compact(
            'leaves',
            'leaveTypes',
            'leaveBalances',
            'stats',
            'currentYear',
            'workflowProgressById'
        ));
    }

    /**
     * طلب إجازة جديد
     */
    public function requestLeave(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $numberOfDays = $startDate->diffInDays($endDate) + 1;

        try {
            DB::beginTransaction();
            $leaveRequest = LeaveRequest::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days_count' => $numberOfDays,
                'reason' => $request->reason,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            app(EmployeeRequestSubmissionService::class)->afterRequestCreated(
                'leave_request',
                $employee,
                $leaveRequest
            );
            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'تم إرسال طلب الإجازة بنجاح');
    }

    /**
     * عرض الحضور
     */
    public function attendance()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $employeeId = $employee->id;
        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;

        $monthQuery = Attendance::where('employee_id', $employeeId)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth);

        $attendances = Attendance::where('employee_id', $employeeId)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        $stats = [
            'month_records' => (clone $monthQuery)->count(),
            'present' => (clone $monthQuery)->where('status', 'present')->count(),
            'absent' => (clone $monthQuery)->where('status', 'absent')->count(),
            'late' => (clone $monthQuery)->where('status', 'late')->count(),
            'hours_month' => round((clone $monthQuery)->sum('hours_worked') / 60, 1),
            'overtime_month' => round((clone $monthQuery)->sum('overtime_minutes') / 60, 1),
        ];

        $monthLabel = now()->translatedFormat('F Y');

        return view('employee.pages.self-service.attendance', compact('attendances', 'stats', 'monthLabel'));
    }

    /**
     * عرض الرواتب
     */
    public function salaries()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $employeeId = $employee->id;

        $salaries = Salary::where('employee_id', $employeeId)
            ->with('currency')
            ->orderBy('salary_year', 'desc')
            ->orderBy('salary_month', 'desc')
            ->paginate(12);

        $payrolls = Payroll::where('employee_id', $employeeId)
            ->with('currency')
            ->orderByDesc('payroll_year')
            ->orderByDesc('payroll_month')
            ->limit(24)
            ->get();

        $latestPayroll = $payrolls->first();
        $salaryQuery = Salary::where('employee_id', $employeeId);

        $stats = [
            'records' => (clone $salaryQuery)->count(),
            'paid' => (clone $salaryQuery)->where('payment_status', 'paid')->count(),
            'pending' => (clone $salaryQuery)->where('payment_status', 'pending')->count(),
            'latest_net' => $latestPayroll?->net_salary,
            'latest_period' => $latestPayroll
                ? ($latestPayroll->month_name.' '.$latestPayroll->payroll_year)
                : null,
            'latest_currency' => $latestPayroll?->currency?->code ?? 'ر.س',
            'payslips' => $payrolls->count(),
        ];

        return view('employee.pages.self-service.salaries', compact('salaries', 'payrolls', 'stats'));
    }

    /**
     * عرض المستندات
     */
    public function documents()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $employeeId = $employee->id;
        $documentsQuery = EmployeeDocument::where('employee_id', $employeeId);

        $documents = (clone $documentsQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => (clone $documentsQuery)->count(),
            'active' => (clone $documentsQuery)->where('status', 'active')->count(),
            'expired' => (clone $documentsQuery)->where(function ($q) {
                $q->where('status', 'expired')->orWhere('is_expired', true);
            })->count(),
            'expiring_soon' => (clone $documentsQuery)->expiringSoon(30)->count(),
        ];

        return view('employee.pages.self-service.documents', compact('documents', 'stats'));
    }

    /**
     * عرض المهارات
     */
    public function skills()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $skills = EmployeeSkill::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $skills->count(),
            'verified' => $skills->where('is_verified', true)->count(),
            'pending' => $skills->where('is_verified', false)->count(),
            'expert' => $skills->where('proficiency_level', 'expert')->count(),
        ];

        return view('employee.pages.self-service.skills', compact('skills', 'stats'));
    }

    /**
     * عرض الشهادات
     */
    public function certificates()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $certificates = EmployeeCertificate::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $certificates->count(),
            'active' => $certificates->filter(fn ($c) => ! $c->isExpired() && $c->status !== 'expired')->count(),
            'expired' => $certificates->filter(fn ($c) => $c->status === 'expired' || $c->isExpired())->count(),
            'expiring_soon' => $certificates->filter(function ($c) {
                if ($c->does_not_expire || ! $c->expiry_date) {
                    return false;
                }

                return ! $c->expiry_date->isPast() && $c->expiry_date->lte(now()->addDays(30));
            })->count(),
        ];

        return view('employee.pages.self-service.certificates', compact('certificates', 'stats'));
    }

    /**
     * عرض الأهداف
     */
    public function goals()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $goals = EmployeeGoal::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $goals->count(),
            'in_progress' => $goals->where('status', 'in_progress')->count(),
            'completed' => $goals->where('status', 'completed')->count(),
            'overdue' => $goals->filter(fn ($g) => $g->target_date->isPast() && ! in_array($g->status, ['completed', 'cancelled']))->count(),
        ];

        return view('employee.pages.self-service.goals', compact('goals', 'stats'));
    }

    /**
     * عرض التقييمات
     */
    public function performanceReviews()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = PerformanceReview::where('employee_id', $employee->id);

        $reviews = (clone $baseQuery)
            ->with('reviewer')
            ->orderByDesc('review_date')
            ->paginate(10);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'pending' => (clone $baseQuery)->whereIn('status', ['draft', 'completed'])->count(),
            'avg_rating' => round((float) ((clone $baseQuery)->where('overall_rating', '>', 0)->avg('overall_rating') ?? 0), 2),
        ];

        return view('employee.pages.self-service.performance-reviews', compact('reviews', 'stats'));
    }

    /**
     * عرض المزايا والتعويضات
     */
    public function benefits()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $benefits = EmployeeBenefit::where('employee_id', $employee->id)
            ->with(['benefitType', 'currency'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $benefits->count(),
            'active' => $benefits->where('status', 'active')->count(),
            'expired' => $benefits->where('status', 'expired')->count(),
            'active_value' => round((float) $benefits->where('status', 'active')->sum('value'), 2),
        ];

        return view('employee.pages.self-service.benefits', compact('benefits', 'stats'));
    }

    /**
     * عرض المهام
     */
    public function tasks()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = Task::whereHas('assignments', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        });

        $tasks = (clone $baseQuery)
            ->with(['project'])
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'overdue' => (clone $baseQuery)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
        ];

        return view('employee.pages.self-service.tasks', compact('tasks', 'stats'));
    }

    /**
     * عرض المشاريع
     */
    public function projects()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $projectsQuery = Project::where(function ($q) use ($employee) {
            $q->where('manager_id', $employee->id)
                ->orWhereHas('members', function ($q2) use ($employee) {
                    $q2->where('employee_id', $employee->id);
                })
                ->orWhereHas('tasks.assignments', function ($q2) use ($employee) {
                    $q2->where('employee_id', $employee->id);
                });
        });

        $projects = (clone $projectsQuery)
            ->with(['manager', 'department', 'currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => (clone $projectsQuery)->count(),
            'active' => (clone $projectsQuery)->where('status', 'active')->count(),
            'planning' => (clone $projectsQuery)->where('status', 'planning')->count(),
            'completed' => (clone $projectsQuery)->where('status', 'completed')->count(),
        ];

        return view('employee.pages.self-service.projects', compact('projects', 'stats'));
    }

    /**
     * عرض التذاكر
     */
    public function tickets()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = Ticket::where(function ($q) use ($employee) {
            $q->where('employee_id', $employee->id)
                ->orWhere('assigned_to', $employee->id);
        });

        $tickets = (clone $baseQuery)
            ->with(['employee', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->whereIn('status', ['open', 'in_progress'])->count(),
            'resolved' => (clone $baseQuery)->where('status', 'resolved')->count(),
            'closed' => (clone $baseQuery)->whereIn('status', ['closed', 'cancelled'])->count(),
        ];

        return view('employee.pages.self-service.tickets', compact('tickets', 'stats', 'employee'));
    }

    /**
     * عرض الاجتماعات
     */
    public function meetings()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = Meeting::where(function ($q) use ($employee) {
            $q->where('organizer_id', $employee->id)
                ->orWhereHas('attendees', function ($q2) use ($employee) {
                    $q2->where('employee_id', $employee->id);
                });
        });

        $meetings = (clone $baseQuery)
            ->with(['organizer', 'attendees.employee'])
            ->orderBy('start_time')
            ->paginate(20);

        $now = now();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'upcoming' => (clone $baseQuery)
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->where('start_time', '>=', $now)
                ->count(),
            'today' => (clone $baseQuery)
                ->whereDate('start_time', $now->toDateString())
                ->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
        ];

        return view('employee.pages.self-service.meetings', compact('meetings', 'stats', 'employee'));
    }

    /**
     * عرض طلبات المصروفات
     */
    public function expenseRequests()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = ExpenseRequest::where('employee_id', $employee->id);

        $expenseRequests = (clone $baseQuery)
            ->with(['category', 'currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->whereIn('status', ['approved', 'paid'])->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        return view('employee.pages.self-service.expense-requests', compact('expenseRequests', 'stats'));
    }

    /**
     * عرض الأصول المعينة
     */
    public function assets()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = AssetAssignment::where('employee_id', $employee->id);

        $assets = (clone $baseQuery)
            ->with(['asset', 'assigner'])
            ->orderBy('assigned_date', 'desc')
            ->paginate(20);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('assignment_status', 'active')->count(),
            'returned' => (clone $baseQuery)->where('assignment_status', 'returned')->count(),
            'overdue' => (clone $baseQuery)
                ->where('assignment_status', 'active')
                ->whereNotNull('expected_return_date')
                ->where('expected_return_date', '<', now()->startOfDay())
                ->count(),
        ];

        return view('employee.pages.self-service.assets', compact('assets', 'stats'));
    }

    /**
     * عرض المخالفات
     */
    public function violations()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = EmployeeViolation::where('employee_id', $employee->id);

        $violations = (clone $baseQuery)
            ->with(['violationType', 'disciplinaryAction'])
            ->orderBy('violation_date', 'desc')
            ->paginate(20);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->whereIn('status', ['pending', 'investigating'])->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'resolved' => (clone $baseQuery)->whereIn('status', ['dismissed', 'resolved'])->count(),
        ];

        return view('employee.pages.self-service.violations', compact('violations', 'stats'));
    }

    /**
     * عرض السياسات المطلوب الاعتراف بها
     */
    public function policies()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $policiesPending = Policy::active()
            ->whereDoesntHave('acknowledgments', function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->orderBy('effective_date', 'desc')
            ->paginate(10);

        $policiesAcknowledged = Policy::active()
            ->whereHas('acknowledgments', function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->with(['acknowledgments' => function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            }])
            ->orderBy('effective_date', 'desc')
            ->limit(50)
            ->get();

        $pendingCount = Policy::active()
            ->whereDoesntHave('acknowledgments', function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->count();

        $stats = [
            'pending' => $pendingCount,
            'acknowledged' => $policiesAcknowledged->count(),
            'total' => $pendingCount + $policiesAcknowledged->count(),
        ];

        return view('employee.pages.self-service.policies', compact('policiesPending', 'policiesAcknowledged', 'stats'));
    }

    /**
     * تسجيل اعتراف الموظف بسياسة
     */
    public function acknowledgePolicy(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $request->validate([
            'policy_id' => 'required|exists:policies,id',
        ]);

        $policy = Policy::findOrFail($request->policy_id);

        if (! $policy->is_active) {
            return redirect()->back()->with('error', 'هذه السياسة غير نشطة.');
        }

        $exists = PolicyAcknowledgment::where('policy_id', $policy->id)
            ->where('employee_id', $employee->id)
            ->exists();

        if ($exists) {
            return redirect()->route('employee.policies')->with('info', 'أنت معترف مسبقاً بهذه السياسة.');
        }

        PolicyAcknowledgment::create([
            'policy_id' => $policy->id,
            'employee_id' => $employee->id,
            'acknowledged_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('employee.policies')->with('success', 'تم تسجيل اعترافك بالسياسة بنجاح.');
    }

    /**
     * عرض عقد الموظف الحالي
     */
    public function contract()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $currentContract = $employee->currentContract();
        $contracts = Contract::where('employee_id', $employee->id)
            ->orderByDesc('start_date')
            ->get();

        $stats = [
            'total' => $contracts->count(),
            'active' => $currentContract ? 1 : 0,
            'expiring_soon' => $currentContract
                && $currentContract->end_date
                && $currentContract->days_remaining !== null
                && $currentContract->days_remaining >= 0
                && $currentContract->days_remaining <= 30
                ? 1
                : 0,
            'ended' => $contracts->whereIn('status', ['expired', 'terminated', 'renewed'])->count(),
        ];

        return view('employee.pages.self-service.contract', compact('employee', 'currentContract', 'contracts', 'stats'));
    }

    /**
     * تحميل قسيمة الراتب PDF (كشوف الرواتب الشهرية)
     */
    public function payslipPdf(string $id)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $payroll = Payroll::with([
            'employee',
            'currency',
            'items',
            'overtimeRecords',
            'approvedBy'
        ])->where('employee_id', $employee->id)->findOrFail($id);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.pages.payrolls.payslip', compact('payroll'));
        $filename = 'payslip-' . $payroll->payroll_code . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * نموذج طلب مصروفات جديد
     */
    public function createExpenseRequest()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $categories = ExpenseCategory::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('employee.pages.self-service.expense-request-create', compact('categories', 'currencies'));
    }

    /**
     * حفظ طلب مصروفات جديد
     */
    public function storeExpenseRequest(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'nullable|exists:currencies,id',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'payment_method' => 'nullable|in:cash,card,transfer,check',
            'vendor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $category = ExpenseCategory::findOrFail($request->expense_category_id);
        if ($category->max_amount && $request->amount > $category->max_amount) {
            return redirect()->back()->withInput()->with('error', 'المبلغ يتجاوز الحد الأقصى المسموح به: ' . number_format($category->max_amount, 2));
        }
        if ($category->requires_receipt && !$request->hasFile('receipt')) {
            return redirect()->back()->withInput()->with('error', 'إيصال المصروف مطلوب لهذا التصنيف.');
        }

        $data = $request->only([
            'expense_category_id', 'amount', 'currency_id', 'expense_date', 'description',
            'payment_method', 'vendor_name', 'notes'
        ]);
        $data['employee_id'] = $employee->id;
        $data['created_by'] = $user->id;
        $data['status'] = 'pending';
        $data['request_code'] = 'EXP-' . strtoupper(Str::random(8));

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $data['receipt_path'] = $file->store('expense_receipts', 'public');
            $data['receipt_file_name'] = $file->getClientOriginalName();
        }

        try {
            DB::beginTransaction();
            $expenseRequest = ExpenseRequest::create($data);
            app(EmployeeRequestSubmissionService::class)->afterRequestCreated(
                'expense_request',
                $employee,
                $expenseRequest
            );
            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('employee.expense-requests')->with('success', 'تم إرسال طلب المصروف بنجاح.');
    }

    /**
     * نموذج فتح تذكرة جديدة
     */
    public function createTicket()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        return view('employee.pages.self-service.ticket-create');
    }

    /**
     * حفظ تذكرة جديدة
     */
    public function storeTicket(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,hr,it,facilities,other',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        try {
            DB::beginTransaction();
            $ticket = Ticket::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'employee_id' => $employee->id,
                'status' => 'pending_approval',
                'created_by' => $user->id,
            ]);

            app(EmployeeRequestSubmissionService::class)->afterRequestCreated(
                'ticket_request',
                $employee,
                $ticket
            );
            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('employee.tickets')->with('success', 'تم إرسال التذكرة للموافقة بنجاح.');
    }

    /**
     * صفحة الإعلانات
     */
    public function announcements()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = Announcement::visible()
            ->where(function ($q) use ($employee) {
                $q->where('target_type', 'all')
                    ->orWhere(function ($q2) use ($employee) {
                        $q2->where('target_type', 'department')->where('department_id', $employee->department_id);
                    })
                    ->orWhere(function ($q2) use ($employee) {
                        $q2->where('target_type', 'branch')->where('branch_id', $employee->branch_id);
                    });
            });

        $announcements = (clone $baseQuery)
            ->orderByDesc('publish_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'this_month' => (clone $baseQuery)
                ->whereYear('publish_date', now()->year)
                ->whereMonth('publish_date', now()->month)
                ->count(),
            'expiring_soon' => (clone $baseQuery)
                ->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
                ->count(),
            'company_wide' => (clone $baseQuery)->where('target_type', 'all')->count(),
        ];

        return view('employee.pages.self-service.announcements', compact('announcements', 'stats'));
    }

    /**
     * سجل التدريب للموظف
     */
    public function trainingRecords()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = TrainingRecord::where('employee_id', $employee->id);

        $records = (clone $baseQuery)
            ->with('training')
            ->orderByDesc('registration_date')
            ->paginate(15);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'in_progress' => (clone $baseQuery)->whereIn('status', ['registered', 'attending'])->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'certificates' => (clone $baseQuery)->where('certificate_issued', true)->count(),
        ];

        return view('employee.pages.self-service.training-records', compact('records', 'stats'));
    }

    /**
     * تفاصيل مشروع يشارك فيه الموظف
     */
    public function showProject(Project $project)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        if (! $project->employeeCanParticipate($employee)) {
            abort(403, 'ليس لديك صلاحية عرض هذا المشروع');
        }

        $project->load(['department', 'manager', 'currency', 'members.employee', 'documents.uploader']);

        $myTasks = Task::where('project_id', $project->id)
            ->whereHas('assignments', fn ($q) => $q->where('employee_id', $employee->id))
            ->orderBy('due_date')
            ->get();

        $totalMyHours = (float) $project->timeEntries()
            ->where('employee_id', $employee->id)
            ->sum('hours');

        return view('employee.pages.self-service.project-show', compact('project', 'employee', 'myTasks', 'totalMyHours'));
    }

    /**
     * تسجيل وقت عمل على مشروع (الموظف الحالي فقط)
     */
    public function storeProjectTime(Request $request, Project $project)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            return redirect()->back()->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        if (! $project->employeeCanParticipate($employee)) {
            abort(403);
        }

        if (! $project->allowsTimeLogging()) {
            return redirect()->back()->with('error', 'لا يمكن تسجيل وقت على هذا المشروع في وضعه الحالي.');
        }

        $validated = $request->validate([
            'task_id' => 'nullable|exists:tasks,id',
            'worked_date' => 'required|date',
            'hours' => 'required|numeric|min:0.01|max:24',
            'description' => 'nullable|string|max:2000',
        ]);

        if (! empty($validated['task_id'])) {
            $belongs = Task::where('project_id', $project->id)
                ->where('id', $validated['task_id'])
                ->exists();
            if (! $belongs) {
                return redirect()->back()->withInput()->with('error', 'المهمة لا تنتمي لهذا المشروع.');
            }
        }

        try {
            DB::beginTransaction();
            $entry = ProjectTimeEntry::create([
                'project_id' => $project->id,
                'employee_id' => $employee->id,
                'task_id' => $validated['task_id'] ?? null,
                'worked_date' => $validated['worked_date'],
                'hours' => $validated['hours'],
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            app(EmployeeRequestSubmissionService::class)->afterRequestCreated(
                'project_time_entry',
                $employee,
                $entry
            );
            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('employee.projects.show', $project)
            ->with('success', 'تم إرسال سجل الوقت للموافقة بنجاح.');
    }

    /**
     * سجلات وقت الموظف (فلترة بالمشروع والفترة)
     */
    public function projectTimeIndex(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $baseQuery = ProjectTimeEntry::where('employee_id', $employee->id);

        $filteredQuery = (clone $baseQuery);

        if ($request->filled('project_id')) {
            $filteredQuery->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('from')) {
            $filteredQuery->whereDate('worked_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $filteredQuery->whereDate('worked_date', '<=', $request->input('to'));
        }

        $entries = (clone $filteredQuery)
            ->with(['project', 'task'])
            ->orderByDesc('worked_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'entries' => (clone $filteredQuery)->count(),
            'total_hours' => round((float) (clone $filteredQuery)->sum('hours'), 2),
            'projects' => (clone $filteredQuery)->distinct()->count('project_id'),
            'month_hours' => round((float) (clone $baseQuery)
                ->whereYear('worked_date', now()->year)
                ->whereMonth('worked_date', now()->month)
                ->sum('hours'), 2),
        ];

        $accessibleProjects = Project::where(function ($q) use ($employee) {
            $q->where('manager_id', $employee->id)
                ->orWhereHas('members', fn ($q2) => $q2->where('employee_id', $employee->id))
                ->orWhereHas('tasks.assignments', fn ($q2) => $q2->where('employee_id', $employee->id));
        })
            ->orderBy('name')
            ->get();

        return view('employee.pages.self-service.project-time', compact('entries', 'accessibleProjects', 'stats'));
    }

    /**
     * عرض التسلسل الإداري للموظف
     */
    public function hierarchy()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        $directManager = $employee->getDirectManager();
        $departmentManager = $employee->getDepartmentManager();
        $managerChain = $employee->getManagerChain();

        $departmentHierarchy = $this->getDepartmentHierarchy($employee->department);

        $yearsOfService = (int) $employee->hire_date->diffInYears(now());
        $monthsOfService = (int) ($employee->hire_date->diffInMonths(now()) % 12);

        $stats = [
            'manager_levels' => count($managerChain),
            'departments' => count($departmentHierarchy),
            'has_direct_manager' => $directManager !== null,
            'has_dept_manager' => $departmentManager !== null,
        ];

        return view('employee.pages.self-service.hierarchy', compact(
            'employee',
            'directManager',
            'departmentManager',
            'managerChain',
            'departmentHierarchy',
            'yearsOfService',
            'monthsOfService',
            'stats',
        ));
    }

    /**
     * بناء التسلسل الهرمي للقسم
     */
    protected function getDepartmentHierarchy($department): array
    {
        if (! $department) return [];

        $hierarchy = [];
        $current = $department;

        while ($current) {
            array_unshift($hierarchy, [
                'id' => $current->id,
                'name' => $current->name,
                'code' => $current->code,
                'manager' => $current->manager,
            ]);
            $current = $current->parent;
        }

        return $hierarchy;
    }

    public function surveys()
    {
        $employee = Auth::user()->employee;
        if (! $employee) {
            return redirect()->route('employee.dashboard')->with('error', 'لا يوجد ملف موظف.');
        }

        $surveys = \App\Models\Survey::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->whereDoesntHave('responses', fn ($q) => $q->where('employee_id', $employee->id))
            ->with('questions')
            ->get();

        return view('employee.pages.self-service.surveys', compact('surveys'));
    }

    public function submitSurvey(Request $request, string $id)
    {
        $employee = Auth::user()->employee;
        if (! $employee) {
            return back()->with('error', 'لا يوجد ملف موظف.');
        }

        $survey = \App\Models\Survey::with('questions')->findOrFail($id);
        $answers = $request->input('answers', []);

        \App\Models\SurveyResponse::create([
            'survey_id' => $survey->id,
            'employee_id' => $employee->id,
            'answers' => $answers,
            'submitted_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $survey->increment('total_responses');

        return redirect()->route('employee.surveys')->with('success', 'شكراً لمشاركتك.');
    }

    public function onboarding()
    {
        $employee = Auth::user()->employee;
        if (! $employee) {
            return redirect()->route('employee.dashboard')->with('error', 'لا يوجد ملف موظف.');
        }

        $process = \App\Models\OnboardingProcess::with(['checklists.task'])
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['not_started', 'in_progress'])
            ->latest()
            ->first();

        return view('employee.pages.self-service.onboarding', compact('process'));
    }

    public function completeOnboardingTask(Request $request, string $checklistId)
    {
        $employee = Auth::user()->employee;
        $checklist = \App\Models\OnboardingChecklist::with('process')->findOrFail($checklistId);

        if ($checklist->process->employee_id !== $employee?->id) {
            abort(403);
        }

        $checklist->update([
            'status' => 'completed',
            'completed_date' => now(),
            'completed_by' => Auth::id(),
            'completion_notes' => $request->input('notes'),
        ]);

        $process = $checklist->process;
        $total = $process->checklists()->count();
        $done = $process->checklists()->where('status', 'completed')->count();
        $process->update([
            'completion_percentage' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
            'status' => $done >= $total ? 'completed' : 'in_progress',
        ]);

        return back()->with('success', 'تم إكمال المهمة.');
    }
}
