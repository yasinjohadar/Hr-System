<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\SelfServiceController;

Route::middleware(['auth', 'check.user.active'])->prefix('employee')->name('employee.')->group(function () {
    // لوحة تحكم الموظف
    Route::get('/dashboard', [SelfServiceController::class, 'dashboard'])->name('dashboard');
    
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
    
    // المشاريع
    Route::get('/projects', [SelfServiceController::class, 'projects'])->name('projects');
    
    // التذاكر
    Route::get('/tickets', [SelfServiceController::class, 'tickets'])->name('tickets');
    
    // الاجتماعات
    Route::get('/meetings', [SelfServiceController::class, 'meetings'])->name('meetings');
    
    // طلبات المصروفات
    Route::get('/expense-requests', [SelfServiceController::class, 'expenseRequests'])->name('expense-requests');
    
    // الأصول المعينة
    Route::get('/assets', [SelfServiceController::class, 'assets'])->name('assets');
    
    // المخالفات
    Route::get('/violations', [SelfServiceController::class, 'violations'])->name('violations');
});
