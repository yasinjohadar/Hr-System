<?php

/**
 * قوالب صلاحيات الأدوار — تُطبَّق عبر RoleController::applyTemplate
 */
return [
    'department_head' => [
        'label' => 'رئيس قسم',
        'permissions' => [
            'employee-list',
            'employee-show',
            'leave-request-list',
            'leave-request-show',
            'leave-request-approve',
            'attendance-list',
            'attendance-show',
            'expense-request-list',
            'expense-request-show',
            'expense-request-approve',
            'performance-review-list',
            'performance-review-show',
            'performance-review-approve',
            'approval-list',
            'approval-show',
            'employee-job-change-list',
            'employee-job-change-show',
            'report-view',
            'report-employees',
            'report-attendance',
            'report-leaves',
            'report-performance',
            'notification-list',
            'notification-mark-read',
            'dashboard-view',
        ],
        'restricted_for_ui' => [
            'role-', 'user-create', 'user-delete', 'user-toggle-status',
            'settings-manage', 'payroll-', 'salary-', 'department-create', 'department-delete',
        ],
    ],
    'hr_manager' => [
        'label' => 'مدير موارد بشرية',
        'permissions' => [
            'dashboard-view',
            'employee-list', 'employee-create', 'employee-edit', 'employee-show',
            'department-list', 'department-show',
            'position-list', 'position-show',
            'leave-request-list', 'leave-request-show', 'leave-request-approve',
            'attendance-list', 'attendance-show',
            'report-view', 'report-employees', 'report-attendance', 'report-leaves',
            'notification-list', 'notification-mark-read',
        ],
        'restricted_for_ui' => ['role-', 'settings-manage', 'payroll-'],
    ],
    'payroll_officer' => [
        'label' => 'مسؤول رواتب',
        'permissions' => [
            'dashboard-view',
            'employee-list', 'employee-show',
            'salary-list', 'salary-show',
            'payroll-list', 'payroll-show',
            'payroll-payment-list',
            'report-view', 'report-salaries',
            'notification-list', 'notification-mark-read',
        ],
        'restricted_for_ui' => ['role-', 'user-', 'department-create'],
    ],
];
