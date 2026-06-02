<?php

namespace App\Jobs;

use App\Models\ScheduledReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RunScheduledReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        ScheduledReport::where('is_active', true)->each(function (ScheduledReport $report) {
            if (! $this->isDue($report)) {
                return;
            }

            foreach ($report->recipients as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                Mail::raw(
                    'تقرير مجدول: ' . $report->name . ' (' . $report->report_type . ')',
                    fn ($m) => $m->to($email)->subject('[HR] ' . $report->name)
                );
            }

            $report->update(['last_run_at' => now()]);
        });
    }

    protected function isDue(ScheduledReport $report): bool
    {
        if (! $report->last_run_at) {
            return true;
        }

        return match ($report->frequency) {
            'daily' => $report->last_run_at->lt(now()->subDay()),
            'weekly' => $report->last_run_at->lt(now()->subWeek()),
            'monthly' => $report->last_run_at->lt(now()->subMonth()),
            default => false,
        };
    }
}
