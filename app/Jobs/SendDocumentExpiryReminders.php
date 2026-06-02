<?php

namespace App\Jobs;

use App\Models\EmployeeCertificate;
use App\Models\EmployeeDocument;
use App\Models\User;
use App\Notifications\CustomNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDocumentExpiryReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $threshold = Carbon::today()->addDays(30);

        EmployeeDocument::with('employee.user')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [Carbon::today(), $threshold])
            ->each(function (EmployeeDocument $doc) {
                $user = $doc->employee?->user;
                if ($user) {
                    $user->notify(new CustomNotification(
                        'تنبيه انتهاء مستند',
                        'المستند «' . $doc->title . '» ينتهي في ' . $doc->expiry_date->format('Y-m-d')
                    ));
                }
            });

        EmployeeCertificate::with('employee.user')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [Carbon::today(), $threshold])
            ->each(function (EmployeeCertificate $cert) {
                $user = $cert->employee?->user;
                if ($user) {
                    $user->notify(new CustomNotification(
                        'تنبيه انتهاء شهادة',
                        'الشهادة «' . ($cert->certificate_name ?? 'شهادة') . '» تنتهي قريباً.'
                    ));
                }
            });
    }
}
