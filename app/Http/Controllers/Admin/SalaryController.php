<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Payroll;
use App\Models\Salary;
use App\Services\SalaryLedgerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

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
     * @return array{0: array, 1: float}
     */
    protected function prepareLedgerAndDeductions(Request $request, SalaryLedgerService $ledgerService): array
    {
        $lines = $ledgerService->normalizeInput($request->input('ledger_lines'));
        $sumDed = $ledgerService->sumDeductionSide($lines);
        if ($sumDed > 0) {
            $deductions = $sumDed;
        } else {
            $deductions = round((float) ($request->input('deductions', 0)), 2);
        }

        return [$lines, $deductions];
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

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html_rows' => view('admin.pages.salaries._index_rows', compact('salaries'))->render(),
                'html_pagination' => view('admin.pages.salaries._index_pagination', compact('salaries'))->render(),
                'total' => $salaries->total(),
            ]);
        }

        return view('admin.pages.salaries.index', compact('salaries', 'employees', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->with('user')->get();
        $currencies = Currency::where('is_active', true)->get();
        $baseCurrency = Currency::where('is_base_currency', true)->first();
        $activeAdvances = EmployeeAdvance::active()->with('employee')->orderBy('employee_id')->orderByDesc('id')->get();

        return view('admin.pages.salaries.create', compact('employees', 'currencies', 'baseCurrency', 'activeAdvances'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ledgerService = app(SalaryLedgerService::class);
        [$lines, $deductions] = $this->prepareLedgerAndDeductions($request, $ledgerService);

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
            'ledger_lines' => 'nullable|array',
            'ledger_lines.*.line_type' => 'nullable|string|max:32',
            'ledger_lines.*.label_ar' => 'nullable|string|max:255',
            'ledger_lines.*.amount' => 'nullable|numeric|min:0',
            'ledger_lines.*.employee_advance_id' => 'nullable|exists:employee_advances,id',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'salary_month.required' => 'الشهر مطلوب',
            'salary_year.required' => 'السنة مطلوبة',
            'base_salary.required' => 'الراتب الأساسي مطلوب',
            'payment_status.required' => 'حالة الدفع مطلوبة',
        ]);

        $existingSalary = Salary::where('employee_id', $request->employee_id)
            ->where('salary_month', $request->salary_month)
            ->where('salary_year', $request->salary_year)
            ->first();

        if ($existingSalary) {
            return back()->withInput()->withErrors(['error' => 'يوجد راتب مسجل بالفعل لهذا الموظف في نفس الشهر والسنة']);
        }

        $totalSalary = $request->base_salary
            + ($request->allowances ?? 0)
            + ($request->bonuses ?? 0)
            + ($request->overtime ?? 0)
            - $deductions;

        try {
            DB::transaction(function () use ($request, $lines, $deductions, $totalSalary, $ledgerService) {
                $salary = Salary::create([
                    'employee_id' => $request->employee_id,
                    'salary_month' => $request->salary_month,
                    'salary_year' => $request->salary_year,
                    'base_salary' => $request->base_salary,
                    'allowances' => $request->allowances ?? 0,
                    'bonuses' => $request->bonuses ?? 0,
                    'deductions' => $deductions,
                    'overtime' => $request->overtime ?? 0,
                    'total_salary' => $totalSalary,
                    'currency_id' => $request->currency_id,
                    'payment_date' => $request->payment_date,
                    'payment_status' => $request->payment_status,
                    'notes' => $request->notes,
                    'created_by' => auth()->id(),
                ]);
                $ledgerService->syncAfterCreate($salary, $lines, (int) $request->employee_id);
            });
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['ledger' => $e->getMessage()]);
        }

        return redirect()->route('admin.salaries.index')->with('success', 'تم إضافة الراتب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $salary = Salary::with([
            'employee.user',
            'currency',
            'creator',
            'ledgerLines.employeeAdvance',
        ])->findOrFail($id);

        $employeeSalaries = Salary::where('employee_id', $salary->employee_id)
            ->orderByDesc('salary_year')
            ->orderByDesc('salary_month')
            ->get();

        $employeePayrolls = Payroll::where('employee_id', $salary->employee_id)
            ->orderByDesc('payroll_year')
            ->orderByDesc('payroll_month')
            ->get();

        $timeline = collect();
        foreach ($employeeSalaries as $s) {
            $timeline->push([
                'source' => 'salary',
                'sort_key' => $s->salary_year * 100 + $s->salary_month,
                'period_label' => $s->month_name.' '.$s->salary_year,
                'total' => $s->total_salary,
                'status' => $s->payment_status_ar,
                'status_raw' => $s->payment_status,
                'url' => route('admin.salaries.show', $s->id),
                'is_current' => (int) $s->id === (int) $salary->id,
            ]);
        }
        foreach ($employeePayrolls as $p) {
            $timeline->push([
                'source' => 'payroll',
                'sort_key' => $p->payroll_year * 100 + $p->payroll_month,
                'period_label' => $p->month_name.' '.$p->payroll_year,
                'total' => $p->net_salary,
                'status' => $p->status_name_ar,
                'status_raw' => $p->status,
                'url' => route('admin.payrolls.show', $p->id),
                'is_current' => false,
            ]);
        }
        $financialTimeline = $timeline->sortByDesc('sort_key')->values();

        return view('admin.pages.salaries.show', compact('salary', 'financialTimeline'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $salary = Salary::with('ledgerLines')->findOrFail($id);
        $employees = Employee::where('is_active', true)->with('user')->get();
        $currencies = Currency::where('is_active', true)->get();

        $advanceIds = $salary->ledgerLines->pluck('employee_advance_id')->filter()->unique()->all();
        $activeAdvances = EmployeeAdvance::with('employee')
            ->where(function ($q) use ($salary, $advanceIds) {
                $q->where(function ($q2) use ($salary) {
                    $q2->where('status', 'active')->where('employee_id', $salary->employee_id);
                });
                if ($advanceIds !== []) {
                    $q->orWhereIn('id', $advanceIds);
                }
            })
            ->orderBy('employee_id')
            ->orderByDesc('id')
            ->get();

        return view('admin.pages.salaries.edit', compact('salary', 'employees', 'currencies', 'activeAdvances'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $salary = Salary::with('ledgerLines')->findOrFail($id);
        $ledgerService = app(SalaryLedgerService::class);
        [$lines, $deductions] = $this->prepareLedgerAndDeductions($request, $ledgerService);

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
            'ledger_lines' => 'nullable|array',
            'ledger_lines.*.line_type' => 'nullable|string|max:32',
            'ledger_lines.*.label_ar' => 'nullable|string|max:255',
            'ledger_lines.*.amount' => 'nullable|numeric|min:0',
            'ledger_lines.*.employee_advance_id' => 'nullable|exists:employee_advances,id',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'salary_month.required' => 'الشهر مطلوب',
            'salary_year.required' => 'السنة مطلوبة',
            'base_salary.required' => 'الراتب الأساسي مطلوب',
            'payment_status.required' => 'حالة الدفع مطلوبة',
        ]);

        $existingSalary = Salary::where('employee_id', $request->employee_id)
            ->where('salary_month', $request->salary_month)
            ->where('salary_year', $request->salary_year)
            ->where('id', '!=', $id)
            ->first();

        if ($existingSalary) {
            return back()->withInput()->withErrors(['error' => 'يوجد راتب مسجل بالفعل لهذا الموظف في نفس الشهر والسنة']);
        }

        $totalSalary = $request->base_salary
            + ($request->allowances ?? 0)
            + ($request->bonuses ?? 0)
            + ($request->overtime ?? 0)
            - $deductions;

        try {
            DB::transaction(function () use ($request, $salary, $lines, $deductions, $totalSalary, $ledgerService) {
                $ledgerService->revertAdvanceRecoveries($salary);
                $ledgerService->deleteLines($salary);
                $salary->update([
                    'employee_id' => $request->employee_id,
                    'salary_month' => $request->salary_month,
                    'salary_year' => $request->salary_year,
                    'base_salary' => $request->base_salary,
                    'allowances' => $request->allowances ?? 0,
                    'bonuses' => $request->bonuses ?? 0,
                    'deductions' => $deductions,
                    'overtime' => $request->overtime ?? 0,
                    'total_salary' => $totalSalary,
                    'currency_id' => $request->currency_id,
                    'payment_date' => $request->payment_date,
                    'payment_status' => $request->payment_status,
                    'notes' => $request->notes,
                ]);
                if ($lines !== []) {
                    $ledgerService->validateLinesForEmployee($lines, (int) $request->employee_id);
                    $ledgerService->persistLines($salary, $lines);
                }
            });
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['ledger' => $e->getMessage()]);
        }

        return redirect()->route('admin.salaries.index')->with('success', 'تم تحديث بيانات الراتب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $salary = Salary::findOrFail($request->id);
        $salary->delete();

        return redirect()->route('admin.salaries.index')->with('success', 'تم حذف الراتب بنجاح');
    }
}
