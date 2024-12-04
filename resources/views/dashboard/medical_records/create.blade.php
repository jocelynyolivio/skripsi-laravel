@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Add Medical Record for Patient: {{ $patientName }}</h3>

    <form action="{{ route('dashboard.medical_records.store', ['patientId' => $patientId]) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="reservation_id" class="form-label">Select Reservation</label>
            <select name="reservation_id" id="reservation_id" class="form-select" required>
                <option value="">Select Reservation</option>
                @foreach($reservations as $reservation)
                <option value="{{ $reservation->id }}">
                    {{ $reservation->tanggal_reservasi }} - Doctor: {{ $reservation->doctor->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Prosedur (Checkbox) -->
        <div class="mb-3">
            <label class="form-label">Select Procedures</label>
            <div>
                @foreach($procedures as $procedure)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="procedure_id[]" value="{{ $procedure->id }}" id="procedure_{{ $procedure->id }}">
                    <label class="form-check-label" for="procedure_{{ $procedure->id }}">
                        {{ $procedure->name }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label for="teeth_condition" class="form-label">Teeth Condition</label>
            <input type="text" class="form-control" id="teeth_condition" name="teeth_condition" required>
        </div>

        <div class="mb-3">
            <label for="treatment" class="form-label">Treatment</label>
            <input type="text" class="form-control" id="treatment" name="treatment" required>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Medical Record</button>
    </form>
</div>

@endsection