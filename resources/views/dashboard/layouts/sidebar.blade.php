<div class="sidebar bg-dark col-md-3 col-lg-2 p-0 text-white d-flex flex-column"> <!-- Tambahkan d-flex flex-column -->
    <div class="offcanvas-md offcanvas-end bg-dark flex-grow-1" tabindex="-1" id="sidebarMenu"> <!-- Tambahkan flex-grow-1 -->
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold text-white" id="sidebarMenuLabel">SenyumQu Dental</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard') ? 'active' : '' }}" aria-current="page" href="/dashboard">
                        <i class="bi bi-house-fill me-3 fs-5"></i>
                        <span class="fs-6">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/profile*') ? 'active' : '' }}" href="/dashboard/profile">
                        <i class="bi bi-person-circle me-3 fs-5"></i>
                        <span class="fs-6">My Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/salaries*') ? 'active' : '' }}" href="/dashboard/salaries">
                        <i class="bi bi-cash-stack me-3 fs-5"></i>
                        <span class="fs-6">Attendances & Slips</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/home_content/') ? 'active' : '' }}" href="/dashboard/home_content/">
                        <i class="bi bi-house-door me-3 fs-5"></i>
                        <span class="fs-6">Home Content</span>
                    </a>
                </li> -->
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">

            <h6 class="sidebar-section-title px-3 mt-3">Master Data</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('users*') ? 'active' : '' }}" href="{{ route('dashboard.masters.index') }}">
                        <i class="bi bi-people-fill me-3 fs-5"></i>
                        <span class="fs-6">Master Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/master/patients') ? 'active' : '' }}" href="{{ route('dashboard.masters.patients') }}">
                        <i class="bi bi-person-badge me-3 fs-5"></i>
                        <span class="fs-6">Master Patients</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/coa') ? 'active' : '' }}" href="{{ route('dashboard.coa.index') }}">
                        <i class="bi bi-person-badge me-3 fs-5"></i>
                        <span class="fs-6">Master COA</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/holidays') ? 'active' : '' }}" href="{{ route('dashboard.holidays.index') }}">
                        <i class="bi bi-person-badge me-3 fs-5"></i>
                        <span class="fs-6">Master Holidays</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/attendances') ? 'active' : '' }}" href="{{ route('dashboard.attendances.index') }}">
                        <i class="bi bi-person-badge me-3 fs-5"></i>
                        <span class="fs-6">Master Attendances</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/attendances') ? 'active' : '' }}" href="{{ route('dashboard.procedures.index') }}">
                        <i class="bi bi-person-badge me-3 fs-5"></i>
                        <span class="fs-6">Master Procedures</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/attendances') ? 'active' : '' }}" href="{{ route('dashboard.procedure_types.index') }}">
                        <i class="bi bi-person-badge me-3 fs-5"></i>
                        <span class="fs-6">Master Procedures Types</span>
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">

            <h6 class="sidebar-section-title px-3 mt-3">Reservations</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/reservations') ? 'active' : '' }}" href="{{ route('dashboard.reservations.index') }}">
                        <i class="bi bi-calendar-check me-3 fs-5"></i>
                        <span class="fs-6">Reservations</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/schedules') ? 'active' : '' }}" href="{{ route('dashboard.schedules.index') }}">
                        <i class="bi bi-calendar me-3 fs-5"></i>
                        <span class="fs-6">Schedules</span>
                    </a>
                </li> -->
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">
            <h6 class="sidebar-section-title px-3 mt-3">MATERIALS</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/dental-materials*') ? 'active' : '' }}" href="{{ route('dashboard.dental-materials.index') }}">
                        <i class="bi bi-box-seam me-3 fs-5"></i>
                        <span class="fs-6">Dental Materials</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/procedure_materials*') ? 'active' : '' }}" href="{{ route('dashboard.procedure_materials.index') }}">
                        <i class="bi bi-clipboard2-pulse me-3 fs-5"></i>
                        <span class="fs-6">Procedure</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/stock_cards*') ? 'active' : '' }}" href="{{ route('dashboard.stock_cards.index') }}">
                        <i class="bi bi-clipboard2-pulse me-3 fs-5"></i>
                        <span class="fs-6">Stock Cards
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">
            <h6 class="sidebar-section-title px-3 mt-3">TRANSACTIONS</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/transactions*') ? 'active' : '' }}" href="{{ route('dashboard.transactions.index') }}">
                        <i class="bi bi-cash-coin me-3 fs-5"></i>
                        <span class="fs-6">Transaction</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/expenses*') ? 'active' : '' }}" href="{{ route('dashboard.expenses.index') }}">
                        <i class="bi bi-currency-dollar me-3 fs-5"></i>
                        <span class="fs-6">Expenses</span>
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">
            <h6 class="sidebar-section-title px-3 mt-3">PURCHASES</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/transactions*') ? 'active' : '' }}" href="{{ route('dashboard.purchase_requests.index') }}">
                        <i class="bi bi-receipt me-3 fs-5"></i>
                        <span class="fs-6">Purchase Request</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/transactions*') ? 'active' : '' }}" href="{{ route('dashboard.purchases.index') }}">
                        <i class="bi bi-receipt me-3 fs-5"></i>
                        <span class="fs-6">Purchase Invoice</span>
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">

            <h6 class="sidebar-section-title px-3 mt-3">REPORTS</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/reports/balance_sheet') ? 'active' : '' }}" href="{{ route('dashboard.reports.balance_sheet') }}">
                        <i class="bi bi-file-earmark-spreadsheet me-3 fs-5"></i>
                        <span class="fs-6">Balance Sheet</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/reports/income_statement') ? 'active' : '' }}" href="{{ route('dashboard.reports.income_statement') }}">
                        <i class="bi bi-graph-up me-3 fs-5"></i>
                        <span class="fs-6">Income Statement</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/reports/cash_flow') ? 'active' : '' }}" href="{{ route('dashboard.reports.cash_flow') }}">
                        <i class="bi bi-cash-stack me-3 fs-5"></i>
                        <span class="fs-6">Cash Flow</span>
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">
            <h6 class="sidebar-section-title px-3 mt-3">JOURNALS</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/transactions*') ? 'active' : '' }}" href="{{ route('dashboard.journals.index') }}">
                        <i class="bi bi-journal-text me-3 fs-5"></i>
                        <span class="fs-6">Journal Details</span>
                    </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-secondary my-2 mx-3">

            <h6 class="sidebar-section-title px-3 mt-3">Schedules Management</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/schedules/templates*') ? 'active' : '' }}" href="{{ route('dashboard.schedules.templates.index') }}">
                        <i class="bi bi-calendar-range me-3 fs-5"></i>
                        <span class="fs-6">Schedule Templates</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center text-white-50 hover-bg-primary {{ Request::is('dashboard/schedules/overrides*') ? 'active' : '' }}" href="{{ route('dashboard.schedules.overrides.index') }}">
                        <i class="bi bi-calendar-event me-3 fs-5"></i>
                        <span class="fs-6">Schedule Overrides</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    /* Sidebar Styling */
    .sidebar {
        background-color: #212529 !important;
        transition: all 0.3s ease;
    }

    .sidebar-section-title {
        color: #adb5bd;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .nav-link {
        padding: 0.75rem 1.5rem;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .nav-link:hover {
        color: white !important;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        background-color: rgba(13, 110, 253, 0.2);
        color: white !important;
        border-left: 3px solid #0d6efd;
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    .hover-bg-primary:hover {
        background-color: rgba(13, 110, 253, 0.15) !important;
    }

    /* Better scrollbar for sidebar */
    .offcanvas-body::-webkit-scrollbar {
        width: 6px;
    }

    .offcanvas-body::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    /* Active state enhancement */
    .nav-link.active i {
        color: #0d6efd;
    }
</style>