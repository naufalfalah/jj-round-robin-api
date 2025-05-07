<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotClient
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('web')->check() && !Auth::guard('admin')->check()) {
            // User is a client, proceed with request
            return $next($request);
        }
        // User is not a client, redirect to admin dashboard
        return redirect()->route('admin.home');
    }
}
