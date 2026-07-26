<?php

use App\Models\EmployeeJobChange;
use App\Models\ExpenseRequest;
use App\Models\FundTransfer;
use App\Models\LeaveRequest;
use App\Models\ProjectTimeEntry;
use App\Models\Ticket;

return [
    /*
    |--------------------------------------------------------------------------
    | Employee request types that use WorkflowInstance approval chains.
    |--------------------------------------------------------------------------
    */
    'types' => [
        'leave_request' => [
            'label_ar' => 'طلب إجازة',
            'model' => LeaveRequest::class,
            'status_field' => 'status',
            'pending_values' => ['pending'],
            'team_filter' => 'leave',
            'with' => ['employee.user', 'employee.department', 'employee.position', 'leaveType'],
            'show_route' => 'admin.leave-requests.show',
            'approve_route' => 'admin.leave-requests.approve',
            'reject_route' => 'admin.leave-requests.reject',
        ],
        'expense_request' => [
            'label_ar' => 'طلب مصروف',
            'model' => ExpenseRequest::class,
            'status_field' => 'status',
            'pending_values' => ['pending'],
            'team_filter' => 'expense',
            'with' => ['employee.user', 'employee.department', 'employee.position', 'category', 'currency'],
            'show_route' => 'admin.expense-requests.show',
            'approve_route' => 'admin.expense-requests.approve',
            'reject_route' => 'admin.expense-requests.reject',
        ],
        'employee_job_change' => [
            'label_ar' => 'تغيير وظيفي',
            'model' => EmployeeJobChange::class,
            'status_field' => 'status',
            'pending_values' => ['pending'],
            'team_filter' => 'job_change',
            'with' => ['employee.user', 'employee.department', 'employee.position'],
            'show_route' => 'admin.employee-job-changes.show',
            'approve_route' => 'admin.employee-job-changes.approve',
            'reject_route' => 'admin.employee-job-changes.reject',
        ],
        'ticket_request' => [
            'label_ar' => 'تذكرة',
            'model' => Ticket::class,
            'status_field' => 'status',
            'pending_values' => ['pending_approval'],
            'team_filter' => 'ticket',
            'with' => ['employee.user', 'employee.department', 'employee.position'],
            'show_route' => 'admin.tickets.show',
            'approve_route' => null,
            'reject_route' => null,
        ],
        'project_time_entry' => [
            'label_ar' => 'وقت مشروع',
            'model' => ProjectTimeEntry::class,
            'status_field' => 'status',
            'pending_values' => ['pending'],
            'team_filter' => 'project_time',
            'with' => ['employee.user', 'employee.department', 'employee.position', 'project'],
            'show_route' => null,
            'approve_route' => null,
            'reject_route' => null,
        ],
        'fund_transfer' => [
            'label_ar' => 'تحويل مالي',
            'model' => FundTransfer::class,
            'status_field' => 'status',
            'pending_values' => ['pending'],
            'team_filter' => 'fund_transfer',
            'with' => ['requester', 'project', 'stage', 'currency'],
            'show_route' => 'admin.fund-transfers.show',
            'approve_route' => 'admin.fund-transfers.approve',
            'reject_route' => 'admin.fund-transfers.reject',
        ],
    ],

    /**
     * Block submission when department has no manager (step 1 department_manager).
     */
    'require_department_manager' => true,
];
