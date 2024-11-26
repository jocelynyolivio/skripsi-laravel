<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">Dashboard SenyumQu</a>
        <div class="d-flex align-items-center justify-content-end w-100">
            <div class="navbar-nav">
                <div class="nav-item text-nowrap">
                    <form action="/logout" method="post">
                        @csrf
                        <button type="submit" class="nav-link px-4"><i class="bi bi-box-arrow-in-right"></i> Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>