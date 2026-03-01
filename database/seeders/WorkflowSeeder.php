<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $workflows = [
            [
                'name' => 'Leave Request Workflow',
                'name_ar' => 'سير عمل طلب الإجازة',
                'description' => 'سير عمل لموافقة طلبات الإجازات',
                'type' => 'leave_request',
                'is_active' => true,
                'created_by' => $createdBy,
                'steps' => [
                    ['name' => 'Manager Approval', 'name_ar' => 'موافقة المدير', 'approver_type' => 'employee_manager', 'step_order' => 1],
                    ['name' => 'HR Approval', 'name_ar' => 'موافقة الموارد البشرية', 'approver_type' => 'role', 'step_order' => 2],
                ]
            ],
            [
                'name' => 'Expense Request Workflow',
                'name_ar' => 'سير عمل طلب المصروفات',
                'description' => 'سير عمل لموافقة طلبات المصروفات',
                'type' => 'expense_request',
                'is_active' => true,
                'created_by' => $createdBy,
                'steps' => [
                    ['name' => 'Manager Approval', 'name_ar' => 'موافقة المدير', 'approver_type' => 'employee_manager', 'step_order' => 1],
                    ['name' => 'Finance Approval', 'name_ar' => 'موافقة المالية', 'approver_type' => 'role', 'step_order' => 2],
                ]
            ],
        ];

        foreach ($workflows as $workflowData) {
            $steps = $workflowData['steps'];
            unset($workflowData['steps']);

            $workflow = Workflow::firstOrCreate(
                ['name' => $workflowData['name']],
                $workflowData
            );

            foreach ($steps as $stepData) {
                WorkflowStep::firstOrCreate(
                    [
                        'workflow_id' => $workflow->id,
                        'step_order' => $stepData['step_order']
                    ],
                    array_merge($stepData, [
                        'workflow_id' => $workflow->id,
                        'is_required' => true,
                    ])
                );
            }
        }

        $this->command->info('✅ تم إنشاء سير العمل بنجاح!');
    }
}
