<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Default Title' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  
  <!-- bootstrap icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- styles -->
  <link rel="stylesheet" href="/css/style.css">

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

</head>

<style>
.carousel-inner img {
    width: 100%; /* Pastikan lebar gambar mengikuti lebar kontainer */
    height: 400px; /* Tentukan tinggi tetap atau bisa disesuaikan */
    object-fit: contain; /* Gambar akan disesuaikan dengan kontainer tanpa terdistorsi atau terpotong */
    max-height: 100%; /* Gambar tidak melebihi tinggi kontainer */
}

@media (max-width: 768px) {
    .carousel-inner img {
        height: 300px; /* Menyesuaikan ukuran gambar pada layar lebih kecil */
    }
}

@media (max-width: 576px) {
    .carousel-inner img {
        height: 250px; /* Menyesuaikan lebih kecil lagi pada perangkat dengan layar kecil */
    }
}

.carousel-caption {
    position: absolute;
    bottom: 20px; /* Letakkan caption di bawah gambar */
    left: 50%;
    transform: translateX(-50%);
    color: white;
}


</style>

<body>

  @include('partial.navbar')
  <div class="container" style="margin-top: 5vh;">
    @yield('container')
  </div>
  @if (!request()->routeIs('reservation.index') && !request()->routeIs('reservations.upcoming'))
      @include('partial.footer')
  @endif
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
      var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
      })
    });
  </script>
</body>

</html>