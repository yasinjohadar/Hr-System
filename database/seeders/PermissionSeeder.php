<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // صلاحيات الأدوار
            "role-list",
            "role-create",
            "role-edit",
            "role-delete",

            // صلاحيات المستخدمين
            "user-list",
            "user-create",
            "user-edit",
            "user-delete",
            "user-show",

            // صلاحيات الموظفين
            "employee-list",
            "employee-create",
            "employee-edit",
            "employee-delete",
            "employee-show",

            // صلاحيات الأقسام
            "department-list",
            "department-create",
            "department-edit",
            "department-delete",
            "department-show",

            // صلاحيات المناصب
            "position-list",
            "position-create",
            "position-edit",
            "position-delete",
            "position-show",

            // صلاحيات الفروع
            "branch-list",
            "branch-create",
            "branch-edit",
            "branch-delete",
            "branch-show",

            // صلاحيات الدول
            "country-list",
            "country-create",
            "country-edit",
            "country-delete",
            "country-show",

            // صلاحيات العملات
            "currency-list",
            "currency-create",
            "currency-edit",
            "currency-delete",
            "currency-show",

            // صلاحيات المناصب
            "position-list",
            "position-create",
            "position-edit",
            "position-delete",
            "position-show",

            // صلاحيات الرواتب
            "salary-list",
            "salary-create",
            "salary-edit",
            "salary-delete",
            "salary-show",

            // صلاحيات أنواع الإجازات
            "leave-type-list",
            "leave-type-create",
            "leave-type-edit",
            "leave-type-delete",
            "leave-type-show",

            // صلاحيات طلبات الإجازات
            "leave-request-list",
            "leave-request-create",
            "leave-request-edit",
            "leave-request-delete",
            "leave-request-show",
            "leave-request-approve",

            // صلاحيات أرصدة الإجازات
            "leave-balance-list",
            "leave-balance-create",
            "leave-balance-edit",
            "leave-balance-delete",
            "leave-balance-show",

            // صلاحيات الحضور والانصراف
            "attendance-list",
            "attendance-create",
            "attendance-edit",
            "attendance-delete",
            "attendance-show",

            // صلاحيات التقييمات
            "performance-review-list",
            "performance-review-create",
            "performance-review-edit",
            "performance-review-delete",
            "performance-review-show",
            "performance-review-approve",

            // صلاحيات التدريب
            "training-list",
            "training-create",
            "training-edit",
            "training-delete",
            "training-show",
            "training-record-list",
            "training-record-create",
            "training-record-edit",
            "training-record-delete",
            "training-record-show",

            // صلاحيات التوظيف
            "job-vacancy-list",
            "job-vacancy-create",
            "job-vacancy-edit",
            "job-vacancy-delete",
            "job-vacancy-show",
            "candidate-list",
            "candidate-create",
            "candidate-edit",
            "candidate-delete",
            "candidate-show",
            "job-application-list",
            "job-application-create",
            "job-application-edit",
            "job-application-delete",
            "job-application-show",
            "interview-list",
            "interview-create",
            "interview-edit",
            "interview-delete",
            "interview-show",

            // صلاحيات عروض التعيين
            "offer-letter-list",
            "offer-letter-create",
            "offer-letter-edit",
            "offer-letter-delete",
            "offer-letter-show",

            // صلاحيات المزايا والتعويضات
            "benefit-type-list",
            "benefit-type-create",
            "benefit-type-edit",
            "benefit-type-delete",
            "benefit-type-show",
            "employee-benefit-list",
            "employee-benefit-create",
            "employee-benefit-edit",
            "employee-benefit-delete",
            "employee-benefit-show",

            // صلاحيات التقارير
            "report-view",
            "report-export",
            "report-create",

            // صلاحيات الإشعارات
            "notification-list",
            "notification-create",
            "notification-edit",
            "notification-delete",
            "notification-show",

            // صلاحيات الإعدادات
            "setting-view",
            "setting-edit",
            "setting-create",
            "setting-delete",

            // صلاحيات إدارة المستندات
            "employee-document-list",
            "employee-document-create",
            "employee-document-edit",
            "employee-document-delete",
            "employee-document-show",

            // صلاحيات إدارة المهارات
            "employee-skill-list",
            "employee-skill-create",
            "employee-skill-edit",
            "employee-skill-delete",
            "employee-skill-show",

            // صلاحيات إدارة الشهادات
            "employee-certificate-list",
            "employee-certificate-create",
            "employee-certificate-edit",
            "employee-certificate-delete",
            "employee-certificate-show",

            // صلاحيات إدارة الأهداف
            "employee-goal-list",
            "employee-goal-create",
            "employee-goal-edit",
            "employee-goal-delete",
            "employee-goal-show",

            // صلاحيات إدارة إنهاء الخدمة
            "employee-exit-list",
            "employee-exit-create",
            "employee-exit-edit",
            "employee-exit-delete",
            "employee-exit-show",

            // صلاحيات إدارة الأصول
            "asset-list",
            "asset-create",
            "asset-edit",
            "asset-delete",
            "asset-show",
            
            // صلاحيات توزيع الأصول
            "asset-assignment-list",
            "asset-assignment-create",
            "asset-assignment-edit",
            "asset-assignment-delete",
            "asset-assignment-show",
            
            // صلاحيات صيانة الأصول
            "asset-maintenance-list",
            "asset-maintenance-create",
            "asset-maintenance-edit",
            "asset-maintenance-delete",
            "asset-maintenance-show",

            // صلاحيات تصنيفات المصروفات
            "expense-category-list",
            "expense-category-create",
            "expense-category-edit",
            "expense-category-delete",
            "expense-category-show",

            // صلاحيات طلبات المصروفات
            "expense-request-list",
            "expense-request-create",
            "expense-request-edit",
            "expense-request-delete",
            "expense-request-show",
            "expense-request-approve",
            "expense-request-pay",

            // صلاحيات أنواع المخالفات
            "violation-type-list",
            "violation-type-create",
            "violation-type-edit",
            "violation-type-delete",
            "violation-type-show",

            // صلاحيات الإجراءات التأديبية
            "disciplinary-action-list",
            "disciplinary-action-create",
            "disciplinary-action-edit",
            "disciplinary-action-delete",
            "disciplinary-action-show",

            // صلاحيات مخالفات الموظفين
            "employee-violation-list",
            "employee-violation-create",
            "employee-violation-edit",
            "employee-violation-delete",
            "employee-violation-show",
            "employee-violation-investigate",
            "employee-violation-approve",

            // صلاحيات المشاريع
            "project-list",
            "project-create",
            "project-edit",
            "project-delete",
            "project-show",

            // صلاحيات المهام
            "task-list",
            "task-create",
            "task-edit",
            "task-delete",
            "task-show",
            "task-assign",
            "task-comment",

            // صلاحيات الهيكل التنظيمي
            "organization-chart-view",

            // صلاحيات دليل الموظفين
            "employee-directory-view",

            // صلاحيات سير العمل
            "workflow-list",
            "workflow-create",
            "workflow-edit",
            "workflow-delete",
            "workflow-show",

            // صلاحيات تخطيط التعاقب
            "succession-plan-list",
            "succession-plan-create",
            "succession-plan-edit",
            "succession-plan-delete",
            "succession-plan-show",

            // صلاحيات التذاكر
            "ticket-list",
            "ticket-create",
            "ticket-edit",
            "ticket-delete",
            "ticket-show",
            "ticket-assign",

            // صلاحيات الاجتماعات
            "meeting-list",
            "meeting-create",
            "meeting-edit",
            "meeting-delete",
            "meeting-show",

            // صلاحيات التقييم 360 درجة
            "feedback-request-list",
            "feedback-request-create",
            "feedback-request-edit",
            "feedback-request-delete",
            "feedback-request-show",

            // صلاحيات أنواع المكافآت
            "reward-type-list",
            "reward-type-create",
            "reward-type-edit",
            "reward-type-delete",
            "reward-type-show",

            // صلاحيات مكافآت الموظفين
            "employee-reward-list",
            "employee-reward-create",
            "employee-reward-edit",
            "employee-reward-delete",
            "employee-reward-show",
            "employee-reward-award",

            // صلاحيات عملية الاستقبال
            "onboarding-template-list",
            "onboarding-template-create",
            "onboarding-template-edit",
            "onboarding-template-delete",
            "onboarding-template-show",
            "onboarding-process-list",
            "onboarding-process-create",
            "onboarding-process-edit",
            "onboarding-process-delete",
            "onboarding-process-show",

            // صلاحيات سجلات التدقيق
            "audit-log-list",
            "audit-log-show",
            "audit-log-export",

            // صلاحيات الاستبيانات
            "survey-list",
            "survey-create",
            "survey-edit",
            "survey-delete",
            "survey-show",

            // صلاحيات قوالب البريد
            "email-template-list",
            "email-template-create",
            "email-template-edit",
            "email-template-delete",
            "email-template-show",

            // صلاحيات قوالب المستندات
            "document-template-list",
            "document-template-create",
            "document-template-edit",
            "document-template-delete",
            "document-template-show",

            // صلاحيات نظام الرواتب المتقدم
            "payroll-list",
            "payroll-create",
            "payroll-edit",
            "payroll-delete",
            "payroll-show",
            "salary-component-list",
            "salary-component-create",
            "salary-component-edit",
            "salary-component-delete",
            "salary-component-show",
            "tax-setting-list",
            "tax-setting-create",
            "tax-setting-edit",
            "tax-setting-delete",
            "tax-setting-show",
            "bank-account-list",
            "bank-account-create",
            "bank-account-edit",
            "bank-account-delete",
            "bank-account-show",
            "payroll-payment-list",
            "payroll-payment-create",
            "payroll-payment-edit",
            "payroll-payment-delete",
            "payroll-payment-show",
            "attendance-location-list",
            "attendance-location-create",
            "attendance-location-edit",
            "attendance-location-delete",
            "attendance-location-show",
            "attendance-break-list",
            "attendance-break-create",
            "attendance-break-edit",
            "attendance-break-delete",
            "attendance-break-show",
            "payroll-approval-list",
            "payroll-approval-create",
            "payroll-approval-edit",
            "payroll-approval-delete",
            "payroll-approval-show",

            // صلاحيات التقويم
            "calendar-list",
            "calendar-create",
            "calendar-edit",
            "calendar-delete",
            "calendar-show",
            "calendar-edit-all",
            "calendar-delete-all",

            // صلاحيات التصدير
            "export-data",

            // صلاحيات نظام الحضور المتقدم
            "shift-list",
            "shift-create",
            "shift-edit",
            "shift-delete",
            "shift-show",
            "shift-assignment-list",
            "shift-assignment-create",
            "shift-assignment-edit",
            "shift-assignment-delete",
            "shift-assignment-show",
            "attendance-rule-list",
            "attendance-rule-create",
            "attendance-rule-edit",
            "attendance-rule-delete",
            "attendance-rule-show",
            "overtime-list",
            "overtime-create",
            "overtime-edit",
            "overtime-delete",
            "overtime-show",

            // صلاحيات إضافية للنظام
            "dashboard-view",
            "settings-manage",
            "reports-view",
        ];

        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
