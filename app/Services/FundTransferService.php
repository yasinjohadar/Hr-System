<?php

namespace App\Services;

use App\Models\CompanyBankAccount;
use App\Models\Employee;
use App\Models\EmployeeBankAccount;
use App\Models\FundTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FundTransferService
{
    public function approvalThreshold(): float
    {
        return (float) config('project_finance.transfer_approval_threshold', 10000);
    }

    protected function workflowService(): WorkflowService
    {
        return app(WorkflowService::class);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function request(array $data, User $requester): FundTransfer
    {
        $amount = round((float) $data['amount'], 2);
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'المبلغ يجب أن يكون أكبر من صفر.']);
        }

        $type = $data['type'] ?? 'internal';
        $this->assertAccountsValid($type, $data);

        $transfer = FundTransfer::create([
            'type' => $type,
            'from_account_type' => $data['from_account_type'] ?? null,
            'from_account_id' => $data['from_account_id'] ?? null,
            'to_account_type' => $data['to_account_type'],
            'to_account_id' => $data['to_account_id'],
            'amount' => $amount,
            'currency_id' => $data['currency_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'project_stage_id' => $data['project_stage_id'] ?? null,
            'status' => 'pending',
            'requested_by' => $requester->id,
            'notes' => $data['notes'] ?? null,
            'meta' => $data['meta'] ?? null,
        ]);

        if ($amount <= $this->approvalThreshold()) {
            return $this->execute($transfer, $requester);
        }

        $this->startApprovalWorkflow($transfer, $requester);

        return $transfer->fresh();
    }

    public function approve(FundTransfer $transfer, User $approver): FundTransfer
    {
        if ($transfer->status !== 'pending') {
            throw ValidationException::withMessages([
                'transfer' => 'لا يمكن الموافقة على تحويل غير معلّق.',
            ]);
        }

        return $this->execute($transfer, $approver);
    }

    public function reject(FundTransfer $transfer, User $approver, ?string $reason = null): FundTransfer
    {
        if ($transfer->status !== 'pending') {
            throw ValidationException::withMessages([
                'transfer' => 'لا يمكن رفض تحويل غير معلّق.',
            ]);
        }

        $transfer->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $transfer->fresh();
    }

    /**
     * Called when workflow marks the transfer as approved.
     */
    public function executeApproved(FundTransfer $transfer): FundTransfer
    {
        if ($transfer->status === 'completed') {
            return $transfer;
        }

        $approver = auth()->user() ?? $transfer->requester;

        return $this->execute($transfer, $approver);
    }

    protected function execute(FundTransfer $transfer, User $actor): FundTransfer
    {
        return DB::transaction(function () use ($transfer, $actor) {
            $transfer = FundTransfer::whereKey($transfer->id)->lockForUpdate()->firstOrFail();

            if ($transfer->status === 'completed') {
                return $transfer;
            }

            if (! in_array($transfer->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages([
                    'transfer' => 'حالة التحويل لا تسمح بالتنفيذ.',
                ]);
            }

            $amount = (float) $transfer->amount;

            if ($transfer->from_account_type === FundTransfer::ACCOUNT_COMPANY && $transfer->from_account_id) {
                $from = CompanyBankAccount::whereKey($transfer->from_account_id)->lockForUpdate()->first();
                if (! $from || ! $from->is_active) {
                    throw ValidationException::withMessages(['from_account_id' => 'حساب المصدر غير متاح.']);
                }
                if ((float) $from->balance < $amount) {
                    throw ValidationException::withMessages(['amount' => 'رصيد حساب المصدر غير كافٍ.']);
                }
                $from->balance = (float) $from->balance - $amount;
                $from->save();
            }

            if ($transfer->to_account_type === FundTransfer::ACCOUNT_COMPANY) {
                $to = CompanyBankAccount::whereKey($transfer->to_account_id)->lockForUpdate()->first();
                if (! $to || ! $to->is_active) {
                    throw ValidationException::withMessages(['to_account_id' => 'حساب الوجهة غير متاح.']);
                }
                if ($transfer->from_account_type === FundTransfer::ACCOUNT_COMPANY
                    && $transfer->currency_id
                    && $to->currency_id
                    && (int) $transfer->currency_id !== (int) $to->currency_id
                    && $transfer->from_account_id) {
                    $fromCurrency = CompanyBankAccount::find($transfer->from_account_id)?->currency_id;
                    if ($fromCurrency && (int) $fromCurrency !== (int) $to->currency_id) {
                        throw ValidationException::withMessages(['currency_id' => 'عملة الحسابين غير متطابقة.']);
                    }
                }
                $to->balance = (float) $to->balance + $amount;
                $to->save();
            } elseif ($transfer->to_account_type === FundTransfer::ACCOUNT_EMPLOYEE) {
                $to = EmployeeBankAccount::find($transfer->to_account_id);
                if (! $to || ! $to->is_active) {
                    throw ValidationException::withMessages(['to_account_id' => 'حساب الموظف البنكي غير متاح.']);
                }
            }

            $transfer->update([
                'status' => 'completed',
                'approved_by' => $actor->id,
                'approved_at' => now(),
                'executed_at' => now(),
            ]);

            return $transfer->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function assertAccountsValid(string $type, array $data): void
    {
        if ($type === 'adjustment') {
            if (($data['to_account_type'] ?? null) !== FundTransfer::ACCOUNT_COMPANY) {
                throw ValidationException::withMessages([
                    'to_account_type' => 'التسوية يجب أن تكون إلى حساب شركة.',
                ]);
            }
            CompanyBankAccount::whereKey($data['to_account_id'])->where('is_active', true)->firstOrFail();

            return;
        }

        if ($type === 'internal') {
            if (($data['from_account_type'] ?? null) !== FundTransfer::ACCOUNT_COMPANY
                || ($data['to_account_type'] ?? null) !== FundTransfer::ACCOUNT_COMPANY) {
                throw ValidationException::withMessages([
                    'type' => 'التحويل الداخلي يكون بين حسابات الشركة فقط.',
                ]);
            }
            if ((int) $data['from_account_id'] === (int) $data['to_account_id']) {
                throw ValidationException::withMessages([
                    'to_account_id' => 'حساب المصدر والوجهة يجب أن يختلفا.',
                ]);
            }
            CompanyBankAccount::whereKey($data['from_account_id'])->where('is_active', true)->firstOrFail();
            CompanyBankAccount::whereKey($data['to_account_id'])->where('is_active', true)->firstOrFail();

            return;
        }

        if ($type === 'disbursement') {
            if (($data['from_account_type'] ?? null) !== FundTransfer::ACCOUNT_COMPANY
                || ($data['to_account_type'] ?? null) !== FundTransfer::ACCOUNT_EMPLOYEE) {
                throw ValidationException::withMessages([
                    'type' => 'صرف الموظف يكون من حساب شركة إلى حساب موظف.',
                ]);
            }
            CompanyBankAccount::whereKey($data['from_account_id'])->where('is_active', true)->firstOrFail();
            EmployeeBankAccount::whereKey($data['to_account_id'])->where('is_active', true)->firstOrFail();
        }
    }

    protected function startApprovalWorkflow(FundTransfer $transfer, User $requester): void
    {
        $employee = $requester->employee;
        if (! $employee instanceof Employee) {
            return;
        }

        try {
            $this->workflowService()->startWorkflow(
                'fund_transfer',
                $employee,
                FundTransfer::class,
                (int) $transfer->id
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
