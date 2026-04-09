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
use App\Models\Payroll;
use App\Models\ExpenseCategory;
use App\Models\Currency;
use App\Models\TrainingRecord;
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

        $payrolls = Payroll::where('employee_id', $employee->id)
            ->with('currency')
            ->orderByDesc('payroll_year')
            ->orderByDesc('payroll_month')
            ->limit(24)
            ->get();

        return view('employee.pages.self-service.salaries', compact('salaries', 'payrolls'));
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

        $tasks = Task::whereHas('assignments', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })
            ->with(['project'])
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

        $projects = Project::where(function ($q) use ($employee) {
            $q->where('manager_id', $employee->id)
                ->orWhereHas('members', function ($q2) use ($employee) {
                    $q2->where('employee_id', $employee->id);
                })
                ->orWhereHas('tasks.assignments', function ($q2) use ($employee) {
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

        $tickets = Ticket::where('employee_id', $employee->id)
            ->orWhere('assigned_to', $employee->id)
            ->with(['employee', 'assignedTo'])
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
            ->limit(20)
            ->get();

        return view('employee.pages.self-service.policies', compact('policiesPending', 'policiesAcknowledged'));
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

        return view('employee.pages.self-service.contract', compact('employee', 'currentContract', 'contracts'));
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

        ExpenseRequest::create($data);

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

        \App\Models\Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'employee_id' => $employee->id,
            'status' => 'open',
            'created_by' => $user->id,
        ]);

        return redirect()->route('employee.tickets')->with('success', 'تم فتح التذكرة بنجاح.');
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
            ->paginate(15);

        return view('employee.pages.self-service.announcements', compact('announcements'));
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

        $records = TrainingRecord::where('employee_id', $employee->id)
            ->with('training')
            ->orderByDesc('registration_date')
            ->paginate(15);

        return view('employee.pages.self-service.training-records', compact('records'));
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

        ProjectTimeEntry::create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'task_id' => $validated['task_id'] ?? null,
            'worked_date' => $validated['worked_date'],
            'hours' => $validated['hours'],
            'description' => $validated['description'] ?? null,
            'created_by' => $user->id,
        ]);

        return redirect()
            ->route('employee.projects.show', $project)
            ->with('success', 'تم تسجيل الوقت بنجاح.');
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

        $query = ProjectTimeEntry::where('employee_id', $employee->id)
            ->with(['project', 'task']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('worked_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('worked_date', '<=', $request->input('to'));
        }

        $entries = $query->orderByDesc('worked_date')->orderByDesc('id')->paginate(20)->withQueryString();

        $accessibleProjects = Project::where(function ($q) use ($employee) {
            $q->where('manager_id', $employee->id)
                ->orWhereHas('members', fn ($q2) => $q2->where('employee_id', $employee->id))
                ->orWhereHas('tasks.assignments', fn ($q2) => $q2->where('employee_id', $employee->id));
        })
            ->orderBy('name')
            ->get();

        return view('employee.pages.self-service.project-time', compact('entries', 'accessibleProjects'));
    }
}
