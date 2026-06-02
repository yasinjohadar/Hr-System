<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
use App\Models\LeaveAccrualRule;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveAccrualRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:leave-type-list');
    }

    public function index()
    {
        $rules = LeaveAccrualRule::with(['leaveType', 'country', 'branch'])->paginate(20);

        return view('admin.pages.leave-accrual-rules.index', compact('rules'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('admin.pages.leave-accrual-rules.create', compact('leaveTypes', 'countries', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'country_id' => 'nullable|exists:countries,id',
            'branch_id' => 'nullable|exists:branches,id',
            'accrual_days_per_month' => 'required|numeric|min:0',
            'max_balance' => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = true;
        LeaveAccrualRule::create($data);

        return redirect()->route('admin.leave-accrual-rules.index')->with('success', 'تم حفظ قاعدة الاكتساب.');
    }
}
