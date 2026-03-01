<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeExit;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeExitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-exit-list')->only(['index', 'show']);
        $this->middleware('permission:employee-exit-create')->only(['create', 'store']);
        $this->middleware('permission:employee-exit-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-exit-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeExit::with(['employee', 'handoverTo', 'approver']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('exit_type')) {
            $query->where('exit_type', $request->input('exit_type'));
        }

        $exits = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.employee-exits.index', compact('exits', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.employee-exits.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'resignation_date' => 'required|date',
            'last_working_day' => 'required|date|after:resignation_date',
            'exit_type' => 'required|in:resignation,termination,retirement,end_of_contract,other',
            'reason' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';

        EmployeeExit::create($data);

        return redirect()->route('admin.employee-exits.index')->with('success', 'تم إنشاء طلب إنهاء الخدمة بنجاح');
    }

    public function show(string $id)
    {
        $exit = EmployeeExit::with(['employee', 'handoverTo', 'approver'])->findOrFail($id);
        return view('admin.pages.employee-exits.show', compact('exit'));
    }

    public function edit(string $id)
    {
        $exit = EmployeeExit::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.employee-exits.edit', compact('exit', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $exit = EmployeeExit::findOrFail($id);

        $request->validate([
            'resignation_date' => 'required|date',
            'last_working_day' => 'required|date|after:resignation_date',
            'exit_type' => 'required|in:resignation,termination,retirement,end_of_contract,other',
            'status' => 'required|in:pending,in_process,completed,cancelled',
        ]);

        $data = $request->all();

        // إذا تم إكمال جميع الخطوات
        if (isset($data['exit_interview_completed']) && isset($data['assets_returned']) && 
            isset($data['handover_completed']) && isset($data['documents_returned']) && 
            isset($data['final_settlement_completed'])) {
            if ($data['exit_interview_completed'] && $data['assets_returned'] && 
                $data['handover_completed'] && $data['documents_returned'] && 
                $data['final_settlement_completed']) {
                $data['status'] = 'completed';
            }
        }

        $exit->update($data);

        return redirect()->route('admin.employee-exits.index')->with('success', 'تم تحديث طلب إنهاء الخدمة بنجاح');
    }

    public function destroy(Request $request)
    {
        $exit = EmployeeExit::findOrFail($request->id);
        $exit->delete();

        return redirect()->route('admin.employee-exits.index')->with('success', 'تم حذف طلب إنهاء الخدمة بنجاح');
    }

    public function completeExitInterview(Request $request, string $id)
    {
        $exit = EmployeeExit::findOrFail($id);

        $request->validate([
            'exit_interview_rating' => 'required|integer|min:1|max:5',
            'exit_interview_feedback' => 'nullable|string',
            'suggestions' => 'nullable|string',
        ]);

        $exit->update([
            'exit_interview_completed' => true,
            'exit_interview_rating' => $request->input('exit_interview_rating'),
            'exit_interview_feedback' => $request->input('exit_interview_feedback'),
            'suggestions' => $request->input('suggestions'),
        ]);

        return redirect()->back()->with('success', 'تم إكمال استبيان إنهاء الخدمة بنجاح');
    }

    public function approve(Request $request, string $id)
    {
        $exit = EmployeeExit::findOrFail($id);
        $exit->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'in_process',
        ]);

        return redirect()->back()->with('success', 'تم الموافقة على طلب إنهاء الخدمة');
    }
}
