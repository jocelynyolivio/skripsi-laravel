<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #8c8d5e; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 15px 0;">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('assets/logo.png') }}" alt="SenyumQu Logo" style="height: 40px; margin-right: 10px;">
            SenyumQu
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#services">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#testimonials">Testimonials</a>
                </li>

                @if(Auth::guard('patient')->check())
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reservation.index') }}">Make Reservation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reservations.upcoming') }}">Upcoming Reservation</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Welcome, {{ Auth::guard('patient')->user()->fname }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- <li><a class="dropdown-item" href="{{ route('reservations.upcoming') }}">My Reservations</a></li>
                            <li><hr class="dropdown-divider"></li> -->
                        <li>
                            <form action="{{ route('patient.logout') }}" method="post">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-light" href="{{ route('patient.login') }}">Make Reservation</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar-brand {
        font-weight: 700;
        font-size: 1.8rem;
        color: white !important;
        display: flex;
        align-items: center;
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.85) !important;
        font-weight: 500;
        margin: 0 10px;
        position: relative;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: white !important;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        background: white;
        bottom: 0;
        left: 0;
        transition: width 0.3s ease;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 100%;
    }

    .nav-link.active {
        color: white !important;
        font-weight: 600;
    }
</style>