<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftAssignment;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:shift-assignment-list')->only('index');
        $this->middleware('permission:shift-assignment-create')->only(['create', 'store']);
        $this->middleware('permission:shift-assignment-edit')->only(['edit', 'update']);
        $this->middleware('permission:shift-assignment-delete')->only('destroy');
        $this->middleware('permission:shift-assignment-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = ShiftAssignment::with(['employee', 'shift', 'assignedBy']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function($q) use ($search) {
                    $q->where('full_name', 'like', "%$search%");
                })
                ->orWhereHas('shift', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->input('shift_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $assignments = $query->latest()->paginate(20);
        $employees = Employee::where('is_active', true)->get();
        $shifts = Shift::where('is_active', true)->get();

        return view('admin.pages.shift-assignments.index', compact('assignments', 'employees', 'shifts'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $shifts = Shift::where('is_active', true)->get();

        return view('admin.pages.shift-assignments.create', compact('employees', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // التحقق من وجود تعيين نشط متداخل
        $overlapping = ShiftAssignment::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->where(function($q) use ($request) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $request->start_date);
            })
            ->where('start_date', '<=', ($request->end_date ?? '2099-12-31'))
            ->first();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'يوجد تعيين مناوبة نشط متداخل لهذا الموظف.');
        }

        $data = $request->all();
        $data['assigned_by'] = auth()->id();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        ShiftAssignment::create($data);

        return redirect()->route('admin.shift-assignments.index')
            ->with('success', 'تم تعيين المناوبة بنجاح.');
    }

    public function show(string $id)
    {
        $assignment = ShiftAssignment::with(['employee', 'shift', 'assignedBy'])->findOrFail($id);
        return view('admin.pages.shift-assignments.show', compact('assignment'));
    }

    public function edit(string $id)
    {
        $assignment = ShiftAssignment::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        $shifts = Shift::where('is_active', true)->get();

        return view('admin.pages.shift-assignments.edit', compact('assignment', 'employees', 'shifts'));
    }

    public function update(Request $request, string $id)
    {
        $assignment = ShiftAssignment::findOrFail($id);

        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        // التحقق من التداخل (إذا تم تغيير التواريخ)
        if ($request->start_date != $assignment->start_date || 
            $request->end_date != $assignment->end_date) {
            
            $overlapping = ShiftAssignment::where('employee_id', $assignment->employee_id)
                ->where('id', '!=', $id)
                ->where('is_active', true)
                ->where(function($q) use ($request) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $request->start_date);
                })
                ->where('start_date', '<=', ($request->end_date ?? '2099-12-31'))
                ->first();

            if ($overlapping) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'يوجد تعيين مناوبة نشط متداخل.');
            }
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $assignment->update($data);

        return redirect()->route('admin.shift-assignments.index')
            ->with('success', 'تم تحديث تعيين المناوبة بنجاح.');
    }

    public function destroy(string $id)
    {
        $assignment = ShiftAssignment::findOrFail($id);
        $assignment->delete();

        return redirect()->route('admin.shift-assignments.index')
            ->with('success', 'تم حذف تعيين المناوبة بنجاح.');
    }
}
