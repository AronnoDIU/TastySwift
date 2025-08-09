<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        @if(auth('admin')->check())
            <!-- User Profile Section -->
            <div class="text-center mb-4">
                <img src="{{ auth('admin')->user()->avatar_url }}" 
                     class="rounded-circle mb-2" 
                     width="80" 
                     height="80" 
                     alt="{{ auth('admin')->user()->name }}">
                <h6 class="mb-1">{{ auth('admin')->user()->name }}</h6>
                <small class="text-muted">{{ auth('admin')->user()->formatted_role }}</small>
            </div>
            
            <hr class="my-3">
            
            <!-- Navigation Menu -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" 
                       href="{{ route('admin.profile') }}">
                        <i class="fas fa-user me-2"></i> My Profile
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" 
                       href="{{ route('admin.settings') }}">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.activity*') ? 'active' : '' }}" 
                       href="{{ route('admin.activity.log') }}">
                        <i class="fas fa-history me-2"></i> Activity Log
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                        <span>Management</span>
                        <i class="fas fa-cog"></i>
                    </h6>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-box me-2"></i> Products
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-shopping-cart me-2"></i> Orders
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-tags me-2"></i> Categories
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-comment-alt me-2"></i> Reviews
                    </a>
                </li>
                
                @if(auth('admin')->user()->isSuperAdmin())
                    <li class="nav-item mt-3">
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                            <span>Administration</span>
                            <i class="fas fa-shield-alt"></i>
                        </h6>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-user-shield me-2"></i> Admins
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-tools me-2"></i> System Settings
                        </a>
                    </li>
                @endif
                
                <!-- Logout Button -->
                <li class="nav-item mt-4">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
            
        @else
            <!-- Guest View -->
            <div class="text-center mb-4">
                <div class="d-flex justify-content-center mb-2">
                    <i class="fas fa-user-circle" style="font-size: 5rem; color: #6c757d;"></i>
                </div>
                <h6 class="mb-1">Guest</h6>
                <small class="text-muted">Not logged in</small>
            </div>
            
            <hr class="my-3">
            
            <div class="text-center p-4">
                <p class="text-muted mb-3">Please log in to access the admin panel</p>
                <a href="{{ route('admin.login') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                </a>
            </div>
        @endif
    </div>
</div>
