<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDepartmentHeadOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (! $user || (! $user->hasRole('admin') && ! $user->isDepartmentHead())) {
            abort(403, 'ليس لديك صلاحية الوصول إلى إدارة الفريق.');
        }

        return $next($request);
    }
}
