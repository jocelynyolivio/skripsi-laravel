@extends('layouts.main')

@section('container')
<div class="container mt-5 col-md-6 justify-content-center">
    
    <h3 class="mb-4 text-center">Upcoming Reservations</h3>

    @if($reservations->isEmpty())
        <div class="alert alert-info">No upcoming reservations.</div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $reservation)
                    <tr>
                        <td>{{ date('l, d M Y', strtotime($reservation->tanggal_reservasi)) }}</td>
                        <td>{{ $reservation->jam_mulai }} - {{ $reservation->jam_selesai }}</td>
                        <td>{{ $reservation->doctor->name }}</td>
                        <td>
                            @if(now()->toDateString() == $reservation->tanggal_reservasi)
                                <span class="badge bg-warning">Today</span>
                            @else
                                <span class="badge bg-success">Upcoming</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
