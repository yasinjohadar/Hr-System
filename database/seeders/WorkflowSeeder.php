<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class WorkflowSeeder extends Seeder
{
    /**
     * تسلسل الموافقات: رئيس القسم (مدير القسم) → المدير التنفيذي → المدير العام.
     * يتطلب: AdminUserSeeder قبل هذا الـ seeder، وتعيين manager_id للأقسام، ومستخدمون لأدوار executive_director و general_manager.
     *
     * الطلبات ذات WorkflowInstance قيد التنفيذ قد تشير لخطوات قديمة؛ راجع البيانات يدوياً عند الترقية.
     */
    public function run(): void
    {
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $execRole = Role::where('name', 'executive_director')->first();
        $gmRole = Role::where('name', 'general_manager')->first();

        if (! $execRole || ! $gmRole) {
            $this->command->warn('⚠️  شغّل AdminUserSeeder أولاً لإنشاء أدوار executive_director و general_manager.');
        }

        $stepTemplate = function (int $execRoleId, int $gmRoleId): array {
            return [
                [
                    'name' => 'Department Head Approval',
                    'name_ar' => 'موافقة رئيس القسم',
                    'approver_type' => 'department_manager',
                    'step_order' => 1,
                    'approver_id' => null,
                    'role_id' => null,
                ],
                [
                    'name' => 'Executive Director Approval',
                    'name_ar' => 'موافقة المدير التنفيذي',
                    'approver_type' => 'role',
                    'step_order' => 2,
                    'approver_id' => null,
                    'role_id' => $execRoleId,
                ],
                [
                    'name' => 'General Manager Approval',
                    'name_ar' => 'موافقة المدير العام',
                    'approver_type' => 'role',
                    'step_order' => 3,
                    'approver_id' => null,
                    'role_id' => $gmRoleId,
                ],
            ];
        };

        $execId = $execRole?->id ?? 0;
        $gmId = $gmRole?->id ?? 0;

        $workflows = [
            [
                'name' => 'Leave Request Workflow',
                'name_ar' => 'سير عمل طلب الإجازة',
                'description' => 'سير عمل لموافقة طلبات الإجازات',
                'type' => 'leave_request',
                'is_active' => true,
                'created_by' => $createdBy,
                'steps' => $stepTemplate($execId, $gmId),
            ],
            [
                'name' => 'Expense Request Workflow',
                'name_ar' => 'سير عمل طلب المصروفات',
                'description' => 'سير عمل لموافقة طلبات المصروفات',
                'type' => 'expense_request',
                'is_active' => true,
                'created_by' => $createdBy,
                'steps' => $stepTemplate($execId, $gmId),
            ],
            [
                'name' => 'Employee Job Change Workflow',
                'name_ar' => 'سير عمل التغيير الوظيفي',
                'description' => 'سير عمل لموافقة طلبات التغيير الوظيفي',
                'type' => 'employee_job_change',
                'is_active' => true,
                'created_by' => $createdBy,
                'steps' => $stepTemplate($execId, $gmId),
            ],
        ];

        foreach ($workflows as $workflowData) {
            $steps = $workflowData['steps'];
            unset($workflowData['steps']);

            $workflow = Workflow::updateOrCreate(
                ['type' => $workflowData['type']],
                $workflowData
            );

            foreach ($steps as $stepData) {
                WorkflowStep::updateOrCreate(
                    [
                        'workflow_id' => $workflow->id,
                        'step_order' => $stepData['step_order'],
                    ],
                    array_merge($stepData, [
                        'workflow_id' => $workflow->id,
                        'is_required' => true,
                        'can_reject' => true,
                    ])
                );
            }
        }

        $this->command->info('✅ تم تحديث سير العمل (3 خطوات لكل نوع).');
    }
}
