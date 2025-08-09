<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form.
     *
     * @return View
     */
    /**
     * Show the admin login form.
     *
     * @return View|RedirectResponse
     */
    public function showLoginForm()
    {
        // If a user is already authenticated as admin, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // If a user is authenticated as a regular user, log them out first
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('status', 'You have been logged out. Please log in as an admin.');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function login(Request $request)
    {
        // Throttle login attempts
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ])->status(429);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Explicitly use the admin guard
        if (Auth::guard('admin')->attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            // Clear login attempts on successful login
            RateLimiter::clear($throttleKey);

            // Log the login activity
            $admin = Auth::guard('admin')->user();
            activity('auth')
                ->causedBy($admin)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('Admin logged in');

            if ($request->wantsJson()) {
                return new JsonResponse([], 204);
            }

            // Explicitly redirect to the admin dashboard
            return redirect()->route('admin.dashboard');
        }

        // Increment login attempts
        RateLimiter::hit($throttleKey, 300); // 5-minute cooldown

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Log the admin out of the application.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        // Log the logout activity
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            activity('auth')
                ->causedBy($admin)
                ->withProperties([
                    'ip_address' => $request->ip(),
                ])
                ->log('Admin logged out');
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('admin.login');
    }

    /**
     * Show the forgot password form.
     *
     * @return View
     */
    public function showLinkRequestForm(): View
    {
        return view('admin.auth.passwords.email');
    }

    /**
     * Handle a forgot password request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the password reset form.
     *
     * @param Request $request
     * @return View
     */
    public function showResetForm(Request $request): View
    {
        $token = $request->route()->parameter('token');
        return view('admin.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle a password reset request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                $admin->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($admin));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Show the email verification notice.
     *
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function showEmailVerificationNotice(Request $request): RedirectResponse|View
    {
        return $request->user('admin')->hasVerifiedEmail()
            ? redirect()->route('admin.dashboard')
            : view('admin.auth.verify-email');
    }

    /**
     * Mark the authenticated admin's email address as verified.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function verifyEmail(Request $request): RedirectResponse|JsonResponse
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user('admin')->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user('admin')->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user('admin')->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect()->route('admin.dashboard');
        }

        if ($request->user('admin')->markEmailAsVerified()) {
            event(new Verified($request->user('admin')));
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('admin.dashboard')->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function resendVerificationEmail(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user('admin')->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect()->route('admin.dashboard');
        }

        $request->user('admin')->sendEmailVerificationNotification();

        return $request->wantsJson()
            ? new JsonResponse([], 202)
            : back()->with('status', 'verification-link-sent');
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }

    /**
     * Show the password confirmation form.
     *
     * @return View
     */
    public function showConfirmForm(): View
    {
        return view('admin.auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'password' => 'required|password:admin',
        ]);

        $request->session()->passwordConfirmed();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended(route('admin.dashboard'));
    }
}
