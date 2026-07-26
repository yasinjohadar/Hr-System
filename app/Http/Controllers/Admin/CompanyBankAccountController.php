<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyBankAccount;
use App\Models\Currency;
use App\Services\CompanyBankAccountService;
use Illuminate\Http\Request;

class CompanyBankAccountController extends Controller
{
    public function __construct(
        protected CompanyBankAccountService $accountService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:company-bank-account-list')->only('index');
        $this->middleware('permission:company-bank-account-create')->only(['create', 'store']);
        $this->middleware('permission:company-bank-account-edit')->only(['edit', 'update']);
        $this->middleware('permission:company-bank-account-delete')->only('destroy');
        $this->middleware('permission:company-bank-account-show')->only('show');
    }

    public function index()
    {
        $accounts = CompanyBankAccount::with('currency')->orderBy('name')->paginate(20);

        $accountStats = [
            'total' => CompanyBankAccount::count(),
            'active' => CompanyBankAccount::where('is_active', true)->count(),
            'balance' => (float) CompanyBankAccount::sum('balance'),
            'inactive' => CompanyBankAccount::where('is_active', false)->count(),
        ];

        return view('admin.pages.company-bank-accounts.index', compact('accounts', 'accountStats'));
    }

    public function create()
    {
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.company-bank-accounts.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'currency_id' => 'nullable|exists:currencies,id',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $this->accountService->create($validated);

        return redirect()->route('admin.company-bank-accounts.index')
            ->with('success', 'تم إنشاء الحساب البنكي.');
    }

    public function show(CompanyBankAccount $companyBankAccount)
    {
        $companyBankAccount->load('currency', 'creator');

        return view('admin.pages.company-bank-accounts.show', [
            'account' => $companyBankAccount,
        ]);
    }

    public function edit(CompanyBankAccount $companyBankAccount)
    {
        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.company-bank-accounts.edit', [
            'account' => $companyBankAccount,
            'currencies' => $currencies,
        ]);
    }

    public function update(Request $request, CompanyBankAccount $companyBankAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'currency_id' => 'nullable|exists:currencies,id',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $this->accountService->update($companyBankAccount, $validated);

        return redirect()->route('admin.company-bank-accounts.index')
            ->with('success', 'تم تحديث الحساب البنكي.');
    }

    public function destroy(CompanyBankAccount $companyBankAccount)
    {
        $this->accountService->delete($companyBankAccount);

        return redirect()->route('admin.company-bank-accounts.index')
            ->with('success', 'تم حذف الحساب البنكي.');
    }
}
