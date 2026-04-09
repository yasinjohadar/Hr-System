<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * منع المستخدمين بدور الموظف من الوصول إلى لوحة الإدارة.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        // من لديه دور موظف فقط (بدون أي دور يسمح بالوصول للإدارة) يُوجّه إلى لوحة الموظف
        if ($user->hasRole('employee') && ! $user->hasAnyRole(['admin', 'user', 'department_head'])) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'ليس لديك صلاحية الدخول إلى لوحة الإدارة.');
        }

        return $next($request);
    }
}
