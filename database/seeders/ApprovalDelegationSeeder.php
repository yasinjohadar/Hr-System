<?php

namespace Database\Seeders;

use App\Models\ApprovalDelegation;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * بيانات تجريبية لتفويض صلاحيات الموافقة (admin/team/delegations).
 *
 * غاية هذا السيدر معاينة توزيع البيانات في الواجهة المُعاد تصميمها: حالات
 * نشطة/منتهية/ملغاة، تفويض لكل الأنواع مقابل أنواع محدّدة، وتفويضات صادرة
 * ومستلمة لنفس المستخدم — لا بيانات إنتاجية.
 *
 * يعتمد على مستخدمَي رئيس قسم وحساب الأدمن الموجودين فعلاً في القاعدة
 * (AdminUserSeeder/EmployeeSeeder)، فيُشغَّل بعدهما في DatabaseSeeder.
 */
class ApprovalDelegationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->first();
        $heads = User::role('department_head')->orderBy('id')->limit(2)->get();

        if (! $admin || $heads->count() < 2) {
            $this->command->warn('ApprovalDelegationSeeder: يلزم حساب أدمن ورئيسا قسم على الأقل — تم التخطي.');

            return;
        }

        [$headA, $headB] = [$heads[0], $heads[1]];

        $rows = [
            // رئيس قسم يفوّض رئيس قسم آخر أثناء إجازته — نشط، أنواع محدّدة
            [
                'delegator_id' => $headA->id,
                'delegate_id' => $headB->id,
                'workflow_types' => ['leave_request', 'expense_request'],
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(5),
                'status' => 'active',
                'notes' => 'تفويض أثناء إجازتي السنوية — يشمل طلبات الإجازة والمصروفات فقط.',
                'created_by' => $headA->id,
            ],

            // رئيس قسم يفوّض الأدمن بكل الأنواع (workflow_types = null)
            [
                'delegator_id' => $headB->id,
                'delegate_id' => $admin->id,
                'workflow_types' => null,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(10),
                'status' => 'active',
                'notes' => null,
                'created_by' => $headB->id,
            ],

            // الأدمن يفوّض رئيس قسم لتغييرات وظيفية تحديداً — نشط
            [
                'delegator_id' => $admin->id,
                'delegate_id' => $headA->id,
                'workflow_types' => ['employee_job_change'],
                'start_date' => now()->subHours(6),
                'end_date' => now()->addDays(3),
                'status' => 'active',
                'notes' => 'اعتماد التغييرات الوظيفية خلال انشغالي بمراجعة الميزانية الفصلية.',
                'created_by' => $admin->id,
            ],

            // الأدمن يفوّض رئيس القسم الآخر بالعمل الإضافي — نشط، بلا ملاحظات
            [
                'delegator_id' => $admin->id,
                'delegate_id' => $headB->id,
                'workflow_types' => ['overtime_request'],
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'status' => 'active',
                'notes' => null,
                'created_by' => $admin->id,
            ],

            // تفويض منتهي — لمعاينة شارة "منتهي" وأنها لا تُظهر زرّ الإلغاء
            [
                'delegator_id' => $headA->id,
                'delegate_id' => $headB->id,
                'workflow_types' => null,
                'start_date' => now()->subMonth(),
                'end_date' => now()->subDays(20),
                'status' => 'expired',
                'notes' => 'تفويض تغطية إجازة الشهر الماضي — انتهى تلقائياً.',
                'created_by' => $headA->id,
            ],

            // تفويض ملغى يدوياً — لمعاينة شارة "ملغي"
            [
                'delegator_id' => $headB->id,
                'delegate_id' => $headA->id,
                'workflow_types' => ['leave_request'],
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(4),
                'status' => 'cancelled',
                'notes' => 'أُلغي بعد إلغاء الإجازة المخطَّطة.',
                'created_by' => $headB->id,
            ],
        ];

        foreach ($rows as $row) {
            ApprovalDelegation::firstOrCreate(
                [
                    'delegator_id' => $row['delegator_id'],
                    'delegate_id' => $row['delegate_id'],
                    'start_date' => $row['start_date'],
                ],
                $row
            );
        }

        $this->command->info('ApprovalDelegationSeeder: تم إنشاء ' . count($rows) . ' تفويضاً تجريبياً.');
    }
}
