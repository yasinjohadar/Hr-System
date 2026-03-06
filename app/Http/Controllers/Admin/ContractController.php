<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Contract::with(['employee', 'creator'])->latest('start_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'expired') {
                $query->expired();
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('expiring')) {
            $days = (int) $request->input('expiring');
            if (in_array($days, [30, 60, 90], true)) {
                $query->expiringInDays($days);
            }
        }

        $contracts = $query->paginate(15);
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();

        return view('admin.pages.contracts.index', compact('contracts', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        return view('admin.pages.contracts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|in:fixed_term,permanent,trial,project',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,expired,renewed,terminated',
            'notes' => 'nullable|string',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->only(['employee_id', 'contract_type', 'start_date', 'end_date', 'status', 'notes']);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('document_path')) {
            $data['document_path'] = $request->file('document_path')->store('contracts', 'public');
        }

        $contract = Contract::create($data);

        if ($contract->status === 'active') {
            $this->syncEmployeeContractDates($contract->employee);
        }

        return redirect()->route('admin.contracts.index')
            ->with('success', 'تم إنشاء العقد بنجاح.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['employee', 'creator']);
        return view('admin.pages.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        return view('admin.pages.contracts.edit', compact('contract', 'employees'));
    }

    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|in:fixed_term,permanent,trial,project',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:active,expired,renewed,terminated',
            'notes' => 'nullable|string',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->only(['employee_id', 'contract_type', 'start_date', 'end_date', 'status', 'notes']);

        if ($request->hasFile('document_path')) {
            if ($contract->document_path) {
                Storage::disk('public')->delete($contract->document_path);
            }
            $data['document_path'] = $request->file('document_path')->store('contracts', 'public');
        }

        $contract->update($data);

        $this->syncEmployeeContractDates($contract->employee);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'تم تحديث العقد بنجاح.');
    }

    public function destroy(Contract $contract)
    {
        if ($contract->document_path) {
            Storage::disk('public')->delete($contract->document_path);
        }
        $employee = $contract->employee;
        $contract->delete();
        $this->syncEmployeeContractDates($employee);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'تم حذف العقد بنجاح.');
    }

    public function renew(Contract $contract)
    {
        $contract->load('employee');
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        return view('admin.pages.contracts.renew', compact('contract', 'employees'));
    }

    public function storeRenew(Request $request, Contract $contract)
    {
        $request->validate([
            'contract_type' => 'required|in:fixed_term,permanent,trial,project',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $contract->update(['status' => Contract::STATUS_RENEWED]);

        $data = [
            'employee_id' => $contract->employee_id,
            'contract_type' => $request->contract_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => Contract::STATUS_ACTIVE,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('document_path')) {
            $data['document_path'] = $request->file('document_path')->store('contracts', 'public');
        }

        $newContract = Contract::create($data);
        $this->syncEmployeeContractDates($contract->employee);

        return redirect()->route('admin.contracts.show', $newContract)
            ->with('success', 'تم تجديد العقد بنجاح.');
    }

    private function syncEmployeeContractDates(Employee $employee): void
    {
        $current = $employee->currentContract();
        $employee->update([
            'contract_start_date' => $current?->start_date,
            'contract_end_date' => $current?->end_date,
        ]);
    }
}
