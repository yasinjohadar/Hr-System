<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // throttle: التسجيل الذاتي مفتوح للعامة، فيُقيَّد لمنع إنشاء حسابات آلي.
    // مُحدِّد مُسمّى بمفتاح مستقل حتى لا يتشارك العدّاد مع بقية مسارات الزوار.
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:register');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('two-factor/challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::get('two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');

    // throttle على كل مسار يستهلك كوداً سرياً — بدونه يمكن تخمين رمز الـ 6 أرقام
    // أو أحد رموز الاستعادة الثمانية بعدد محاولات غير محدود.
    // المُحدِّد 'two-factor' يفصل العدّاد لكل مسار على حدة (AppServiceProvider).
    Route::post('two-factor/verify', [TwoFactorController::class, 'verify'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.verify');
    Route::post('two-factor/confirm', [TwoFactorController::class, 'confirmSetup'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.confirm');
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.disable');
});
