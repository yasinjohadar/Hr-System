<?php

namespace App\Providers;

use App\Models\Employee;
use App\Policies\EmployeePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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

        // فرض HTTPS خارج بيئة التطوير — يمنع إرسال كوكي الجلسة عبر اتصال غير مشفّر
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(Employee::class, EmployeePolicy::class);

        $this->configureRateLimiters();

        // تسجيل PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);
    }

    /**
     * مُحدِّدات معدّل الطلبات المُسمّاة.
     *
     * مهم: عند استخدام throttle مباشرةً (مثل 'throttle:5,1') لا يدخل مسار الطلب
     * في مفتاح العدّاد، فتتشارك كل المسارات المجهولة عدّاداً واحداً لكل IP.
     * لذلك نُعرّف مُحدِّدات مُسمّاة بمفاتيح صريحة ومنفصلة.
     */
    protected function configureRateLimiters(): void
    {
        // استهلاك كود الدخول لمرة واحدة. لا نحتسب تحميل الصفحة نفسها —
        // فقط الطلبات التي تحمل كوداً فعلياً — حتى لا يُستهلك الحد بفتح الصفحة.
        RateLimiter::for('login-code', function (Request $request) {
            if (! $request->filled('code')) {
                return Limit::none();
            }

            return Limit::perMinute(5)->by('login-code|'.$request->ip());
        });

        // التسجيل الذاتي: 5 محاولات كل 10 دقائق لكل IP.
        RateLimiter::for('register', fn (Request $request) => Limit::perMinutes(10, 5)->by('register|'.$request->ip()));

        // مسارات التحقق الثنائي: عدّاد مستقل لكل مسار حتى لا يُقفل «التعطيل»
        // بسبب محاولات فاشلة على «التحقق».
        RateLimiter::for('two-factor', function (Request $request) {
            $identifier = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(5)->by('2fa|'.$request->route()?->getName().'|'.$identifier);
        });
    }
}