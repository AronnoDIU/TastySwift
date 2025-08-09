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
                            <i class="fas fa-redo-alt text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="mt-3 mb-1">Reset Your Password</h3>
                        <p class="text-muted">Create a new password for your account</p>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1 text-primary"></i> Email Address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-at text-muted"></i>
                                </span>
                                <input id="email" 
                                       type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ $email ?? old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       {{ $email ? 'readonly' : '' }}>
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1 text-primary"></i> New Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key text-muted"></i>
                                </span>
                                <input id="password" 
                                       type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Enter new password">
                                <button class="btn btn-outline-secondary password-toggle" type="button" onclick="togglePassword('password')">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <small>Must be at least 8 characters long</small>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">
                                <i class="fas fa-check-circle me-1 text-primary"></i> Confirm New Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key text-muted"></i>
                                </span>
                                <input id="password-confirm" 
                                       type="password" 
                                       class="form-control form-control-lg" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Confirm new password">
                                <button class="btn btn-outline-secondary password-toggle" type="button" onclick="togglePassword('password-confirm')">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sync-alt me-2"></i> Reset Password
                            </button>
                            
                            <a href="{{ route('admin.login') }}" class="btn btn-link text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-transparent text-center py-3">
                    <p class="text-muted small mb-0">
                        Having trouble? <a href="{{ route('contact') }}" class="text-decoration-none">Contact support</a>
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
    
    .password-toggle {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
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

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Password strength indicator
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const strengthMeter = document.createElement('div');
        strengthMeter.className = 'password-strength mt-2';
        passwordField.parentNode.insertBefore(strengthMeter, passwordField.nextSibling);
        
        passwordField.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updateStrengthMeter(strength);
        });
        
        function checkPasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 1;
            
            // Contains lowercase
            if (password.match(/[a-z]+/)) strength += 1;
            
            // Contains uppercase
            if (password.match(/[A-Z]+/)) strength += 1;
            
            // Contains numbers
            if (password.match(/[0-9]+/)) strength += 1;
            
            // Contains special chars
            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength += 1;
            
            return strength;
        }
        
        function updateStrengthMeter(strength) {
            let strengthText = '';
            let strengthClass = '';
            let width = 0;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthText = 'Very Weak';
                    strengthClass = 'danger';
                    width = '20%';
                    break;
                case 2:
                    strengthText = 'Weak';
                    strengthClass = 'warning';
                    width = '40%';
                    break;
                case 3:
                    strengthText = 'Moderate';
                    strengthClass = 'info';
                    width = '60%';
                    break;
                case 4:
                    strengthText = 'Strong';
                    strengthClass = 'primary';
                    width = '80%';
                    break;
                case 5:
                    strengthText = 'Very Strong';
                    strengthClass = 'success';
                    width = '100%';
                    break;
            }
            
            strengthMeter.innerHTML = `
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-${strengthClass}" role="progressbar" 
                         style="width: ${width};" aria-valuenow="${width}" 
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small class="text-${strengthClass}">${strengthText}</small>
            `;
        }
    });
</script>
@endpush
@endsection
