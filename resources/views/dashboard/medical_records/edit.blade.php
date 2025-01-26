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
            <label for="procedures" class="form-label">Procedures</label>
            <select class="form-select" name="procedure_id[]" id="procedures" multiple required>
                @foreach($procedures as $procedure)
                    <option value="{{ $procedure->id }}" 
                        {{ in_array($procedure->id, $selectedProcedures) ? 'selected' : '' }}>
                        {{ $procedure->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tooth_numbers" class="form-label">Tooth Numbers</label>
            <div id="tooth-container">
                @foreach($procedureOdontograms as $odontogram)
                    <div class="tooth-entry d-flex mb-2">
                        <input type="text" class="form-control me-2" name="tooth_numbers[]" value="{{ $odontogram['tooth_number'] }}" placeholder="Tooth Number" required>
                        <input type="hidden" name="procedure_id[]" value="{{ $odontogram['procedure_id'] }}">
                        <input type="text" class="form-control" name="procedure_notes[]" value="{{ $odontogram['notes'] }}" placeholder="Notes">
                        <!-- Remove button to delete the existing entry -->
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
