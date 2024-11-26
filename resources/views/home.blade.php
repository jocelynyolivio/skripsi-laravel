<!-- resources/views/home.blade.php -->

@extends('layouts.main')
@section('container')
<div class="container mt-5">
    <!-- Carousel -->
    <div id="clinicCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://via.placeholder.com/800x400?text=Welcome+to+Our+Dental+Clinic" class="d-block w-100" alt="Welcome">
            </div>
            <div class="carousel-item">
                <img src="https://via.placeholder.com/800x400?text=Professional+Dental+Care" class="d-block w-100" alt="Professional Dental Care">
            </div>
            <div class="carousel-item">
                <img src="https://via.placeholder.com/800x400?text=Your+Smile+Matters+to+Us" class="d-block w-100" alt="Your Smile Matters">
            </div>
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
    <h3>Welcome to {{ $title }}</h3>
    <p class="lead">Your trusted clinic for quality dental care and personalized service.</p>

    <!-- About Section -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h4>About Our Clinic</h4>
            <p>Our clinic is dedicated to providing top-notch dental care with the latest technology and a compassionate approach. Our team of experienced professionals is here to ensure your comfort and satisfaction.</p>
        </div>
        <div class="col-md-6">
            <img src="https://via.placeholder.com/500x300?text=Clinic+Image" class="img-fluid rounded" alt="Clinic Image">
        </div>
    </div>

    <!-- Services Section -->
    <div class="mt-5">
        <h4>Our Services</h4>
        <p>We offer a range of dental services, including preventive care, cosmetic dentistry, and emergency treatments. Our goal is to help you maintain a healthy and beautiful smile.</p>
    </div>
</div>
@include('partial.footer')
@endsection


