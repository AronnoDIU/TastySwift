<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = 'admin')
    {
        if (!Auth::guard($guard)->check()) {
            // Not logged in as admin, redirect to admin login
            return redirect()->route('admin.login')
                ->with('error', 'You must be logged in as an admin to access this page.');
        }

        // Check if the user is active
        $admin = Auth::guard($guard)->user();
        
        if (!$admin->isActive()) {
            Auth::guard($guard)->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account is not active. Please contact the administrator.');
        }

        // Check if email is verified if required
        if (config('auth.verify_email') && !$admin->hasVerifiedEmail()) {
            return redirect()->route('admin.verification.notice')
                ->with('warning', 'Please verify your email address.');
        }

        // Share admin data with all views
        view()->share('admin', $admin);

        return $next($request);
    }
}
