<footer class="bg-body-tertiary text-dark py-2 mt-4">
    <div class="container">
        <div class="row">
            <!-- Footer Navigation -->
            <div class="col-md-4">
                <h5 class="fw-bold" style="font-size: 0.85rem;">Quick Links</h5>
                <ul class="list-unstyled" style="font-size: 0.8rem;">
                    <li><a href="/" class="text-decoration-none text-dark">Home</a></li>
                    <li><a href="{{ auth()->check() && auth()->user()->role_id == 4 ? route('reservation.index') : route('login') }}" class="text-decoration-none text-dark">Reservasi</a></li>
                    @auth
                        <li><a href="/dashboard" class="text-decoration-none text-dark">Dashboard</a></li>
                    @else
                        <li><a href="/login" class="text-decoration-none text-dark">Login</a></li>
                    @endauth
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="col-md-4">
                <h5 class="fw-bold" style="font-size: 0.85rem;">Contact Us</h5>
                <p class="mb-1" style="font-size: 0.8rem;">SenyumQu Clinic</p>
                <p class="mb-1" style="font-size: 0.8rem;">123 Dental Street, Malang</p>
                <p class="mb-1" style="font-size: 0.8rem;">Phone: +62 812-3456-7890</p>
                <p style="font-size: 0.8rem;">Email: info@senyumqu.com</p>
            </div>

            <!-- Social Media Links -->
            <div class="col-md-4 text-md-end">
                <h5 class="fw-bold" style="font-size: 0.85rem;">Follow Us</h5>
                <a href="#" class="text-dark me-2" style="font-size: 0.8rem;"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-dark me-2" style="font-size: 0.8rem;"><i class="bi bi-twitter"></i></a>
                <a href="#" class="text-dark me-2" style="font-size: 0.8rem;"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-dark" style="font-size: 0.8rem;"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>

        <hr class="my-2">

        <!-- Copyright Section -->
        <div class="row">
            <div class="col text-center">
                <p class="mb-0" style="font-size: 0.8rem;">&copy; 2024 SenyumQu. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
