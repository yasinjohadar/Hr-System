<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Employee;
use App\Models\SalaryComponent;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRecord;
use App\Models\Currency;
use App\Models\TaxSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:payroll-list')->only('index');
        $this->middleware('permission:payroll-create')->only(['create', 'store', 'calculate']);
        $this->middleware('permission:payroll-edit')->only(['edit', 'update', 'approve']);
        $this->middleware('permission:payroll-delete')->only('destroy');
        $this->middleware('permission:payroll-show')->only(['show', 'payslip', 'payslipPdf']);
        $this->middleware('permission:payroll-list')->only(['exportBankFile']);
    }

    public function index(Request $request)
    {
        $query = Payroll::with(['employee', 'currency', 'approvedBy']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('payroll_code', 'like', "%$search%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('full_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('month')) {
            $query->where('payroll_month', $request->input('month'));
        }

        if ($request->filled('year')) {
            $query->where('payroll_year', $request->input('year'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        $payrolls = $query->latest('payroll_year', 'desc')
            ->latest('payroll_month', 'desc')
            ->paginate(20);

        $employees = Employee::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.payrolls.index', compact('payrolls', 'employees', 'currencies'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $components = SalaryComponent::where('is_active', true)->get();

        return view('admin.pages.payrolls.create', compact('employees', 'currencies', 'components'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_month' => 'required|integer|between:1,12',
            'payroll_year' => 'required|integer|min:2020|max:2100',
        ]);

        // التحقق من عدم وجود كشف راتب مسبق
        $existing = Payroll::where('employee_id', $request->employee_id)
            ->where('payroll_month', $request->payroll_month)
            ->where('payroll_year', $request->payroll_year)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'يوجد كشف راتب مسبق لهذا الموظف في نفس الشهر والسنة.');
        }

        $payroll = new Payroll();
        $payroll->employee_id = $request->employee_id;
        $payroll->payroll_month = $request->payroll_month;
        $payroll->payroll_year = $request->payroll_year;
        $payroll->currency_id = $request->currency_id ?? Employee::find($request->employee_id)->currency_id;
        $payroll->status = 'draft';
        $payroll->created_by = auth()->id();

        // حساب فترة الراتب
        $startDate = Carbon::create($request->payroll_year, $request->payroll_month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $payroll->period_start = $startDate;
        $payroll->period_end = $endDate;

        $payroll->save();

        return redirect()->route('admin.payrolls.calculate', $payroll->id)
            ->with('success', 'تم إنشاء كشف الراتب. يمكنك الآن حساب الراتب تلقائياً.');
    }

    public function show(string $id)
    {
        $payroll = Payroll::with([
            'employee',
            'currency',
            'items',
            'overtimeRecords',
            'approvedBy',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.payrolls.show', compact('payroll'));
    }

    public function edit(string $id)
    {
        $payroll = Payroll::with(['items', 'employee'])->findOrFail($id);
        
        if ($payroll->status === 'paid') {
            return redirect()->route('admin.payrolls.show', $id)
                ->with('error', 'لا يمكن تعديل كشف راتب مدفوع.');
        }

        $components = SalaryComponent::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.payrolls.edit', compact('payroll', 'components', 'currencies'));
    }

    public function update(Request $request, string $id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status === 'paid') {
            return redirect()->back()->with('error', 'لا يمكن تعديل كشف راتب مدفوع.');
        }

        $request->validate([
            'base_salary' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($request->filled('base_salary')) {
            $payroll->base_salary = $request->base_salary;
        }

        $payroll->notes = $request->notes;
        $payroll->save();

        // إعادة حساب الإجماليات
        $payroll->calculateTotals();
        $payroll->save();

        return redirect()->route('admin.payrolls.show', $id)
            ->with('success', 'تم تحديث كشف الراتب بنجاح.');
    }

    public function destroy(string $id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status === 'paid') {
            return redirect()->back()->with('error', 'لا يمكن حذف كشف راتب مدفوع.');
        }

        $payroll->delete();

        return redirect()->route('admin.payrolls.index')
            ->with('success', 'تم حذف كشف الراتب بنجاح.');
    }

    /**
     * حساب الراتب تلقائياً
     */
    public function calculate(string $id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        
        if ($payroll->status === 'paid') {
            return redirect()->back()->with('error', 'لا يمكن إعادة حساب كشف راتب مدفوع.');
        }

        $employee = $payroll->employee;
        $startDate = $payroll->period_start;
        $endDate = $payroll->period_end;

        // 1. الراتب الأساسي
        $baseSalary = $employee->salary ?? 0;
        $payroll->base_salary = $baseSalary;

        // 2. حساب أيام العمل والحضور
        $workingDays = $this->calculateWorkingDays($startDate, $endDate, $employee);
        $attendanceData = $this->calculateAttendance($employee, $startDate, $endDate);
        
        $payroll->working_days = $workingDays;
        $payroll->present_days = $attendanceData['present_days'];
        $payroll->absent_days = $attendanceData['absent_days'];
        $payroll->late_days = $attendanceData['late_days'];
        $payroll->late_deduction = $attendanceData['late_deduction'];

        // 3. حساب الإجازات
        $leaveData = $this->calculateLeaves($employee, $startDate, $endDate);
        $payroll->leave_days = $leaveData['leave_days'];
        $payroll->leave_deduction = $leaveData['leave_deduction'];

        // 4. حساب الساعات الإضافية
        $overtimeData = $this->calculateOvertime($employee, $startDate, $endDate, $payroll);
        $payroll->overtime_hours = $overtimeData['overtime_hours'];
        $payroll->overtime_amount = $overtimeData['overtime_amount'];

        // 5. حساب البدلات والخصومات من المكونات
        $componentsData = $this->calculateComponents($employee, $baseSalary, $payroll);
        $payroll->total_allowances = $componentsData['allowances'];
        $payroll->total_deductions = $componentsData['deductions'];
        $payroll->bonuses = $componentsData['bonuses'];

        // 6. حفظ بنود الراتب
        $this->savePayrollItems($payroll, $componentsData['items']);

        // 7. حساب الضرائب والاستقطاعات
        $taxData = $this->calculateTaxes($employee, $payroll);
        $payroll->income_tax = $taxData['income_tax'];
        $payroll->social_insurance_employee = $taxData['social_insurance_employee'];
        $payroll->social_insurance_employer = $taxData['social_insurance_employer'];
        $payroll->health_insurance_employee = $taxData['health_insurance_employee'];
        $payroll->health_insurance_employer = $taxData['health_insurance_employer'];
        $payroll->other_taxes = $taxData['other_taxes'];

        // 8. حساب الإجماليات
        $payroll->calculateTotals();
        $payroll->status = 'calculated';
        $payroll->save();

        return redirect()->route('admin.payrolls.show', $id)
            ->with('success', 'تم حساب الراتب تلقائياً بنجاح.');
    }

    /**
     * حساب أيام العمل
     */
    private function calculateWorkingDays(Carbon $start, Carbon $end, Employee $employee): int
    {
        $days = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            // تخطي عطلات نهاية الأسبوع (يمكن تخصيصها)
            if ($current->dayOfWeek !== Carbon::FRIDAY && $current->dayOfWeek !== Carbon::SATURDAY) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    /**
     * حساب الحضور
     */
    private function calculateAttendance(Employee $employee, Carbon $start, Carbon $end): array
    {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$start, $end])
            ->get();

        $presentDays = 0;
        $absentDays = 0;
        $lateDays = 0;
        $lateDeduction = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->status === 'present') {
                $presentDays++;
                if ($attendance->late_minutes > 0) {
                    $lateDays++;
                    // حساب خصم التأخير (مثال: 1% لكل 15 دقيقة تأخير)
                    $lateDeduction += ($attendance->late_minutes / 15) * (($employee->salary ?? 0) * 0.01);
                }
            } elseif ($attendance->status === 'absent') {
                $absentDays++;
            }
        }

        return [
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'late_deduction' => $lateDeduction,
        ];
    }

    /**
     * حساب الإجازات
     */
    private function calculateLeaves(Employee $employee, Carbon $start, Carbon $end): array
    {
        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start)
                         ->where('end_date', '>=', $end);
                  });
            })
            ->get();

        $leaveDays = 0;
        $leaveDeduction = 0;

        foreach ($leaves as $leave) {
            $leaveStart = Carbon::parse(max($leave->start_date->format('Y-m-d'), $start->format('Y-m-d')));
            $leaveEndDate = $leave->end_date ?? $leave->start_date;
            $leaveEnd = Carbon::parse(min($leaveEndDate->format('Y-m-d'), $end->format('Y-m-d')));
            $days = $leaveStart->diffInDays($leaveEnd) + 1;
            $leaveDays += $days;

            // خصم الإجازات غير مدفوعة
            if ($leave->leaveType && !$leave->leaveType->is_paid) {
                $dailySalary = ($employee->salary ?? 0) / 30; // متوسط الراتب اليومي
                $leaveDeduction += $dailySalary * $days;
            }
        }

        return [
            'leave_days' => $leaveDays,
            'leave_deduction' => $leaveDeduction,
        ];
    }

    /**
     * حساب الساعات الإضافية
     */
    private function calculateOvertime(Employee $employee, Carbon $start, Carbon $end, Payroll $payroll): array
    {
        $overtimeRecords = OvertimeRecord::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereBetween('overtime_date', [$start, $end])
            ->whereNull('payroll_id')
            ->get();

        $totalHours = 0;
        $totalAmount = 0;

        foreach ($overtimeRecords as $record) {
            $totalHours += $record->overtime_hours;
            $totalAmount += $record->overtime_amount;
            
            // ربط الساعات الإضافية بكشف الراتب
            $record->payroll_id = $payroll->id;
            $record->save();
        }

        return [
            'overtime_hours' => $totalHours,
            'overtime_amount' => $totalAmount,
        ];
    }

    /**
     * حساب المكونات (بدلات وخصومات)
     */
    private function calculateComponents(Employee $employee, float $baseSalary, Payroll $payroll): array
    {
        $components = SalaryComponent::where('is_active', true)
            ->where(function($q) use ($employee) {
                $q->where('apply_to_all', true)
                  ->orWhereJsonContains('applicable_positions', $employee->position_id)
                  ->orWhereJsonContains('applicable_departments', $employee->department_id);
            })
            ->get();

        $allowances = 0;
        $deductions = 0;
        $bonuses = 0;
        $items = [];

        foreach ($components as $component) {
            $amount = $this->calculateComponentAmount($component, $baseSalary, $employee, $payroll);
            
            if ($amount > 0) {
                $items[] = [
                    'component' => $component,
                    'amount' => $amount,
                ];

                match($component->type) {
                    'allowance' => $allowances += $amount,
                    'deduction' => $deductions += $amount,
                    'bonus' => $bonuses += $amount,
                    default => null,
                };
            }
        }

        return [
            'allowances' => $allowances,
            'deductions' => $deductions,
            'bonuses' => $bonuses,
            'items' => $items,
        ];
    }

    /**
     * حساب قيمة مكون معين
     */
    private function calculateComponentAmount(SalaryComponent $component, float $baseSalary, Employee $employee, Payroll $payroll): float
    {
        return match($component->calculation_type) {
            'fixed' => $component->default_value,
            'percentage' => ($baseSalary * $component->percentage) / 100,
            'formula' => $this->evaluateFormula($component->formula, $baseSalary, $employee, $payroll),
            'attendance_based' => $this->calculateAttendanceBased($component, $employee, $payroll),
            'leave_based' => $this->calculateLeaveBased($component, $employee, $payroll),
            default => 0,
        };
    }

    /**
     * تقييم الصيغة
     */
    private function evaluateFormula(string $formula, float $baseSalary, Employee $employee, Payroll $payroll): float
    {
        // استبدال المتغيرات في الصيغة
        $formula = str_replace('{base_salary}', $baseSalary, $formula);
        $formula = str_replace('{present_days}', $payroll->present_days, $formula);
        $formula = str_replace('{working_days}', $payroll->working_days, $formula);
        
        // تقييم الصيغة (يجب التأكد من الأمان)
        try {
            $result = @eval("return $formula;");
            return is_numeric($result) ? (float)$result : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * حساب بناءً على الحضور
     */
    private function calculateAttendanceBased(SalaryComponent $component, Employee $employee, Payroll $payroll): float
    {
        // مثال: بدل حضور = عدد أيام الحضور * قيمة ثابتة
        return $payroll->present_days * $component->default_value;
    }

    /**
     * حساب بناءً على الإجازات
     */
    private function calculateLeaveBased(SalaryComponent $component, Employee $employee, Payroll $payroll): float
    {
        // مثال: خصم إجازة = عدد أيام الإجازة * قيمة ثابتة
        return $payroll->leave_days * $component->default_value;
    }

    /**
     * حساب الضرائب والاستقطاعات
     */
    private function calculateTaxes(Employee $employee, Payroll $payroll): array
    {
        $grossSalary = $payroll->base_salary 
            + $payroll->total_allowances 
            + $payroll->bonuses 
            + $payroll->overtime_amount;

        $taxSettings = TaxSetting::where('is_active', true)->get();
        
        $incomeTax = 0;
        $socialInsuranceEmployee = 0;
        $socialInsuranceEmployer = 0;
        $healthInsuranceEmployee = 0;
        $healthInsuranceEmployer = 0;
        $otherTaxes = 0;

        foreach ($taxSettings as $tax) {
            $taxAmount = $tax->calculateTax($grossSalary);
            
            match($tax->type) {
                'income_tax' => $incomeTax += $taxAmount,
                'social_insurance' => $socialInsuranceEmployee += $taxAmount,
                'health_insurance' => $healthInsuranceEmployee += $taxAmount,
                'other' => $otherTaxes += $taxAmount,
                default => null,
            };
        }

        // حساب استقطاعات صاحب العمل (عادة نسبة من الراتب)
        // يمكن تخصيصها حسب القوانين المحلية
        $socialInsuranceEmployer = $grossSalary * 0.12; // مثال: 12%
        $healthInsuranceEmployer = $grossSalary * 0.02; // مثال: 2%

        return [
            'income_tax' => $incomeTax,
            'social_insurance_employee' => $socialInsuranceEmployee,
            'social_insurance_employer' => $socialInsuranceEmployer,
            'health_insurance_employee' => $healthInsuranceEmployee,
            'health_insurance_employer' => $healthInsuranceEmployer,
            'other_taxes' => $otherTaxes,
        ];
    }

    /**
     * حفظ بنود الراتب
     */
    private function savePayrollItems(Payroll $payroll, array $items): void
    {
        // حذف البنود القديمة
        PayrollItem::where('payroll_id', $payroll->id)->delete();

        $sortOrder = 1;

        foreach ($items as $item) {
            $payrollItem = new PayrollItem();
            $payrollItem->payroll_id = $payroll->id;
            $payrollItem->item_type = $item['component']->type;
            $payrollItem->item_name = $item['component']->name;
            $payrollItem->item_name_ar = $item['component']->name_ar;
            $payrollItem->component_code = $item['component']->code;
            $payrollItem->calculation_type = $item['component']->calculation_type;
            $payrollItem->amount = $item['amount'];
            $payrollItem->sort_order = $sortOrder++;
            $payrollItem->save();
        }
    }

    /**
     * الموافقة على كشف الراتب
     */
    public function approve(Request $request, string $id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'calculated') {
            return redirect()->back()->with('error', 'يجب حساب الراتب أولاً قبل الموافقة.');
        }

        $payroll->status = 'approved';
        $payroll->approved_by = auth()->id();
        $payroll->approved_at = now();
        $payroll->save();

        return redirect()->back()->with('success', 'تم الموافقة على كشف الراتب بنجاح.');
    }

    /**
     * طباعة كشف الراتب (PDF)
     */
    public function payslip(string $id)
    {
        $payroll = Payroll::with([
            'employee',
            'currency',
            'items',
            'overtimeRecords',
            'approvedBy'
        ])->findOrFail($id);

        return view('admin.pages.payrolls.payslip', compact('payroll'));
    }

    /**
     * تحميل قسيمة الراتب كـ PDF
     */
    public function payslipPdf(string $id)
    {
        $payroll = Payroll::with([
            'employee',
            'currency',
            'items',
            'overtimeRecords',
            'approvedBy'
        ])->findOrFail($id);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.pages.payrolls.payslip', compact('payroll'));
        $filename = 'payslip-' . $payroll->payroll_code . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * عرض صفحة تصدير ملف الرواتب للبنك أو تحميل الملف
     */
    public function exportBankFile(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if ($month && $year) {
            return $this->downloadBankFile((int) $month, (int) $year);
        }

        return view('admin.pages.payrolls.export-bank', []);
    }

    /**
     * توليد وتحميل ملف CSV للبنك لشهر/سنة معينين
     */
    protected function downloadBankFile(int $month, int $year)
    {
        $payrolls = Payroll::with(['employee.primaryBankAccount', 'employee.bankAccounts', 'currency'])
            ->whereIn('status', ['approved', 'paid'])
            ->where('payroll_month', $month)
            ->where('payroll_year', $year)
            ->orderBy('employee_id')
            ->get();

        $filename = 'payroll-bank-' . $year . '-' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($payrolls) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($out, ['beneficiary_name', 'iban', 'account_number', 'amount', 'currency', 'reference']);

            foreach ($payrolls as $payroll) {
                $account = $payroll->employee->primaryBankAccount ?? $payroll->employee->bankAccounts->where('is_active', true)->first();
                $beneficiaryName = $account?->account_holder_name ?? $payroll->employee->full_name ?? 'N/A';
                $iban = $account?->iban ?? '';
                $accountNumber = $account?->account_number ?? '';
                $amount = number_format((float) $payroll->net_salary, 2, '.', '');
                $currency = $payroll->currency->code ?? 'SAR';
                $reference = $payroll->payroll_code ?? '';

                fputcsv($out, [$beneficiaryName, $iban, $accountNumber, $amount, $currency, $reference]);
            }

            fclose($out);
        }, $filename, $headers);
    }
}
