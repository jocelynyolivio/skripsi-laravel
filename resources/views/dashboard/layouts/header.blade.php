<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <!-- Brand/Logo with Icon -->
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 py-2 fs-6 fw-bold text-white" href="{{ route('dashboard') }}">
        <i class="bi bi-tooth me-2"></i>
        <span class="align-middle">SenyumQu Dashboard</span>
    </a>
    
    <!-- Mobile Toggle Button (for sidebar) -->
    <button class="navbar-toggler position-absolute d-md-none" 
            type="button" 
            data-bs-toggle="offcanvas" 
            data-bs-target="#sidebarMenu"
            aria-controls="sidebarMenu"
            aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- User Menu -->
    <div class="navbar-nav ms-auto pe-3">
        <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle py-2 px-3 text-white d-flex align-items-center" 
               href="#" 
               role="button" 
               data-bs-toggle="dropdown"
               aria-expanded="false">
                <i class="bi bi-person-circle me-2"></i>
                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('dashboard.profile') }}">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>