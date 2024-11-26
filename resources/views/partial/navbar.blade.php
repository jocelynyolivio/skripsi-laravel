<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <a class="navbar-brand" href="/">SenyumQu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link {{ ($title === 'homeee') ? 'active' : 'home' }}" href="/">Home</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link {{ ($title === 'abouttt') ? 'active' : 'about' }}" href="/about">About</a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link {{ ($title === 'blog') ? 'active' : 'blog' }}" href="/blog">Blog</a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link {{ ($title === 'categories') ? 'active' : 'category' }}" href="/categories">Category</a>
        </li> -->
        <li class="nav-item">
    @if (auth()->check() && auth()->user()->role_id == 4)
        <a class="nav-link" href="{{ route('reservation.index') }}">Reservasi</a>
    @else
        <a class="nav-link" href="{{ route('login') }}">Reservasi</a>
    @endif
</li>

        <!-- <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li> -->
      </ul>

      <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Welcome Back, {{ auth()->user()->name }}
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/dashboard"><i class="bi bi-layout-text-sidebar-reverse"></i> Dashboard</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <form action="/logout" method="post">
                @csrf
                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-in-right"></i> Logout</button>
              </form>
            </li>
          </ul>
        </li>
        @else
        <li class="nav-item">
          <a href="/login" class="nav-link {{ ($title === 'login') ? 'active' : '' }}"><i class="bi bi-box-arrow-in-right"></i> Regist/Login</a>
        </li>
        @endauth
      </ul>
      <!-- <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form> -->
    </div>
  </div>
</nav>