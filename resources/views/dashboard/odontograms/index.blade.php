@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Odontogram for {{ $patient->name }}</h3>

    <div class="odontogram-diagram mb-4">
    @foreach(range(1, 32) as $toothNumber)
    @php

        $toothProcedures = $procedureOdontograms->get($toothNumber, collect());
        $hasMedicalRecord = $toothProcedures->isNotEmpty();

        // Cek apakah gigi ini ada di odontogram manual
        $toothManual = $odontograms->get($toothNumber);
        $manualCondition = $toothManual->condition ?? null;

        // Tentukan warna berdasarkan prioritas
        if ($hasMedicalRecord) {
            $buttonClass = 'btn-success'; // Gigi yang sudah ditangani di rekam medis
        } elseif ($manualCondition) {
            $buttonClass = match($manualCondition) {
                'Cavity' => 'btn-warning',
                'Filled' => 'btn-danger',
                'Extracted' => 'btn-secondary',
                default => 'btn-primary',
            };
        } else {
            $buttonClass = 'btn-outline-primary'; // Default untuk gigi sehat
        }
    @endphp

    <button
        type="button"
        class="tooth btn {{ $buttonClass }} mb-2"
        data-bs-toggle="modal"
        data-bs-target="#editToothModal"
        data-tooth-number="{{ $toothNumber }}"
        data-condition="{{ $manualCondition ?? 'Healthy' }}"
        data-notes="{{ $toothManual->notes ?? '' }}"
        data-procedures="{{ json_encode($toothProcedures->pluck('procedure_id')) }}">
        {{ $toothNumber }}
    </button>
@endforeach

    </div>
</div>

<div class="modal fade" id="editToothModal" tabindex="-1" aria-labelledby="editToothModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('dashboard.odontograms.store', ['patientId' => $patient->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="tooth_number" id="tooth_number">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editToothModalLabel">Edit Tooth</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="condition" class="form-label">Condition</label>
                        <select name="condition" id="condition" class="form-select">
                            <option value="Healthy">Healthy</option>
                            <option value="Cavity">Cavity</option>
                            <option value="Filled">Filled</option>
                            <option value="Extracted">Extracted</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Procedures</label>
                        <select name="procedure_id[]" id="procedures" class="form-select" multiple>
                            @foreach($procedures as $procedure)
                                <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toothButtons = document.querySelectorAll('.tooth');

        toothButtons.forEach(button => {
            button.addEventListener('click', function() {
                const toothNumber = this.getAttribute('data-tooth-number');
                const condition = this.getAttribute('data-condition');
                const notes = this.getAttribute('data-notes');
                const procedures = JSON.parse(this.getAttribute('data-procedures') || '[]');

                document.getElementById('tooth_number').value = toothNumber;
                document.getElementById('condition').value = condition;
                document.getElementById('notes').value = notes;

                const procedureSelect = document.getElementById('procedures');
                [...procedureSelect.options].forEach(option => option.selected = false);
                procedures.forEach(procedureId => {
                    const option = procedureSelect.querySelector(`option[value="${procedureId}"]`);
                    if (option) option.selected = true;
                });
            });
        });
    });
</script>
@endsection
