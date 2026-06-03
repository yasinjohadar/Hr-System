<?php

/**
 * ربط أقسام السايدبار بمسارات التطبيق (لتجنب وميض القوائم عند التحميل).
 */
return [
    'admin_center' => [
        'routes' => ['admin.dashboard', 'admin.dashboard.*', 'roles.*', 'users.*', 'admin.announcements.*', 'admin.department-heads.*'],
        'paths' => ['admin', 'roles', 'roles/*', 'users', 'users/*'],
    ],
    'team' => [
        'routes' => ['admin.team.*'],
        'paths' => ['admin/team', 'admin/team/*'],
    ],
    'hr' => [
        'routes' => ['admin.employees.*', 'admin.contracts.*', 'admin.employee-job-changes.*', 'admin.policies.*', 'admin.salaries.*', 'admin.payroll*', 'admin.employee-advances.*'],
        'paths' => ['admin/employees*', 'admin/contracts*', 'admin/employee-job-changes*', 'admin/policies*', 'admin/salaries*', 'admin/payroll*', 'admin/employee-advances*', 'admin/salary-components*', 'admin/tax-settings*', 'admin/employee-bank-accounts*', 'admin/payroll-payments*', 'admin/payroll-approvals*'],
    ],
    'leave' => [
        'routes' => ['admin.leave-types.*', 'admin.leave-requests.*', 'admin.leave-balances.*'],
        'paths' => ['admin/leave-*'],
    ],
    'attendance' => [
        'routes' => ['admin.attendances.*', 'admin.shifts.*', 'admin.shift-assignments.*', 'admin.attendance-rules.*', 'admin.overtimes.*', 'admin.attendance-locations.*', 'admin.attendance-breaks.*', 'admin.performance-reviews.*', 'admin.trainings.*', 'admin.training-records.*'],
        'paths' => ['admin/attendances*', 'admin/shifts*', 'admin/shift-assignments*', 'admin/attendance-*', 'admin/overtimes*', 'admin/performance-reviews*', 'admin/trainings*', 'admin/training-records*'],
    ],
    'recruitment' => [
        'routes' => ['admin.requisitions.*', 'admin.job-vacancies.*', 'admin.candidates.*', 'admin.job-applications.*', 'admin.interviews.*', 'admin.offer-letters.*'],
        'paths' => ['admin/requisitions*', 'admin/job-vacancies*', 'admin/candidates*', 'admin/job-applications*', 'admin/interviews*', 'admin/offer-letters*'],
    ],
    'benefits' => [
        'routes' => ['admin.benefit-types.*', 'admin.employee-benefits.*'],
        'paths' => ['admin/benefit-*', 'admin/employee-benefits*'],
    ],
    'reports' => [
        'routes' => ['admin.reports.*'],
        'paths' => ['admin/reports', 'admin/reports/*'],
    ],
    'settings' => [
        'routes' => ['admin.settings.*'],
        'paths' => ['admin/settings*'],
    ],
    'employee_advanced' => [
        'routes' => ['admin.employee-documents.*', 'admin.employee-skills.*', 'admin.employee-certificates.*', 'admin.employee-goals.*', 'admin.employee-exits.*'],
        'paths' => ['admin/employee-documents*', 'admin/employee-skills*', 'admin/employee-certificates*', 'admin/employee-goals*', 'admin/employee-exits*'],
    ],
    'assets' => [
        'routes' => ['admin.assets.*', 'admin.asset-assignments.*', 'admin.asset-maintenances.*'],
        'paths' => ['admin/assets*', 'admin/asset-*'],
    ],
    'expenses' => [
        'routes' => ['admin.expense-categories.*', 'admin.expense-requests.*'],
        'paths' => ['admin/expense-*'],
    ],
    'disciplinary' => [
        'routes' => ['admin.violation-types.*', 'admin.disciplinary-actions.*', 'admin.employee-violations.*'],
        'paths' => ['admin/violation-*', 'admin/disciplinary-*', 'admin/employee-violations*'],
    ],
    'projects' => [
        'routes' => ['admin.projects.*', 'admin.tasks.*'],
        'paths' => ['admin/projects*', 'admin/tasks*'],
    ],
    'org_view' => [
        'routes' => ['admin.organization-chart.*', 'admin.employee-directory.*'],
        'paths' => ['admin/organization-chart*', 'admin/employee-directory*'],
    ],
    'advanced' => [
        'routes' => ['admin.workflows.*', 'admin.succession-plans.*', 'admin.tickets.*', 'admin.meetings.*', 'admin.feedback-requests.*', 'admin.reward-types.*', 'admin.employee-rewards.*'],
        'paths' => ['admin/workflows*', 'admin/succession-*', 'admin/tickets*', 'admin/meetings*', 'admin/feedback-*', 'admin/reward-*', 'admin/employee-rewards*'],
    ],
    'extra' => [
        'routes' => ['admin.onboarding-*', 'admin.audit-logs.*', 'admin.surveys.*', 'admin.email-templates.*', 'admin.document-templates.*', 'admin.departments.*', 'admin.positions.*', 'admin.branches.*'],
        'paths' => ['admin/onboarding-*', 'admin/audit-logs*', 'admin/surveys*', 'admin/email-templates*', 'admin/document-templates*', 'admin/departments*', 'admin/positions*', 'admin/branches*'],
    ],
    'calendar' => [
        'routes' => ['admin.calendar-*', 'admin.calendar.*'],
        'paths' => ['admin/calendar-*', 'admin/calendar*'],
    ],
    'geo' => [
        'routes' => ['admin.countries.*', 'admin.currencies.*'],
        'paths' => ['admin/countries*', 'admin/currencies*'],
    ],
];
