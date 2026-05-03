<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginByCodeController extends Controller
{
    /**
     * عرض صفحة إدخال الكود، أو استهلاك الكود مباشرة إذا وُجد في الرابط.
     */
    public function show(Request $request)
    {
        if ($request->filled('code')) {
            return $this->useCode($request);
        }

        return view('employee.login-by-code');
    }

    /**
     * استهلاك الكود وتسجيل الدخول بحساب الموظف.
     */
    public function useCode(Request $request)
    {
        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('employee.login-by-code')
                ->with('error', 'يرجى إدخال الكود.');
        }

        $code = trim($code);

        // Try employee code first
        $employeeCacheKey = 'employee_login_code:' . $code;
        $data = Cache::get($employeeCacheKey);

        if ($data && isset($data['employee_id'])) {
            $employee = Employee::with('user')->find($data['employee_id']);
            if (!$employee || !$employee->user_id || !$employee->user || !$employee->user->is_active) {
                Cache::forget($employeeCacheKey);
                return redirect()->route('employee.login-by-code')
                    ->with('error', 'حساب الموظف غير متوفر.');
            }
            Cache::forget($employeeCacheKey);
            Auth::login($employee->user);
            return redirect()->route('employee.dashboard')->with('success', 'تم تسجيل الدخول بنجاح.');
        }

        // Try user (admin) code
        $userCacheKey = 'user_login_code:' . $code;
        $data = Cache::get($userCacheKey);

        if ($data && isset($data['user_id'])) {
            $user = User::find($data['user_id']);
            if (!$user || !$user->is_active) {
                Cache::forget($userCacheKey);
                return redirect()->route('employee.login-by-code')
                    ->with('error', 'حساب المستخدم غير نشط أو غير متوفر.');
            }
            Cache::forget($userCacheKey);
            Auth::login($user);
            return redirect()->route('admin.dashboard')->with('success', 'تم تسجيل الدخول بنجاح.');
        }

        return redirect()->route('employee.login-by-code')
            ->with('error', 'الكود غير صالح أو منتهي الصلاحية.')
            ->withInput($request->only('code'));
    }
}
