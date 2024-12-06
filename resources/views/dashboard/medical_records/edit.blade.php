@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Edit Medical Record for Patient: {{ $medicalRecord->patient->name }}</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('dashboard.medical_records.update', ['patientId' => $patientId, 'recordId' => $medicalRecord->id]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="teeth_condition" class="form-label">Teeth Condition</label>
            <input type="text" class="form-control @error('teeth_condition') is-invalid @enderror" id="teeth_condition" name="teeth_condition" value="{{ old('teeth_condition', $medicalRecord->teeth_condition) }}" required>
            @error('teeth_condition')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="treatment" class="form-label">Treatment</label>
            <input type="text" class="form-control @error('treatment') is-invalid @enderror" id="treatment" name="treatment" value="{{ old('treatment', $medicalRecord->treatment) }}" required>
            @error('treatment')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes">{{ old('notes', $medicalRecord->notes) }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('dashboard.medical_records.index', ['patientId' => $patientId]) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
