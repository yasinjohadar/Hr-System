<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalDelegation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamDelegationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('ensure.department.head.or.admin');
    }

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

        // إحصاءات البنر — من المجموعتين المُحمَّلتين أصلاً، بلا استعلامات إضافية
        $stats = [
            'sent_active'     => $delegations->where('status', 'active')->count(),
            'sent_total'      => $delegations->count(),
            'received_active' => $receivedDelegations->where('status', 'active')->count(),
            'received_total'  => $receivedDelegations->count(),
        ];

        return view('admin.pages.team.delegations', compact('delegations', 'receivedDelegations', 'stats'));
    }

    public function create()
    {
        $users = User::where('is_active', true)
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        $workflowTypes = ApprovalDelegation::WORKFLOW_TYPES;

        return view('admin.pages.team.delegation-create', compact('users', 'workflowTypes'));
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

        ApprovalDelegation::create([
            'delegator_id' => Auth::id(),
            'delegate_id' => $request->delegate_id,
            'workflow_types' => $request->workflow_types ?? [],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.team.delegations.index')
            ->with('success', 'تم إنشاء التفويض بنجاح');
    }

    public function cancel($id)
    {
        $delegation = ApprovalDelegation::where('delegator_id', Auth::id())->findOrFail($id);
        $delegation->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'تم إلغاء التفويض');
    }
}
