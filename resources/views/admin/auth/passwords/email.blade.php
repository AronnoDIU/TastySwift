@extends('admin.layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle" style="width: 80px; height: 80px;">
                            <i class="fas fa-key text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="mt-3 mb-1">Forgot Password</h3>
                        <p class="text-muted">Enter your email to receive a password reset link</p>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1 text-primary"></i> Email Address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-at text-muted"></i>
                                </span>
                                <input id="email" type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter your email address"
                                       required 
                                       autocomplete="email" 
                                       autofocus>
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                            <div class="form-text mt-2">
                                We'll send you a link to reset your password.
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> Send Password Reset Link
                            </button>
                            
                            <a href="{{ route('admin.login') }}" class="btn btn-link text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-transparent text-center py-3">
                    <p class="text-muted small mb-0">
                        Need help? <a href="{{ route('contact') }}" class="text-decoration-none">Contact support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .card {
        border-radius: 1rem;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 2.5rem 2rem 0.5rem;
    }
    
    .card-body {
        padding: 2rem;
    }
    
    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        border-color: #b3c1f8;
    }
    
    .btn-primary {
        background-color: #4361ee;
        border: none;
        padding: 0.75rem;
        font-weight: 500;
        border-radius: 0.5rem;
    }
    
    .btn-primary:hover {
        background-color: #3a56d4;
    }
    
    .btn-link {
        color: #6c757d;
    }
    
    .btn-link:hover {
        color: #4361ee;
    }
    
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0;
        color: #6c757d;
    }
    
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #dee2e6;
    }
    
    .divider:not(:empty)::before {
        margin-right: 1rem;
    }
    
    .divider:not(:empty)::after {
        margin-left: 1rem;
    }
    
    @media (max-width: 575.98px) {
        .container {
            padding: 0 1rem;
        }
        
        .card-header {
            padding: 1.5rem 1.5rem 0.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
    }
</style>
@endsection
