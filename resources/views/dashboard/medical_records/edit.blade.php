@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Edit Medical Record for Patient: {{ $medicalRecord->reservation->patient->name }}</h3>

    <form action="{{ route('dashboard.medical_records.update', ['patientId' => $patientId, 'recordId' => $medicalRecord->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="teeth_condition" class="form-label">Teeth Condition</label>
            <input type="text" class="form-control" id="teeth_condition" name="teeth_condition" 
                   value="{{ old('teeth_condition', $medicalRecord->teeth_condition) }}" required>
        </div>

        <!-- Prosedur dan Nomor Gigi -->
        <div class="mb-3">
            <label for="tooth_numbers" class="form-label">Tooth Numbers and Procedures</label>
            <div id="tooth-container">
                @foreach($procedureOdontograms as $odontogram)
                    @php
                        $procedure = $procedures->firstWhere('id', $odontogram['procedure_id']);
                    @endphp
                    
                    <div class="tooth-entry d-flex mb-2 align-items-center">
                        <!-- Hidden input untuk id -->
                        <input type="hidden" name="id[]" value="{{ $odontogram['id'] }}">

                        <!-- Nama prosedur (read-only) -->
                        <span class="form-control me-2 bg-light">{{ $procedure->name }}</span>

                        <!-- Menampilkan nomor gigi jika prosedur membutuhkannya -->
                        @if($procedure->requires_tooth)
                            <input type="text" class="form-control me-2" 
                                   name="tooth_numbers[]" 
                                   value="{{ old('tooth_numbers.' . $loop->index, $odontogram['tooth_number']) }}" 
                                   placeholder="Tooth Number">
                        @endif

                        <!-- Menampilkan notes -->
                        <input type="text" class="form-control me-2" name="procedure_notes[]" 
                               value="{{ old('procedure_notes.' . $loop->index, $odontogram['notes']) }}" 
                               placeholder="Notes">
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
