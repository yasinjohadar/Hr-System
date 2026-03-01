<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\PayrollApproval;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PayrollApprovalSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $users = User::where('id', '!=', $adminUser->id)->take(5)->get();

        if ($users->isEmpty()) {
            $users = collect([$adminUser]);
        }

        $payrolls = Payroll::whereIn('status', ['calculated', 'approved', 'paid'])->get();

        if ($payrolls->isEmpty()) {
            $this->command->warn('لا توجد كشوف رواتب!');
            return;
        }

        foreach ($payrolls as $payroll) {
            // موافقة مستوى 1
            $approver1 = $users->random();
            $status1 = $payroll->status === 'draft' ? 'pending' : 'approved';

            PayrollApproval::firstOrCreate(
                [
                    'payroll_id' => $payroll->id,
                    'approval_level' => 1,
                    'approver_id' => $approver1->id,
                ],
                [
                    'status' => $status1,
                    'approved_at' => $status1 === 'approved' ? Carbon::now()->subDays(rand(1, 5)) : null,
                    'comments' => $status1 === 'approved' ? 'تمت الموافقة' : null,
                    'sort_order' => 1,
                ]
            );

            // موافقة مستوى 2 (للكشوف الموافق عليها)
            if ($payroll->status === 'approved' || $payroll->status === 'paid') {
                $approver2 = $users->where('id', '!=', $approver1->id)->random() ?? $users->random();
                
                PayrollApproval::firstOrCreate(
                    [
                        'payroll_id' => $payroll->id,
                        'approval_level' => 2,
                        'approver_id' => $approver2->id,
                    ],
                    [
                        'status' => 'approved',
                        'approved_at' => Carbon::now()->subDays(rand(1, 3)),
                        'comments' => 'تمت الموافقة النهائية',
                        'sort_order' => 2,
                    ]
                );
            }
        }

        $totalApprovals = PayrollApproval::count();
        $this->command->info("✅ تم إنشاء $totalApprovals موافقة راتب");
    }
}
