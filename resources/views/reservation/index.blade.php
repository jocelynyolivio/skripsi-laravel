@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Available Schedules</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('reservation.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="schedule_id" class="form-label">Select Schedule</label>
            <select name="schedule_id" id="schedule_id" class="form-select" required>
                <option value="" disabled selected>-- Choose Schedule --</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}">
                        Doctor: {{ $schedule->doctor->name }} | 
                        Date: {{ $schedule->date }} | 
                        Time: {{ $schedule->time_start }} - {{ $schedule->time_end }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Reserve</button>
    </form>
</div>
@endsection
