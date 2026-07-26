<?php

namespace App\Services;

use App\Models\CompanyBankAccount;
use Illuminate\Validation\ValidationException;

class CompanyBankAccountService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): CompanyBankAccount
    {
        $data['created_by'] = $data['created_by'] ?? auth()->id();
        $data['balance'] = $data['balance'] ?? 0;

        return CompanyBankAccount::create($data);
    }

    /**
     * Update metadata only — balance changes must go through FundTransferService.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(CompanyBankAccount $account, array $data): CompanyBankAccount
    {
        unset($data['balance']);

        $account->update($data);

        return $account->fresh();
    }

    public function delete(CompanyBankAccount $account): void
    {
        if ((float) $account->balance !== 0.0) {
            throw ValidationException::withMessages([
                'account' => 'لا يمكن حذف حساب برصيد غير صفري. صفّر الرصيد عبر التحويلات أولاً.',
            ]);
        }

        $account->delete();
    }
}
