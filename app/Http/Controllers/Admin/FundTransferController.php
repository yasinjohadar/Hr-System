<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyBankAccount;
use App\Models\Currency;
use App\Models\EmployeeBankAccount;
use App\Models\FundTransfer;
use App\Models\Project;
use App\Services\FundTransferService;
use Illuminate\Http\Request;

class FundTransferController extends Controller
{
    public function __construct(
        protected FundTransferService $transferService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:fund-transfer-list')->only(['index', 'show']);
        $this->middleware('permission:fund-transfer-create')->only(['create', 'store']);
        $this->middleware('permission:fund-transfer-approve')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        $query = FundTransfer::with(['requester', 'approver', 'project', 'currency'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $transfers = $query->paginate(20)->withQueryString();
        $projects = Project::orderBy('name')->get(['id', 'name', 'name_ar', 'project_code']);
        $threshold = $this->transferService->approvalThreshold();

        $transferStats = [
            'total' => FundTransfer::count(),
            'pending' => FundTransfer::where('status', 'pending')->count(),
            'completed' => FundTransfer::where('status', 'completed')->count(),
            'month_amount' => (float) FundTransfer::where('status', 'completed')
                ->whereMonth('executed_at', now()->month)
                ->whereYear('executed_at', now()->year)
                ->sum('amount'),
        ];

        return view('admin.pages.fund-transfers.index', compact(
            'transfers',
            'projects',
            'threshold',
            'transferStats'
        ));
    }

    public function create(Request $request)
    {
        $companyAccounts = CompanyBankAccount::where('is_active', true)->with('currency')->orderBy('name')->get();
        $employeeAccounts = EmployeeBankAccount::where('is_active', true)
            ->with('employee')
            ->orderByDesc('is_primary')
            ->limit(500)
            ->get();
        $projects = Project::with('stages')->orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->get();
        $threshold = $this->transferService->approvalThreshold();

        return view('admin.pages.fund-transfers.create', compact(
            'companyAccounts',
            'employeeAccounts',
            'projects',
            'currencies',
            'threshold'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:internal,disbursement,adjustment',
            'from_account_id' => 'nullable|integer',
            'to_account_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'nullable|exists:currencies,id',
            'project_id' => 'nullable|exists:projects,id',
            'project_stage_id' => 'nullable|exists:project_stages,id',
            'notes' => 'nullable|string',
        ]);

        // Derive account types from transfer type (UI does not send them)
        match ($validated['type']) {
            'adjustment' => [
                $validated['from_account_type'] = null,
                $validated['from_account_id'] = null,
                $validated['to_account_type'] = 'company',
            ],
            'internal' => [
                $validated['from_account_type'] = 'company',
                $validated['to_account_type'] = 'company',
            ],
            'disbursement' => [
                $validated['from_account_type'] = 'company',
                $validated['to_account_type'] = 'employee',
            ],
        };

        if (in_array($validated['type'], ['internal', 'disbursement'], true) && empty($validated['from_account_id'])) {
            return back()->withInput()->withErrors([
                'from_account_id' => 'حساب المصدر مطلوب لهذا النوع من التحويل.',
            ]);
        }

        $transfer = $this->transferService->request($validated, $request->user());

        $message = $transfer->status === 'completed'
            ? 'تم تنفيذ التحويل بنجاح.'
            : 'تم إنشاء التحويل وبانتظار الموافقة (فوق عتبة الاعتماد).';

        return redirect()->route('admin.fund-transfers.show', $transfer)
            ->with('success', $message);
    }

    public function show(FundTransfer $fundTransfer)
    {
        $fundTransfer->load(['requester', 'approver', 'project', 'stage', 'currency']);

        return view('admin.pages.fund-transfers.show', [
            'transfer' => $fundTransfer,
            'fromAccount' => $fundTransfer->resolveFromAccount(),
            'toAccount' => $fundTransfer->resolveToAccount(),
            'threshold' => $this->transferService->approvalThreshold(),
        ]);
    }

    public function approve(FundTransfer $fundTransfer)
    {
        $this->transferService->approve($fundTransfer, request()->user());

        return redirect()->route('admin.fund-transfers.show', $fundTransfer)
            ->with('success', 'تمت الموافقة وتنفيذ التحويل.');
    }

    public function reject(Request $request, FundTransfer $fundTransfer)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:2000',
        ]);

        $this->transferService->reject(
            $fundTransfer,
            $request->user(),
            $validated['rejection_reason'] ?? null
        );

        return redirect()->route('admin.fund-transfers.show', $fundTransfer)
            ->with('success', 'تم رفض التحويل.');
    }
}
