<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRule;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class AttendanceRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:attendance-rule-list')->only('index');
        $this->middleware('permission:attendance-rule-create')->only(['create', 'store']);
        $this->middleware('permission:attendance-rule-edit')->only(['edit', 'update']);
        $this->middleware('permission:attendance-rule-delete')->only('destroy');
        $this->middleware('permission:attendance-rule-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = AttendanceRule::with('creator');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('rule_code', 'like', "%$search%");
            });
        }

        if ($request->filled('rule_type')) {
            $query->where('rule_type', $request->input('rule_type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $rules = $query->orderBy('priority', 'desc')->latest()->paginate(20);

        return view('admin.pages.attendance-rules.index', compact('rules'));
    }

    public function create()
    {
        $positions = Position::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.attendance-rules.create', compact('positions', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'rule_type' => 'required|in:late,absent,early_leave,overtime,break,holiday',
            'threshold_minutes' => 'required|integer|min:0',
            'action_type' => 'required|in:warning,deduction,notification,block',
            'deduction_amount' => 'nullable|numeric|min:0|required_if:action_type,deduction',
            'deduction_percentage' => 'nullable|integer|min:0|max:100|required_if:action_type,deduction',
            'apply_to_all' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['apply_to_all'] = $request->has('apply_to_all') ? 1 : 0;
        $data['send_notification'] = $request->has('send_notification') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->filled('applicable_positions')) {
            $data['applicable_positions'] = $request->applicable_positions;
        }

        if ($request->filled('applicable_departments')) {
            $data['applicable_departments'] = $request->applicable_departments;
        }

        AttendanceRule::create($data);

        return redirect()->route('admin.attendance-rules.index')
            ->with('success', 'تم إضافة قاعدة الحضور بنجاح.');
    }

    public function show(string $id)
    {
        $rule = AttendanceRule::with('creator')->findOrFail($id);
        return view('admin.pages.attendance-rules.show', compact('rule'));
    }

    public function edit(string $id)
    {
        $rule = AttendanceRule::findOrFail($id);
        $positions = Position::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.attendance-rules.edit', compact('rule', 'positions', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        $rule = AttendanceRule::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'rule_type' => 'required|in:late,absent,early_leave,overtime,break,holiday',
            'threshold_minutes' => 'required|integer|min:0',
            'action_type' => 'required|in:warning,deduction,notification,block',
            'deduction_amount' => 'nullable|numeric|min:0|required_if:action_type,deduction',
            'deduction_percentage' => 'nullable|integer|min:0|max:100|required_if:action_type,deduction',
            'apply_to_all' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['apply_to_all'] = $request->has('apply_to_all') ? 1 : 0;
        $data['send_notification'] = $request->has('send_notification') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->filled('applicable_positions')) {
            $data['applicable_positions'] = $request->applicable_positions;
        } else {
            $data['applicable_positions'] = null;
        }

        if ($request->filled('applicable_departments')) {
            $data['applicable_departments'] = $request->applicable_departments;
        } else {
            $data['applicable_departments'] = null;
        }

        $rule->update($data);

        return redirect()->route('admin.attendance-rules.index')
            ->with('success', 'تم تحديث قاعدة الحضور بنجاح.');
    }

    public function destroy(string $id)
    {
        $rule = AttendanceRule::findOrFail($id);
        $rule->delete();

        return redirect()->route('admin.attendance-rules.index')
            ->with('success', 'تم حذف قاعدة الحضور بنجاح.');
    }
}
