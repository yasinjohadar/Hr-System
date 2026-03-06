<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return Auth::user()->hasRole('employee')
        ? redirect()->route('employee.dashboard')
        : redirect()->route('admin.dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'check.user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('leave-impersonation', [ImpersonationController::class, 'leave'])->name('leave-impersonation');

    // Admin routes
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::put('users/{user}/change-password', [UserController::class, 'updatePassword'])->name('users.update-password');
});

// مسار toggle-status بدون middleware check.user.active
Route::middleware(['auth'])->group(function () {
    Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

// مسار بديل للتجربة
Route::post('toggle-user-status/{id}', [UserController::class, 'toggleStatus'])->name('users.toggle-status-alt');

require __DIR__.'/auth.php';
require __DIR__.'/employee.php';
require __DIR__.'/admin.php';

// Broadcasting authentication route
Route::middleware(['auth'])->post('/broadcasting/auth', function () {
    return Broadcast::auth(request());
})->name('broadcasting.auth');
