<div class="sidebar bg-secondary col-md-3 col-lg-2 p-0 text-white vh-100">
    <div class="offcanvas-md offcanvas-end bg-secondary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Company Name</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard') ? 'active' : '' }}" aria-current="page" href="/dashboard">
                        <i class="bi bi-house-fill"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/profile*') ? 'active' : '' }}" href="/dashboard/profile">
                        <i class="bi bi-file-earmark"></i>
                        My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/salaries*') ? 'active' : '' }}" href="/dashboard/salaries">
                        <i class="bi bi-file-earmark"></i>
                        Attendances and Slips
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/home_content/') ? 'active' : '' }}" href="/dashboard/home_content/">
                        <i class="bi bi-house-door"></i>
                        Home Content
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-light">

            <h6 class="text-uppercase px-3 text-light mt-3">Master Data</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('users*') ? 'active' : '' }}" href="{{ route('dashboard.masters.index') }}">
                        <i class="bi bi-people-fill"></i> Master Users
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

                <!-- Divider -->
            <hr class="border-light">
            <h6 class="text-uppercase px-3 text-light mt-3">MATERIALS</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/dental-materials*') ? 'active' : '' }}" href="{{ route('dashboard.dental-materials.index') }}">
                        <i class="bi bi-calendar-plus"></i> Dental Materials
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/procedure_materials*') ? 'active' : '' }}" href="{{ route('dashboard.procedure_materials.index') }}">
                        <i class="bi bi-calendar-plus"></i> Procedure
                    </a>
                </li>
            </ul>
            <!-- Divider -->
            <hr class="border-light">
            <h6 class="text-uppercase px-3 text-light mt-3">TRANSACTIONS</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/transactions*') ? 'active' : '' }}" href="{{ route('dashboard.transactions.index') }}">
                        <i class="bi bi-calendar-plus"></i> Transaction
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/expenses*') ? 'active' : '' }}" href="{{ route('dashboard.expenses.index') }}">
                        <i class="bi bi-calendar-plus"></i> Expenses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('dashboard/expense_requests*') ? 'active' : '' }}" href="{{ route('dashboard.expense_requests.index') }}">
                        <i class="bi bi-calendar-plus"></i> Expenses Request
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
