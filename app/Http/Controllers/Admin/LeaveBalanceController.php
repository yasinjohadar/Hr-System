<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveBalance;
use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:leave-balance-list')->only('index');
        $this->middleware('permission:leave-balance-create')->only(['create', 'store']);
        $this->middleware('permission:leave-balance-edit')->only(['edit', 'update']);
        $this->middleware('permission:leave-balance-delete')->only('destroy');
        $this->middleware('permission:leave-balance-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaveBalancesQuery = LeaveBalance::with(['employee.user', 'leaveType']);

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $leaveBalancesQuery->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب نوع الإجازة
        if ($request->filled('leave_type_id')) {
            $leaveBalancesQuery->where('leave_type_id', $request->input('leave_type_id'));
        }

        // فلترة حسب السنة
        if ($request->filled('year')) {
            $leaveBalancesQuery->where('year', $request->input('year'));
        }

        $leaveBalances = $leaveBalancesQuery->orderBy('year', 'desc')
            ->orderBy('employee_id')
            ->paginate(20);

        $employees = Employee::where('is_active', true)->with('user')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $years = LeaveBalance::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $currentYear = $request->input('year', date('Y'));

        return view("admin.pages.leave-balances.index", compact("leaveBalances", "employees", "leaveTypes", "years", "currentYear"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->with('user')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        return view("admin.pages.leave-balances.create", compact("employees", "leaveTypes"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'year' => 'required|integer|min:2020|max:2100',
            'total_days' => 'required|integer|min:0',
            'carried_forward' => 'nullable|integer|min:0',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'leave_type_id.required' => 'نوع الإجازة مطلوب',
            'year.required' => 'السنة مطلوبة',
            'total_days.required' => 'إجمالي الأيام مطلوب',
        ]);

        // التحقق من عدم وجود رصيد لنفس الموظف ونوع الإجازة في نفس السنة
        $existingBalance = LeaveBalance::where('employee_id', $request->employee_id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('year', $request->year)
            ->first();

        if ($existingBalance) {
            return back()->withInput()->withErrors(['error' => 'يوجد رصيد مسجل بالفعل لهذا الموظف ونوع الإجازة في نفس السنة']);
        }

        $remainingDays = $request->total_days + ($request->carried_forward ?? 0) - ($request->used_days ?? 0);

        LeaveBalance::create([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'year' => $request->year,
            'total_days' => $request->total_days,
            'used_days' => $request->used_days ?? 0,
            'remaining_days' => $remainingDays,
            'carried_forward' => $request->carried_forward ?? 0,
        ]);

        return redirect()->route("admin.leave-balances.index")->with("success", "تم إضافة رصيد الإجازة بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $leaveBalance = LeaveBalance::with(['employee.user', 'leaveType'])->findOrFail($id);
        return view("admin.pages.leave-balances.show", compact("leaveBalance"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $leaveBalance = LeaveBalance::findOrFail($id);
        $employees = Employee::where('is_active', true)->with('user')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        return view("admin.pages.leave-balances.edit", compact("leaveBalance", "employees", "leaveTypes"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $leaveBalance = LeaveBalance::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'year' => 'required|integer|min:2020|max:2100',
            'total_days' => 'required|integer|min:0',
            'used_days' => 'nullable|integer|min:0',
            'carried_forward' => 'nullable|integer|min:0',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'leave_type_id.required' => 'نوع الإجازة مطلوب',
            'year.required' => 'السنة مطلوبة',
            'total_days.required' => 'إجمالي الأيام مطلوب',
        ]);

        // التحقق من عدم وجود رصيد آخر لنفس الموظف ونوع الإجازة في نفس السنة (عدا السجل الحالي)
        $existingBalance = LeaveBalance::where('employee_id', $request->employee_id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('year', $request->year)
            ->where('id', '!=', $id)
            ->first();

        if ($existingBalance) {
            return back()->withInput()->withErrors(['error' => 'يوجد رصيد مسجل بالفعل لهذا الموظف ونوع الإجازة في نفس السنة']);
        }

        $remainingDays = $request->total_days + ($request->carried_forward ?? 0) - ($request->used_days ?? 0);

        $leaveBalance->update([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'year' => $request->year,
            'total_days' => $request->total_days,
            'used_days' => $request->used_days ?? 0,
            'remaining_days' => $remainingDays,
            'carried_forward' => $request->carried_forward ?? 0,
        ]);

        return redirect()->route('admin.leave-balances.index')->with('success', 'تم تحديث رصيد الإجازة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $leaveBalance = LeaveBalance::findOrFail($request->id);
        $leaveBalance->delete();

        return redirect()->route("admin.leave-balances.index")->with("success", "تم حذف رصيد الإجازة بنجاح");
    }
}
