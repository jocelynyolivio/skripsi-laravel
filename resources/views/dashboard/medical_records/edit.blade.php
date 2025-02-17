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
                @foreach($procedures as $procedure)
                    @php
                        $procedureInstances = $medicalRecord->procedures->where('id', $procedure->id);
                    @endphp
                    
                    <div class="tooth-entry d-flex flex-column mb-3">
                        <!-- Hidden input untuk procedure_id -->
                        <input type="hidden" name="procedure_ids[]" value="{{ $procedure->id }}">

                        <!-- Nama prosedur (read-only) -->
                        <label class="form-label">{{ $procedure->name }}</label>

                        <!-- Menampilkan nomor gigi dan notes jika prosedur membutuhkannya -->
                        @if($procedure->requires_tooth)
                            <div class="tooth-number-container">
                                @foreach($procedureInstances as $procedureData)
                                    <div class="d-flex mb-2">
                                        <input type="text" class="form-control me-2" 
                                               name="tooth_numbers[{{ $procedure->id }}][]" 
                                               value="{{ old('tooth_numbers.' . $procedure->id, $procedureData->pivot->tooth_number ?? '') }}" 
                                               placeholder="Tooth Number">
                                        <input type="text" class="form-control" 
                                               name="procedure_notes[{{ $procedure->id }}][]" 
                                               value="{{ old('procedure_notes.' . $procedure->id, $procedureData->pivot->notes ?? '') }}" 
                                               placeholder="Notes">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <input type="hidden" name="tooth_numbers[{{ $procedure->id }}][]" value="">
                            <input type="hidden" name="procedure_notes[{{ $procedure->id }}][]" value="">
                        @endif
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
