<?php

namespace Database\Seeders;

use App\Models\ExpenseRequest;
use App\Models\ExpenseApproval;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpenseApprovalSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $users = User::where('id', '!=', $adminUser->id)->take(5)->get();

        if ($users->isEmpty()) {
            $users = collect([$adminUser]);
        }

        $expenseRequests = ExpenseRequest::whereIn('status', ['pending', 'approved', 'rejected', 'paid'])->get();

        if ($expenseRequests->isEmpty()) {
            $this->command->warn('لا توجد طلبات مصروفات!');
            return;
        }

        foreach ($expenseRequests as $request) {
            // موافقة مستوى 1
            $approver1 = $users->random();
            $status1 = in_array($request->status, ['approved', 'paid']) ? 'approved' : 'pending';

            ExpenseApproval::firstOrCreate(
                [
                    'expense_request_id' => $request->id,
                    'approval_level' => 1,
                    'approver_id' => $approver1->id,
                ],
                [
                    'status' => $status1,
                    'comments' => $status1 === 'approved' ? 'تمت الموافقة' : null,
                    'approved_at' => $status1 === 'approved' ? Carbon::now()->subDays(rand(1, 5)) : null,
                ]
            );

            // موافقة مستوى 2 (للطلبات الموافق عليها)
            if (in_array($request->status, ['approved', 'paid'])) {
                $approver2 = $users->where('id', '!=', $approver1->id)->random() ?? $users->random();
                
                ExpenseApproval::firstOrCreate(
                    [
                        'expense_request_id' => $request->id,
                        'approval_level' => 2,
                        'approver_id' => $approver2->id,
                    ],
                    [
                        'status' => 'approved',
                        'comments' => 'تمت الموافقة النهائية',
                        'approved_at' => Carbon::now()->subDays(rand(1, 3)),
                    ]
                );
            }
        }

        $totalApprovals = ExpenseApproval::count();
        $this->command->info("✅ تم إنشاء $totalApprovals موافقة مصروف");
    }
}
