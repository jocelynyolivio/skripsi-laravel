@extends('layouts.main')

@section('container')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-4 text-center">Formulir Reservasi</h3>

            <!-- Tampilkan pesan sukses jika ada -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('reservation.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="mb-3">
                    <label for="reservation_date" class="form-label">Tanggal Reservasi</label>
                    <input type="date" class="form-control" id="reservation_date" name="reservation_date" required>
                </div>

                <div class="mb-3">
                    <label for="reservation_time" class="form-label">Jam Reservasi</label>
                    <input type="time" class="form-control" id="reservation_time" name="reservation_time" required>
                </div>

                <div class="mb-3">
                    <label for="exampleDropdown" class="form-label">Dokter</label>
                    <select class="form-select" id="exampleDropdown" aria-label="Select Option">
                        <option selected>Pilih dokter</option>
                        <option value="1">John Doe 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keluhan" class="form-label">Keluhan</label>
                    <input type="text" class="form-control" id="keluhan" name="keluhan" required>
                </div>

                <!-- Full-width Submit Button -->
                <button type="submit" class="btn btn-primary w-100">Reservasi</button>
            </form>
        </div>
    </div>
</div>
@endsection
