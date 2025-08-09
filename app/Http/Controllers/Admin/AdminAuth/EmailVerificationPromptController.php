<?php

namespace App\Http\Controllers\Admin\AdminAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): Response|RedirectResponse|View
    {
        return $request->user('admin')->hasVerifiedEmail()
            ? redirect()->intended(route('admin.dashboard'))
            : view('admin.auth.verify-email');
    }
}
