@extends('admin.layouts.app')

@section('title', 'Confirm Password')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1 class="login-title">Confirm Password</h1>
            <p class="login-subtitle">Please confirm your password before continuing.</p>
        </div>
        
        <div class="login-body">
            <form method="POST" action="{{ route('admin.password.confirm') }}">
                @csrf

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <div class="password-input-group" style="flex: 1;">
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Enter your password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="far fa-eye" id="togglePassword"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-check-circle me-2"></i> Confirm Password
                    </button>
                </div>
                
                @if (Route::has('admin.password.request'))
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.password.request') }}" class="text-decoration-none">
                            Forgot Your Password?
                        </a>
                    </div>
                @endif
            </form>
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
        max-width: 450px;
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
        margin-bottom: 0;
    }
    
    .login-body {
        padding: 2.5rem 2rem;
    }
    
    .form-control {
        height: 50px;
        border: 1px solid #e1e4e8;
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
        border: 1px solid #e1e4e8;
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
    
    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        z-index: 10;
    }
    
    .password-input-group {
        position: relative;
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

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(`toggle${fieldId.charAt(0).toUpperCase() + fieldId.slice(1)}`);
        
        if (field.type === 'password') {
            field.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
