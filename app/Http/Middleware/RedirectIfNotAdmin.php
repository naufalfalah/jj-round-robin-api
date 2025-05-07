<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check() && !Auth::guard('web')->check()) {
            // User is an admin, proceed with request
            return $next($request);
        }

        // User is not an admin, redirect to client dashboard
        return redirect()->route('user.dashboard');
    }
}
