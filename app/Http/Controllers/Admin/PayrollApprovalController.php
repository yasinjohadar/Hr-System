<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollApproval;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:payroll-approval-list')->only('index', 'show');
        $this->middleware('permission:payroll-approval-create')->only('create', 'store');
        $this->middleware('permission:payroll-approval-edit')->only('edit', 'update', 'approve', 'reject');
        $this->middleware('permission:payroll-approval-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = PayrollApproval::with(['payroll.employee', 'approver']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('payroll', function($q) use ($search) {
                    $q->where('payroll_code', 'like', "%$search%");
                })
                ->orWhereHas('payroll.employee', function($q) use ($search) {
                    $q->where('full_name', 'like', "%$search%");
                })
                ->orWhereHas('approver', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        if ($request->filled('payroll_id')) {
            $query->where('payroll_id', $request->input('payroll_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('approval_level')) {
            $query->where('approval_level', $request->input('approval_level'));
        }

        $approvals = $query->latest()->paginate(20);
        $payrolls = Payroll::where('status', '!=', 'paid')->with('employee')->get();

        return view('admin.pages.payroll-approvals.index', compact('approvals', 'payrolls'));
    }

    public function create(Request $request)
    {
        $payrollId = $request->input('payroll_id');
        $payroll = $payrollId ? Payroll::with('employee')->findOrFail($payrollId) : null;
        
        $payrolls = Payroll::where('status', '!=', 'paid')->with('employee')->get();
        $approvers = User::where('is_active', true)->get();

        return view('admin.pages.payroll-approvals.create', compact('payroll', 'payrolls', 'approvers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payroll_id' => 'required|exists:payrolls,id',
            'approval_level' => 'required|integer|min:1',
            'approver_id' => 'required|exists:users,id',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'payroll_id.required' => 'كشف الراتب مطلوب',
            'approval_level.required' => 'مستوى الموافقة مطلوب',
            'approver_id.required' => 'الموافق مطلوب',
        ]);

        // التحقق من عدم تكرار الموافقة
        $exists = PayrollApproval::where('payroll_id', $request->payroll_id)
            ->where('approval_level', $request->approval_level)
            ->where('approver_id', $request->approver_id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['approver_id' => 'هذا الموافق موجود بالفعل في هذا المستوى']);
        }

        $data = $request->all();
        $data['status'] = 'pending';
        $data['sort_order'] = $request->sort_order ?? 0;

        PayrollApproval::create($data);

        return redirect()->route('admin.payroll-approvals.index')
            ->with('success', 'تم إضافة الموافقة بنجاح.');
    }

    public function show(string $id)
    {
        $approval = PayrollApproval::with(['payroll.employee', 'approver'])->findOrFail($id);
        return view('admin.pages.payroll-approvals.show', compact('approval'));
    }

    public function edit(string $id)
    {
        $approval = PayrollApproval::with('payroll.employee')->findOrFail($id);
        
        if ($approval->status !== 'pending') {
            return redirect()->route('admin.payroll-approvals.show', $id)
                ->with('error', 'لا يمكن تعديل موافقة غير قيد الانتظار.');
        }

        $payrolls = Payroll::where('status', '!=', 'paid')->with('employee')->get();
        $approvers = User::where('is_active', true)->get();

        return view('admin.pages.payroll-approvals.edit', compact('approval', 'payrolls', 'approvers'));
    }

    public function update(Request $request, string $id)
    {
        $approval = PayrollApproval::findOrFail($id);

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن تعديل موافقة غير قيد الانتظار.');
        }

        $request->validate([
            'approval_level' => 'required|integer|min:1',
            'approver_id' => 'required|exists:users,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // التحقق من عدم تكرار الموافقة
        $exists = PayrollApproval::where('payroll_id', $approval->payroll_id)
            ->where('approval_level', $request->approval_level)
            ->where('approver_id', $request->approver_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['approver_id' => 'هذا الموافق موجود بالفعل في هذا المستوى']);
        }

        $data = $request->all();
        $data['sort_order'] = $request->sort_order ?? 0;

        $approval->update($data);

        return redirect()->route('admin.payroll-approvals.index')
            ->with('success', 'تم تحديث الموافقة بنجاح.');
    }

    public function destroy(string $id)
    {
        $approval = PayrollApproval::findOrFail($id);

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن حذف موافقة غير قيد الانتظار.');
        }

        $approval->delete();

        return redirect()->route('admin.payroll-approvals.index')
            ->with('success', 'تم حذف الموافقة بنجاح.');
    }

    /**
     * الموافقة على كشف الراتب
     */
    public function approve(Request $request, string $id)
    {
        $approval = PayrollApproval::with('payroll')->findOrFail($id);

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'هذه الموافقة غير قيد الانتظار.');
        }

        $request->validate([
            'comments' => 'nullable|string',
        ]);

        $approval->status = 'approved';
        $approval->approved_at = now();
        $approval->comments = $request->comments;
        $approval->save();

        // التحقق من جميع الموافقات المطلوبة
        $allApproved = PayrollApproval::where('payroll_id', $approval->payroll_id)
            ->where('status', '!=', 'approved')
            ->doesntExist();

        if ($allApproved) {
            $approval->payroll->status = 'approved';
            $approval->payroll->save();
        }

        return redirect()->back()->with('success', 'تم الموافقة على كشف الراتب بنجاح.');
    }

    /**
     * رفض كشف الراتب
     */
    public function reject(Request $request, string $id)
    {
        $approval = PayrollApproval::with('payroll')->findOrFail($id);

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'هذه الموافقة غير قيد الانتظار.');
        }

        $request->validate([
            'comments' => 'required|string',
        ], [
            'comments.required' => 'يجب إدخال سبب الرفض',
        ]);

        $approval->status = 'rejected';
        $approval->rejected_at = now();
        $approval->comments = $request->comments;
        $approval->save();

        // تحديث حالة كشف الراتب
        $approval->payroll->status = 'rejected';
        $approval->payroll->save();

        return redirect()->back()->with('success', 'تم رفض كشف الراتب بنجاح.');
    }
}

