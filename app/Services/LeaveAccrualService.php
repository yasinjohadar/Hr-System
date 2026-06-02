<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Employee;
use App\Models\LeaveAccrualRule;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeaveAccrualService
{
    public function runMonthlyAccrual(?int $year = null, ?int $month = null): int
    {
        $year = $year ?? (int) now()->year;
        $month = $month ?? (int) now()->month;
        $processed = 0;

        $rules = LeaveAccrualRule::with('leaveType')->where('is_active', true)->get();
        if ($rules->isEmpty()) {
            return 0;
        }

        Employee::where('is_active', true)->with(['branch'])->chunk(100, function ($employees) use ($rules, $year, &$processed) {
            foreach ($employees as $employee) {
                foreach ($rules as $rule) {
                    if (! $this->ruleAppliesToEmployee($rule, $employee)) {
                        continue;
                    }

                    $days = (float) $rule->accrual_days_per_month;
                    if ($days <= 0) {
                        continue;
                    }

                    $balance = LeaveBalance::firstOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'leave_type_id' => $rule->leave_type_id,
                            'year' => $year,
                        ],
                        [
                            'total_days' => 0,
                            'used_days' => 0,
                            'remaining_days' => 0,
                            'carried_forward' => 0,
                        ]
                    );

                    $newTotal = $balance->total_days + (int) round($days);
                    if ($rule->max_balance !== null) {
                        $newTotal = min($newTotal, (int) $rule->max_balance);
                    }

                    $balance->total_days = $newTotal;
                    $balance->updateRemaining();
                    $processed++;
                }
            }
        });

        Log::info('Leave accrual completed', ['year' => $year, 'month' => $month, 'processed' => $processed]);

        return $processed;
    }

    protected function ruleAppliesToEmployee(LeaveAccrualRule $rule, Employee $employee): bool
    {
        if ($rule->branch_id && $employee->branch_id !== $rule->branch_id) {
            return false;
        }

        if ($rule->country_id && $employee->branch) {
            $country = Country::find($rule->country_id);
            if ($country && $employee->branch->country && strcasecmp($employee->branch->country, $country->name) !== 0
                && strcasecmp($employee->branch->country, $country->name_ar ?? '') !== 0) {
                return false;
            }
        }

        return true;
    }
}
