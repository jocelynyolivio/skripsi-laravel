<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <a class="navbar-brand" href="/">SenyumQu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link {{ ($title ?? 'home') === 'home' ? 'active' : '' }}" href="/">Home</a>
        </li>
        <li class="nav-item">
          @if(Auth::guard('patient')->check())
          <a class="nav-link" href="{{ route('reservation.index') }}">Reservasi</a>
          @else
          <a class="nav-link" href="{{ route('patient.login') }}">Reservasi</a>
          @endif
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        @if(Auth::guard('patient')->check())
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Welcome Back, {{ Auth::guard('patient')->user()->name }}
            </a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                  <i class="bi bi-person"></i> Profile
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="{{ route('patient.logout') }}" method="post">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="bi bi-box-arrow-right"></i> Logout
                  </button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <li class="nav-item">
            <a href="{{ route('patient.login') }}" class="nav-link {{ ($title ?? '') === 'login' ? 'active' : '' }}">
              <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>
