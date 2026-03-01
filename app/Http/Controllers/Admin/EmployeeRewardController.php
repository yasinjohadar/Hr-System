<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeReward;
use App\Models\Employee;
use App\Models\RewardType;
use App\Models\Currency;
use Illuminate\Http\Request;

class EmployeeRewardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-reward-list')->only('index');
        $this->middleware('permission:employee-reward-create')->only(['create', 'store']);
        $this->middleware('permission:employee-reward-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-reward-delete')->only('destroy');
        $this->middleware('permission:employee-reward-show')->only('show');
        $this->middleware('permission:employee-reward-award')->only(['award']);
    }

    public function index(Request $request)
    {
        $query = EmployeeReward::with(['employee', 'rewardType', 'currency', 'creator']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('reward_type_id')) {
            $query->where('reward_type_id', $request->input('reward_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->input('reason'));
        }

        $rewards = $query->latest()->paginate(15);
        $employees = Employee::where('is_active', true)->get();
        $rewardTypes = RewardType::where('is_active', true)->get();

        return view('admin.pages.employee-rewards.index', compact('rewards', 'employees', 'rewardTypes'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $rewardTypes = RewardType::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.employee-rewards.create', compact('employees', 'rewardTypes', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reward_type_id' => 'required|exists:reward_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reward_date' => 'required|date',
            'reason' => 'required|in:performance,achievement,milestone,recognition,other',
            'monetary_value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'points' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';

        EmployeeReward::create($data);

        return redirect()->route('admin.employee-rewards.index')->with('success', 'تم إضافة المكافأة بنجاح.');
    }

    public function show(string $id)
    {
        $reward = EmployeeReward::with([
            'employee',
            'rewardType',
            'currency',
            'awardedBy',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.employee-rewards.show', compact('reward'));
    }

    public function edit(string $id)
    {
        $reward = EmployeeReward::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        $rewardTypes = RewardType::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.employee-rewards.edit', compact('reward', 'employees', 'rewardTypes', 'currencies'));
    }

    public function update(Request $request, string $id)
    {
        $reward = EmployeeReward::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reward_type_id' => 'required|exists:reward_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reward_date' => 'required|date',
            'reason' => 'required|in:performance,achievement,milestone,recognition,other',
            'monetary_value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'points' => 'nullable|integer|min:0',
            'status' => 'required|in:pending,approved,awarded,cancelled',
            'notes' => 'nullable|string',
        ]);

        $reward->update($request->all());

        return redirect()->route('admin.employee-rewards.index')->with('success', 'تم تحديث المكافأة بنجاح.');
    }

    public function destroy(string $id)
    {
        $reward = EmployeeReward::findOrFail($id);
        $reward->delete();

        return redirect()->route('admin.employee-rewards.index')->with('success', 'تم حذف المكافأة بنجاح.');
    }

    public function award(string $id)
    {
        $reward = EmployeeReward::findOrFail($id);

        if ($reward->status !== 'approved') {
            return redirect()->back()->with('error', 'يجب الموافقة على المكافأة أولاً.');
        }

        $reward->update([
            'status' => 'awarded',
            'awarded_by' => auth()->id(),
            'awarded_at' => now()
        ]);

        return redirect()->back()->with('success', 'تم منح المكافأة بنجاح.');
    }
}
