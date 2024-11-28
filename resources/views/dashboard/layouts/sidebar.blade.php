<div class="sidebar bg-secondary col-md-3 col-lg-2 p-0 text-white vh-100">
    <div class="offcanvas-md offcanvas-end bg-secondary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Company Name</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white {{Request::is('dashboard') ? 'active' : ''}}" aria-current="page" href="/dashboard">
                        <i class="bi bi-house-fill"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <!-- <a class="nav-link text-white {{Request::is('dashboard/posts*') ? 'active' : ''}}" href="/dashboard/posts">
                        <i class="bi bi-file-earmark"></i>
                        My Profile
                    </a> -->
                    <a class="nav-link text-white {{Request::is('dashboard/profile*') ? 'active' : ''}}" href="/dashboard/profile">
                        <i class="bi bi-file-earmark"></i>
                        My Profile
                    </a>
                    <a class="nav-link text-white {{Request::is('dashboard/salaries*') ? 'active' : ''}}" href="/dashboard/salaries">
                        <i class="bi bi-file-earmark"></i>
                        Attendances and Slips
                    </a>

                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-light">

            <h6 class="text-uppercase px-3 text-light mt-3">Master Data</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/masters/1') ? 'active' : '' }}" href="{{ route('dashboard.masters.role', ['role_id' => 1]) }}">
                        <i class="bi bi-person-badge"></i> Master Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/masters/2') ? 'active' : '' }}" href="{{ route('dashboard.masters.role', ['role_id' => 2]) }}">
                        <i class="bi bi-person-badge"></i> Master Dokter
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/masters/3') ? 'active' : '' }}" href="{{ route('dashboard.masters.role', ['role_id' => 3]) }}">
                        <i class="bi bi-person-badge"></i> Master Manager
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/master/patients') ? 'active' : '' }}" href="{{ route('dashboard.masters.patients') }}">
                        <i class="bi bi-person-badge"></i> Master Pasien
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-light">

            <h6 class="text-uppercase px-3 text-light mt-3">Reservations</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/reservations') ? 'active' : '' }}" href="{{ route('dashboard.reservations.index') }}">
                        <i class="bi bi-calendar-check"></i> Data Reservasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/schedules') ? 'active' : '' }}" href="{{ route('dashboard.schedules.index') }}">
                        <i class="bi bi-calendar-plus"></i> Data Schedules
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>