<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollPayment;
use App\Models\Payroll;
use App\Models\Currency;
use Illuminate\Http\Request;

class PayrollPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:payroll-payment-list')->only('index', 'show');
        $this->middleware('permission:payroll-payment-create')->only('create', 'store');
        $this->middleware('permission:payroll-payment-edit')->only('edit', 'update', 'process');
        $this->middleware('permission:payroll-payment-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = PayrollPayment::with(['payroll.employee', 'currency', 'bankAccount']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('payment_code', 'like', "%$search%")
                  ->orWhere('reference_number', 'like', "%$search%")
                  ->orWhereHas('payroll.employee', function($q) use ($search) {
                      $q->where('full_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('payroll_id')) {
            $query->where('payroll_id', $request->input('payroll_id'));
        }

        $payments = $query->latest()->paginate(20);
        $payrolls = Payroll::where('status', 'approved')->with('employee')->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.payroll-payments.index', compact('payments', 'payrolls', 'currencies'));
    }

    public function create(Request $request)
    {
        $payrollId = $request->input('payroll_id');
        $payroll = $payrollId ? Payroll::with('employee')->findOrFail($payrollId) : null;
        
        $payrolls = Payroll::where('status', 'approved')
            ->whereDoesntHave('payments', function($q) {
                $q->where('status', 'completed');
            })
            ->with('employee')
            ->get();
        
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.payroll-payments.create', compact('payroll', 'payrolls', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payroll_id' => 'required|exists:payrolls,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'nullable|exists:currencies,id',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,card,other',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|exists:employee_bank_accounts,id',
            'payment_notes' => 'nullable|string',
        ], [
            'payroll_id.required' => 'كشف الراتب مطلوب',
            'amount.required' => 'المبلغ مطلوب',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_date.required' => 'تاريخ الدفع مطلوب',
        ]);

        $payroll = Payroll::findOrFail($request->payroll_id);

        // التحقق من أن المبلغ لا يتجاوز الراتب الصافي
        if ($request->amount > $payroll->net_salary) {
            return back()->withInput()->withErrors(['amount' => 'المبلغ لا يمكن أن يتجاوز الراتب الصافي']);
        }

        $data = $request->all();
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();

        PayrollPayment::create($data);

        return redirect()->route('admin.payroll-payments.index')
            ->with('success', 'تم إنشاء سجل الدفع بنجاح.');
    }

    public function show(string $id)
    {
        $payment = PayrollPayment::with([
            'payroll.employee',
            'currency',
            'bankAccount',
            'processedBy',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.payroll-payments.show', compact('payment'));
    }

    public function edit(string $id)
    {
        $payment = PayrollPayment::with('payroll.employee')->findOrFail($id);
        
        if ($payment->status === 'completed') {
            return redirect()->route('admin.payroll-payments.show', $id)
                ->with('error', 'لا يمكن تعديل دفعة مكتملة.');
        }

        $payrolls = Payroll::where('status', 'approved')->with('employee')->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.payroll-payments.edit', compact('payment', 'payrolls', 'currencies'));
    }

    public function update(Request $request, string $id)
    {
        $payment = PayrollPayment::findOrFail($id);

        if ($payment->status === 'completed') {
            return redirect()->back()->with('error', 'لا يمكن تعديل دفعة مكتملة.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'nullable|exists:currencies,id',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,card,other',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|exists:employee_bank_accounts,id',
            'payment_notes' => 'nullable|string',
        ]);

        $payment->update($request->all());

        return redirect()->route('admin.payroll-payments.index')
            ->with('success', 'تم تحديث سجل الدفع بنجاح.');
    }

    public function destroy(string $id)
    {
        $payment = PayrollPayment::findOrFail($id);

        if ($payment->status === 'completed') {
            return redirect()->back()->with('error', 'لا يمكن حذف دفعة مكتملة.');
        }

        $payment->delete();

        return redirect()->route('admin.payroll-payments.index')
            ->with('success', 'تم حذف سجل الدفع بنجاح.');
    }

    /**
     * معالجة الدفعة
     */
    public function process(Request $request, string $id)
    {
        $payment = PayrollPayment::with('payroll')->findOrFail($id);

        if ($payment->status === 'completed') {
            return redirect()->back()->with('error', 'الدفعة مكتملة بالفعل.');
        }

        $request->validate([
            'status' => 'required|in:processing,completed,failed,cancelled',
            'failure_reason' => 'required_if:status,failed|nullable|string',
        ]);

        $payment->status = $request->status;
        $payment->processed_by = auth()->id();
        $payment->processed_at = now();

        if ($request->status === 'failed' && $request->filled('failure_reason')) {
            $payment->failure_reason = $request->failure_reason;
        }

        $payment->save();

        // إذا تمت المعالجة بنجاح، تحديث حالة كشف الراتب
        if ($request->status === 'completed') {
            $payment->payroll->status = 'paid';
            $payment->payroll->payment_date = $payment->payment_date;
            $payment->payroll->payment_method = $payment->payment_method;
            $payment->payroll->payment_reference = $payment->reference_number;
            $payment->payroll->save();
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الدفعة بنجاح.');
    }
}
