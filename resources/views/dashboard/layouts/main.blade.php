<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SenyumQu Dashboard</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --sidebar-width-expanded: 260px;
            --sidebar-width-collapsed: 80px;
            --header-height: 56px;
            --sidebar-bg: #212529;
            --sidebar-link-color: rgba(255, 255, 255, 0.7);
            --sidebar-link-hover-bg: rgba(255, 255, 255, 0.1);
            --sidebar-link-active-color: #ffffff;
            --sidebar-link-active-bg: rgba(13, 110, 253, 0.2);
            --sidebar-link-active-border: #0d6efd;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding-top: var(--header-height);
            transition: margin-left 0.3s ease-in-out;
        }

        .navbar-header {
            height: var(--header-height);
            background-color: var(--sidebar-bg);
            z-index: 1035;
            padding: 0;
            /* Pastikan header adalah flex container agar ms-auto berfungsi */
            display: flex;
            justify-content: space-between;
            /* Ini akan membantu jika ada item lain */
            align-items: center;
        }

        .sidebar-toggle-btn {
            color: var(--sidebar-link-color);
        }

        .sidebar-toggle-btn:hover {
            color: var(--sidebar-link-active-color);
        }

        .sidebar {
            background-color: var(--sidebar-bg);
            color: var(--sidebar-link-color);
            height: 100vh;
            transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
            overflow-x: hidden;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--sidebar-link-color);
            white-space: nowrap;
            border-left: 3px solid transparent;
            overflow: hidden;
        }

        .sidebar .nav-link .bi {
            font-size: 1.2rem;
            text-align: center;
            transition: margin-right 0.3s ease-in-out, min-width 0.3s ease-in-out;
        }

        .sidebar .nav-link span,
        .sidebar .sidebar-heading {
            transition: opacity 0.2s ease-in-out 0.1s, visibility 0.2s ease-in-out 0.1s, width 0.2s ease-in-out 0.1s;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-link-hover-bg);
            color: var(--sidebar-link-active-color);
        }

        .sidebar .nav-link.active {
            color: var(--sidebar-link-active-color);
            background-color: var(--sidebar-link-active-bg);
            border-left-color: var(--sidebar-link-active-border);
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #adb5bd;
            padding: 0.8rem 1.5rem 0.5rem;
            margin-top: 1rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar .menu-wrapper {
            height: calc(100vh - var(--header-height));
            display: block;
            overflow-y: auto;
        }

        .sidebar.offcanvas .menu-wrapper.offcanvas-body {
            height: 100%;
            padding: 0;
        }

        main {
            padding: 2rem;
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 992px) {
            #sidebar-toggle-mobile {
                display: none !important;
            }

            #sidebar-toggle-desktop {
                display: block !important;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                padding-top: var(--header-height);
                z-index: 1020;
            }

            body.sidebar-collapsed .sidebar {
                width: var(--sidebar-width-collapsed);
            }

            body.sidebar-collapsed main {
                margin-left: var(--sidebar-width-collapsed);
            }

            body.sidebar-collapsed .sidebar .nav-link span,
            body.sidebar-collapsed .sidebar .sidebar-heading {
                opacity: 0;
                visibility: hidden;
                width: 0;
            }

            body.sidebar-collapsed .sidebar .nav-link .bi {
                margin-right: 0;
                min-width: calc(var(--sidebar-width-collapsed) - 3rem);
            }

            body.sidebar-collapsed .sidebar .nav-link {
                justify-content: center;
            }

            body.sidebar-collapsed .sidebar .menu-wrapper {
                overflow-y: hidden;
                display: block;
                justify-content: flex-start;
            }

            body.sidebar-collapsed .sidebar:hover {
                width: var(--sidebar-width-expanded);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
            }

            body.sidebar-collapsed .sidebar:hover .menu-wrapper {
                overflow-y: auto;
                display: block;
                justify-content: flex-start;
            }

            body.sidebar-collapsed .sidebar:hover+main {
                margin-left: var(--sidebar-width-expanded);
            }

            body.sidebar-collapsed .sidebar:hover .nav-link span,
            body.sidebar-collapsed .sidebar:hover .sidebar-heading {
                opacity: 1;
                visibility: visible;
                width: auto;
                transition-delay: 0.15s;
            }

            body.sidebar-collapsed .sidebar:hover .nav-link .bi {
                margin-right: 1rem;
                min-width: auto;
            }

            body.sidebar-collapsed .sidebar:hover .nav-link {
                justify-content: flex-start;
            }

            body:not(.sidebar-collapsed) .sidebar {
                width: var(--sidebar-width-expanded);
            }

            body:not(.sidebar-collapsed) main {
                margin-left: var(--sidebar-width-expanded);
            }

            body:not(.sidebar-collapsed) .sidebar .menu-wrapper {
                overflow-y: auto;
                display: block;
                justify-content: flex-start;
            }

            body:not(.sidebar-collapsed) .sidebar .nav-link span,
            body:not(.sidebar-collapsed) .sidebar .sidebar-heading {
                opacity: 1;
                visibility: visible;
                width: auto;
            }

            body:not(.sidebar-collapsed) .sidebar .nav-link .bi {
                margin-right: 1rem;
                min-width: auto;
            }

            body:not(.sidebar-collapsed) .sidebar .nav-link {
                justify-content: flex-start;
            }
        }

        @media (max-width: 991.98px) {
            #sidebar-toggle-desktop {
                display: none !important;
            }

            #sidebar-toggle-mobile {
                display: block !important;
            }

            #mainSidebar.offcanvas {
                width: var(--sidebar-width-expanded) !important;
                padding-top: 0;
                z-index: 1045;
            }

            #mainSidebar.offcanvas .offcanvas-header {
                background-color: var(--sidebar-bg);
                color: var(--sidebar-link-active-color);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            #mainSidebar.offcanvas .menu-wrapper.offcanvas-body {
                background-color: var(--sidebar-bg);
                overflow-y: auto;
                display: block;
                justify-content: flex-start;
            }

            #mainSidebar.offcanvas .btn-close {
                filter: invert(1) grayscale(100%) brightness(200%);
            }

            #mainSidebar.offcanvas .nav-link span,
            #mainSidebar.offcanvas .sidebar-heading {
                opacity: 1;
                visibility: visible;
                width: auto;
            }

            #mainSidebar.offcanvas .nav-link .bi {
                margin-right: 1rem;
                min-width: auto;
            }

            #mainSidebar.offcanvas .nav-link {
                justify-content: flex-start;
            }

            main {
                margin-left: 0 !important;
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <header class="navbar navbar-header fixed-top p-0 shadow-sm">
        <div class="d-flex align-items-center">
            <button id="sidebar-toggle-desktop" class="btn sidebar-toggle-btn fs-4" type="button">
                <i class="bi bi-list"></i>
            </button>
            <button id="sidebar-toggle-mobile" class="btn sidebar-toggle-btn fs-4" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#mainSidebar" aria-controls="mainSidebar">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand text-white ms-2" href="/dashboard">
                <i class="bi bi-tooth me-2"></i> SenyumQu Dental
            </a>
        </div>
        <div class="navbar-nav ms-auto me-3">
            <div class="nav-item text-nowrap">
                <form action="/logout" method="post">
                    @csrf
                    <button type="submit" class="nav-link px-3 bg-transparent border-0 text-white">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <nav id="mainSidebar" class="sidebar" tabindex="-1" aria-labelledby="mainSidebarLabel">
        <div class="offcanvas-header d-lg-none">
            <h5 class="offcanvas-title" id="mainSidebarLabel">Menu Navigasi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="menu-wrapper">
            <ul class="nav flex-column py-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                        <i class="bi bi-house-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if(auth()->user()?->role?->role_name === 'manager')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/salaries*') ? 'active' : '' }}" href="/dashboard/salaries" data-bs-toggle="tooltip" data-bs-placement="right" title="Attendances & Slips">
                        <i class="bi bi-cash-stack"></i>
                        <span>Attendances & Slips</span>
                    </a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/salaries/slips') ? 'active' : '' }}" href="/dashboard/salaries/slips" data-bs-toggle="tooltip" data-bs-placement="right" title="Attendances & Slips">
                        <i class="bi bi-cash-stack"></i>
                        <span>Slips</span>
                    </a>
                </li>
                @endif

                <li class="sidebar-heading">Master Data</li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->routeIs('dashboard.masters.*') && !request()->routeIs('dashboard.masters.patients*')) ? 'active' : '' }}"
                        href="{{ route('dashboard.masters.index') }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Master Users">
                        <i class="bi bi-people-fill"></i>
                        <span>Master Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/masters/patients*') ? 'active' : '' }}" href="{{ route('dashboard.masters.patients') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Patients">
                        <i class="bi bi-person-badge"></i>
                        <span>Master Patients</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/suppliers*') ? 'active' : '' }}" href="{{ route('dashboard.suppliers.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Suppliers">
                        <i class="bi bi-truck"></i> <span>Master Suppliers</span>
                    </a>
                </li>
                @if(auth()->user()?->role?->role_name === 'manager')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/coa*') ? 'active' : '' }}" href="{{ route('dashboard.coa.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master COA">
                        <i class="bi bi-journal-album"></i> <span>Master COA</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/pricelists*') ? 'active' : '' }}" href="{{ route('dashboard.pricelists.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Pricelists">
                        <i class="bi bi-tags-fill"></i> <span>Master Pricelists</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/holidays*') ? 'active' : '' }}" href="{{ route('dashboard.holidays.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Holidays">
                        <i class="bi bi-calendar-x"></i> <span>Master Holidays</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/attendances*') ? 'active' : '' }}" href="{{ route('dashboard.attendances.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Attendances">
                        <i class="bi bi-check-circle-fill"></i> <span>Master Attendances</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/procedure_types*') ? 'active' : '' }}" href="{{ route('dashboard.procedure_types.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Procedure Types">
                        <i class="bi bi-heart-pulse"></i> <span>Master Procedure Types</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/dental-materials*') ? 'active' : '' }}" href="{{ route('dashboard.dental-materials.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Dental Materials">
                        <i class="bi bi-box-seam"></i>
                        <span>Master Dental Materials</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/procedures*') ? 'active' : '' }}" href="{{ route('dashboard.procedures.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Procedures">
                        <i class="bi bi-clipboard-pulse"></i> <span>Master Procedures</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/procedure_materials*') ? 'active' : '' }}" href="{{ route('dashboard.procedure_materials.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Procedure Material">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <span>Master Procedure Material</span>
                    </a>
                </li>

                <li class="sidebar-heading">Reservations</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/reservations*') ? 'active' : '' }}" href="{{ route('dashboard.reservations.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Reservations">
                        <i class="bi bi-calendar-check"></i>
                        <span>Reservations</span>
                    </a>
                </li>

                <li class="sidebar-heading">Materials</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/stock_cards*') ? 'active' : '' }}" href="{{ route('dashboard.stock_cards.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Stock Cards">
                        <i class="bi bi-card-checklist"></i> <span>Stock Cards</span>
                    </a>
                </li>
                @if(auth()->user()?->role?->role_name === 'manager' || auth()->user()?->role?->role_name === 'admin')
                <li class="sidebar-heading">Transactions</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/transactions*') ? 'active' : '' }}" href="{{ route('dashboard.transactions.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Transaction">
                        <i class="bi bi-cash-coin"></i>
                        <span>Transaction</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/expenses*') ? 'active' : '' }}" href="{{ route('dashboard.expenses.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Expenses">
                        <i class="bi bi-currency-dollar"></i>
                        <span>Expenses</span>
                    </a>
                </li>

                <li class="sidebar-heading">Purchases</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/purchase_requests*') ? 'active' : '' }}" href="{{ route('dashboard.purchase_requests.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Purchase Request">
                        <i class="bi bi-receipt"></i>
                        <span>Purchase Request</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/purchase_orders*') ? 'active' : '' }}" href="{{ route('dashboard.purchase_orders.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Purchase Orders">
                        <i class="bi bi-cart-check"></i> <span>Purchase Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/purchases*') ? 'active' : '' }}" href="{{ route('dashboard.purchases.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Purchase Invoice">
                        <i class="bi bi-file-earmark-ruled"></i> <span>Purchase Invoice</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()?->role?->role_name === 'manager')
                <li class="sidebar-heading">Reports</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/reports/balance_sheet*') ? 'active' : '' }}" href="{{ route('dashboard.reports.balance_sheet') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Balance Sheet">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span>Balance Sheet</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/reports/income_statement*') ? 'active' : '' }}" href="{{ route('dashboard.reports.income_statement') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Income Statement">
                        <i class="bi bi-graph-up"></i>
                        <span>Income Statement</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/reports/cash_flow*') ? 'active' : '' }}" href="{{ route('dashboard.reports.cash_flow') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Cash Flow">
                        <i class="bi bi-cash-stack"></i>
                        <span>Cash Flow</span>
                    </a>
                </li>

                <li class="sidebar-heading">Journals</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/journals*') ? 'active' : '' }}" href="{{ route('dashboard.journals.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Journal Details">
                        <i class="bi bi-journal-text"></i>
                        <span>Journal Details</span>
                    </a>
                </li>
                @endif

                <li class="sidebar-heading">Schedules Management</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/schedules/templates*') ? 'active' : '' }}" href="{{ route('dashboard.schedules.templates.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Schedule Templates">
                        <i class="bi bi-calendar-range"></i>
                        <span>Schedule Templates</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/schedules/overrides*') ? 'active' : '' }}" href="{{ route('dashboard.schedules.overrides.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Schedule Overrides">
                        <i class="bi bi-calendar-event"></i>
                        <span>Schedule Overrides</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main>
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                @yield('breadcrumbs')
            </ol>
        </nav>
        @yield('container')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const body = document.body;
            const mainSidebar = document.getElementById('mainSidebar');
            const desktopToggler = document.getElementById('sidebar-toggle-desktop');
            const mobileToggler = document.getElementById('sidebar-toggle-mobile');
            const menuWrapper = mainSidebar.querySelector('.menu-wrapper');
            let bsOffcanvasInstance = null;

            function configureLayout() {
                const isDesktop = window.innerWidth >= 992;

                if (isDesktop) {
                    // Hanya tambahkan 'sidebar-collapsed' jika tidak ada 'sidebar-pinned-open'
                    if (!body.classList.contains('sidebar-pinned-open')) {
                        body.classList.add('sidebar-collapsed');
                    } else {
                        body.classList.remove('sidebar-collapsed'); // Jika di-pin terbuka, pastikan tidak collapsed
                    }
                    mainSidebar.classList.remove('offcanvas', 'offcanvas-start', 'show');
                    mainSidebar.style.paddingTop = 'var(--header-height)';

                    if (menuWrapper) {
                        menuWrapper.classList.remove('offcanvas-body');
                        // Height diatur oleh CSS
                    }

                    mobileToggler.removeAttribute('data-bs-toggle');
                    mobileToggler.removeAttribute('data-bs-target');

                    const existingOffcanvas = bootstrap.Offcanvas.getInstance(mainSidebar);
                    if (existingOffcanvas) {
                        existingOffcanvas.dispose();
                        bsOffcanvasInstance = null;
                    }
                } else { // MOBILE / TABLET
                    body.classList.remove('sidebar-collapsed', 'sidebar-pinned-open');
                    mainSidebar.classList.add('offcanvas', 'offcanvas-start');
                    mainSidebar.style.paddingTop = '0';

                    if (menuWrapper) {
                        menuWrapper.classList.add('offcanvas-body');
                    }

                    mobileToggler.setAttribute('data-bs-toggle', 'offcanvas');
                    mobileToggler.setAttribute('data-bs-target', '#mainSidebar');
                    if (!bootstrap.Offcanvas.getInstance(mainSidebar)) {
                        bsOffcanvasInstance = new bootstrap.Offcanvas(mainSidebar);
                    }
                }
            }

            configureLayout();
            window.addEventListener('resize', configureLayout);

            if (desktopToggler) {
                desktopToggler.addEventListener('click', function() {
                    if (window.innerWidth >= 992) {
                        body.classList.toggle('sidebar-collapsed');
                        body.classList.toggle('sidebar-pinned-open', !body.classList.contains('sidebar-collapsed'));
                        tooltipList.forEach(tooltip => tooltip.hide());
                    }
                });
            }

            const navLinks = document.querySelectorAll('#mainSidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992 && mainSidebar.classList.contains('show')) {
                        const offcanvasInstance = bootstrap.Offcanvas.getInstance(mainSidebar);
                        if (offcanvasInstance) {
                            offcanvasInstance.hide();
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>