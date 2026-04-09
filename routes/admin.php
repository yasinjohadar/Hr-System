<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\EmployeeAdvanceController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\LeaveBalanceController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\PerformanceReviewController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\TrainingRecordController;
use App\Http\Controllers\Admin\RequisitionController;
use App\Http\Controllers\Admin\JobVacancyController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\JobApplicationController;
use App\Http\Controllers\Admin\OfferLetterController;
use App\Http\Controllers\Admin\InterviewController;
use App\Http\Controllers\Admin\BenefitTypeController;
use App\Http\Controllers\Admin\EmployeeBenefitController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\EmployeeDocumentController;
use App\Http\Controllers\Admin\EmployeeSkillController;
use App\Http\Controllers\Admin\EmployeeCertificateController;
use App\Http\Controllers\Admin\EmployeeGoalController;
use App\Http\Controllers\Admin\EmployeeExitController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetAssignmentController;
use App\Http\Controllers\Admin\AssetMaintenanceController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseRequestController;
use App\Http\Controllers\Admin\ViolationTypeController;
use App\Http\Controllers\Admin\DisciplinaryActionController;
use App\Http\Controllers\Admin\EmployeeViolationController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectMemberController;
use App\Http\Controllers\Admin\ProjectDocumentController;
use App\Http\Controllers\Admin\ProjectTimeEntryController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\OrganizationChartController;
use App\Http\Controllers\Admin\EmployeeDirectoryController;
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\Admin\SuccessionPlanController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\FeedbackRequestController;
use App\Http\Controllers\Admin\RewardTypeController;
use App\Http\Controllers\Admin\EmployeeRewardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OnboardingTemplateController;
use App\Http\Controllers\Admin\OnboardingProcessController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\SalaryComponentController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ShiftAssignmentController;
use App\Http\Controllers\Admin\AttendanceRuleController;
use App\Http\Controllers\Admin\OvertimeController;
use App\Http\Controllers\Admin\TaxSettingController;
use App\Http\Controllers\Admin\EmployeeBankAccountController;
use App\Http\Controllers\Admin\PayrollPaymentController;
use App\Http\Controllers\Admin\AttendanceLocationController;
use App\Http\Controllers\Admin\AttendanceBreakController;
use App\Http\Controllers\Admin\PayrollApprovalController;
use App\Http\Controllers\Admin\CalendarEventController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\EmployeeJobChangeController;
use App\Http\Controllers\Admin\PolicyController;

