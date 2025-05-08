<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccessibleMenu
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $menu)
    {
        $accessibleMenus = auth('api')->user()->getAccessibleMenus();

        if (!in_array($menu, $accessibleMenus)) {
            return response()->json(['error' => 'Menu is not accessible'], 401);
        }

        return $next($request);
    }
}
