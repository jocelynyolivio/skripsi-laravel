@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <!-- Tambahkan ini di bagian yang sesuai, biasanya di atas konten utama -->
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

    <!-- Carousel -->
    <div id="clinicCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            @if($contents->isEmpty())
                <!-- Jika database kosong, gunakan gambar lokal -->
                <div class="carousel-item active">
                    <img src="{{ asset('assets/klinik1.jpeg') }}" class="d-block w-100" alt="Clinic Image">
                    <div class="carousel-caption">
                        <h5>Welcome to Our Clinic</h5>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('assets/klinik2.jpeg') }}" class="d-block w-100" alt="Clinic Image">
                    <div class="carousel-caption">
                        <h5>Providing the Best Dental Care</h5>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('assets/klinik3.jpeg') }}" class="d-block w-100" alt="Clinic Image">
                    <div class="carousel-caption">
                        <h5>Your Smile, Our Priority</h5>
                    </div>
                </div>
            @else
                <!-- Jika database ada konten, tampilkan dari storage -->
                @foreach($contents as $content)
                    <div class="carousel-item @if ($loop->first) active @endif">
                        <img src="{{ asset($content->carousel_image ? 'storage/' . $content->carousel_image : 'assets/klinik1.jpeg') }}" class="d-block w-100" alt="{{ $content->carousel_text ?? 'Clinic Image' }}">
                        <div class="carousel-caption">
                            <h5>{{ $content->carousel_text ?? 'Welcome to Our Clinic' }}</h5>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#clinicCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#clinicCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Welcome Section -->
    <div class="text-center mt-4">
        <h3>{{ $contents->first()->welcome_title ?? 'Welcome to Our Dental Clinic' }}</h3>
        <p class="lead">{{ $contents->first()->welcome_message ?? 'Your trusted clinic for quality dental care.' }}</p>
    </div>

    <!-- About Section -->
    @if($contents->isNotEmpty() && $contents->first()->about_text)
    <div class="row mt-5 align-items-center">
        <div class="col-md-6">
            <h4>About Our Clinic</h4>
            <p>{{ $contents->first()->about_text }}</p>
        </div>
        <div class="col-md-6 text-center">
        @if($contents->isNotEmpty() && $contents->first()->about_image)
    <img src="{{ asset('storage/' . $contents->first()->about_image) }}" class="img-fluid rounded shadow" alt="Clinic Image">
@else
    <img src="{{ asset('assets/klinik2.jpeg') }}" class="img-fluid rounded shadow" alt="Default Clinic Image">
@endif

        </div>
    </div>
    @endif

    <!-- Services Section -->
    <div class="mt-5">
        <h4>Our Services</h4>
        <p>{{ $contents->first()->services_text ?? 'We offer a range of dental services to help you maintain a healthy and beautiful smile.' }}</p>
    </div>
</div>
@endsection
