<footer class="text-white" style="background-color: #8c8d5e; margin-top: auto;">
    <div class="container-fluid px-0">
        <div class="container py-5">
            <div class="row">
                <!-- Quick Links -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="fw-bold text-uppercase mb-3">Quick Links</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="/" class="text-decoration-none text-white">Home</a></li>
                        <li class="mb-2">
                            @if(Auth::guard('patient')->check())
                            <a class="text-decoration-none text-white" href="{{ route('reservation.index') }}" class="btn btn-primary">Reservation</a>
                            @else
                            <a class="text-decoration-none text-white" href="{{ route('patient.login') }}" class="btn btn-primary">Reservation</a>
                            @endif
                        </li>
                        @auth
                        <li class="mb-2"><a href="/dashboard" class="text-decoration-none text-white">Dashboard</a></li>
                        @else
                        <li class="mb-2"><a href="/patient/login" class="text-decoration-none text-white">Login</a></li>
                        @endauth
                        <li class="mb-2"><a href="#about" class="text-decoration-none text-white">About</a></li>
                        <li class="mb-2"><a href="#services" class="text-decoration-none text-white">Services</a></li>
                        <li class="mb-2"><a href="#testimonials" class="text-decoration-none text-white">Testimonials</a></li>
                        <li class="mb-2"><a href="#contact" class="text-decoration-none text-white">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="fw-bold text-uppercase mb-3">Contact Us</h6>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-map-marker-alt me-3 mt-1"></i>
                        <div>
                            <p class="small mb-1">SenyumQu Clinic</p>
                            <p class="small">123 Dental Street, Malang</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-phone-alt me-3"></i>
                        <p class="small mb-0">0812-3456-7890</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-envelope me-3"></i>
                        <p class="small mb-0">info@senyumqu.com</p>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="col-md-4">
                    <h6 class="fw-bold text-uppercase mb-3">Follow Us</h6>
                    <div class="d-flex gap-3 mb-4">
                        <a href="#" class="text-white fs-5"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/senyumqu.dental.malang/" class="text-white fs-5" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/6281234567890" class="text-white fs-5" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="text-white fs-5"><i class="fab fa-linkedin-in"></i></a>
                    </div>

                    <!-- Testimonials Preview -->
                    <div class="testimonial-preview">
                        <h6 class="fw-bold text-uppercase mb-3">Patient Testimonials</h6>
                        <div class="testimonial-item mb-3">
                            <p class="small mb-1 fst-italic">"The staff were incredibly friendly and professional. My teeth have never looked better!"</p>
                            <p class="small fw-bold mb-0">- Sarah W.</p>
                        </div>
                        <div class="testimonial-item">
                            <p class="small mb-1 fst-italic">"Professional, modern, and pain-free. I'm very happy with my new smile!"</p>
                            <p class="small fw-bold mb-0">- Linda K.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top border-white opacity-25"></div>

        <!-- Copyright -->
        <div class="container py-3">
            <div class="row">
                <div class="col text-center">
                    <p class="small mb-0">&copy; 2025 SenyumQu Dental Clinic. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>