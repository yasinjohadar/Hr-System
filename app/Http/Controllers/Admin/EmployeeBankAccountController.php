<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeBankAccount;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeBankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:bank-account-list')->only('index', 'show');
        $this->middleware('permission:bank-account-create')->only('create', 'store');
        $this->middleware('permission:bank-account-edit')->only('edit', 'update');
        $this->middleware('permission:bank-account-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeBankAccount::with('employee');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%$search%")
                  ->orWhere('account_number', 'like', "%$search%")
                  ->orWhere('iban', 'like', "%$search%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('full_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('is_primary')) {
            $query->where('is_primary', $request->input('is_primary') == '1');
        }

        $bankAccounts = $query->latest()->paginate(20);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.bank-accounts.index', compact('bankAccounts', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.bank-accounts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bank_name' => 'required|string|max:255',
            'bank_name_ar' => 'nullable|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:34',
            'swift_code' => 'nullable|string|max:11',
            'account_holder_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'branch_address' => 'nullable|string|max:500',
            'account_type' => 'required|in:savings,current,salary',
            'currency_code' => 'required|string|size:3',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'bank_name.required' => 'اسم البنك مطلوب',
            'account_number.required' => 'رقم الحساب مطلوب',
            'account_type.required' => 'نوع الحساب مطلوب',
        ]);

        $data = $request->all();
        $data['is_primary'] = $request->has('is_primary');
        $data['is_active'] = $request->has('is_active');
        $data['created_by'] = auth()->id();

        EmployeeBankAccount::create($data);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'تم إنشاء الحساب البنكي بنجاح.');
    }

    public function show(string $id)
    {
        $bankAccount = EmployeeBankAccount::with(['employee', 'creator'])->findOrFail($id);
        return view('admin.pages.bank-accounts.show', compact('bankAccount'));
    }

    public function edit(string $id)
    {
        $bankAccount = EmployeeBankAccount::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.bank-accounts.edit', compact('bankAccount', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $bankAccount = EmployeeBankAccount::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bank_name' => 'required|string|max:255',
            'bank_name_ar' => 'nullable|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:34',
            'swift_code' => 'nullable|string|max:11',
            'account_holder_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'branch_address' => 'nullable|string|max:500',
            'account_type' => 'required|in:savings,current,salary',
            'currency_code' => 'required|string|size:3',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['is_primary'] = $request->has('is_primary');
        $data['is_active'] = $request->has('is_active');

        $bankAccount->update($data);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'تم تحديث الحساب البنكي بنجاح.');
    }

    public function destroy(string $id)
    {
        $bankAccount = EmployeeBankAccount::findOrFail($id);
        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'تم حذف الحساب البنكي بنجاح.');
    }
}
