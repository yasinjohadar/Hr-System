<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Position;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Salary;
use App\Models\PerformanceReview;
use App\Models\Training;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\Ticket;
use App\Models\Meeting;
use App\Models\ExpenseRequest;
use App\Models\EmployeeViolation;
use App\Models\Project;
use App\Models\Task;
use App\Models\Announcement;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض لوحة التحكم الرئيسية
     */
    public function index()
    {
        $user = auth()->user();
        
        // إحصائيات عامة
        $stats = $this->getGeneralStats();
        
        // إحصائيات الحضور
        $attendanceStats = $this->getAttendanceStats();
        
        // إحصائيات الإجازات
        $leaveStats = $this->getLeaveStats();
        
        // إحصائيات الرواتب
        $salaryStats = $this->getSalaryStats();
        
        // إحصائيات التوظيف
        $recruitmentStats = $this->getRecruitmentStats();
        
        // إحصائيات التدريب
        $trainingStats = $this->getTrainingStats();
        
        // إحصائيات الأداء
        $performanceStats = $this->getPerformanceStats();
        
        // آخر الأنشطة
        $recentActivities = $this->getRecentActivities();
        
        // المهام العاجلة
        $urgentTasks = $this->getUrgentTasks();
        
        // الإشعارات المهمة
        $importantNotifications = $this->getImportantNotifications();
        
        // بيانات الرسوم البيانية
        $chartData = $this->getChartData();
        
        // إعلانات الشركة الظاهرة حالياً
        $announcements = Announcement::visible()->orderByDesc('publish_date')->orderByDesc('created_at')->limit(5)->get();
        
        return view('admin.dashboard', compact(
            'stats',
            'attendanceStats',
            'leaveStats',
            'salaryStats',
            'recruitmentStats',
            'trainingStats',
            'performanceStats',
            'recentActivities',
            'urgentTasks',
            'importantNotifications',
            'chartData',
            'announcements'
        ));
    }

    /**
     * الحصول على إحصائيات عامة
     */
    private function getGeneralStats()
    {
        return [
            'total_employees' => Employee::where('is_active', true)->count(),
            'new_employees_this_month' => Employee::where('is_active', true)
                ->whereMonth('hire_date', Carbon::now()->month)
                ->whereYear('hire_date', Carbon::now()->year)
                ->count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'total_positions' => Position::where('is_active', true)->count(),
            'total_branches' => Branch::where('is_active', true)->count(),
        ];
    }

    /**
     * الحصول على إحصائيات الحضور
     */
    private function getAttendanceStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'today_present' => Attendance::whereDate('attendance_date', $today)
                ->where('status', 'present')->count(),
            'today_absent' => Attendance::whereDate('attendance_date', $today)
                ->where('status', 'absent')->count(),
            'today_late' => Attendance::whereDate('attendance_date', $today)
                ->where('status', 'late')->count(),
            'monthly_attendance_rate' => $this->calculateMonthlyAttendanceRate(),
            'total_hours_this_month' => Attendance::where('attendance_date', '>=', $thisMonth)
                ->sum('hours_worked') / 60,
            'total_overtime_this_month' => Attendance::where('attendance_date', '>=', $thisMonth)
                ->sum('overtime_minutes') / 60,
        ];
    }

    /**
     * حساب معدل الحضور الشهري
     */
    private function calculateMonthlyAttendanceRate()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $totalDays = Attendance::where('attendance_date', '>=', $thisMonth)->count();
        $presentDays = Attendance::where('attendance_date', '>=', $thisMonth)
            ->where('status', 'present')->count();
        
        return $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
    }

    /**
     * الحصول على إحصائيات الإجازات
     */
    private function getLeaveStats()
    {
        return [
            'pending_requests' => LeaveRequest::where('status', 'pending')->count(),
            'approved_today' => LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', Carbon::today())
                ->where('end_date', '>=', Carbon::today())
                ->count(),
            'total_this_month' => LeaveRequest::whereMonth('start_date', Carbon::now()->month)
                ->whereYear('start_date', Carbon::now()->year)
                ->count(),
            'approved_this_month' => LeaveRequest::where('status', 'approved')
                ->whereMonth('start_date', Carbon::now()->month)
                ->whereYear('start_date', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * الحصول على إحصائيات الرواتب
     */
    private function getSalaryStats()
    {
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;
        
        $monthlySalaries = Salary::where('salary_month', $thisMonth)
            ->where('salary_year', $thisYear)
            ->get();
        
        return [
            'total_this_month' => $monthlySalaries->sum('gross_salary'),
            'paid_count' => $monthlySalaries->where('payment_status', 'paid')->count(),
            'pending_count' => $monthlySalaries->where('payment_status', 'pending')->count(),
            'total_employees' => $monthlySalaries->count(),
        ];
    }

    /**
     * الحصول على إحصائيات التوظيف
     */
    private function getRecruitmentStats()
    {
        return [
            'active_vacancies' => JobVacancy::where('status', 'published')->count(),
            'pending_applications' => JobApplication::where('status', 'pending')->count(),
            'total_applications' => JobApplication::count(),
            'hired_this_month' => Employee::whereMonth('hire_date', Carbon::now()->month)
                ->whereYear('hire_date', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * الحصول على إحصائيات التدريب
     */
    private function getTrainingStats()
    {
        return [
            'ongoing_trainings' => Training::where('status', 'ongoing')->count(),
            'scheduled_trainings' => Training::where('status', 'scheduled')->count(),
            'total_participants' => DB::table('training_records')
                ->where('status', 'attending')
                ->count(),
            'completed_this_month' => Training::where('status', 'completed')
                ->whereMonth('end_date', Carbon::now()->month)
                ->whereYear('end_date', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * الحصول على إحصائيات الأداء
     */
    private function getPerformanceStats()
    {
        $thisYear = Carbon::now()->year;
        
        $reviews = PerformanceReview::whereYear('review_date', $thisYear)->get();
        
        return [
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->avg('overall_rating'),
            'pending_reviews' => PerformanceReview::where('status', 'pending')->count(),
            'completed_reviews' => PerformanceReview::where('status', 'approved')->count(),
        ];
    }

    /**
     * الحصول على آخر الأنشطة
     */
    private function getRecentActivities()
    {
        $activities = collect();
        
        // آخر الموظفين المضافة
        $activities = $activities->merge(
            Employee::latest()->take(5)->get()->map(function ($employee) {
                return [
                    'type' => 'employee_added',
                    'title' => 'تم إضافة موظف جديد',
                    'description' => $employee->full_name,
                    'time' => $employee->created_at,
                    'icon' => 'fas fa-user-plus',
                    'color' => 'primary',
                ];
            })
        );
        
        // آخر طلبات الإجازات
        $activities = $activities->merge(
            LeaveRequest::latest()->take(5)->get()->map(function ($leave) {
                return [
                    'type' => 'leave_request',
                    'title' => 'طلب إجازة جديد',
                    'description' => $leave->employee->full_name ?? 'موظف',
                    'time' => $leave->created_at,
                    'icon' => 'fas fa-calendar',
                    'color' => 'info',
                ];
            })
        );
        
        // آخر التذاكر
        $activities = $activities->merge(
            Ticket::latest()->take(5)->get()->map(function ($ticket) {
                return [
                    'type' => 'ticket',
                    'title' => 'تذكرة جديدة',
                    'description' => $ticket->title,
                    'time' => $ticket->created_at,
                    'icon' => 'fas fa-ticket-alt',
                    'color' => 'warning',
                ];
            })
        );
        
        return $activities->sortByDesc('time')->take(10)->values();
    }

    /**
     * الحصول على المهام العاجلة
     */
    private function getUrgentTasks()
    {
        return [
            'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
            'pending_expenses' => ExpenseRequest::where('status', 'pending')->count(),
            'open_tickets' => Ticket::where('status', 'open')->count(),
            'pending_violations' => EmployeeViolation::where('status', 'pending')->count(),
            'upcoming_meetings' => Meeting::where('status', 'scheduled')
                ->where('start_time', '>=', Carbon::now())
                ->where('start_time', '<=', Carbon::now()->addDays(7))
                ->count(),
            'overdue_tasks' => Task::where('status', '!=', 'completed')
                ->where('due_date', '<', Carbon::today())
                ->count(),
        ];
    }

    /**
     * الحصول على الإشعارات المهمة
     */
    private function getImportantNotifications()
    {
        return [
            'expiring_documents' => DB::table('employee_documents')
                ->where('expiry_date', '>=', Carbon::today())
                ->where('expiry_date', '<=', Carbon::today()->addDays(30))
                ->where('status', 'active')
                ->count(),
            'expiring_certificates' => DB::table('employee_certificates')
                ->where('expiry_date', '>=', Carbon::today())
                ->where('expiry_date', '<=', Carbon::today()->addDays(30))
                ->count(),
            'contracts_expiring' => Contract::active()
                ->whereDate('end_date', '>=', Carbon::today())
                ->whereDate('end_date', '<=', Carbon::today()->addDays(90))
                ->count(),
        ];
    }

    /**
     * الحصول على بيانات الرسوم البيانية
     */
    private function getChartData()
    {
        // بيانات الحضور الشهرية (آخر 6 أشهر)
        $attendanceData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $attendanceData[] = [
                'month' => $date->format('M Y'),
                'present' => Attendance::whereMonth('attendance_date', $date->month)
                    ->whereYear('attendance_date', $date->year)
                    ->where('status', 'present')
                    ->count(),
                'absent' => Attendance::whereMonth('attendance_date', $date->month)
                    ->whereYear('attendance_date', $date->year)
                    ->where('status', 'absent')
                    ->count(),
            ];
        }
        
        // بيانات الإجازات الشهرية
        $leaveData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $leaveData[] = [
                'month' => $date->format('M Y'),
                'approved' => LeaveRequest::whereMonth('start_date', $date->month)
                    ->whereYear('start_date', $date->year)
                    ->where('status', 'approved')
                    ->count(),
                'pending' => LeaveRequest::whereMonth('start_date', $date->month)
                    ->whereYear('start_date', $date->year)
                    ->where('status', 'pending')
                    ->count(),
            ];
        }
        
        // بيانات الموظفين حسب القسم
        $employeesByDepartment = Department::withCount('employees')
            ->where('is_active', true)
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name_ar ?? $dept->name,
                    'count' => $dept->employees_count,
                ];
            });
        
        // بيانات الموظفين حسب الفرع
        $employeesByBranch = Branch::withCount('employees')
            ->where('is_active', true)
            ->get()
            ->map(function ($branch) {
                return [
                    'name' => $branch->name,
                    'count' => $branch->employees_count,
                ];
            });
        
        return [
            'attendance' => $attendanceData,
            'leaves' => $leaveData,
            'employees_by_department' => $employeesByDepartment,
            'employees_by_branch' => $employeesByBranch,
        ];
    }

    /**
     * API للحصول على بيانات Dashboard (لـ AJAX)
     */
    public function getStats(Request $request)
    {
        $type = $request->input('type', 'all');
        
        $data = [];
        
        if ($type === 'all' || $type === 'general') {
            $data['general'] = $this->getGeneralStats();
        }
        
        if ($type === 'all' || $type === 'attendance') {
            $data['attendance'] = $this->getAttendanceStats();
        }
        
        if ($type === 'all' || $type === 'leaves') {
            $data['leaves'] = $this->getLeaveStats();
        }
        
        if ($type === 'all' || $type === 'salaries') {
            $data['salaries'] = $this->getSalaryStats();
        }
        
        return response()->json($data);
    }
}
