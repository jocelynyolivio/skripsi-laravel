@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <!-- Carousel -->
    <div id="clinicCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            @if($contents->isEmpty())
                <div class="carousel-item active">
                    <img src="https://via.placeholder.com/800x400?text=No+Content+Available" class="d-block w-100" alt="No Content">
                </div>
            @else
                @foreach($contents as $content)
                    <div class="carousel-item @if ($loop->first) active @endif">
                        <img src="{{ asset('storage/' . $content->carousel_image) }}" class="d-block w-100" alt="{{ $content->carousel_text }}">
                        <div class="carousel-caption">
                            <h5>{{ $content->carousel_text }}</h5>
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
    @if($contents->isNotEmpty())
        <h3>{{ $contents->first()->welcome_title ?? 'Welcome to Our Dental Clinic' }}</h3>
        <p class="lead">{{ $contents->first()->welcome_message ?? 'Your trusted clinic for quality dental care.' }}</p>
    @endif

    <!-- About Section -->
    @if($contents->isNotEmpty())
    <div class="row mt-5">
        <div class="col-md-6">
            <h4>About Our Clinic</h4>
            <p>{{ $contents->first()->about_text }}</p>
        </div>
        <div class="col-md-6">
            <img src="{{ asset('storage/' . $contents->first()->about_image) }}" class="img-fluid rounded" alt="Clinic Image">
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
