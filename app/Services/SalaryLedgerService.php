<?php

namespace App\Services;

use App\Models\EmployeeAdvance;
use App\Models\Salary;
use App\Models\SalaryLedgerLine;
use InvalidArgumentException;

class SalaryLedgerService
{
    public static function allowedLineTypes(): array
    {
        return ['allowance', 'bonus', 'deduction', 'advance_recovery', 'loan_installment', 'overtime', 'other'];
    }

    /**
     * @return array<int, array{line_type: string, label_ar: ?string, amount: float, employee_advance_id: ?int, sort_order: int}>
     */
    public function normalizeInput(?array $rows): array
    {
        if (!$rows) {
            return [];
        }
        $allowed = self::allowedLineTypes();
        $out = [];
        foreach ($rows as $i => $row) {
            if (!is_array($row)) {
                continue;
            }
            $type = $row['line_type'] ?? '';
            if (!in_array($type, $allowed, true)) {
                continue;
            }
            $amount = isset($row['amount']) ? (float) $row['amount'] : 0;
            if ($amount < 0) {
                continue;
            }
            $labelAr = trim((string) ($row['label_ar'] ?? ''));
            $advanceRaw = $row['employee_advance_id'] ?? null;
            $advanceId = ($advanceRaw === '' || $advanceRaw === null) ? null : (int) $advanceRaw;
            if ($amount <= 0) {
                continue;
            }
            $out[] = [
                'line_type' => $type,
                'label_ar' => $labelAr !== '' ? $labelAr : null,
                'amount' => round($amount, 2),
                'employee_advance_id' => $advanceId,
                'sort_order' => count($out),
            ];
        }

        return $out;
    }

    public function sumDeductionSide(array $lines): float
    {
        $sum = 0;
        foreach ($lines as $l) {
            if (in_array($l['line_type'], SalaryLedgerLine::DEDUCTION_SIDE_TYPES, true)) {
                $sum += $l['amount'];
            }
        }

        return round($sum, 2);
    }

    /**
     * @param  array<int, array{line_type: string, label_ar: ?string, amount: float, employee_advance_id: ?int, sort_order: int}>  $lines
     */
    public function validateLinesForEmployee(array $lines, int $employeeId): void
    {
        $recoveryByAdvance = [];
        foreach ($lines as $l) {
            if ($l['line_type'] !== 'advance_recovery') {
                continue;
            }
            if (!$l['employee_advance_id']) {
                throw new InvalidArgumentException('يجب اختيار السلفة عند نوع «استرداد سلفة».');
            }
            $aid = $l['employee_advance_id'];
            $recoveryByAdvance[$aid] = ($recoveryByAdvance[$aid] ?? 0) + $l['amount'];
        }

        foreach ($recoveryByAdvance as $advanceId => $totalRecovery) {
            $adv = EmployeeAdvance::whereKey($advanceId)->lockForUpdate()->first();
            if (!$adv || (int) $adv->employee_id !== $employeeId) {
                throw new InvalidArgumentException('السلفة غير صالحة لهذا الموظف.');
            }
            if ($adv->status !== 'active') {
                throw new InvalidArgumentException('السلفة غير نشطة.');
            }
            if ($totalRecovery > (float) $adv->remaining_balance + 0.009) {
                throw new InvalidArgumentException('مجموع استرداد السلفة #'.$advanceId.' يتجاوز الرصيد المتبقي ('.$adv->remaining_balance.').');
            }
        }
    }

    public function revertAdvanceRecoveries(Salary $salary): void
    {
        foreach ($salary->ledgerLines()->where('line_type', 'advance_recovery')->whereNotNull('employee_advance_id')->get() as $line) {
            $adv = EmployeeAdvance::whereKey($line->employee_advance_id)->lockForUpdate()->first();
            if ($adv) {
                $adv->increment('remaining_balance', $line->amount);
                if ((float) $adv->remaining_balance > 0 && $adv->status === 'closed') {
                    $adv->update(['status' => 'active']);
                }
            }
        }
    }

    public function deleteLines(Salary $salary): void
    {
        $salary->ledgerLines()->delete();
    }

    /**
     * @param  array<int, array{line_type: string, label_ar: ?string, amount: float, employee_advance_id: ?int, sort_order: int}>  $lines
     */
    public function persistLines(Salary $salary, array $lines): void
    {
        foreach ($lines as $l) {
            SalaryLedgerLine::create([
                'salary_id' => $salary->id,
                'line_type' => $l['line_type'],
                'label' => $l['label_ar'],
                'label_ar' => $l['label_ar'],
                'amount' => $l['amount'],
                'employee_advance_id' => $l['employee_advance_id'],
                'sort_order' => $l['sort_order'],
            ]);
            if ($l['line_type'] === 'advance_recovery' && $l['employee_advance_id']) {
                $adv = EmployeeAdvance::whereKey($l['employee_advance_id'])->lockForUpdate()->first();
                if ($adv) {
                    $adv->decrement('remaining_balance', $l['amount']);
                    $adv->refresh();
                    if ((float) $adv->remaining_balance <= 0.009) {
                        $adv->update(['remaining_balance' => 0, 'status' => 'closed']);
                    }
                }
            }
        }
    }

    /**
     * @param  array<int, array{line_type: string, label_ar: ?string, amount: float, employee_advance_id: ?int, sort_order: int}>  $lines
     */
    public function syncAfterCreate(Salary $salary, array $lines, int $employeeId): void
    {
        if ($lines === []) {
            return;
        }
        $this->validateLinesForEmployee($lines, $employeeId);
        $this->persistLines($salary, $lines);
    }

}
