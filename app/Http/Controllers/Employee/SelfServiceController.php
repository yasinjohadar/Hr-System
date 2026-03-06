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
use App\Models\EmployeeBenefit;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\ExpenseRequest;
use App\Models\AssetAssignment;
use App\Models\EmployeeViolation;
use App\Models\Announcement;
use Illuminate\Http\Request;
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
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'لا يوجد ملف موظف مرتبط بحسابك');
        }

        // إحصائيات سريعة
        $stats = [
            'pending_leaves' => LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'pending')->count(),
            'approved_leaves' => LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')->count(),
            'total_attendance' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('attendance_date', now()->month)
                ->where('status', 'present')->count(),
            'pending_goals' => EmployeeGoal::where('employee_id', $employee->id)
                ->where('status', 'in_progress')->count(),
        ];

        // آخر الإجازات
        $recentLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // آخر الحضور
        $recentAttendance = Attendance::where('employee_id', $employee->id)
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->get();

        // إعلانات الشركة الظاهرة لهذا الموظف
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

        return view('employee.pages.self-service.dashboard', compact('employee', 'stats', 'recentLeaves', 'recentAttendance', 'announcements'));
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

        return view('employee.pages.self-service.profile', compact('employee'));
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

        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();

        return view('employee.pages.self-service.leaves', compact('leaves', 'leaveTypes'));
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

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $numberOfDays,
            'reason' => $request->reason,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

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

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        return view('employee.pages.self-service.attendance', compact('attendances'));
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

        $salaries = Salary::where('employee_id', $employee->id)
            ->with('currency')
            ->orderBy('salary_year', 'desc')
            ->orderBy('salary_month', 'desc')
            ->paginate(12);

        return view('employee.pages.self-service.salaries', compact('salaries'));
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

        $documents = EmployeeDocument::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('employee.pages.self-service.documents', compact('documents'));
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

        return view('employee.pages.self-service.skills', compact('skills'));
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

        return view('employee.pages.self-service.certificates', compact('certificates'));
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

        return view('employee.pages.self-service.goals', compact('goals'));
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

        $reviews = PerformanceReview::where('employee_id', $employee->id)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.pages.self-service.performance-reviews', compact('reviews'));
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
            ->with('benefitType')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.pages.self-service.benefits', compact('benefits'));
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

        $tasks = Task::where('assigned_to', $employee->id)
            ->orWhereHas('assignments', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->with(['project', 'assignedTo'])
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        return view('employee.pages.self-service.tasks', compact('tasks'));
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

        $projects = Project::where('manager_id', $employee->id)
            ->orWhereHas('tasks', function($q) use ($employee) {
                $q->where('assigned_to', $employee->id)
                  ->orWhereHas('assignments', function($q2) use ($employee) {
                      $q2->where('employee_id', $employee->id);
                  });
            })
            ->with(['manager', 'department', 'currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('employee.pages.self-service.projects', compact('projects'));
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

        $tickets = Ticket::where('requester_id', $employee->id)
            ->orWhere('assigned_to_id', $employee->id)
            ->with(['requester', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('employee.pages.self-service.tickets', compact('tickets'));
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

        $meetings = Meeting::where('organizer_id', $employee->id)
            ->orWhereHas('attendees', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->with(['organizer', 'attendees.employee'])
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        return view('employee.pages.self-service.meetings', compact('meetings'));
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

        $expenseRequests = ExpenseRequest::where('employee_id', $employee->id)
            ->with(['category', 'currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('employee.pages.self-service.expense-requests', compact('expenseRequests'));
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

        $assets = AssetAssignment::where('employee_id', $employee->id)
            ->with(['asset', 'assigner'])
            ->orderBy('assigned_date', 'desc')
            ->paginate(20);

        return view('employee.pages.self-service.assets', compact('assets'));
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

        $violations = EmployeeViolation::where('employee_id', $employee->id)
            ->with(['violationType', 'disciplinaryAction'])
            ->orderBy('violation_date', 'desc')
            ->paginate(20);

        return view('employee.pages.self-service.violations', compact('violations'));
    }
}
