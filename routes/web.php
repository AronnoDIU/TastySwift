<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminAuth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\AdminAuth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\AdminAuth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\AdminAuth\NewPasswordController;
use App\Http\Controllers\Admin\AdminAuth\PasswordResetLinkController;
use App\Http\Controllers\Admin\AdminAuth\RegisteredUserController;
use App\Http\Controllers\Admin\AdminLoginController;

// Main Routes
Route::get('/', static function () {
    return view('welcome');
});

// User Dashboard (Regular User)
Route::get('/dashboard', static function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Test routes to help diagnose the authentication issue
Route::get('/test-admin', function() {
    return 'Admin test route is working!';
});

Route::get('/test-auth', [\App\Http\Controllers\TestController::class, 'testAuth']);
Route::get('/test-session', [\App\Http\Controllers\TestController::class, 'testSession']);

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function() {
    // Guest routes (not authenticated)
    // Temporarily removing guest middleware for testing
    // Route::middleware('guest:admin')->group(function() {
    Route::group([], function() {
        // Login Routes
        Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminLoginController::class, 'login']);
        
        // Password Reset Routes
        Route::get('forgot-password', [AdminLoginController::class, 'showLinkRequestForm'])
            ->name('password.request');
            
        Route::post('forgot-password', [AdminLoginController::class, 'sendResetLinkEmail'])
            ->name('password.email');
            
        Route::get('reset-password/{token}', [AdminLoginController::class, 'showResetForm'])->name('password.reset');
            
        Route::post('reset-password', [AdminLoginController::class, 'reset'])->name('password.update');
    }); // End of Route::group

    // Authenticated routes
    Route::middleware(['admin.auth'])->group(function() {
        // Email Verification Notice
        Route::get('email/verify', [AdminLoginController::class, 'showEmailVerificationNotice'])
            ->middleware('throttle:6,1')
            ->name('verification.notice');
            
        // Email Verification Handler
        Route::get('email/verify/{id}/{hash}', [AdminLoginController::class, 'verifyEmail'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
            
        // Resend Verification Email
        Route::post('email/verification-notification', [AdminLoginController::class, 'resendVerificationEmail'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
            
        // Password Confirmation
        Route::get('confirm-password', [AdminLoginController::class, 'showConfirmForm'])
            ->name('password.confirm');
            
        Route::post('confirm-password', [AdminLoginController::class, 'confirm']);
        
        // Logout
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
    });
    
    // Verified Admin Routes
    Route::middleware(['auth:admin', 'verified'])->group(function() {
        // Dashboard
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Profile
        Route::get('profile', [AdminController::class, 'profile'])->name('profile');
        Route::put('profile', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::put('password', [AdminController::class, 'updatePassword'])->name('password.update');
        
        // Settings
        Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        
        // Activity Log
        Route::get('activity-log', [AdminController::class, 'activityLog'])->name('activity.log');
        
        // API Tokens
        Route::post('api/token/generate', [AdminController::class, 'generateApiToken'])
            ->name('api.token.generate');
            
        Route::delete('api/token/revoke', [AdminController::class, 'revokeApiToken'])
            ->name('api.token.revoke');
            
        // Email Verification
        Route::get('email/verify/{id}/{hash}', [AdminLoginController::class, 'verifyEmail'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
            
        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
        
        // Password Confirmation
        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
        
        // Logout
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

// Include regular auth routes
require __DIR__.'/auth.php';
