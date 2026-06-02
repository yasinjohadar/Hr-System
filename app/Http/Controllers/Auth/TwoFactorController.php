<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function challenge(): View|RedirectResponse
    {
        if (! Auth::check() || ! Auth::user()->requiresTwoFactor()) {
            return redirect()->route('admin.dashboard');
        }

        if (session('two_factor_passed')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $user = Auth::user();
        if (! $user || ! $user->two_factor_secret) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA;
        $valid = $google2fa->verifyKey(
            $user->two_factor_secret,
            $request->input('code')
        );

        if (! $valid && $user->two_factor_recovery_codes) {
            $codes = $user->two_factor_recovery_codes;
            $index = array_search($request->input('code'), $codes, true);
            if ($index !== false) {
                unset($codes[$index]);
                $user->two_factor_recovery_codes = array_values($codes);
                $user->save();
                $valid = true;
            }
        }

        if (! $valid) {
            return back()->withErrors(['code' => 'رمز التحقق غير صحيح.']);
        }

        $request->session()->put('two_factor_passed', true);

        return redirect()->intended(
            $user->hasRole('employee') && ! $user->hasAnyRole(['admin', 'user', 'department_head'])
                ? route('employee.dashboard')
                : route('admin.dashboard')
        );
    }

    public function setup(): View
    {
        $user = Auth::user();
        $google2fa = new Google2FA;
        $secret = $google2fa->generateSecretKey();

        session(['two_factor_setup_secret' => $secret]);

        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.two-factor-setup', compact('qrUrl', 'secret'));
    }

    public function confirmSetup(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $secret = session('two_factor_setup_secret');
        if (! $secret) {
            return redirect()->route('profile.edit')->with('error', 'انتهت جلسة الإعداد.');
        }

        $google2fa = new Google2FA;
        if (! $google2fa->verifyKey($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'رمز التحقق غير صحيح.']);
        }

        $user = Auth::user();
        $recovery = collect(range(1, 8))->map(fn () => bin2hex(random_bytes(4)))->all();

        $user->two_factor_secret = $secret;
        $user->two_factor_recovery_codes = $recovery;
        $user->two_factor_confirmed_at = now();
        $user->save();

        session()->forget('two_factor_setup_secret');
        session(['two_factor_passed' => true]);

        return redirect()->route('profile.edit')->with('success', 'تم تفعيل المصادقة الثنائية.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password']);

        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $request->session()->forget('two_factor_passed');

        return back()->with('success', 'تم إيقاف المصادقة الثنائية.');
    }
}