Route::middleware(['auth', 'check.user.active', 'ensure.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    // Routes لإعلانات الشركة
    Route::resource('announcements', AnnouncementController::class);
    // Routes لإدارة العقود
    Route::get('contracts/{contract}/renew', [ContractController::class, 'renew'])->name('contracts.renew');
    Route::post('contracts/{contract}/renew', [ContractController::class, 'storeRenew'])->name('contracts.store-renew');
    Route::resource('contracts', ContractController::class);
    // Routes للموظفين
    Route::post('employees/{employee}/login-code', [EmployeeController::class, 'generateLoginCode'])->name('employees.login-code');
    Route::post('employees/{employee}/login-as', [EmployeeController::class, 'loginAs'])->name('employees.login-as');
    Route::resource('employees', EmployeeController::class);

    // Routes للتغييرات الوظيفية (النقل والترقية)
    Route::post('employee-job-changes/{employee_job_change}/approve', [EmployeeJobChangeController::class, 'approve'])->name('employee-job-changes.approve');
    Route::post('employee-job-changes/{employee_job_change}/reject', [EmployeeJobChangeController::class, 'reject'])->name('employee-job-changes.reject');
    Route::resource('employee-job-changes', EmployeeJobChangeController::class)->except(['destroy']);

    // السياسات واللوائح والاعتراف بالمستندات
    Route::post('policies/{policy}/acknowledge', [PolicyController::class, 'acknowledge'])->name('policies.acknowledge');
    Route::resource('policies', PolicyController::class);
    Route::resource('departments', DepartmentController::class);
    
    // Routes للفروع
    Route::resource('branches', BranchController::class);
    
    // Routes للدول
    Route::resource('countries', CountryController::class);
    
    // Routes للعملات
    Route::resource('currencies', CurrencyController::class);
    
    // Routes للمناصب
    Route::resource('positions', PositionController::class);
    
    // Routes للرواتب
    Route::resource('salaries', SalaryController::class);

    Route::resource('employee-advances', EmployeeAdvanceController::class)->except(['show']);
    
    // Routes لأنواع الإجازات
    Route::resource('leave-types', LeaveTypeController::class);
    
    // Routes لطلبات الإجازات
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::post('leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    
    // Routes لأرصدة الإجازات
    Route::resource('leave-balances', LeaveBalanceController::class);
    
    // Routes للحضور والانصراف
    Route::resource('attendances', AttendanceController::class);
    Route::post('attendances/{employeeId}/check-in', [AttendanceController::class, 'checkIn'])->name('attendances.check-in');
    Route::post('attendances/{employeeId}/check-out', [AttendanceController::class, 'checkOut'])->name('attendances.check-out');
    
    // Routes للتقييمات
    Route::resource('performance-reviews', PerformanceReviewController::class);
    Route::post('performance-reviews/{id}/approve', [PerformanceReviewController::class, 'approve'])->name('performance-reviews.approve');
    Route::post('performance-reviews/{id}/reject', [PerformanceReviewController::class, 'reject'])->name('performance-reviews.reject');
    
    // Routes للتدريب
    Route::resource('trainings', TrainingController::class);
    Route::resource('training-records', TrainingRecordController::class);
    
    // Routes للتوظيف
    Route::resource('job-vacancies', JobVacancyController::class);
    Route::resource('candidates', CandidateController::class);
    Route::resource('job-applications', JobApplicationController::class);
    Route::post('offer-letters/{offer_letter}/send', [OfferLetterController::class, 'send'])->name('offer-letters.send');
    Route::post('offer-letters/{offer_letter}/accept', [OfferLetterController::class, 'accept'])->name('offer-letters.accept');
    Route::post('offer-letters/{offer_letter}/reject', [OfferLetterController::class, 'reject'])->name('offer-letters.reject');
    Route::resource('offer-letters', OfferLetterController::class);
    Route::post('requisitions/{requisition}/approve', [RequisitionController::class, 'approve'])->name('requisitions.approve');
    Route::post('requisitions/{requisition}/reject', [RequisitionController::class, 'reject'])->name('requisitions.reject');
    Route::resource('requisitions', RequisitionController::class);
    Route::resource('interviews', InterviewController::class);
    
    // Routes للمزايا والتعويضات
    Route::resource('benefit-types', BenefitTypeController::class);
    Route::resource('employee-benefits', EmployeeBenefitController::class);
    
    // Routes للتقارير
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/employees', [ReportController::class, 'employeesReport'])->name('employees');
        Route::get('/attendance', [ReportController::class, 'attendanceReport'])->name('attendance');
        Route::get('/salaries', [ReportController::class, 'salariesReport'])->name('salaries');
        Route::get('/leaves', [ReportController::class, 'leavesReport'])->name('leaves');
        Route::get('/performance', [ReportController::class, 'performanceReport'])->name('performance');
        Route::get('/training', [ReportController::class, 'trainingReport'])->name('training');
        Route::get('/recruitment', [ReportController::class, 'recruitmentReport'])->name('recruitment');
        Route::get('/benefits', [ReportController::class, 'benefitsReport'])->name('benefits');
        Route::get('/dashboard', [ReportController::class, 'dashboardReport'])->name('dashboard');
        Route::get('/turnover', [ReportController::class, 'turnoverReport'])->name('turnover');
        Route::get('/training-effectiveness', [ReportController::class, 'trainingEffectivenessReport'])->name('training-effectiveness');
        Route::get('/kpis', [ReportController::class, 'kpisReport'])->name('kpis');
    });
    
    // Routes للإشعارات
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/api/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/api/latest', [NotificationController::class, 'getLatest'])->name('latest');
    });
    
    // Routes للإعدادات
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::get('/{group}', [SettingController::class, 'show'])->name('group');
        Route::put('/{group}', [SettingController::class, 'updateGroup'])->name('update-group');
        Route::put('/{id}/update', [SettingController::class, 'update'])->name('update');
    });
    
    // Routes لإدارة المستندات
    Route::resource('employee-documents', EmployeeDocumentController::class);
    Route::get('employee-documents/{id}/download', [EmployeeDocumentController::class, 'download'])->name('employee-documents.download');
    
    // Routes لإدارة المهارات
    Route::resource('employee-skills', EmployeeSkillController::class);
    Route::post('employee-skills/{id}/verify', [EmployeeSkillController::class, 'verify'])->name('employee-skills.verify');
    
    // Routes لإدارة الشهادات
    Route::resource('employee-certificates', EmployeeCertificateController::class);
    
    // Routes لإدارة الأهداف
    Route::resource('employee-goals', EmployeeGoalController::class);
    
    // Routes لإدارة إنهاء الخدمة
    Route::resource('employee-exits', EmployeeExitController::class);
    Route::post('employee-exits/{id}/complete-interview', [EmployeeExitController::class, 'completeExitInterview'])->name('employee-exits.complete-interview');
    Route::post('employee-exits/{id}/approve', [EmployeeExitController::class, 'approve'])->name('employee-exits.approve');
    
    // Routes لإدارة الأصول
    Route::post('assets/{asset}/lifecycle-events', [AssetController::class, 'storeLifecycleEvent'])->name('assets.lifecycle-events.store');
    Route::resource('assets', AssetController::class);
    Route::resource('asset-assignments', AssetAssignmentController::class);
    Route::get('asset-assignments/{id}/return', [AssetAssignmentController::class, 'showReturnForm'])->name('asset-assignments.return-form');
    Route::post('asset-assignments/{id}/return', [AssetAssignmentController::class, 'return'])->name('asset-assignments.return');
    Route::resource('asset-maintenances', AssetMaintenanceController::class);
    
    // Routes لإدارة المصروفات
    Route::resource('expense-categories', ExpenseCategoryController::class);
    Route::resource('expense-requests', ExpenseRequestController::class);
    Route::get('expense-requests/{id}/approve', [ExpenseRequestController::class, 'showApproveForm'])->name('expense-requests.approve-form');
    Route::post('expense-requests/{id}/approve', [ExpenseRequestController::class, 'approve'])->name('expense-requests.approve');
    Route::post('expense-requests/{id}/reject', [ExpenseRequestController::class, 'reject'])->name('expense-requests.reject');
    Route::post('expense-requests/{id}/pay', [ExpenseRequestController::class, 'markAsPaid'])->name('expense-requests.pay');

    // Routes لأنواع المخالفات
    Route::resource('violation-types', ViolationTypeController::class);

    // Routes للإجراءات التأديبية
    Route::resource('disciplinary-actions', DisciplinaryActionController::class);

    // Routes لمخالفات الموظفين
    Route::resource('employee-violations', EmployeeViolationController::class);
    Route::post('employee-violations/{id}/investigate', [EmployeeViolationController::class, 'investigate'])->name('employee-violations.investigate');
    Route::post('employee-violations/{id}/confirm', [EmployeeViolationController::class, 'confirm'])->name('employee-violations.confirm');
    Route::post('employee-violations/{id}/dismiss', [EmployeeViolationController::class, 'dismiss'])->name('employee-violations.dismiss');
    Route::post('employee-violations/{id}/approve', [EmployeeViolationController::class, 'approve'])->name('employee-violations.approve');
    Route::post('employee-violations/{id}/apply-action', [EmployeeViolationController::class, 'applyAction'])->name('employee-violations.apply-action');

    // Routes للمشاريع
    Route::get('projects/{project}/time-entries/export', [ProjectController::class, 'exportTimeEntries'])->name('projects.time-entries.export');
    Route::post('projects/{project}/members', [ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::delete('projects/{project}/members/{member}', [ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');
    Route::post('projects/{project}/documents', [ProjectDocumentController::class, 'store'])->name('projects.documents.store');
    Route::delete('projects/{project}/documents/{document}', [ProjectDocumentController::class, 'destroy'])->name('projects.documents.destroy');
    Route::post('projects/{project}/time-entries', [ProjectTimeEntryController::class, 'store'])->name('projects.time-entries.store');
    Route::delete('projects/{project}/time-entries/{timeEntry}', [ProjectTimeEntryController::class, 'destroy'])->name('projects.time-entries.destroy');
    Route::resource('projects', ProjectController::class);

    // Routes للمهام
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{id}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::post('tasks/{taskId}/assignments/{assignmentId}/update', [TaskController::class, 'updateAssignment'])->name('tasks.update-assignment');
    Route::post('tasks/{id}/add-comment', [TaskController::class, 'addComment'])->name('tasks.add-comment');
    Route::post('tasks/{id}/upload-attachment', [TaskController::class, 'uploadAttachment'])->name('tasks.upload-attachment');
    Route::delete('tasks/{taskId}/attachments/{attachmentId}', [TaskController::class, 'deleteAttachment'])->name('tasks.delete-attachment');

    // Routes للهيكل التنظيمي
    Route::get('organization-chart', [OrganizationChartController::class, 'index'])->name('organization-chart.index');
    Route::get('organization-chart/get-data', [OrganizationChartController::class, 'getData'])->name('organization-chart.get-data');

    // Routes لدليل الموظفين
    Route::get('employee-directory', [EmployeeDirectoryController::class, 'index'])->name('employee-directory.index');
    Route::get('employee-directory/export', [EmployeeDirectoryController::class, 'export'])->name('employee-directory.export');

    // Routes لسير العمل
    Route::resource('workflows', WorkflowController::class);

    // Routes لتخطيط التعاقب
    Route::resource('succession-plans', SuccessionPlanController::class);

    // Routes للتذاكر
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{id}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{id}/resolve', [TicketController::class, 'resolve'])->name('tickets.resolve');

    // Routes للاجتماعات
    Route::resource('meetings', MeetingController::class);

    // Routes للتقييم 360 درجة
    Route::resource('feedback-requests', FeedbackRequestController::class);

    // Routes لأنواع المكافآت
    Route::resource('reward-types', RewardTypeController::class);

    // Routes لمكافآت الموظفين
    Route::resource('employee-rewards', EmployeeRewardController::class);
    Route::post('employee-rewards/{id}/award', [EmployeeRewardController::class, 'award'])->name('employee-rewards.award');

    // Routes لعملية الاستقبال
    Route::resource('onboarding-templates', OnboardingTemplateController::class);
    Route::resource('onboarding-processes', OnboardingProcessController::class);

    // Routes لسجلات التدقيق
    Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');

    // Routes للاستبيانات
    Route::resource('surveys', SurveyController::class);

    // Routes لقوالب البريد
    Route::resource('email-templates', EmailTemplateController::class);

    // Routes لقوالب المستندات
    Route::resource('document-templates', DocumentTemplateController::class);

    // Routes لنظام الرواتب المتقدم
    Route::get('payrolls/export-bank-file', [PayrollController::class, 'exportBankFile'])->name('payrolls.export-bank-file');
    Route::get('payrolls/{id}/payslip/pdf', [PayrollController::class, 'payslipPdf'])->name('payrolls.payslip.pdf');
    Route::resource('payrolls', PayrollController::class);
    Route::post('payrolls/{id}/calculate', [PayrollController::class, 'calculate'])->name('payrolls.calculate');
    Route::post('payrolls/{id}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
    Route::get('payrolls/{id}/payslip', [PayrollController::class, 'payslip'])->name('payrolls.payslip');
    
    Route::resource('salary-components', SalaryComponentController::class);

    // Routes لإعدادات الضرائب
    Route::resource('tax-settings', TaxSettingController::class);

    // Routes للحسابات البنكية
    Route::resource('bank-accounts', EmployeeBankAccountController::class);

    // Routes لسجلات الدفع
    Route::resource('payroll-payments', PayrollPaymentController::class);
    Route::post('payroll-payments/{id}/process', [PayrollPaymentController::class, 'process'])->name('payroll-payments.process');

    // Routes لمواقع الحضور (GPS)
    Route::resource('attendance-locations', AttendanceLocationController::class);
    
    // Routes لاستراحات الحضور
    Route::resource('attendance-breaks', AttendanceBreakController::class);
    
    // Routes لموافقات الرواتب
    Route::resource('payroll-approvals', PayrollApprovalController::class);
    Route::post('payroll-approvals/{id}/approve', [PayrollApprovalController::class, 'approve'])->name('payroll-approvals.approve');
    Route::post('payroll-approvals/{id}/reject', [PayrollApprovalController::class, 'reject'])->name('payroll-approvals.reject');

    // Routes للتقويم
    Route::resource('calendar-events', CalendarEventController::class);
    Route::get('calendar-events/api/events', [CalendarEventController::class, 'getEvents'])->name('calendar-events.api.events');
    
    // Routes لطلبات الموافقة
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/{type}/{id}', [ApprovalController::class, 'show'])->name('approvals.show');
    
    // Route للصفحة الرئيسية للتصدير
    Route::get('export', function () {
        return view('admin.pages.export.index');
    })->middleware('permission:export-data')->name('export.index');

    // Routes للتصدير
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('employees', [ExportController::class, 'employees'])->name('employees');
        Route::get('departments', [ExportController::class, 'departments'])->name('departments');
        Route::get('branches', [ExportController::class, 'branches'])->name('branches');
        Route::get('positions', [ExportController::class, 'positions'])->name('positions');
        Route::get('salaries', [ExportController::class, 'salaries'])->name('salaries');
        Route::get('leave-types', [ExportController::class, 'leaveTypes'])->name('leave-types');
        Route::get('leave-requests', [ExportController::class, 'leaveRequests'])->name('leave-requests');
        Route::get('leave-balances', [ExportController::class, 'leaveBalances'])->name('leave-balances');
        Route::get('attendances', [ExportController::class, 'attendances'])->name('attendances');
        Route::get('payrolls', [ExportController::class, 'payrolls'])->name('payrolls');
        Route::get('payroll-payments', [ExportController::class, 'payrollPayments'])->name('payroll-payments');
        Route::get('trainings', [ExportController::class, 'trainings'])->name('trainings');
        Route::get('training-records', [ExportController::class, 'trainingRecords'])->name('training-records');
        Route::get('performance-reviews', [ExportController::class, 'performanceReviews'])->name('performance-reviews');
        Route::get('job-vacancies', [ExportController::class, 'jobVacancies'])->name('job-vacancies');
        Route::get('candidates', [ExportController::class, 'candidates'])->name('candidates');
        Route::get('job-applications', [ExportController::class, 'jobApplications'])->name('job-applications');
        Route::get('interviews', [ExportController::class, 'interviews'])->name('interviews');
        Route::get('benefit-types', [ExportController::class, 'benefitTypes'])->name('benefit-types');
        Route::get('employee-benefits', [ExportController::class, 'employeeBenefits'])->name('employee-benefits');
        Route::get('expense-categories', [ExportController::class, 'expenseCategories'])->name('expense-categories');
        Route::get('expense-requests', [ExportController::class, 'expenseRequests'])->name('expense-requests');
        Route::get('assets', [ExportController::class, 'assets'])->name('assets');
        Route::get('asset-assignments', [ExportController::class, 'assetAssignments'])->name('asset-assignments');
        Route::get('asset-maintenances', [ExportController::class, 'assetMaintenances'])->name('asset-maintenances');
        Route::get('violation-types', [ExportController::class, 'violationTypes'])->name('violation-types');
        Route::get('disciplinary-actions', [ExportController::class, 'disciplinaryActions'])->name('disciplinary-actions');
        Route::get('employee-violations', [ExportController::class, 'employeeViolations'])->name('employee-violations');
        Route::get('projects', [ExportController::class, 'projects'])->name('projects');
        Route::get('tasks', [ExportController::class, 'tasks'])->name('tasks');
        Route::get('tickets', [ExportController::class, 'tickets'])->name('tickets');
        Route::get('meetings', [ExportController::class, 'meetings'])->name('meetings');
        Route::get('calendar-events', [ExportController::class, 'calendarEvents'])->name('calendar-events');
        Route::get('shifts', [ExportController::class, 'shifts'])->name('shifts');
        Route::get('overtimes', [ExportController::class, 'overtimes'])->name('overtimes');
        Route::get('bank-accounts', [ExportController::class, 'bankAccounts'])->name('bank-accounts');
        Route::get('tax-settings', [ExportController::class, 'taxSettings'])->name('tax-settings');
        Route::get('salary-components', [ExportController::class, 'salaryComponents'])->name('salary-components');
    });

    // Routes لنظام الحضور المتقدم
    Route::resource('shifts', ShiftController::class);
    Route::resource('shift-assignments', ShiftAssignmentController::class);
    Route::resource('attendance-rules', AttendanceRuleController::class);
    Route::resource('overtimes', OvertimeController::class);
    Route::post('overtimes/{id}/approve', [OvertimeController::class, 'approve'])->name('overtimes.approve');
    Route::post('overtimes/{id}/reject', [OvertimeController::class, 'reject'])->name('overtimes.reject');
    Route::post('overtimes/calculate-from-attendance', [OvertimeController::class, 'calculateFromAttendance'])->name('overtimes.calculate-from-attendance');
});