<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeBenefit;
use App\Models\BenefitType;
use App\Models\Employee;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeBenefitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-benefit-list')->only('index');
        $this->middleware('permission:employee-benefit-create')->only(['create', 'store']);
        $this->middleware('permission:employee-benefit-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-benefit-delete')->only('destroy');
        $this->middleware('permission:employee-benefit-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = EmployeeBenefit::with(['employee', 'benefitType', 'currency']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%");
            })->orWhereHas('benefitType', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('benefit_type_id')) {
            $query->where('benefit_type_id', $request->input('benefit_type_id'));
        }

        $employeeBenefits = $query->orderBy('start_date', 'desc')->paginate(20);
        $employees = Employee::where('is_active', true)->get();
        $benefitTypes = BenefitType::where('is_active', true)->get();

        return view('admin.pages.employee-benefits.index', compact('employeeBenefits', 'employees', 'benefitTypes'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $benefitTypes = BenefitType::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.pages.employee-benefits.create', compact('employees', 'benefitTypes', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'benefit_type_id' => 'required|exists:benefit_types,id',
            'value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,suspended,expired,cancelled',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        // رفع المستند
        if ($request->hasFile('document_path')) {
            $data['document_path'] = $request->file('document_path')->store('employee-benefits/documents', 'public');
        }

        EmployeeBenefit::create($data);

        return redirect()->route('admin.employee-benefits.index')->with('success', 'تم إضافة ميزة الموظف بنجاح');
    }

    public function show(string $id)
    {
        $employeeBenefit = EmployeeBenefit::with(['employee', 'benefitType', 'currency', 'approver', 'creator'])
            ->findOrFail($id);
        return view('admin.pages.employee-benefits.show', compact('employeeBenefit'));
    }

    public function edit(string $id)
    {
        $employeeBenefit = EmployeeBenefit::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        $benefitTypes = BenefitType::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.pages.employee-benefits.edit', compact('employeeBenefit', 'employees', 'benefitTypes', 'currencies'));
    }

    public function update(Request $request, string $id)
    {
        $employeeBenefit = EmployeeBenefit::findOrFail($id);

        $request->validate([
            'value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,suspended,expired,cancelled',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();

        // رفع المستند
        if ($request->hasFile('document_path')) {
            if ($employeeBenefit->document_path) {
                Storage::disk('public')->delete($employeeBenefit->document_path);
            }
            $data['document_path'] = $request->file('document_path')->store('employee-benefits/documents', 'public');
        }

        // إذا تمت الموافقة
        if ($request->has('approve') && $employeeBenefit->benefitType->requires_approval) {
            $data['approved_by'] = auth()->id();
            $data['approval_date'] = now();
        }

        $employeeBenefit->update($data);

        return redirect()->route('admin.employee-benefits.index')->with('success', 'تم تحديث ميزة الموظف بنجاح');
    }

    public function destroy(Request $request)
    {
        $employeeBenefit = EmployeeBenefit::findOrFail($request->id);
        
        // حذف المستند
        if ($employeeBenefit->document_path) {
            Storage::disk('public')->delete($employeeBenefit->document_path);
        }

        $employeeBenefit->delete();

        return redirect()->route('admin.employee-benefits.index')->with('success', 'تم حذف ميزة الموظف بنجاح');
    }
}
