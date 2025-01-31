@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Edit Medical Record for Patient: {{ $medicalRecord->patient->name }}</h3>

    <form action="{{ route('dashboard.medical_records.update', ['patientId' => $patientId, 'recordId' => $medicalRecord->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="teeth_condition" class="form-label">Teeth Condition</label>
            <input type="text" class="form-control" id="teeth_condition" name="teeth_condition" value="{{ old('teeth_condition', $medicalRecord->teeth_condition) }}" required>
        </div>

        <div class="mb-3">
            <label for="treatment" class="form-label">Treatment</label>
            <input type="text" class="form-control" id="treatment" name="treatment" value="{{ old('treatment', $medicalRecord->treatment) }}" required>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes">{{ old('notes', $medicalRecord->notes) }}</textarea>
        </div>

        <div class="mb-3">
    <label for="tooth_numbers" class="form-label">Tooth Numbers and Procedures</label>
    <div id="tooth-container">
        @foreach($procedureOdontograms as $odontogram)
            <div class="tooth-entry d-flex mb-2 align-items-center">
                <!-- Menampilkan nama prosedur (read-only) -->
                <span class="form-control me-2 bg-light">{{ $procedures->firstWhere('id', $odontogram['procedure_id'])->name }}</span>
                
                <!-- Menampilkan nomor gigi (editable) -->
                <input type="text" class="form-control me-2" name="tooth_numbers[]" value="{{ $odontogram['tooth_number'] }}" placeholder="Tooth Number" required>
                
                <!-- Hidden input untuk procedure_id -->
                <input type="hidden" name="procedure_id[]" value="{{ $odontogram['procedure_id'] }}">
                
                <!-- Menampilkan notes (editable) -->
                <input type="text" class="form-control me-2" name="procedure_notes[]" value="{{ $odontogram['notes'] }}" placeholder="Notes">
                
                <!-- Remove button -->
                <button type="button" class="btn btn-danger ms-2 remove-tooth">X</button>
            </div>
        @endforeach
    </div>
</div>


        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('dashboard.medical_records.index', ['patientId' => $patientId]) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
