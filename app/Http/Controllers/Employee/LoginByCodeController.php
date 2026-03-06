<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
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
        $cacheKey = 'employee_login_code:' . $code;
        $data = Cache::get($cacheKey);

        if (!$data || !isset($data['employee_id'])) {
            return redirect()->route('employee.login-by-code')
                ->with('error', 'الكود غير صالح أو منتهي الصلاحية.')
                ->withInput($request->only('code'));
        }

        $employee = Employee::with('user')->find($data['employee_id']);
        if (!$employee || !$employee->user_id || !$employee->user || !$employee->user->is_active) {
            Cache::forget($cacheKey);
            return redirect()->route('employee.login-by-code')
                ->with('error', 'حساب الموظف غير متوفر.');
        }

        Cache::forget($cacheKey);

        Auth::login($employee->user);

        return redirect()->route('employee.dashboard')->with('success', 'تم تسجيل الدخول بنجاح.');
    }
}
