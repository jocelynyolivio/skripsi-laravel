@extends('layouts.main')

@section('container')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-4 text-center">Formulir Reservasi</h3>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('reservation.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" required value="{{ $user->name }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required value="{{ $user->phone ?? '' }}">
                </div>

                <div class="mb-3">
                    <label for="schedule_id" class="form-label">Pilih Jadwal</label>
                    <select name="schedule_id" id="schedule_id" class="form-select" required>
                        <option value="" disabled selected>Pilih Jadwal yang Tersedia</option>
                        @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ $schedule->doctor->name }} - {{ $schedule->date }} ({{ $schedule->time_start }} - {{ $schedule->time_end }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Reservasi</button>
            </form>
        </div>
    </div>
</div>
@endsection
