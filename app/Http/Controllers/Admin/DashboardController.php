<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeViolation;
use App\Models\ExpenseRequest;
use App\Models\LeaveRequest;
use App\Models\Meeting;
use App\Models\Position;
use App\Models\Salary;
use App\Models\Task;
use App\Models\EmployeeCertificate;
use App\Models\EmployeeDocument;
use App\Models\PublicHoliday;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    private const CACHE_KEY = 'admin.dashboard.payload';

    private const CACHE_TTL_SECONDS = 300;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard-view')->only(['index', 'getStats']);
    }

    /**
     * عرض لوحة التحكم الرئيسية
     */
    public function index(Request $request)
    {
        if ($request->boolean('refresh')) {
            self::clearCache();
        }

        $data = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn () => $this->buildDashboardPayload());

        return view('admin.dashboard', $data);
    }

    /**
     * API للحصول على بيانات Dashboard (لـ AJAX)
     */
    public function getStats(Request $request)
    {
        $type = $request->input('type', 'all');
        $payload = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn () => $this->buildDashboardPayload());

        if ($type === 'all') {
            return response()->json([
                'general' => $payload['stats'],
                'attendance' => $payload['attendanceStats'],
                'leaves' => $payload['leaveStats'],
                'salaries' => $payload['salaryStats'],
            ]);
        }

        $data = match ($type) {
            'general' => ['general' => $payload['stats']],
            'attendance' => ['attendance' => $payload['attendanceStats']],
            'leaves' => ['leaves' => $payload['leaveStats']],
            'salaries' => ['salaries' => $payload['salaryStats']],
            default => [],
        };

        return response()->json($data);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDashboardPayload(): array
    {
        $stats = $this->getGeneralStats();
        $attendanceStats = $this->getAttendanceStats();
        $leaveStats = $this->getLeaveStats();
        $salaryStats = $this->getSalaryStats();
        $menaKpis = $this->getMenaKpis();

        return [
            'stats' => $stats,
            'attendanceStats' => $attendanceStats,
            'leaveStats' => $leaveStats,
            'salaryStats' => $salaryStats,
            'urgentTasks' => $this->getUrgentTasks(),
            'importantNotifications' => $this->getImportantNotifications(),
            'chartData' => ['attendance' => $this->getAttendanceChartData()],
            'menaKpis' => $menaKpis,
            'announcements' => Announcement::visible()
                ->orderByDesc('publish_date')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(['id', 'title', 'content', 'publish_date', 'expiry_date', 'created_at']),
            'dashboardWidgets' => $this->buildDashboardWidgets($stats, $attendanceStats, $leaveStats, $salaryStats),
            'secondaryWidgets' => $this->buildSecondaryWidgets($stats, $attendanceStats, $menaKpis),
            'todaySummary' => $this->buildTodaySummary($attendanceStats, $leaveStats, $salaryStats, $menaKpis),
            'quickShortcuts' => $this->buildQuickShortcuts(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildDashboardWidgets(array $stats, array $attendanceStats, array $leaveStats, array $salaryStats): array
    {
        return [
            [
                'title' => 'إجمالي الموظفين',
                'value' => $stats['total_employees'] ?? 0,
                'subtext' => '+' . ($stats['new_employees_this_month'] ?? 0) . ' هذا الشهر',
                'icon' => 'ri-team-line',
                'theme' => 'blue',
                'route' => 'admin.employees.index',
            ],
            [
                'title' => 'حضور اليوم',
                'value' => $attendanceStats['today_present'] ?? 0,
                'subtext' => 'غائب: ' . ($attendanceStats['today_absent'] ?? 0) . ' · متأخر: ' . ($attendanceStats['today_late'] ?? 0),
                'icon' => 'ri-calendar-check-line',
                'theme' => 'green',
                'route' => 'admin.attendances.index',
            ],
            [
                'title' => 'إجازات معلقة',
                'value' => $leaveStats['pending_requests'] ?? 0,
                'subtext' => 'في إجازة اليوم: ' . ($leaveStats['approved_today'] ?? 0),
                'icon' => 'ri-sun-line',
                'theme' => 'orange',
                'route' => 'admin.leave-requests.index',
                'route_params' => ['status' => 'pending'],
            ],
            [
                'title' => 'رواتب الشهر',
                'value' => (int) round($salaryStats['total_this_month'] ?? 0),
                'subtext' => 'مدفوعة: ' . ($salaryStats['paid_count'] ?? 0),
                'icon' => 'ri-money-dollar-circle-line',
                'theme' => 'purple',
                'route' => 'admin.salaries.index',
                'suffix' => ' ر.س',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildSecondaryWidgets(array $stats, array $attendanceStats, array $menaKpis): array
    {
        $widgets = [
            [
                'title' => 'الأقسام',
                'value' => $stats['total_departments'] ?? 0,
                'icon' => 'ri-building-line',
                'color' => 'blue',
                'route' => 'admin.departments.index',
            ],
            [
                'title' => 'المناصب',
                'value' => $stats['total_positions'] ?? 0,
                'icon' => 'ri-briefcase-line',
                'color' => 'green',
                'route' => 'admin.positions.index',
            ],
            [
                'title' => 'الفروع',
                'value' => $stats['total_branches'] ?? 0,
                'icon' => 'ri-map-pin-line',
                'color' => 'cyan',
                'route' => 'admin.branches.index',
            ],
            [
                'title' => 'معدل الحضور الشهري',
                'value' => ($attendanceStats['monthly_attendance_rate'] ?? 0) . '%',
                'icon' => 'ri-line-chart-line',
                'color' => 'orange',
                'route' => 'admin.attendances.index',
            ],
            [
                'title' => 'عطل رسمية (السنة)',
                'value' => $menaKpis['public_holidays_this_year'] ?? 0,
                'icon' => 'ri-calendar-event-line',
                'color' => 'purple',
                'route' => 'admin.public-holidays.index',
            ],
            [
                'title' => 'مستندات تنتهي خلال 30 يوماً',
                'value' => $menaKpis['documents_expiring_30d'] ?? 0,
                'icon' => 'ri-file-text-line',
                'color' => 'yellow',
                'route' => 'admin.employee-documents.index',
            ],
            [
                'title' => 'شهادات تنتهي خلال 30 يوماً',
                'value' => $menaKpis['certificates_expiring_30d'] ?? 0,
                'icon' => 'ri-award-line',
                'color' => 'pink',
                'route' => 'admin.employee-certificates.index',
            ],
            [
                'title' => 'إجازات معلقة',
                'value' => $menaKpis['pending_leave_requests'] ?? 0,
                'icon' => 'ri-time-line',
                'color' => 'red',
                'route' => 'admin.leave-requests.index',
                'route_params' => ['status' => 'pending'],
            ],
        ];

        return array_values(array_filter($widgets, fn ($item) => Route::has($item['route'])));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildTodaySummary(array $attendanceStats, array $leaveStats, array $salaryStats, array $menaKpis): array
    {
        return [
            [
                'label' => 'حضور اليوم',
                'value' => $attendanceStats['today_present'] ?? 0,
                'icon' => 'ri-user-follow-line',
                'color' => 'green',
            ],
            [
                'label' => 'غياب اليوم',
                'value' => $attendanceStats['today_absent'] ?? 0,
                'icon' => 'ri-user-unfollow-line',
                'color' => 'red',
            ],
            [
                'label' => 'متأخرون اليوم',
                'value' => $attendanceStats['today_late'] ?? 0,
                'icon' => 'ri-time-line',
                'color' => 'orange',
            ],
            [
                'label' => 'إجازات معلقة',
                'value' => $leaveStats['pending_requests'] ?? 0,
                'icon' => 'ri-sun-line',
                'color' => 'yellow',
            ],
            [
                'label' => 'رواتب مدفوعة',
                'value' => $salaryStats['paid_count'] ?? 0,
                'icon' => 'ri-wallet-3-line',
                'color' => 'blue',
            ],
            [
                'label' => 'مستندات تنتهي قريباً',
                'value' => $menaKpis['documents_expiring_30d'] ?? 0,
                'icon' => 'ri-file-warning-line',
                'color' => 'pink',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildQuickShortcuts(): array
    {
        return array_values(array_filter([
            ['title' => 'إضافة موظف', 'desc' => 'تسجيل موظف جديد', 'route' => 'admin.employees.create', 'icon' => 'ri-user-add-line', 'color' => 'blue'],
            ['title' => 'طلب إجازة', 'desc' => 'إنشاء طلب إجازة', 'route' => 'admin.leave-requests.create', 'icon' => 'ri-calendar-event-line', 'color' => 'green'],
            ['title' => 'إضافة راتب', 'desc' => 'تسجيل راتب شهري', 'route' => 'admin.salaries.create', 'icon' => 'ri-wallet-3-line', 'color' => 'orange'],
            ['title' => 'تذكرة جديدة', 'desc' => 'فتح تذكرة دعم', 'route' => 'admin.tickets.create', 'icon' => 'ri-customer-service-2-line', 'color' => 'pink'],
            ['title' => 'اجتماع جديد', 'desc' => 'جدولة اجتماع', 'route' => 'admin.meetings.create', 'icon' => 'ri-video-add-line', 'color' => 'purple'],
            ['title' => 'التقارير', 'desc' => 'عرض التقارير', 'route' => 'admin.reports.index', 'icon' => 'ri-pie-chart-line', 'color' => 'indigo'],
            ['title' => 'الحضور', 'desc' => 'سجل الحضور', 'route' => 'admin.attendances.index', 'icon' => 'ri-calendar-check-line', 'color' => 'teal'],
            ['title' => 'الموظفون', 'desc' => 'إدارة الموظفين', 'route' => 'admin.employees.index', 'icon' => 'ri-team-line', 'color' => 'cyan'],
        ], fn ($item) => Route::has($item['route'])));
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function getMenaKpis(): array
    {
        $today = Carbon::today();

        return [
            'public_holidays_this_year' => PublicHoliday::where('is_active', true)->whereYear('holiday_date', $today->year)->count(),
            'documents_expiring_30d' => EmployeeDocument::whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$today, $today->copy()->addDays(30)])->count(),
            'certificates_expiring_30d' => EmployeeCertificate::whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$today, $today->copy()->addDays(30)])->count(),
            'pending_leave_requests' => LeaveRequest::where('status', 'pending')->count(),
        ];
    }

    private function getGeneralStats(): array
    {
        $now = Carbon::now();

        return [
            'total_employees' => Employee::where('is_active', true)->count(),
            'new_employees_this_month' => Employee::where('is_active', true)
                ->whereMonth('hire_date', $now->month)
                ->whereYear('hire_date', $now->year)
                ->count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'total_positions' => Position::where('is_active', true)->count(),
            'total_branches' => Branch::where('is_active', true)->count(),
        ];
    }

    private function getAttendanceStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $todayByStatus = Attendance::query()
            ->whereDate('attendance_date', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthAgg = Attendance::query()
            ->where('attendance_date', '>=', $thisMonth)
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present_days,
                COALESCE(SUM(hours_worked), 0) as total_hours_worked,
                COALESCE(SUM(overtime_minutes), 0) as total_overtime_minutes
            ', ['present'])
            ->first();

        $totalDays = (int) ($monthAgg->total_days ?? 0);
        $presentDays = (int) ($monthAgg->present_days ?? 0);

        return [
            'today_present' => (int) ($todayByStatus['present'] ?? 0),
            'today_absent' => (int) ($todayByStatus['absent'] ?? 0),
            'today_late' => (int) ($todayByStatus['late'] ?? 0),
            'monthly_attendance_rate' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            'total_hours_this_month' => ((int) ($monthAgg->total_hours_worked ?? 0)) / 60,
            'total_overtime_this_month' => ((int) ($monthAgg->total_overtime_minutes ?? 0)) / 60,
        ];
    }

    private function getLeaveStats(): array
    {
        $today = Carbon::today();
        $now = Carbon::now();

        return [
            'pending_requests' => LeaveRequest::where('status', 'pending')->count(),
            'approved_today' => LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->count(),
            'total_this_month' => LeaveRequest::whereMonth('start_date', $now->month)
                ->whereYear('start_date', $now->year)
                ->count(),
            'approved_this_month' => LeaveRequest::where('status', 'approved')
                ->whereMonth('start_date', $now->month)
                ->whereYear('start_date', $now->year)
                ->count(),
        ];
    }

    private function getSalaryStats(): array
    {
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $row = Salary::query()
            ->where('salary_month', $thisMonth)
            ->where('salary_year', $thisYear)
            ->selectRaw('
                COALESCE(SUM(total_salary), 0) as total_this_month,
                SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as pending_count,
                COUNT(*) as total_employees
            ', ['paid', 'pending'])
            ->first();

        return [
            'total_this_month' => (float) ($row->total_this_month ?? 0),
            'paid_count' => (int) ($row->paid_count ?? 0),
            'pending_count' => (int) ($row->pending_count ?? 0),
            'total_employees' => (int) ($row->total_employees ?? 0),
        ];
    }

    private function getUrgentTasks(): array
    {
        $now = Carbon::now();

        return [
            'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
            'pending_expenses' => ExpenseRequest::where('status', 'pending')->count(),
            'open_tickets' => Ticket::where('status', 'open')->count(),
            'pending_violations' => EmployeeViolation::where('status', 'pending')->count(),
            'upcoming_meetings' => Meeting::where('status', 'scheduled')
                ->whereBetween('start_time', [$now, $now->copy()->addDays(7)])
                ->count(),
            'overdue_tasks' => Task::where('status', '!=', 'completed')
                ->where('due_date', '<', Carbon::today())
                ->count(),
        ];
    }

    private function getImportantNotifications(): array
    {
        $today = Carbon::today();

        return [
            'expiring_documents' => DB::table('employee_documents')
                ->whereBetween('expiry_date', [$today, $today->copy()->addDays(30)])
                ->where('status', 'active')
                ->count(),
            'expiring_certificates' => DB::table('employee_certificates')
                ->whereBetween('expiry_date', [$today, $today->copy()->addDays(30)])
                ->count(),
            'contracts_expiring' => Contract::active()
                ->whereBetween('end_date', [$today, $today->copy()->addDays(90)])
                ->count(),
        ];
    }

    /**
     * بيانات رسم الحضور — استعلامان مجمّعان بدل 12+ استعلام في حلقة
     */
    private function getAttendanceChartData(): array
    {
        $start = Carbon::now()->subMonths(5)->startOfMonth();

        $rows = Attendance::query()
            ->where('attendance_date', '>=', $start)
            ->selectRaw('YEAR(attendance_date) as year, MONTH(attendance_date) as month, status, COUNT(*) as total')
            ->groupByRaw('YEAR(attendance_date), MONTH(attendance_date), status')
            ->get();

        $indexed = [];
        foreach ($rows as $row) {
            $key = $row->year . '-' . str_pad((string) $row->month, 2, '0', STR_PAD_LEFT);
            $indexed[$key][$row->status] = (int) $row->total;
        }

        $attendanceData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('Y-m');
            $attendanceData[] = [
                'month' => $date->format('M Y'),
                'present' => $indexed[$key]['present'] ?? 0,
                'absent' => $indexed[$key]['absent'] ?? 0,
            ];
        }

        return $attendanceData;
    }
}
