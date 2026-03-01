<?php

namespace App\Http\Controllers\Admin;

use App\Models\Salary;
use App\Models\Employee;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:salary-list')->only('index');
        $this->middleware('permission:salary-create')->only(['create', 'store']);
        $this->middleware('permission:salary-edit')->only(['edit', 'update']);
        $this->middleware('permission:salary-delete')->only('destroy');
        $this->middleware('permission:salary-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $salariesQuery = Salary::with(['employee.user', 'currency', 'creator']);

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $salariesQuery->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب الشهر
        if ($request->filled('salary_month')) {
            $salariesQuery->where('salary_month', $request->input('salary_month'));
        }

        // فلترة حسب السنة
        if ($request->filled('salary_year')) {
            $salariesQuery->where('salary_year', $request->input('salary_year'));
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $salariesQuery->where('payment_status', $request->input('payment_status'));
        }

        $salaries = $salariesQuery->orderBy('salary_year', 'desc')
            ->orderBy('salary_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $employees = Employee::where('is_active', true)->with('user')->get();
        $years = Salary::select('salary_year')->distinct()->orderBy('salary_year', 'desc')->pluck('salary_year');
        $currentYear = $request->input('salary_year', date('Y'));
        $currentMonth = $request->input('salary_month', date('n'));

        return view("admin.pages.salaries.index", compact("salaries", "employees", "years", "currentYear", "currentMonth"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->with('user')->get();
        $currencies = Currency::where('is_active', true)->get();
        $baseCurrency = Currency::where('is_base_currency', true)->first();
        
        return view("admin.pages.salaries.create", compact("employees", "currencies", "baseCurrency"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|integer|between:1,12',
            'salary_year' => 'required|integer|min:2020|max:2100',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'overtime' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'payment_date' => 'nullable|date',
            'payment_status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'salary_month.required' => 'الشهر مطلوب',
            'salary_year.required' => 'السنة مطلوبة',
            'base_salary.required' => 'الراتب الأساسي مطلوب',
            'payment_status.required' => 'حالة الدفع مطلوبة',
        ]);

        // التحقق من عدم وجود راتب لنفس الموظف في نفس الشهر والسنة
        $existingSalary = Salary::where('employee_id', $request->employee_id)
            ->where('salary_month', $request->salary_month)
            ->where('salary_year', $request->salary_year)
            ->first();

        if ($existingSalary) {
            return back()->withInput()->withErrors(['error' => 'يوجد راتب مسجل بالفعل لهذا الموظف في نفس الشهر والسنة']);
        }

        // حساب الراتب الإجمالي
        $totalSalary = $request->base_salary 
            + ($request->allowances ?? 0) 
            + ($request->bonuses ?? 0) 
            + ($request->overtime ?? 0) 
            - ($request->deductions ?? 0);

        $salary = Salary::create([
            'employee_id' => $request->employee_id,
            'salary_month' => $request->salary_month,
            'salary_year' => $request->salary_year,
            'base_salary' => $request->base_salary,
            'allowances' => $request->allowances ?? 0,
            'bonuses' => $request->bonuses ?? 0,
            'deductions' => $request->deductions ?? 0,
            'overtime' => $request->overtime ?? 0,
            'total_salary' => $totalSalary,
            'currency_id' => $request->currency_id,
            'payment_date' => $request->payment_date,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route("admin.salaries.index")->with("success", "تم إضافة الراتب بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $salary = Salary::with(['employee.user', 'currency', 'creator'])->findOrFail($id);
        return view("admin.pages.salaries.show", compact("salary"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $salary = Salary::findOrFail($id);
        $employees = Employee::where('is_active', true)->with('user')->get();
        $currencies = Currency::where('is_active', true)->get();
        
        return view("admin.pages.salaries.edit", compact("salary", "employees", "currencies"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $salary = Salary::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|integer|between:1,12',
            'salary_year' => 'required|integer|min:2020|max:2100',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'overtime' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'payment_date' => 'nullable|date',
            'payment_status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'salary_month.required' => 'الشهر مطلوب',
            'salary_year.required' => 'السنة مطلوبة',
            'base_salary.required' => 'الراتب الأساسي مطلوب',
            'payment_status.required' => 'حالة الدفع مطلوبة',
        ]);

        // التحقق من عدم وجود راتب آخر لنفس الموظف في نفس الشهر والسنة (عدا السجل الحالي)
        $existingSalary = Salary::where('employee_id', $request->employee_id)
            ->where('salary_month', $request->salary_month)
            ->where('salary_year', $request->salary_year)
            ->where('id', '!=', $id)
            ->first();

        if ($existingSalary) {
            return back()->withInput()->withErrors(['error' => 'يوجد راتب مسجل بالفعل لهذا الموظف في نفس الشهر والسنة']);
        }

        // حساب الراتب الإجمالي
        $totalSalary = $request->base_salary 
            + ($request->allowances ?? 0) 
            + ($request->bonuses ?? 0) 
            + ($request->overtime ?? 0) 
            - ($request->deductions ?? 0);

        $salary->update([
            'employee_id' => $request->employee_id,
            'salary_month' => $request->salary_month,
            'salary_year' => $request->salary_year,
            'base_salary' => $request->base_salary,
            'allowances' => $request->allowances ?? 0,
            'bonuses' => $request->bonuses ?? 0,
            'deductions' => $request->deductions ?? 0,
            'overtime' => $request->overtime ?? 0,
            'total_salary' => $totalSalary,
            'currency_id' => $request->currency_id,
            'payment_date' => $request->payment_date,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.salaries.index')->with('success', 'تم تحديث بيانات الراتب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $salary = Salary::findOrFail($request->id);
        $salary->delete();

        return redirect()->route("admin.salaries.index")->with("success", "تم حذف الراتب بنجاح");
    }
}
