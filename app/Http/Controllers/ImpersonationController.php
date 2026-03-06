<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * إنهاء الدخول كموظف والعودة لحساب الإدارة.
     */
    public function leave(Request $request)
    {
        $impersonatorId = session('impersonator_id');
        if (!$impersonatorId) {
            return redirect()->route('employee.dashboard')->with('info', 'لا توجد جلسة دخول كموظف.');
        }

        session()->forget('impersonator_id');
        Auth::logout();
        $impersonator = User::find($impersonatorId);
        if ($impersonator) {
            Auth::login($impersonator);
        }

        return redirect()->route('admin.dashboard')->with('success', 'تم استعادة حساب الإدارة.');
    }
}
