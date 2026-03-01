<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeGoal;
use App\Models\Employee;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;

class EmployeeGoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-goal-list')->only(['index', 'show']);
        $this->middleware('permission:employee-goal-create')->only(['create', 'store']);
        $this->middleware('permission:employee-goal-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-goal-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeGoal::with(['employee', 'performanceReview']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $goals = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.employee-goals.index', compact('goals', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $reviews = PerformanceReview::latest()->get();
        return view('admin.pages.employee-goals.create', compact('employees', 'reviews'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:personal,team,department,company',
            'priority' => 'required|in:low,medium,high,critical',
            'start_date' => 'required|date',
            'target_date' => 'required|date|after:start_date',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'not_started';
        $data['progress_percentage'] = 0;

        EmployeeGoal::create($data);

        return redirect()->route('admin.employee-goals.index')->with('success', 'تم إضافة الهدف بنجاح');
    }

    public function show(string $id)
    {
        $goal = EmployeeGoal::with(['employee', 'performanceReview'])->findOrFail($id);
        return view('admin.pages.employee-goals.show', compact('goal'));
    }

    public function edit(string $id)
    {
        $goal = EmployeeGoal::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        $reviews = PerformanceReview::latest()->get();
        return view('admin.pages.employee-goals.edit', compact('goal', 'employees', 'reviews'));
    }

    public function update(Request $request, string $id)
    {
        $goal = EmployeeGoal::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:personal,team,department,company',
            'priority' => 'required|in:low,medium,high,critical',
            'start_date' => 'required|date',
            'target_date' => 'required|date|after:start_date',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        $data = $request->all();

        // تحديث الحالة حسب التقدم
        if ($data['progress_percentage'] >= 100) {
            $data['status'] = 'completed';
            $data['completion_date'] = now();
        } elseif ($data['progress_percentage'] > 0) {
            $data['status'] = 'in_progress';
        } else {
            $data['status'] = 'not_started';
        }

        $goal->update($data);

        return redirect()->route('admin.employee-goals.index')->with('success', 'تم تحديث الهدف بنجاح');
    }

    public function destroy(Request $request)
    {
        $goal = EmployeeGoal::findOrFail($request->id);
        $goal->delete();

        return redirect()->route('admin.employee-goals.index')->with('success', 'تم حذف الهدف بنجاح');
    }
}
