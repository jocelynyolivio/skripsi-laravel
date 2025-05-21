@extends('layouts.main')

@section('container')
<style>
    :root {
        --primary-color: #8c8d5e;
        --primary-color-light: #a3a47a;
        --primary-color-dark: #75764d;
        --secondary-color: #f8f9fa;
        --text-color: #333;
        --light-text: #f8f9fa;
    }

    .reservation-container {
        background-color: var(--secondary-color);
        border: 1px solid #ddd;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        color: var(--text-color);
    }

    .reservation-container h3 {
        color: var(--primary-color-dark);
        font-weight: bold;
    }

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #f5f5f5;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #333;
    }

    .badge.bg-success {
        background-color: var(--primary-color) !important;
        color: var(--light-text);
    }

    .alert-info {
        background-color: var(--primary-color-light);
        border: none;
        color: var(--light-text);
    }
</style>

<div class="container mt-5 col-md-6 justify-content-center reservation-container">
    <h3 class="mb-4 text-center">Upcoming Reservations</h3>

    @if($reservations->isEmpty())
        <div class="alert alert-info text-center">No upcoming reservations.</div>
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
