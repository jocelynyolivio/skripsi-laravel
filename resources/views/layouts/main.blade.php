<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'SenyumQu Dental Clinic' }}</title>
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/bootstrap-icons.min.css') }}" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">


</head>

<style>
  .carousel-inner img {
    width: 100%;
    height: 400px;
    object-fit: contain;
    max-height: 100%;
  }

  @media (max-width: 768px) {
    .carousel-inner img {
      height: 300px;
    }
  }

  @media (max-width: 576px) {
    .carousel-inner img {
      height: 250px;
    }
  }

  .carousel-caption {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
  }

  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  main {
    flex: 1;
  }
</style>

<body>
  @if (!request()->routeIs('login'))
  @include('partial.navbar')
  @endif
  <main class="flex-shrink-0">
    <div class="container" style="margin-top: 5vh;">
      @yield('container')
    </div>
  </main>
  @if (request()->routeIs('index'))
  @include('partial.footer')
  @endif
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
      var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
      })
    });
  </script>
</body>

</html>