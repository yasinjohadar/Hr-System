<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseRequest;
use App\Models\ExpenseCategory;
use App\Models\ExpenseApproval;
use App\Models\Employee;
use App\Models\Currency;
use App\Services\WorkflowService;
use App\Services\ApprovalService;
use App\Services\EmployeeRequestSubmissionService;
use App\Services\WorkflowProgressPresenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ExpenseRequestController extends Controller
{
    use Concerns\ScopesByDepartment;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:expense-request-list')->only('index');
        $this->middleware('permission:expense-request-create')->only(['create', 'store']);
        $this->middleware('permission:expense-request-edit')->only(['edit', 'update']);
        $this->middleware('permission:expense-request-delete')->only('destroy');
        $this->middleware('permission:expense-request-show')->only('show');
        $this->middleware('permission:expense-request-approve')->only(['showApproveForm', 'approve', 'reject']);
        $this->middleware('permission:expense-request-pay')->only('markAsPaid');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExpenseRequest::with(['employee', 'category', 'currency', 'creator']);

        $this->scopeByEmployeeQuery($query);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('expense_category_id')) {
            $query->where('expense_category_id', $request->input('expense_category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('start_date')) {
            $query->where('expense_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->where('expense_date', '<=', $request->input('end_date'));
        }

        $expenseRequests = $query->latest()->paginate(15);

        $canApproveNowById = app(ApprovalService::class)->canActOnEntitiesMap(
            Auth::user(),
            $expenseRequests->getCollection()
        );

        $workflowProgressById = app(WorkflowProgressPresenter::class)->mapForEntities(
            $expenseRequests->getCollection()
        );

        $employees = Employee::where('is_active', true)->get();
        $employees = collect($this->departmentScope()->filterEmployeeCollection($employees));
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('admin.pages.expense-requests.index', compact('expenseRequests', 'employees', 'categories', 'canApproveNowById', 'workflowProgressById'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.expense-requests.create', compact('employees', 'categories', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'nullable|exists:currencies,id',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
            'payment_method' => 'nullable|in:cash,card,transfer,check',
            'vendor_name' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $category = ExpenseCategory::findOrFail($request->expense_category_id);

        // التحقق من الحد الأقصى
        if ($category->max_amount && $request->amount > $category->max_amount) {
            return redirect()->back()->withInput()->with('error', 'المبلغ يتجاوز الحد الأقصى المسموح به: ' . number_format($category->max_amount, 2));
        }

        // التحقق من وجود إيصال إذا كان مطلوباً
        if ($category->requires_receipt && !$request->hasFile('receipt')) {
            return redirect()->back()->withInput()->with('error', 'إيصال المصروف مطلوب لهذا التصنيف.');
        }

        $data = $request->except('receipt');
        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';
        $data['request_code'] = 'EXP-' . strtoupper(Str::random(8));

        // رفع الإيصال
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $data['receipt_path'] = $file->store('expense_receipts', 'public');
            $data['receipt_file_name'] = $file->getClientOriginalName();
            $data['receipt_file_size'] = $file->getSize();
        }

        $employee = Employee::findOrFail($request->employee_id);

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

        return redirect()->route('admin.expense-requests.index')->with('success', 'تم إضافة طلب المصروف بنجاح وتم إرساله للموافقة.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expenseRequest = ExpenseRequest::with([
            'employee',
            'category',
            'currency',
            'approvals.approver',
            'payer',
            'creator'
        ])->findOrFail($id);

        $this->authorizeManagedEmployeeId((int) $expenseRequest->employee_id, 'غير مصرح لك بعرض هذا الطلب.');

        $canApproveNow = app(ApprovalService::class)->canActOnEntity(Auth::user(), $expenseRequest);
        $workflowProgress = app(WorkflowProgressPresenter::class)->resolveForEntity($expenseRequest);

        return view('admin.pages.expense-requests.show', compact('expenseRequest', 'canApproveNow', 'workflowProgress'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        if (!in_array($expenseRequest->status, ['pending', 'rejected'])) {
            return redirect()->back()->with('error', 'لا يمكن تعديل طلب في هذه الحالة.');
        }

        $employees = Employee::where('is_active', true)->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.expense-requests.edit', compact('expenseRequest', 'employees', 'categories', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        if (!in_array($expenseRequest->status, ['pending', 'rejected'])) {
            return redirect()->back()->with('error', 'لا يمكن تعديل طلب في هذه الحالة.');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'nullable|exists:currencies,id',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'payment_method' => 'nullable|in:cash,card,transfer,check',
            'vendor_name' => 'nullable|string|max:255',
            'project_code' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $category = ExpenseCategory::findOrFail($request->expense_category_id);

        if ($category->max_amount && $request->amount > $category->max_amount) {
            return redirect()->back()->withInput()->with('error', 'المبلغ يتجاوز الحد الأقصى المسموح به.');
        }

        $data = $request->except('receipt');

        // تحديث الإيصال إذا تم رفعه
        if ($request->hasFile('receipt')) {
            if ($expenseRequest->receipt_path) {
                Storage::disk('public')->delete($expenseRequest->receipt_path);
            }
            $file = $request->file('receipt');
            $data['receipt_path'] = $file->store('expense_receipts', 'public');
            $data['receipt_file_name'] = $file->getClientOriginalName();
            $data['receipt_file_size'] = $file->getSize();
        }

        // إعادة تعيين الحالة إذا كان مرفوضاً
        if ($expenseRequest->status === 'rejected') {
            $data['status'] = 'pending';
            $data['rejection_reason'] = null;
        }

        $expenseRequest->update($data);

        return redirect()->route('admin.expense-requests.index')->with('success', 'تم تحديث طلب المصروف بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        if (!in_array($expenseRequest->status, ['pending', 'rejected', 'cancelled'])) {
            return redirect()->back()->with('error', 'لا يمكن حذف طلب في هذه الحالة.');
        }

        if ($expenseRequest->receipt_path) {
            Storage::disk('public')->delete($expenseRequest->receipt_path);
        }

        $expenseRequest->delete();

        return redirect()->route('admin.expense-requests.index')->with('success', 'تم حذف طلب المصروف بنجاح.');
    }

    /**
     * عرض نموذج الموافقة
     */
    public function showApproveForm(string $id)
    {
        $expenseRequest = ExpenseRequest::with(['employee', 'category'])->findOrFail($id);

        $this->authorizeManagedEmployeeId((int) $expenseRequest->employee_id, 'غير مصرح لك بالموافقة على هذا الطلب.');

        if ($expenseRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن الموافقة على طلب في هذه الحالة.');
        }

        if (! app(ApprovalService::class)->canActOnEntity(Auth::user(), $expenseRequest)) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الموافقة على هذا الطلب في المرحلة الحالية.');
        }

        return view('admin.pages.expense-requests.approve', compact('expenseRequest'));
    }

    /**
     * الموافقة على طلب المصروف
     */
    public function approve(Request $request, string $id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);
        $employee = $expenseRequest->employee;

        $this->authorizeManagedEmployeeId((int) $expenseRequest->employee_id, 'غير مصرح لك بالموافقة على هذا الطلب.');

        if ($expenseRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن الموافقة على طلب في هذه الحالة.');
        }

        $request->validate([
            'comments' => 'nullable|string|max:2000',
        ]);

        // البحث عن workflow instance
        $instance = WorkflowService::findInstanceForEntity(ExpenseRequest::class, $expenseRequest->id);

        if ($instance) {
            // استخدام نظام سير العمل
            $workflowService = app(WorkflowService::class);
            $approvalService = app(ApprovalService::class);
            
            $currentStep = $instance->currentStep;
            if ($currentStep) {
                $canApprove = $approvalService->canActOnCurrentStep(
                    auth()->user(),
                    'expense_request',
                    $employee,
                    $currentStep->step_order,
                    $expenseRequest
                );

                if (! $canApprove) {
                    return redirect()->back()->with('error', 'ليس لديك صلاحية الموافقة على هذا الطلب');
                }

                // معالجة الموافقة من خلال سير العمل
                $approved = $workflowService->processApproval($instance, auth()->user(), true, $request->comments ?? null);

                if ($approved) {
                    $instance->refresh();
                    if ($instance->status === 'approved') {
                        $expenseRequest->update(['status' => 'approved']);
                    }

                    return redirect()->route('admin.expense-requests.index')
                        ->with('success', $workflowService->approvalFlashMessage($instance, true));
                } else {
                    return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة الموافقة');
                }
            }
        }

        return redirect()->back()->with('error', 'لا يوجد مسار موافقة نشط لهذا الطلب.');
    }

    /**
     * رفض طلب المصروف
     */
    public function reject(Request $request, string $id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);
        $employee = $expenseRequest->employee;

        $this->authorizeManagedEmployeeId((int) $expenseRequest->employee_id, 'غير مصرح لك برفض هذا الطلب.');

        // البحث عن workflow instance
        $instance = WorkflowService::findInstanceForEntity(ExpenseRequest::class, $expenseRequest->id);

        if ($instance) {
            $workflowService = app(WorkflowService::class);
            $approvalService = app(ApprovalService::class);
            
            $currentStep = $instance->currentStep;
            if ($currentStep) {
                $canApprove = $approvalService->canActOnCurrentStep(
                    auth()->user(),
                    'expense_request',
                    $employee,
                    $currentStep->step_order,
                    $expenseRequest
                );

                if (! $canApprove) {
                    return redirect()->back()->with('error', 'ليس لديك صلاحية رفض هذا الطلب');
                }

                // معالجة الرفض من خلال سير العمل
                $rejected = $workflowService->processApproval($instance, auth()->user(), false, $request->rejection_reason ?? null);

                if ($rejected) {
                    $instance->refresh();

                    return redirect()->route('admin.expense-requests.index')
                        ->with('success', $workflowService->approvalFlashMessage($instance, false));
                }
            }
        }

        return redirect()->back()->with('error', 'لا يوجد مسار موافقة نشط لهذا الطلب.');
    }

    /**
     * تحديد طلب المصروف كمدفوع
     */
    public function markAsPaid(Request $request, string $id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        if ($expenseRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'يجب الموافقة على الطلب أولاً.');
        }

        $request->validate([
            'paid_date' => 'required|date',
        ]);

        $expenseRequest->update([
            'status' => 'paid',
            'paid_date' => $request->paid_date,
            'paid_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم تحديد الطلب كمدفوع بنجاح.');
    }
}
