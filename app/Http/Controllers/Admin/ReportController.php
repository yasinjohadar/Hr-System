<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\PerformanceReview;
use App\Models\Training;
use App\Models\TrainingRecord;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\Interview;
use App\Models\EmployeeBenefit;
use App\Models\BenefitType;
use App\Models\Department;
use App\Models\Position;
use App\Models\Branch;
use App\Models\LeaveType;
use App\Models\EmployeeExit;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:report-view')->only(['index', 'show', 'turnoverReport', 'trainingEffectivenessReport', 'kpisReport']);
    }

    /**
     * الصفحة الرئيسية للتقارير
     */
    public function index()
    {
        return view('admin.pages.reports.index');
    }

    /**
     * تقرير الموظفين الشامل
     */
    public function employeesReport(Request $request)
    {
        $query = Employee::with(['department', 'position', 'branch', 'manager']);

        if (Auth::user()->isDepartmentHead()) {
            $departmentIds = Auth::user()->getManagedDepartmentIds();
            if (!empty($departmentIds)) {
                $query->whereIn('department_id', $departmentIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // فلترة حسب القسم
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // فلترة حسب المنصب
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }

        // فلترة حسب الفرع
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        // فلترة حسب الحالة الوظيفية
        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->input('employment_status'));
        }

        // فلترة حسب نوع التوظيف
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->input('employment_type'));
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        // فلترة حسب تاريخ التوظيف
        if ($request->filled('hire_date_from')) {
            $query->where('hire_date', '>=', $request->input('hire_date_from'));
        }
        if ($request->filled('hire_date_to')) {
            $query->where('hire_date', '<=', $request->input('hire_date_to'));
        }

        $employees = $query->get();

        // إحصائيات
        $stats = [
            'total' => $employees->count(),
            'active' => $employees->where('is_active', true)->count(),
            'by_department' => $employees->groupBy('department_id')->map->count(),
            'by_position' => $employees->groupBy('position_id')->map->count(),
            'by_status' => $employees->groupBy('employment_status')->map->count(),
            'by_type' => $employees->groupBy('employment_type')->map->count(),
        ];

        $departments = Department::where('is_active', true)
            ->when(Auth::user()->isDepartmentHead(), function ($q) {
                $ids = Auth::user()->getManagedDepartmentIds();
                if (!empty($ids)) {
                    $q->whereIn('id', $ids);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->get();
        $positions = Position::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('admin.pages.reports.employees', compact('employees', 'stats', 'departments', 'positions', 'branches'));
    }

    /**
     * تقرير الحضور والانصراف
     */
    public function attendanceReport(Request $request)
    {
        $query = Attendance::with(['employee.department', 'employee.position']);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $query->whereIn('employee_id', $employeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب التاريخ
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $query->whereBetween('attendance_date', [$dateFrom, $dateTo]);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $attendances = $query->get();

        // إحصائيات
        $stats = [
            'total_days' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'total_hours' => $attendances->sum('hours_worked') / 60, // تحويل من دقائق إلى ساعات
            'total_overtime' => $attendances->sum('overtime_minutes') / 60,
            'total_late_minutes' => $attendances->sum('late_minutes'),
            'by_employee' => $attendances->groupBy('employee_id')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'present' => $items->where('status', 'present')->count(),
                    'hours' => $items->sum('hours_worked') / 60,
                ];
            }),
        ];

        $employees = Employee::where('is_active', true)
            ->when(Auth::user()->isDepartmentHead(), function ($q) {
                $ids = Auth::user()->getManagedEmployeeIds();
                if (!empty($ids)) {
                    $q->whereIn('id', $ids);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->get();

        return view('admin.pages.reports.attendance', compact('attendances', 'stats', 'employees', 'dateFrom', 'dateTo'));
    }

    /**
     * تقرير الرواتب
     */
    public function salariesReport(Request $request)
    {
        $query = Salary::with(['employee', 'currency']);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $query->whereIn('employee_id', $employeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب الشهر والسنة
        if ($request->filled('month')) {
            $query->where('salary_month', $request->input('month'));
        }
        if ($request->filled('year')) {
            $query->where('salary_year', $request->input('year'));
        } else {
            $query->where('salary_year', Carbon::now()->year);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $salaries = $query->get();

        // إحصائيات
        $stats = [
            'total_salaries' => $salaries->count(),
            'total_amount' => $salaries->sum('total_salary'),
            'total_base' => $salaries->sum('base_salary'),
            'total_allowances' => $salaries->sum('allowances'),
            'total_bonuses' => $salaries->sum('bonuses'),
            'total_deductions' => $salaries->sum('deductions'),
            'total_overtime' => $salaries->sum('overtime'),
            'paid' => $salaries->where('payment_status', 'paid')->count(),
            'pending' => $salaries->where('payment_status', 'pending')->count(),
            'by_month' => $salaries->groupBy('salary_month')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('total_salary'),
                ];
            }),
        ];

        $employees = Employee::where('is_active', true)
            ->when(Auth::user()->isDepartmentHead(), function ($q) {
                $ids = Auth::user()->getManagedEmployeeIds();
                if (!empty($ids)) {
                    $q->whereIn('id', $ids);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->get();

        return view('admin.pages.reports.salaries', compact('salaries', 'stats', 'employees'));
    }

    /**
     * تقرير الإجازات
     */
    public function leavesReport(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'leaveType']);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $query->whereIn('employee_id', $employeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب نوع الإجازة
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->input('leave_type_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->input('date_to'));
        }

        $leaveRequests = $query->get();

        // إحصائيات
        $stats = [
            'total_requests' => $leaveRequests->count(),
            'approved' => $leaveRequests->where('status', 'approved')->count(),
            'pending' => $leaveRequests->where('status', 'pending')->count(),
            'rejected' => $leaveRequests->where('status', 'rejected')->count(),
            'total_days' => $leaveRequests->sum('number_of_days'),
            'by_type' => $leaveRequests->groupBy('leave_type_id')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'days' => $items->sum('number_of_days'),
                ];
            }),
            'by_month' => $leaveRequests->groupBy(function ($item) {
                return Carbon::parse($item->start_date)->format('Y-m');
            })->map->count(),
        ];

        $employees = Employee::where('is_active', true)
            ->when(Auth::user()->isDepartmentHead(), function ($q) {
                $ids = Auth::user()->getManagedEmployeeIds();
                if (!empty($ids)) {
                    $q->whereIn('id', $ids);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return view('admin.pages.reports.leaves', compact('leaveRequests', 'stats', 'employees', 'leaveTypes'));
    }

    /**
     * تقرير التقييمات
     */
    public function performanceReport(Request $request)
    {
        $query = PerformanceReview::with(['employee', 'reviewer']);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $query->whereIn('employee_id', $employeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // فلترة حسب الفترة
        if ($request->filled('period_from')) {
            $query->where('review_period_start', '>=', $request->input('period_from'));
        }
        if ($request->filled('period_to')) {
            $query->where('review_period_end', '<=', $request->input('period_to'));
        }

        $reviews = $query->get();

        // إحصائيات
        $stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->avg('overall_rating'),
            'by_status' => $reviews->groupBy('status')->map->count(),
            'by_rating' => $reviews->groupBy(function ($item) {
                return floor($item->overall_rating);
            })->map->count(),
            'top_performers' => $reviews->sortByDesc('overall_rating')->take(10),
        ];

        $employees = Employee::where('is_active', true)
            ->when(Auth::user()->isDepartmentHead(), function ($q) {
                $ids = Auth::user()->getManagedEmployeeIds();
                if (!empty($ids)) {
                    $q->whereIn('id', $ids);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->get();

        return view('admin.pages.reports.performance', compact('reviews', 'stats', 'employees'));
    }

    /**
     * تقرير التدريب
     */
    public function trainingReport(Request $request)
    {
        $query = TrainingRecord::with(['training', 'employee']);

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب الدورة
        if ($request->filled('training_id')) {
            $query->where('training_id', $request->input('training_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $records = $query->get();

        // إحصائيات
        $stats = [
            'total_records' => $records->count(),
            'completed' => $records->where('status', 'completed')->count(),
            'in_progress' => $records->where('status', 'attending')->count(),
            'average_score' => $records->whereNotNull('score')->avg('score'),
            'by_training' => $records->groupBy('training_id')->map->count(),
            'by_status' => $records->groupBy('status')->map->count(),
        ];

        $employees = Employee::where('is_active', true)->get();
        $trainings = Training::all();

        return view('admin.pages.reports.training', compact('records', 'stats', 'employees', 'trainings'));
    }

    /**
     * تقرير التوظيف
     */
    public function recruitmentReport(Request $request)
    {
        // تقرير الوظائف الشاغرة
        $vacanciesQuery = JobVacancy::with(['department', 'position']);
        if ($request->filled('status')) {
            $vacanciesQuery->where('status', $request->input('status'));
        }
        $vacancies = $vacanciesQuery->get();

        // تقرير طلبات التوظيف
        $applicationsQuery = JobApplication::with(['candidate', 'jobVacancy']);
        if ($request->filled('status')) {
            $applicationsQuery->where('status', $request->input('status'));
        }
        if ($request->filled('date_from')) {
            $applicationsQuery->where('application_date', '>=', $request->input('date_from'));
        }
        $applications = $applicationsQuery->get();

        // تقرير المقابلات
        $interviewsQuery = Interview::with(['candidate', 'jobVacancy']);
        if ($request->filled('status')) {
            $interviewsQuery->where('status', $request->input('status'));
        }
        $interviews = $interviewsQuery->get();

        // إحصائيات
        $stats = [
            'total_vacancies' => $vacancies->count(),
            'published_vacancies' => $vacancies->where('status', 'published')->count(),
            'total_applications' => $applications->count(),
            'accepted_applications' => $applications->where('status', 'accepted')->count(),
            'total_interviews' => $interviews->count(),
            'completed_interviews' => $interviews->where('status', 'completed')->count(),
            'hired' => $applications->where('status', 'accepted')->count(),
            'by_vacancy' => $applications->groupBy('job_vacancy_id')->map->count(),
            'by_status' => $applications->groupBy('status')->map->count(),
        ];

        return view('admin.pages.reports.recruitment', compact('vacancies', 'applications', 'interviews', 'stats'));
    }

    /**
     * تقرير المزايا
     */
    public function benefitsReport(Request $request)
    {
        $query = EmployeeBenefit::with(['employee', 'benefitType', 'currency']);

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب نوع الميزة
        if ($request->filled('benefit_type_id')) {
            $query->where('benefit_type_id', $request->input('benefit_type_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $benefits = $query->get();

        // إحصائيات
        $stats = [
            'total_benefits' => $benefits->count(),
            'active' => $benefits->where('status', 'active')->count(),
            'total_value' => $benefits->sum('value'),
            'by_type' => $benefits->groupBy('benefit_type_id')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total_value' => $items->sum('value'),
                ];
            }),
            'by_employee' => $benefits->groupBy('employee_id')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total_value' => $items->sum('value'),
                ];
            }),
        ];

        $employees = Employee::where('is_active', true)->get();
        $benefitTypes = BenefitType::where('is_active', true)->get();

        return view('admin.pages.reports.benefits', compact('benefits', 'stats', 'employees', 'benefitTypes'));
    }

    /**
     * التقرير الشامل (Dashboard)
     */
    public function dashboardReport()
    {
        // إحصائيات عامة
        $stats = [
            'total_employees' => Employee::where('is_active', true)->count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'total_positions' => Position::where('is_active', true)->count(),
            'total_branches' => Branch::where('is_active', true)->count(),
            
            // الحضور
            'today_attendance' => Attendance::whereDate('attendance_date', Carbon::today())
                ->where('status', 'present')->count(),
            'today_absent' => Attendance::whereDate('attendance_date', Carbon::today())
                ->where('status', 'absent')->count(),
            
            // الإجازات
            'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
            'approved_leaves' => LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', Carbon::today())
                ->where('end_date', '>=', Carbon::today())
                ->count(),
            
            // التوظيف
            'active_vacancies' => JobVacancy::where('status', 'published')->count(),
            'pending_applications' => JobApplication::where('status', 'pending')->count(),
            
            // التدريب
            'ongoing_trainings' => Training::where('status', 'ongoing')->count(),
            'training_participants' => TrainingRecord::where('status', 'attending')->count(),
        ];

        // إحصائيات الحضور الشهرية
        $monthlyAttendance = Attendance::whereMonth('attendance_date', Carbon::now()->month)
            ->whereYear('attendance_date', Carbon::now()->year)
            ->selectRaw('DATE(attendance_date) as date, COUNT(*) as count, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // إحصائيات الرواتب
        $monthlySalaries = Salary::where('salary_month', Carbon::now()->month)
            ->where('salary_year', Carbon::now()->year)
            ->selectRaw('SUM(total_salary) as total, SUM(base_salary) as base, COUNT(*) as count')
            ->first();

        return view('admin.pages.reports.dashboard', compact('stats', 'monthlyAttendance', 'monthlySalaries'));
    }

    /**
     * تقرير معدل دوران الموظفين
     */
    public function turnoverReport(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $dateFrom = $request->input('date_from', $year . '-01-01');
        $dateTo = $request->input('date_to', $year . '-12-31');

        $exitsQuery = EmployeeExit::query()
            ->whereIn('status', ['completed', 'in_process'])
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('last_working_day', [$dateFrom, $dateTo])
                    ->orWhereBetween('resignation_date', [$dateFrom, $dateTo]);
            });
        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $exitsQuery->whereIn('employee_id', $employeeIds);
            } else {
                $exitsQuery->whereRaw('1 = 0');
            }
        }
        $exitsCount = (clone $exitsQuery)->count();
        $exits = (clone $exitsQuery)->with('employee')->orderBy('last_working_day', 'desc')->get();

        $currentActiveQuery = Employee::where('is_active', true);
        if (Auth::user()->isDepartmentHead()) {
            $departmentIds = Auth::user()->getManagedDepartmentIds();
            if (!empty($departmentIds)) {
                $currentActiveQuery->whereIn('department_id', $departmentIds);
            } else {
                $currentActiveQuery->whereRaw('1 = 0');
            }
        }
        $currentActive = $currentActiveQuery->count();
        $avgHeadcount = $currentActive + $exitsCount / 2;
        $turnoverRate = $avgHeadcount > 0 ? round(($exitsCount / $avgHeadcount) * 100, 2) : 0;

        return view('admin.pages.reports.turnover', compact('exits', 'exitsCount', 'currentActive', 'avgHeadcount', 'turnoverRate', 'dateFrom', 'dateTo', 'year'));
    }

    /**
     * تقرير فعالية التدريب
     */
    public function trainingEffectivenessReport(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $dateFrom = $request->input('date_from', $year . '-01-01');
        $dateTo = $request->input('date_to', $year . '-12-31');

        $recordsQuery = TrainingRecord::with(['training', 'employee'])
            ->where('status', 'completed')
            ->whereBetween('completion_date', [$dateFrom, $dateTo]);
        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $recordsQuery->whereIn('employee_id', $employeeIds);
            } else {
                $recordsQuery->whereRaw('1 = 0');
            }
        }
        $completedRecords = (clone $recordsQuery)->get();
        $completedCount = $completedRecords->count();
        $employeesTrained = $completedRecords->pluck('employee_id')->unique()->count();
        $coursesCompleted = $completedRecords->pluck('training_id')->unique()->count();

        $totalRegisteredQuery = TrainingRecord::whereBetween('registration_date', [$dateFrom, $dateTo]);
        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $totalRegisteredQuery->whereIn('employee_id', $employeeIds);
            } else {
                $totalRegisteredQuery->whereRaw('1 = 0');
            }
        }
        $totalRegistered = $totalRegisteredQuery->count();
        $completionRate = $totalRegistered > 0 ? round(($completedCount / $totalRegistered) * 100, 2) : 0;
        $avgScore = $completedRecords->whereNotNull('score')->avg('score');

        $stats = [
            'completed_records' => $completedCount,
            'employees_trained' => $employeesTrained,
            'courses_completed' => $coursesCompleted,
            'completion_rate' => $completionRate,
            'average_score' => $avgScore ? round($avgScore, 2) : null,
        ];

        return view('admin.pages.reports.training-effectiveness', compact('stats', 'completedRecords', 'dateFrom', 'dateTo', 'year'));
    }

    /**
     * لوحة مؤشرات الإدارة العليا
     */
    public function kpisReport()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $exitsQuery = EmployeeExit::whereIn('status', ['completed', 'in_process'])
            ->where(function ($q) use ($currentYear) {
                $q->whereYear('last_working_day', $currentYear)
                    ->orWhere(function ($q2) use ($currentYear) {
                        $q2->whereNull('last_working_day')->whereYear('resignation_date', $currentYear);
                    });
            });
        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $exitsQuery->whereIn('employee_id', $employeeIds);
            } else {
                $exitsQuery->whereRaw('1 = 0');
            }
        }
        $exitsThisYear = $exitsQuery->count();

        $activeEmployeesQuery = Employee::where('is_active', true);
        if (Auth::user()->isDepartmentHead()) {
            $departmentIds = Auth::user()->getManagedDepartmentIds();
            if (!empty($departmentIds)) {
                $activeEmployeesQuery->whereIn('department_id', $departmentIds);
            } else {
                $activeEmployeesQuery->whereRaw('1 = 0');
            }
        }
        $activeEmployees = $activeEmployeesQuery->count();
        $avgHeadcount = $activeEmployees + $exitsThisYear / 2;
        $turnoverRate = $avgHeadcount > 0 ? round(($exitsThisYear / $avgHeadcount) * 100, 2) : 0;

        $payrollTotal = Payroll::whereIn('status', ['approved', 'paid'])
            ->where('payroll_month', $currentMonth)
            ->where('payroll_year', $currentYear)
            ->when(Auth::user()->isDepartmentHead(), function ($q) {
                $ids = Auth::user()->getManagedEmployeeIds();
                if (!empty($ids)) {
                    $q->whereIn('employee_id', $ids);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->sum('net_salary');
        $pendingLeavesQuery = LeaveRequest::where('status', 'pending');
        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $pendingLeavesQuery->whereIn('employee_id', $employeeIds);
            } else {
                $pendingLeavesQuery->whereRaw('1 = 0');
            }
        }
        $pendingLeaves = $pendingLeavesQuery->count();
        $publishedVacancies = JobVacancy::where('status', 'published')->count();
        $hiredThisYear = JobApplication::where('status', 'accepted')
            ->whereYear('application_date', $currentYear)
            ->count();

        $trainingCompletedQuery = TrainingRecord::where('status', 'completed')->whereYear('completion_date', $currentYear);
        $trainingParticipantsQuery = TrainingRecord::where('status', 'attending');
        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $trainingCompletedQuery->whereIn('employee_id', $employeeIds);
                $trainingParticipantsQuery->whereIn('employee_id', $employeeIds);
            } else {
                $trainingCompletedQuery->whereRaw('1 = 0');
                $trainingParticipantsQuery->whereRaw('1 = 0');
            }
        }
        $trainingCompleted = $trainingCompletedQuery->count();
        $trainingParticipants = $trainingParticipantsQuery->count();

        $kpis = [
            'active_employees' => $activeEmployees,
            'exits_this_year' => $exitsThisYear,
            'turnover_rate' => $turnoverRate,
            'payroll_total' => $payrollTotal,
            'pending_leaves' => $pendingLeaves,
            'published_vacancies' => $publishedVacancies,
            'hired_this_year' => $hiredThisYear,
            'training_completed' => $trainingCompleted,
            'training_participants' => $trainingParticipants,
        ];

        return view('admin.pages.reports.kpis', compact('kpis'));
    }
}
