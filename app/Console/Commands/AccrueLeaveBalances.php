<?php

namespace App\Console\Commands;

use App\Services\LeaveAccrualService;
use Illuminate\Console\Command;

class AccrueLeaveBalances extends Command
{
    protected $signature = 'leave:accrue-monthly {--year=} {--month=}';

    protected $description = 'Run monthly leave balance accrual from active rules';

    public function handle(LeaveAccrualService $service): int
    {
        $count = $service->runMonthlyAccrual(
            $this->option('year') ? (int) $this->option('year') : null,
            $this->option('month') ? (int) $this->option('month') : null
        );

        $this->info("Processed {$count} balance updates.");

        return self::SUCCESS;
    }
}
