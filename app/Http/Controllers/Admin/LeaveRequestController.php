<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Services\WorkflowService;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:leave-request-list')->only('index');
        $this->middleware('permission:leave-request-create')->only(['create', 'store']);
        $this->middleware('permission:leave-request-edit')->only(['edit', 'update']);
        $this->middleware('permission:leave-request-delete')->only('destroy');
        $this->middleware('permission:leave-request-show')->only('show');
        $this->middleware('permission:leave-request-approve')->only(['approve', 'reject']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaveRequestsQuery = LeaveRequest::with(['employee.user', 'leaveType', 'approver']);

        // رئيس القسم: فقط طلبات موظفي أقسامه
        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $leaveRequestsQuery->whereIn('employee_id', $employeeIds);
            } else {
                $leaveRequestsQuery->whereRaw('1 = 0');
            }
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $leaveRequestsQuery->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب نوع الإجازة
        if ($request->filled('leave_type_id')) {
            $leaveRequestsQuery->where('leave_type_id', $request->input('leave_type_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $leaveRequestsQuery->where('status', $request->input('status'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('start_date')) {
            $leaveRequestsQuery->where('start_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $leaveRequestsQuery->where('end_date', '<=', $request->input('end_date'));
        }

        $leaveRequests = $leaveRequestsQuery->orderBy('created_at', 'desc')->paginate(20);

        $employees = Employee::where('is_active', true)->with('user')->get();
        // رئيس القسم: قائمة موظفين لأقسامه فقط
        if (Auth::user()->isDepartmentHead()) {
            $managedIds = Auth::user()->getManagedEmployeeIds();
            $employees = $employees->whereIn('id', $managedIds)->values();
        }

        $leaveTypes = LeaveType::where('is_active', true)->get();

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html_rows' => view('admin.pages.leave-requests._index_rows', compact('leaveRequests'))->render(),
                'html_pagination' => view('admin.pages.leave-requests._index_pagination', compact('leaveRequests'))->render(),
                'total' => $leaveRequests->total(),
            ]);
        }

        return view('admin.pages.leave-requests.index', compact('leaveRequests', 'employees', 'leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->with('user')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        return view("admin.pages.leave-requests.create", compact("employees", "leaveTypes"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'leave_type_id.required' => 'نوع الإجازة مطلوب',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $daysCount = $startDate->diffInDays($endDate) + 1; // +1 لتضمين اليوم الأول

        // التحقق من رصيد الإجازة
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $year = $startDate->year;
        
        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'year' => $year,
            ],
            [
                'total_days' => $leaveType->max_days ?? 0,
                'used_days' => 0,
                'remaining_days' => $leaveType->max_days ?? 0,
                'carried_forward' => 0,
            ]
        );

        if ($balance->remaining_days < $daysCount) {
            return back()->withInput()->withErrors(['error' => 'رصيد الإجازة غير كافي. المتبقي: ' . $balance->remaining_days . ' يوم']);
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // بدء سير العمل التلقائي
        $employee = Employee::findOrFail($request->employee_id);
        $workflowService = app(WorkflowService::class);
        $workflowService->startWorkflow('leave_request', $employee, 'LeaveRequest', $leaveRequest->id);

        return redirect()->route("admin.leave-requests.index")->with("success", "تم إضافة طلب الإجازة بنجاح وتم إرساله للموافقة");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $leaveRequest = LeaveRequest::with(['employee.user', 'leaveType', 'approver', 'creator'])->findOrFail($id);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!in_array($leaveRequest->employee_id, $employeeIds)) {
                abort(403, 'غير مصرح لك بعرض هذا الطلب.');
            }
        }

        return view("admin.pages.leave-requests.show", compact("leaveRequest"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // لا يمكن تعديل طلب موافق عليه أو مرفوض
        if (in_array($leaveRequest->status, ['approved', 'rejected'])) {
            return redirect()->route('admin.leave-requests.index')
                ->with('error', 'لا يمكن تعديل طلب موافق عليه أو مرفوض');
        }

        $employees = Employee::where('is_active', true)->with('user')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        return view("admin.pages.leave-requests.edit", compact("leaveRequest", "employees", "leaveTypes"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // لا يمكن تعديل طلب موافق عليه أو مرفوض
        if (in_array($leaveRequest->status, ['approved', 'rejected'])) {
            return redirect()->route('admin.leave-requests.index')
                ->with('error', 'لا يمكن تعديل طلب موافق عليه أو مرفوض');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'leave_type_id.required' => 'نوع الإجازة مطلوب',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $daysCount = $startDate->diffInDays($endDate) + 1;

        // التحقق من رصيد الإجازة (إذا تغير عدد الأيام)
        if ($daysCount != $leaveRequest->days_count) {
            $year = $startDate->year;
            $balance = LeaveBalance::firstOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'leave_type_id' => $request->leave_type_id,
                    'year' => $year,
                ],
                [
                    'total_days' => LeaveType::find($request->leave_type_id)->max_days ?? 0,
                    'used_days' => 0,
                    'remaining_days' => LeaveType::find($request->leave_type_id)->max_days ?? 0,
                    'carried_forward' => 0,
                ]
            );

            // إعادة الأيام القديمة
            $balance->used_days -= $leaveRequest->days_count;
            $balance->updateRemaining();

            // التحقق من الرصيد الجديد
            if ($balance->remaining_days < $daysCount) {
                return back()->withInput()->withErrors(['error' => 'رصيد الإجازة غير كافي. المتبقي: ' . $balance->remaining_days . ' يوم']);
            }
        }

        $leaveRequest->update([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.leave-requests.index')->with('success', 'تم تحديث طلب الإجازة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $leaveRequest = LeaveRequest::findOrFail($request->id);
        
        // إذا كان الطلب موافق عليه، إعادة الرصيد
        if ($leaveRequest->status == 'approved') {
            $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', Carbon::parse($leaveRequest->start_date)->year)
                ->first();
            
            if ($balance) {
                $balance->used_days -= $leaveRequest->days_count;
                $balance->updateRemaining();
            }
        }

        $leaveRequest->delete();

        return redirect()->route("admin.leave-requests.index")->with("success", "تم حذف طلب الإجازة بنجاح");
    }

    /**
     * الموافقة على طلب الإجازة
     */
    public function approve(Request $request, string $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $employee = $leaveRequest->employee;

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!in_array($leaveRequest->employee_id, $employeeIds)) {
                abort(403, 'غير مصرح لك بالموافقة على هذا الطلب.');
            }
        }

        if ($leaveRequest->status != 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        // التحقق من صلاحيات الموافقة
        $workflowService = app(WorkflowService::class);
        $approvalService = app(ApprovalService::class);
        
        // البحث عن workflow instance
        $instance = \App\Models\WorkflowInstance::where('entity_type', 'LeaveRequest')
            ->where('entity_id', $leaveRequest->id)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'approved')
            ->first();

        if ($instance) {
            // استخدام نظام سير العمل
            $currentStep = $instance->currentStep;
            if ($currentStep) {
                $canApprove = $approvalService->canUserApprove(
                    auth()->user(),
                    'leave_request',
                    $employee,
                    $currentStep->step_order
                );

                if (!$canApprove) {
                    return back()->with('error', 'ليس لديك صلاحية الموافقة على هذا الطلب');
                }

                // معالجة الموافقة من خلال سير العمل
                $approved = $workflowService->processApproval($instance, auth()->user(), true, $request->comments ?? null);

                if ($approved) {
                    // إذا اكتمل سير العمل، تحديث الرصيد
                    $instance->refresh();
                    if ($instance->status === 'approved') {
                        $this->updateLeaveBalance($leaveRequest);
                    }

                    return back()->with('success', 'تم الموافقة على طلب الإجازة بنجاح');
                } else {
                    return back()->with('error', 'حدث خطأ أثناء معالجة الموافقة');
                }
            }
        }

        // النظام القديم (fallback) - للموافقة المباشرة
        $this->updateLeaveBalance($leaveRequest);
        
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم الموافقة على طلب الإجازة بنجاح');
    }

    /**
     * تحديث رصيد الإجازة
     */
    private function updateLeaveBalance(LeaveRequest $leaveRequest): void
    {
        $year = Carbon::parse($leaveRequest->start_date)->year;
        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id' => $leaveRequest->employee_id,
                'leave_type_id' => $leaveRequest->leave_type_id,
                'year' => $year,
            ],
            [
                'total_days' => $leaveRequest->leaveType->max_days ?? 0,
                'used_days' => 0,
                'remaining_days' => $leaveRequest->leaveType->max_days ?? 0,
                'carried_forward' => 0,
            ]
        );

        $balance->used_days += $leaveRequest->days_count;
        $balance->updateRemaining();
    }

    /**
     * رفض طلب الإجازة
     */
    public function reject(Request $request, string $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $employee = $leaveRequest->employee;

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!in_array($leaveRequest->employee_id, $employeeIds)) {
                abort(403, 'غير مصرح لك برفض هذا الطلب.');
            }
        }

        // البحث عن workflow instance
        $instance = \App\Models\WorkflowInstance::where('entity_type', 'LeaveRequest')
            ->where('entity_id', $leaveRequest->id)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'approved')
            ->first();

        if ($instance) {
            $workflowService = app(WorkflowService::class);
            $approvalService = app(ApprovalService::class);
            
            $currentStep = $instance->currentStep;
            if ($currentStep) {
                $canApprove = $approvalService->canUserApprove(
                    auth()->user(),
                    'leave_request',
                    $employee,
                    $currentStep->step_order
                );

                if (!$canApprove) {
                    return back()->with('error', 'ليس لديك صلاحية رفض هذا الطلب');
                }

                // معالجة الرفض من خلال سير العمل
                $rejected = $workflowService->processApproval($instance, auth()->user(), false, $request->rejection_reason ?? null);

                if ($rejected) {
                    return back()->with('success', 'تم رفض طلب الإجازة');
                } else {
                    return back()->with('error', 'حدث خطأ أثناء معالجة الرفض');
                }
            }
        }

        // النظام القديم (fallback)

        if ($leaveRequest->status != 'pending') {
            return back()->with('error', 'لا يمكن رفض هذا الطلب');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'سبب الرفض مطلوب',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'تم رفض طلب الإجازة بنجاح');
    }
}
