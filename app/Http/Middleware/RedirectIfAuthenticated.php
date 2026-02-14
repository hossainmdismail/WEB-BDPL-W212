<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{

    public function handle(Request $request, Closure $next, ...$guards)
    {
        // $guards = empty($guards) ? [null] : $guards;

        // foreach ($guards as $guard) {

        //     if ($guard == "user" && Auth::guard($guard)->check()) {
        //         return redirect()->route('dashboard');
        //     }
        // }
        // return $next($request);
        // if no guard passed, check default Auth::check()
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (is_null($guard)) {
                if (Auth::check()) {
                    return redirect()->route('dashboard'); // default user dashboard
                }
            } else {
                if (Auth::guard($guard)->check()) {
                    // direct guards to their dashboards (customize names as needed)
                    if ($guard === 'admin') {
                        return redirect()->route('admin.dashboard');
                    }
                    if ($guard === 'customer') {
                        return redirect()->route('customer.dashboard');
                    }
                    // fallback for any other guard
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}
