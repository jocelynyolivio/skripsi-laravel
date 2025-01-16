@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Make a Reservation</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form untuk memilih tanggal -->
    <form id="filterForm" action="{{ route('reservation.index') }}" method="GET">
        <div class="form-group">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Find Available Schedules</button>
    </form>

    <div id="results" class="mt-4">
        @if(isset($schedules) && $schedules->count() > 0)
            <h5>Schedules for {{ $date }} ({{ $day_of_week }}):</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Available Times</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $doctorSchedules)
                        <tr>
                            <td>{{ $doctorSchedules['doctor']->name }}</td>
                            <td>
                                @foreach($doctorSchedules['schedules'] as $time)
                                    <form action="{{ route('reservation.store') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="doctor_id" value="{{ $doctorSchedules['doctor']->id }}">
                                        <input type="hidden" name="tanggal_reservasi" value="{{ $date }}">
                                        <input type="hidden" name="jam_mulai" value="{{ $time->time_start }}">
                                        <input type="hidden" name="jam_selesai" value="{{ $time->time_end }}">
                                        <button type="submit" class="badge bg-success border-0">
                                            {{ $time->time_start }} - {{ $time->time_end }}
                                        </button>
                                    </form>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif(isset($schedules))
            <div class="alert alert-info">
                No available schedules found for this date.
            </div>
        @endif
    </div>

    <!-- Form untuk reservasi -->
    <form id="reservationForm" action="{{ route('reservation.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="doctor_id" id="doctor_id">
        <input type="hidden" name="tanggal_reservasi" id="reservation_date">
        <input type="hidden" name="jam_mulai" id="time_start">
        <input type="hidden" name="jam_selesai" id="time_end">
        
        <button type="submit" class="btn btn-success mt-3">Confirm Reservation</button>
    </form>
</div>

@endsection
