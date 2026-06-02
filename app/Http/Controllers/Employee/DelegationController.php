<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ApprovalDelegation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DelegationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $delegations = ApprovalDelegation::where('delegator_id', $user->id)
            ->with(['delegate', 'creator'])
            ->orderByDesc('created_at')
            ->get();

        $receivedDelegations = ApprovalDelegation::where('delegate_id', $user->id)
            ->with(['delegator', 'creator'])
            ->orderByDesc('created_at')
            ->get();

        return view('employee.pages.department-head.delegations', compact('delegations', 'receivedDelegations'));
    }

    public function create()
    {
        $users = User::where('is_active', true)
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        $workflowTypes = [
            'leave_request' => 'طلب الإجازة',
            'expense_request' => 'طلب المصروفات',
            'employee_job_change' => 'تغيير الوظيفة',
            'overtime_request' => 'العمل الإضافي',
        ];

        return view('employee.pages.department-head.delegation-create', compact('users', 'workflowTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delegate_id' => 'required|exists:users,id|different:id',
            'workflow_types' => 'nullable|array',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $delegation = ApprovalDelegation::create([
            'delegator_id' => Auth::id(),
            'delegate_id' => $request->delegate_id,
            'workflow_types' => $request->workflow_types ?? [],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('employee.department-head.delegations')
            ->with('success', 'تم إنشاء التفويض بنجاح');
    }

    public function cancel($id)
    {
        $delegation = ApprovalDelegation::where('delegator_id', Auth::id())->findOrFail($id);
        $delegation->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'تم إلغاء التفويض');
    }

    public function expireOld()
    {
        ApprovalDelegation::where('status', 'active')
            ->where('end_date', '<', now())
            ->update(['status' => 'expired']);

        return response()->json(['message' => 'Expired delegations updated']);
    }
}
