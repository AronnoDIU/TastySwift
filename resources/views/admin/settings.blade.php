@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Settings</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">General Settings</h4>
                    
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select @error('timezone') is-invalid @enderror" 
                                        id="timezone" name="timezone" required>
                                    @php
                                        $currentTz = old('timezone', $admin->settings['timezone'] ?? 'UTC');
                                        $timezones = [
                                            'UTC' => 'UTC',
                                            'America/New_York' => 'Eastern Time (ET)',
                                            'America/Chicago' => 'Central Time (CT)',
                                            'America/Denver' => 'Mountain Time (MT)',
                                            'America/Los_Angeles' => 'Pacific Time (PT)',
                                            'Europe/London' => 'London',
                                            'Europe/Paris' => 'Paris',
                                            'Asia/Dubai' => 'Dubai',
                                            'Asia/Tokyo' => 'Tokyo',
                                            'Australia/Sydney' => 'Sydney',
                                        ];
                                        asort($timezones);
                                    @endphp
                                    
                                    @foreach($timezones as $tz => $label)
                                        <option value="{{ $tz }}" {{ $currentTz === $tz ? 'selected' : '' }}>
                                            {{ $label }} ({{ $tz }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="date_format" class="form-label">Date Format</label>
                                <select class="form-select @error('date_format') is-invalid @enderror" 
                                        id="date_format" name="date_format" required>
                                    @php
                                        $currentFormat = old('date_format', $admin->settings['date_format'] ?? 'Y-m-d');
                                        $formats = [
                                            'Y-m-d' => 'YYYY-MM-DD (2023-04-15)',
                                            'd/m/Y' => 'DD/MM/YYYY (15/04/2023)',
                                            'm/d/Y' => 'MM/DD/YYYY (04/15/2023)',
                                            'd M Y' => 'DD MMM YYYY (15 Apr 2023)',
                                            'M d, Y' => 'MMMM DD, YYYY (April 15, 2023)',
                                        ];
                                    @endphp
                                    
                                    @foreach($formats as $format => $label)
                                        <option value="{{ $format }}" {{ $currentFormat === $format ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('date_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="time_format" class="form-label">Time Format</label>
                                <select class="form-select @error('time_format') is-invalid @enderror" 
                                        id="time_format" name="time_format" required>
                                    @php
                                        $currentTimeFormat = old('time_format', $admin->settings['time_format'] ?? 'H:i');
                                    @endphp
                                    <option value="H:i" {{ $currentTimeFormat === 'H:i' ? 'selected' : '' }}>24-hour (14:30)</option>
                                    <option value="h:i A" {{ $currentTimeFormat === 'h:i A' ? 'selected' : '' }}>12-hour (2:30 PM)</option>
                                </select>
                                @error('time_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="per_page" class="form-label">Items Per Page</label>
                                <input type="number" class="form-control @error('per_page') is-invalid @enderror" 
                                       id="per_page" name="per_page" 
                                       min="5" max="100" 
                                       value="{{ old('per_page', $admin->settings['per_page'] ?? 15) }}" required>
                                @error('per_page')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Number of items to display per page in tables.
                                </small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Notification Preferences</label>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="email_notifications" name="email_notifications" 
                                           value="1" {{ (old('email_notifications', $admin->settings['email_notifications'] ?? true) ? 'checked' : '') }}>
                                    <label class="form-check-label" for="email_notifications">Enable email notifications</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="browser_notifications" name="browser_notifications" 
                                           value="1" {{ (old('browser_notifications', $admin->settings['browser_notifications'] ?? true) ? 'checked' : '') }}>
                                    <label class="form-check-label" for="browser_notifications">Enable browser notifications</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="header-title mb-3">API Access</h4>
                    
                    @if(session('api_token'))
                        <div class="alert alert-success">
                            <h5 class="alert-heading">API Token Generated Successfully!</h5>
                            <p class="mb-0">Please copy your new API token now. You won't be able to see it again!</p>
                            <div class="input-group mt-3">
                                <input type="text" class="form-control" value="{{ session('api_token') }}" id="apiToken" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="copyTokenBtn">
                                    <i class="far fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <h5 class="alert-heading">API Documentation</h5>
                        <p>You can use your API token to authenticate with our API. Include it in the <code>Authorization</code> header as a Bearer token.</p>
                        <pre class="bg-dark text-light p-2 rounded">Authorization: Bearer &lt;your-api-token&gt;</pre>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Current API Status</h6>
                            <p class="text-muted mb-0">
                                @if($admin->tokens()->count() > 0)
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                    <small class="d-block text-muted">Last used: {{ $admin->tokens()->first()->last_used_at ? $admin->tokens()->first()->last_used_at->diffForHumans() : 'Never' }}</small>
                                @else
                                    <span class="text-muted"><i class="fas fa-times-circle me-1"></i> No active tokens</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            @if($admin->tokens()->count() > 0)
                                <form action="{{ route('admin.api.token.revoke') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to revoke your API token? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger me-2">
                                        <i class="fas fa-ban me-1"></i> Revoke Token
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.api.token.generate') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-1"></i> {{ $admin->tokens()->count() > 0 ? 'Regenerate Token' : 'Generate Token' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Account Security</h4>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Two-Factor Authentication</h6>
                            <p class="text-muted mb-0">
                                @if($admin->two_factor_secret)
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i> Enabled</span>
                                @else
                                    <span class="text-muted"><i class="fas fa-times-circle me-1"></i> Not enabled</span>
                                @endif
                            </p>
                        </div>
                        
                        @if($admin->two_factor_secret)
                            <form action="{{ route('admin.two-factor.disable') }}" method="POST" onsubmit="return confirm('Are you sure you want to disable two-factor authentication?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-ban me-1"></i> Disable
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#enable2FAModal">
                                <i class="fas fa-shield-alt me-1"></i> Enable
                            </button>
                        @endif
                    </div>
                    
                    <div class="border-top pt-3 mt-3">
                        <h6 class="mb-2">Recent Activity</h6>
                        <div class="list-group list-group-flush">
                            @forelse($activities as $activity)
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-sm">
                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                    <i class="fas fa-{{ $activity->event_icon ?? 'info-circle' }}"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $activity->description }}</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $activity->created_at->diffForHumans() }} • 
                                                {{ $activity->properties->get('ip_address') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted text-center py-3">
                                    No recent activity found.
                                </div>
                            @endforelse
                        </div>
                        
                        @if($activities->hasPages())
                            <div class="mt-3">
                                {{ $activities->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="header-title mb-3">Danger Zone</h4>
                    
                    <div class="border rounded p-3 mb-3">
                        <h6 class="text-danger">Delete Account</h6>
                        <p class="small text-muted">
                            Once you delete your account, there is no going back. Please be certain.
                        </p>
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="fas fa-trash-alt me-1"></i> Delete My Account
                        </button>
                    </div>
                    
                    <div class="border rounded p-3">
                        <h6 class="text-warning">Export Data</h6>
                        <p class="small text-muted">
                            Download all your data in a JSON file.
                        </p>
                        <button class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-file-export me-1"></i> Export My Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enable 2FA Modal -->
<div class="modal fade" id="enable2FAModal" tabindex="-1" aria-labelledby="enable2FAModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enable2FAModalLabel">Enable Two-Factor Authentication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.two-factor.enable') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Scan the QR code below with your authenticator app (like Google Authenticator or Authy) to enable two-factor authentication.</p>
                    
                    <div class="text-center my-4">
                        <div id="qrcode" class="d-inline-block p-3 bg-white rounded border">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Generating QR Code...</p>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-muted small">
                        Can't scan the code? Enter this secret key manually: 
                        <code id="2fa-secret" class="user-select-all">loading...</code>
                    </p>
                    
                    <div class="mb-3">
                        <label for="verification_code" class="form-label">Enter the 6-digit code from your app</label>
                        <input type="text" class="form-control" id="verification_code" name="code" 
                               placeholder="123456" pattern="\d{6}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="enable2FABtn">
                        <span class="spinner-border spinner-border-sm d-none" id="enable2FASpinner" role="status"></span>
                        Verify & Enable
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">Delete Your Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Warning: This action is irreversible!</h5>
                        <p class="mb-0">
                            All your data will be permanently deleted. This includes your profile, settings, and any content you've created.
                            Please type <strong>DELETE MY ACCOUNT</strong> to confirm.
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="delete_confirmation" class="form-label">Type "DELETE MY ACCOUNT" to confirm</label>
                        <input type="text" class="form-control" id="delete_confirmation" name="confirmation" required>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="delete_data" name="delete_data" required>
                        <label class="form-check-label" for="delete_data">
                            I understand that all my data will be permanently deleted and cannot be recovered.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="deleteAccountBtn" disabled>
                        <i class="fas fa-exclamation-triangle me-1"></i> Delete My Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Copy API token to clipboard
        const copyTokenBtn = document.getElementById('copyTokenBtn');
        if (copyTokenBtn) {
            copyTokenBtn.addEventListener('click', function() {
                const apiToken = document.getElementById('apiToken');
                apiToken.select();
                document.execCommand('copy');
                
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        }
        
        // 2FA Modal
        const enable2FAModal = document.getElementById('enable2FAModal');
        if (enable2FAModal) {
            enable2FAModal.addEventListener('shown.bs.modal', function() {
                // In a real app, you would make an AJAX call to generate the 2FA secret
                // and then generate the QR code client-side using a library like qrcode.js
                
                // Simulate API call
                setTimeout(() => {
                    const qrCodeDiv = document.getElementById('qrcode');
                    const secretCode = 'JBSWY3DPEHPK3PXP';
                    
                    // In a real app, you would use the actual user's email and app name
                    const otpauthUrl = `otpauth://totp/TastySwift:{{ $admin->email }}?secret=${secretCode}&issuer=TastySwift`;
                    
                    // Generate QR code (you would need to include qrcode.js)
                    if (typeof QRCode !== 'undefined') {
                        qrCodeDiv.innerHTML = '';
                        new QRCode(qrCodeDiv, {
                            text: otpauthUrl,
                            width: 200,
                            height: 200,
                            colorDark: '#000000',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } else {
                        qrCodeDiv.innerHTML = `
                            <div class="alert alert-warning">
                                QR Code library not loaded. Please scan this secret key manually:
                                <div class="mt-2 p-2 bg-light rounded text-center">
                                    <code class="h4">${secretCode}</code>
                                </div>
                            </div>
                        `;
                    }
                    
                    document.getElementById('2fa-secret').textContent = secretCode;
                }, 1000);
            });
            
            // Handle form submission
            const enable2FAForm = enable2FAModal.querySelector('form');
            if (enable2FAForm) {
                enable2FAForm.addEventListener('submit', function(e) {
                    const btn = document.getElementById('enable2FABtn');
                    const spinner = document.getElementById('enable2FASpinner');
                    
                    btn.disabled = true;
                    spinner.classList.remove('d-none');
                });
            }
        }
        
        // Delete account confirmation
        const deleteConfirmation = document.getElementById('delete_confirmation');
        const deleteAccountBtn = document.getElementById('deleteAccountBtn');
        
        if (deleteConfirmation && deleteAccountBtn) {
            deleteConfirmation.addEventListener('input', function() {
                deleteAccountBtn.disabled = this.value.trim().toUpperCase() !== 'DELETE MY ACCOUNT';
            });
        }
    });
</script>
@endpush
