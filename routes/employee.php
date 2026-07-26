<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\SelfServiceController;
use App\Http\Controllers\Employee\LoginByCodeController;

// الدخول بكود (متاح للجميع بدون تسجيل دخول)
// throttle إجباري: الكود يمنح دخولاً كاملاً (بما فيه حسابات الإدارة)، فبدون تقييد
// يمكن تخمينه بعدد محاولات غير محدود خلال مدة صلاحيته.
// المُحدِّد 'login-code' مُعرَّف في AppServiceProvider ويحتسب الطلبات الحاملة لكود فقط.
Route::prefix('employee')->name('employee.')->middleware('throttle:login-code')->group(function () {
    Route::get('/login-by-code', [LoginByCodeController::class, 'show'])->name('login-by-code');
    Route::post('/login-by-code', [LoginByCodeController::class, 'useCode'])->name('login-by-code.use');
});

Route::middleware(['auth', 'check.user.active', 'ensure.employee', 'two.factor'])->prefix('employee')->name('employee.')->group(function () {
    // لوحة تحكم الموظف
    Route::get('/dashboard', [SelfServiceController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/refresh', [SelfServiceController::class, 'dashboardRefresh'])->name('dashboard.refresh');
    Route::get('/dashboard/widgets/{widget}', [SelfServiceController::class, 'dashboardWidget'])->name('dashboard.widget');
    
    // التسلسل الإداري
    Route::get('/hierarchy', [SelfServiceController::class, 'hierarchy'])->name('hierarchy');
    
    // إعادة توجيه مسارات رئيس القسم القديمة إلى لوحة الإدارة
    Route::redirect('/department-head/dashboard', '/admin/team/dashboard', 301);
    Route::redirect('/department-head/team', '/admin/team/members', 301);
    Route::redirect('/department-head/approvals', '/admin/team/approvals', 301);
    Route::redirect('/department-head/structure', '/admin/team/structure', 301);
    Route::redirect('/department-head/delegations', '/admin/team/delegations', 301);
    Route::redirect('/department-head/delegations/create', '/admin/team/delegations/create', 301);
    
    // الملف الشخصي
    Route::get('/profile', [SelfServiceController::class, 'profile'])->name('profile');
    Route::put('/profile', [SelfServiceController::class, 'updateProfile'])->name('profile.update');
    
    // الإجازات
    Route::get('/leaves', [SelfServiceController::class, 'leaves'])->name('leaves');
    Route::post('/leaves/request', [SelfServiceController::class, 'requestLeave'])->name('leaves.request');
    
    // الحضور
    Route::get('/attendance', [SelfServiceController::class, 'attendance'])->name('attendance');
    
    // الرواتب
    Route::get('/salaries', [SelfServiceController::class, 'salaries'])->name('salaries');
    
    // المستندات
    Route::get('/documents', [SelfServiceController::class, 'documents'])->name('documents');
    
    // المهارات
    Route::get('/skills', [SelfServiceController::class, 'skills'])->name('skills');
    
    // الشهادات
    Route::get('/certificates', [SelfServiceController::class, 'certificates'])->name('certificates');
    
    // الأهداف
    Route::get('/goals', [SelfServiceController::class, 'goals'])->name('goals');
    
    // التقييمات
    Route::get('/performance-reviews', [SelfServiceController::class, 'performanceReviews'])->name('performance-reviews');
    
    // المزايا والتعويضات
    Route::get('/benefits', [SelfServiceController::class, 'benefits'])->name('benefits');
    
    // المهام
    Route::get('/tasks', [SelfServiceController::class, 'tasks'])->name('tasks');
    
    // المشاريع وسجلات الوقت
    Route::get('/projects', [SelfServiceController::class, 'projects'])->name('projects');
    Route::get('/project-time', [SelfServiceController::class, 'projectTimeIndex'])->name('project-time.index');
    Route::get('/projects/{project}', [SelfServiceController::class, 'showProject'])->name('projects.show');
    Route::post('/projects/{project}/time', [SelfServiceController::class, 'storeProjectTime'])->name('projects.time.store');
    
    // التذاكر
    Route::get('/tickets', [SelfServiceController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/create', [SelfServiceController::class, 'createTicket'])->name('tickets.create');
    Route::post('/tickets', [SelfServiceController::class, 'storeTicket'])->name('tickets.store');
    
    // الاجتماعات
    Route::get('/meetings', [SelfServiceController::class, 'meetings'])->name('meetings');
    
    // طلبات المصروفات
    Route::get('/expense-requests', [SelfServiceController::class, 'expenseRequests'])->name('expense-requests');
    Route::get('/expense-requests/create', [SelfServiceController::class, 'createExpenseRequest'])->name('expense-requests.create');
    Route::post('/expense-requests', [SelfServiceController::class, 'storeExpenseRequest'])->name('expense-requests.store');
    
    // الأصول المعينة
    Route::get('/assets', [SelfServiceController::class, 'assets'])->name('assets');
    
    // المخالفات
    Route::get('/violations', [SelfServiceController::class, 'violations'])->name('violations');
    
    // السياسات واللوائح والاعتراف
    Route::get('/policies', [SelfServiceController::class, 'policies'])->name('policies');
    Route::post('/policies/acknowledge', [SelfServiceController::class, 'acknowledgePolicy'])->name('policies.acknowledge');
    
    // عقد الموظف
    Route::get('/contract', [SelfServiceController::class, 'contract'])->name('contract');
    
    // قسيمة الراتب PDF
    Route::get('/payrolls/{id}/payslip/pdf', [SelfServiceController::class, 'payslipPdf'])->name('payrolls.payslip.pdf');
    
    // الإعلانات
    Route::get('/announcements', [SelfServiceController::class, 'announcements'])->name('announcements');
    
    // سجل التدريب
    Route::get('/training-records', [SelfServiceController::class, 'trainingRecords'])->name('training-records');

    // الاستبيانات
    Route::get('/surveys', [SelfServiceController::class, 'surveys'])->name('surveys');
    Route::post('/surveys/{id}', [SelfServiceController::class, 'submitSurvey'])->name('surveys.submit');

    // الاستقبال
    Route::get('/onboarding', [SelfServiceController::class, 'onboarding'])->name('onboarding');
    Route::post('/onboarding/checklist/{checklistId}/complete', [SelfServiceController::class, 'completeOnboardingTask'])->name('onboarding.complete-task');
});
