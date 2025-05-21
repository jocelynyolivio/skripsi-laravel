@extends('layouts.main')

@section('container')
<div class="container mt-5">
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Your Perfect Smile Starts Here</h1>
            <p class="hero-subtitle">Professional dental care in a comfortable and modern environment</p>
            <div class="d-flex justify-content-center gap-3" >
                @if(Auth::guard('patient')->check())
                <a href="{{ route('reservation.index') }}" class="btn btn-primary">Make Reservation</a>
                @else
                <a href="{{ route('patient.login') }}" class="btn btn-primary">Make Reservation</a>
                @endif

                <a href="#services" class="btn btn-outline-light">Our Services</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="img-fluid about-img" alt="About Us">
                </div>
                <div class="col-md-6">
                    <h2 class="section-title">About Us</h2>
                    <p>At <strong>SenyumQu Dental Clinic</strong>, we are dedicated to providing exceptional dental care tailored to your needs. Our clinic combines modern technology with a warm and friendly atmosphere, making every visit a pleasant experience.</p>
                    <p>Located in Malang, we offer comprehensive dental services including preventive care, cosmetic dentistry, orthodontics, and more — all under one roof.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section" style="background-color: #8c8d5e;">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title text-white">Our Services</h2>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card service-card text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-tooth"></i>
                        </div>
                        <h5>General Dentistry</h5>
                        <p>Routine check-ups, cleaning, and oral health consultations to keep your smile healthy.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-teeth"></i>
                        </div>
                        <h5>Cosmetic Dentistry</h5>
                        <p>Whitening, veneers, and reshaping to enhance the aesthetics of your smile.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-teeth-open"></i>
                        </div>
                        <h5>Orthodontics</h5>
                        <p>Braces and aligners to straighten your teeth and improve your bite.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">What Our Patients Say</h2>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="testimonial-card text-center">
                        <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="Patient" class="testimonial-img">
                        <h6>Sarah W.</h6>
                        <p>"The staff were incredibly friendly and professional. My teeth have never looked better!"</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card text-center">
                        <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Patient" class="testimonial-img">
                        <h6>John D.</h6>
                        <p>"Great experience from start to finish. Highly recommended for anyone in Malang."</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card text-center">
                        <img src="https://randomuser.me/api/portraits/women/3.jpg" alt="Patient" class="testimonial-img">
                        <h6>Linda K.</h6>
                        <p>"Professional, modern, and pain-free. I’m very happy with my new smile!"</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <!-- <section id="contact" class="contact-section bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Contact Us</h2>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h5>Address</h5>
                    <p>Jl. Sehat No. 123, Malang, Jawa Timur</p>
                    <h5>Phone</h5>
                    <p>(0341) 123-4567</p>
                    <h5>Email</h5>
                    <p>info@senyumquclinic.com</p>
                </div>
                <div class="col-md-6">
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Your Name">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Your Email">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="5" placeholder="Your Message"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section> -->


    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/6281234567890" class="floating-btn" target="_blank">
    <i class="bi bi-whatsapp fs-3"></i>
</a>

    <style>
        :root {
            --primary-color: #8c8d5e;
            --primary-color-light: #a3a47a;
            --primary-color-dark: #75764d;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --light-text: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: #fff;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            text-align: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-color-dark);
            border-color: var(--primary-color-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-light {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 3px;
            background: var(--primary-color);
            bottom: -10px;
            left: 0;
        }

        .about-section,
        .services-section,
        .testimonials-section {
            padding: 80px 0;
        }

        .about-img {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .about-img:hover {
            transform: scale(1.02);
        }

        .service-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .service-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .testimonial-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin: 15px;
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin-bottom: 20px;
        }

        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin-right: 15px;
            transition: transform 0.3s ease;
        }

        .social-icons a:hover {
            transform: translateY(-5px);
        }

        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .floating-btn:hover {
            transform: scale(1.1);
            color: white;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .navbar-brand {
                font-size: 1.5rem;
            }
        }
    </style>
    @endsection