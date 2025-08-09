@extends('admin.layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="login-title">Forgot Password</h1>
            <p class="login-subtitle">Enter your email to reset your password</p>
        </div>
        
        <div class="login-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    </div>
                    @error('email')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-paper-plane me-2"></i> Send Password Reset Link
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="{{ route('admin.login') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Reuse the same styles from login.blade.php */
    :root {
        --primary-color: #4361ee;
        --primary-dark: #3a56d4;
        --text-color: #2b2d42;
        --border-color: #e1e4e8;
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        min-height: 100vh;
        color: var(--text-color);
        line-height: 1.6;
        display: flex;
        align-items: center;
        padding: 20px;
    }
    
    .login-container {
        max-width: 420px;
        width: 100%;
        margin: 0 auto;
    }
    
    .login-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    .login-header {
        background: var(--primary-color);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .login-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: rotate(30deg);
    }
    
    .login-logo {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 28px;
    }
    
    .login-title {
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .login-subtitle {
        opacity: 0.9;
        font-weight: 400;
        font-size: 0.95rem;
        position: relative;
        z-index: 1;
    }
    
    .login-body {
        padding: 2.5rem 2rem;
    }
    
    .form-control {
        height: 50px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-color);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid var(--border-color);
        border-right: none;
        border-radius: 8px 0 0 8px !important;
    }
    
    .input-group .form-control {
        border-left: none;
        border-radius: 0 8px 8px 0 !important;
    }
    
    .btn-login {
        background: var(--primary-color);
        border: none;
        height: 50px;
        font-weight: 600;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    }
    
    .btn-login:hover, .btn-login:focus {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(67, 97, 238, 0.3);
    }
    
    .btn-login:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(67, 97, 238, 0.2);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 576px) {
        body {
            padding: 15px;
        }
        
        .login-container {
            max-width: 100%;
        }
        
        .login-body {
            padding: 2rem 1.5rem;
        }
        
        .login-header {
            padding: 1.5rem 1rem;
        }
    }
</style>
@endpush
@endsection
