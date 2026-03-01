<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessionPlan;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;

class SuccessionPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:succession-plan-list')->only('index');
        $this->middleware('permission:succession-plan-create')->only(['create', 'store']);
        $this->middleware('permission:succession-plan-edit')->only(['edit', 'update']);
        $this->middleware('permission:succession-plan-delete')->only('destroy');
        $this->middleware('permission:succession-plan-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = SuccessionPlan::with(['position', 'currentEmployee', 'creator'])->withCount('candidates');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('plan_code', 'like', "%$search%")
                  ->orWhereHas('position', function($q) use ($search) {
                      $q->where('title', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->input('urgency'));
        }

        $plans = $query->latest()->paginate(15);
        $positions = Position::where('is_active', true)->get();

        return view('admin.pages.succession-plans.index', compact('plans', 'positions'));
    }

    public function create()
    {
        $positions = Position::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.succession-plans.create', compact('positions', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_id' => 'required|exists:positions,id',
            'current_employee_id' => 'nullable|exists:employees,id',
            'plan_code' => 'nullable|string|max:50|unique:succession_plans,plan_code',
            'description' => 'nullable|string',
            'urgency' => 'required|in:low,medium,high,critical',
            'target_date' => 'nullable|date',
            'status' => 'required|in:planning,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        SuccessionPlan::create($data);

        return redirect()->route('admin.succession-plans.index')->with('success', 'تم إضافة خطة التعاقب بنجاح.');
    }

    public function show(string $id)
    {
        $plan = SuccessionPlan::with([
            'position',
            'currentEmployee',
            'candidates.employee',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.succession-plans.show', compact('plan'));
    }

    public function edit(string $id)
    {
        $plan = SuccessionPlan::findOrFail($id);
        $positions = Position::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.succession-plans.edit', compact('plan', 'positions', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $plan = SuccessionPlan::findOrFail($id);

        $request->validate([
            'position_id' => 'required|exists:positions,id',
            'current_employee_id' => 'nullable|exists:employees,id',
            'plan_code' => 'nullable|string|max:50|unique:succession_plans,plan_code,' . $id,
            'description' => 'nullable|string',
            'urgency' => 'required|in:low,medium,high,critical',
            'target_date' => 'nullable|date',
            'status' => 'required|in:planning,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.succession-plans.index')->with('success', 'تم تحديث خطة التعاقب بنجاح.');
    }

    public function destroy(string $id)
    {
        $plan = SuccessionPlan::findOrFail($id);
        $plan->delete();

        return redirect()->route('admin.succession-plans.index')->with('success', 'تم حذف خطة التعاقب بنجاح.');
    }
}
