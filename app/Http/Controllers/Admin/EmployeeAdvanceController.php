<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use Illuminate\Http\Request;

class EmployeeAdvanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-advance-list')->only('index');
        $this->middleware('permission:employee-advance-create')->only(['create', 'store']);
        $this->middleware('permission:employee-advance-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-advance-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeAdvance::with(['employee'])->orderByDesc('granted_at')->orderByDesc('id');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        $advances = $query->paginate(20);
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();

        return view('admin.pages.employee-advances.index', compact('advances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->with('user')->get();

        return view('admin.pages.employee-advances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'principal_amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:2000',
            'granted_at' => 'nullable|date',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'principal_amount.required' => 'مبلغ السلفة مطلوب',
        ]);

        $principal = round((float) $request->principal_amount, 2);

        EmployeeAdvance::create([
            'employee_id' => $request->employee_id,
            'principal_amount' => $principal,
            'remaining_balance' => $principal,
            'description' => $request->description,
            'granted_at' => $request->granted_at,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.employee-advances.index')->with('success', 'تم تسجيل السلفة بنجاح');
    }

    public function edit(string $id)
    {
        $advance = EmployeeAdvance::with('employee')->findOrFail($id);

        return view('admin.pages.employee-advances.edit', compact('advance'));
    }

    public function update(Request $request, string $id)
    {
        $advance = EmployeeAdvance::findOrFail($id);

        $request->validate([
            'description' => 'nullable|string|max:2000',
            'granted_at' => 'nullable|date',
            'status' => 'required|in:active,closed',
        ]);

        if ($request->status === 'closed' && (float) $advance->remaining_balance > 0.009) {
            return back()->withInput()->withErrors(['status' => 'لا يمكن إغلاق السلفة قبل سداد الرصيد المتبقي بالكامل.']);
        }

        $advance->update([
            'description' => $request->description,
            'granted_at' => $request->granted_at,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.employee-advances.index')->with('success', 'تم تحديث السلفة');
    }

    public function destroy(EmployeeAdvance $employee_advance)
    {
        if ($employee_advance->ledgerLines()->exists()) {
            return redirect()->route('admin.employee-advances.index')->with('error', 'لا يمكن حذف سلفة مرتبطة بخصومات رواتب.');
        }

        $employee_advance->delete();

        return redirect()->route('admin.employee-advances.index')->with('success', 'تم حذف السلفة');
    }
}
