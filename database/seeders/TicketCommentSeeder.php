<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketCommentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $tickets = Ticket::all();
        $employees = Employee::where('is_active', true)->get();

        if ($tickets->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد تذاكر أو موظفين!');
            return;
        }

        $comments = [
            'تم فحص المشكلة',
            'يتم العمل على الحل',
            'تم حل المشكلة',
            'يحتاج إلى معلومات إضافية',
            'تم التصعيد للمستوى الأعلى',
            'في انتظار رد العميل',
            'تم الإغلاق',
            'يحتاج إلى متابعة',
        ];

        foreach ($tickets as $ticket) {
            // 1-5 تعليقات لكل تذكرة
            $numComments = rand(1, 5);

            for ($i = 0; $i < $numComments; $i++) {
                $commenter = $employees->random();
                $isInternal = rand(1, 100) <= 30; // 30% تعليقات داخلية
                $isResolution = ($i === $numComments - 1 && $ticket->status === 'resolved'); // آخر تعليق قد يكون الحل

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $commenter->user_id ?? $createdBy,
                    'employee_id' => $commenter->id,
                    'comment' => $comments[array_rand($comments)] . ' - ' . ($i + 1),
                    'is_internal' => $isInternal,
                    'is_resolution' => $isResolution,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $totalComments = TicketComment::count();
        $this->command->info("✅ تم إنشاء $totalComments تعليق تذكرة");
    }
}
