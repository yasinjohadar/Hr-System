<?php

namespace App\Providers;

use App\Models\Employee;
use App\Policies\EmployeePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $sidebarHelpers = app_path('Support/sidebar_helpers.php');
        if (is_file($sidebarHelpers)) {
            require_once $sidebarHelpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(Employee::class, EmployeePolicy::class);

        // تسجيل PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);
    }
}