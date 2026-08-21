<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login', [
            'quickLoginAccounts' => $this->quickLoginAccounts(),
        ]);
    }

    /**
     * حسابات الدخول السريع لبيئة التطوير (مصفوفة فارغة في أي بيئة أخرى).
     *
     * لا نثبّت البريد في الإعدادات: نستخرج أول حساب نشط لكل دور من قاعدة
     * البيانات، فتبقى الأزرار صحيحة بعد إعادة تشغيل الـ seeders.
     */
    private function quickLoginAccounts(): array
    {
        if (! app()->environment('local') || ! config('dev.quick_login.enabled')) {
            return [];
        }

        $password = config('dev.quick_login.password');

        return collect(config('dev.quick_login.accounts', []))
            ->map(function (array $account) use ($password) {
                // الأقل أدواراً أولاً: يعطي حساباً "نقياً" لاختبار الدور المطلوب وحده
                $user = User::query()
                    ->where('is_active', true)
                    ->whereHas('roles', fn ($q) => $q->where('name', $account['role']))
                    ->withCount('roles')
                    ->orderBy('roles_count')
                    ->orderBy('id')
                    ->first();

                if (! $user) {
                    return null;
                }

                return $account + [
                    'email'    => $user->email,
                    'user'     => $user->name,
                    'password' => $password,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // التحقق من أن المستخدم نشط
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'تم إلغاء تفعيل حسابك. يرجى التواصل مع الإدارة.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->forget('two_factor_passed');

        $user = Auth::user();
        if ($user->requiresTwoFactor()) {
            return redirect()->route('two-factor.challenge');
        }

        if ($user->hasRole('employee')) {
            return redirect()->intended(route('employee.dashboard', absolute: false));
        }
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
