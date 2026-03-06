<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployee
{
    /**
     * تقييد لوحة الموظف بمن لديهم دور الموظف فقط.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->hasRole('employee')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'لوحة الموظف مخصصة للموظفين فقط.');
        }

        return $next($request);
    }
}
