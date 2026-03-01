<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeViolation;
use App\Models\ViolationType;
use App\Models\DisciplinaryAction;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class EmployeeViolationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-violation-list')->only('index');
        $this->middleware('permission:employee-violation-create')->only(['create', 'store']);
        $this->middleware('permission:employee-violation-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-violation-delete')->only('destroy');
        $this->middleware('permission:employee-violation-show')->only('show');
        $this->middleware('permission:employee-violation-investigate')->only(['investigate', 'confirm', 'dismiss']);
        $this->middleware('permission:employee-violation-approve')->only(['approve', 'applyAction']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeViolation::with(['employee', 'violationType', 'disciplinaryAction', 'reporter']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('violation_type_id')) {
            $query->where('violation_type_id', $request->input('violation_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('start_date')) {
            $query->where('violation_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->where('violation_date', '<=', $request->input('end_date'));
        }

        $violations = $query->latest()->paginate(15);
        $employees = Employee::where('is_active', true)->get();
        $violationTypes = ViolationType::where('is_active', true)->get();

        return view('admin.pages.employee-violations.index', compact('violations', 'employees', 'violationTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $violationTypes = ViolationType::where('is_active', true)->get();
        $disciplinaryActions = DisciplinaryAction::where('is_active', true)->get();
        $attendances = Attendance::latest()->take(100)->get();
        $leaveRequests = LeaveRequest::latest()->take(100)->get();

        return view('admin.pages.employee-violations.create', compact('employees', 'violationTypes', 'disciplinaryActions', 'attendances', 'leaveRequests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'disciplinary_action_id' => 'nullable|exists:disciplinary_actions,id',
            'violation_date' => 'required|date',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'witnesses' => 'nullable|string',
            'severity' => 'required|in:low,medium,high,critical',
            'attendance_id' => 'nullable|exists:attendances,id',
            'leave_request_id' => 'nullable|exists:leave_requests,id',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['reported_by'] = auth()->id();
        $data['status'] = 'pending';

        EmployeeViolation::create($data);

        return redirect()->route('admin.employee-violations.index')->with('success', 'تم إضافة المخالفة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $violation = EmployeeViolation::with([
            'employee',
            'violationType',
            'disciplinaryAction',
            'reporter',
            'investigator',
            'approver',
            'attendance',
            'leaveRequest',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.employee-violations.show', compact('violation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if (!in_array($violation->status, ['pending', 'dismissed'])) {
            return redirect()->back()->with('error', 'لا يمكن تعديل مخالفة في هذه الحالة.');
        }

        $employees = Employee::where('is_active', true)->get();
        $violationTypes = ViolationType::where('is_active', true)->get();
        $disciplinaryActions = DisciplinaryAction::where('is_active', true)->get();
        $attendances = Attendance::latest()->take(100)->get();
        $leaveRequests = LeaveRequest::latest()->take(100)->get();

        return view('admin.pages.employee-violations.edit', compact('violation', 'employees', 'violationTypes', 'disciplinaryActions', 'attendances', 'leaveRequests'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if (!in_array($violation->status, ['pending', 'dismissed'])) {
            return redirect()->back()->with('error', 'لا يمكن تعديل مخالفة في هذه الحالة.');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'disciplinary_action_id' => 'nullable|exists:disciplinary_actions,id',
            'violation_date' => 'required|date',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'witnesses' => 'nullable|string',
            'severity' => 'required|in:low,medium,high,critical',
            'attendance_id' => 'nullable|exists:attendances,id',
            'leave_request_id' => 'nullable|exists:leave_requests,id',
            'notes' => 'nullable|string',
        ]);

        $violation->update($request->all());

        return redirect()->route('admin.employee-violations.index')->with('success', 'تم تحديث المخالفة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if (!in_array($violation->status, ['pending', 'dismissed'])) {
            return redirect()->back()->with('error', 'لا يمكن حذف مخالفة في هذه الحالة.');
        }

        $violation->delete();

        return redirect()->route('admin.employee-violations.index')->with('success', 'تم حذف المخالفة بنجاح.');
    }

    /**
     * بدء التحقيق في المخالفة
     */
    public function investigate(Request $request, string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if ($violation->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن بدء التحقيق في مخالفة في هذه الحالة.');
        }

        $request->validate([
            'investigation_notes' => 'required|string',
        ]);

        $violation->update([
            'status' => 'investigating',
            'investigated_by' => auth()->id(),
            'investigation_date' => now(),
            'investigation_notes' => $request->investigation_notes,
        ]);

        return redirect()->back()->with('success', 'تم بدء التحقيق في المخالفة.');
    }

    /**
     * تأكيد المخالفة
     */
    public function confirm(Request $request, string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if (!in_array($violation->status, ['investigating', 'pending'])) {
            return redirect()->back()->with('error', 'لا يمكن تأكيد مخالفة في هذه الحالة.');
        }

        $request->validate([
            'disciplinary_action_id' => 'required|exists:disciplinary_actions,id',
            'action_notes' => 'nullable|string',
        ]);

        $disciplinaryAction = DisciplinaryAction::findOrFail($request->disciplinary_action_id);

        $violation->update([
            'status' => 'confirmed',
            'disciplinary_action_id' => $request->disciplinary_action_id,
            'action_notes' => $request->action_notes,
        ]);

        // إذا كان الإجراء يتطلب موافقة
        if ($disciplinaryAction->requires_approval) {
            // يبقى في حالة confirmed حتى يتم الموافقة
        } else {
            // تطبيق الإجراء مباشرة
            $violation->update([
                'action_date' => now(),
                'status' => 'resolved',
                'resolution_date' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'تم تأكيد المخالفة.');
    }

    /**
     * رفض/إلغاء المخالفة
     */
    public function dismiss(Request $request, string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if (!in_array($violation->status, ['pending', 'investigating'])) {
            return redirect()->back()->with('error', 'لا يمكن رفض مخالفة في هذه الحالة.');
        }

        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $violation->update([
            'status' => 'dismissed',
            'resolution_notes' => $request->resolution_notes,
            'resolution_date' => now(),
        ]);

        return redirect()->back()->with('success', 'تم رفض المخالفة.');
    }

    /**
     * الموافقة على الإجراء التأديبي
     */
    public function approve(Request $request, string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if ($violation->status !== 'confirmed') {
            return redirect()->back()->with('error', 'لا يمكن الموافقة على إجراء في هذه الحالة.');
        }

        $violation->update([
            'approved_by' => auth()->id(),
            'approval_date' => now(),
        ]);

        // تطبيق الإجراء
        $this->executeAction($violation);

        return redirect()->back()->with('success', 'تم الموافقة على الإجراء التأديبي.');
    }

    /**
     * تطبيق الإجراء التأديبي
     */
    private function executeAction(EmployeeViolation $violation)
    {
        if (!$violation->disciplinaryAction) {
            return;
        }

        $action = $violation->disciplinaryAction;

        // تطبيق الإجراء حسب النوع
        switch ($action->action_type) {
            case 'deduction':
                // يمكن ربط هذا بنظام الرواتب لخصم المبلغ
                break;
            case 'suspension':
                // يمكن ربط هذا بنظام الإجازات
                break;
            case 'termination':
                // يمكن ربط هذا بنظام إنهاء الخدمة
                break;
        }

        $violation->update([
            'action_date' => now(),
            'status' => 'resolved',
            'resolution_date' => now(),
        ]);
    }

    /**
     * تطبيق الإجراء يدوياً
     */
    public function applyAction(Request $request, string $id)
    {
        $violation = EmployeeViolation::findOrFail($id);

        if ($violation->status !== 'confirmed') {
            return redirect()->back()->with('error', 'يجب تأكيد المخالفة أولاً.');
        }

        $request->validate([
            'action_notes' => 'nullable|string',
        ]);

        $violation->update([
            'action_notes' => $request->action_notes,
        ]);

        $this->executeAction($violation);

        return redirect()->back()->with('success', 'تم تطبيق الإجراء التأديبي.');
    }
}
