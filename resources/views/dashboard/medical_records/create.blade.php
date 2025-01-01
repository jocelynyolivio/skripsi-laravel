@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Add Medical Record for Patient: {{ $patientName }}</h3>

    <form action="{{ route('dashboard.medical_records.store', ['patientId' => $patientId]) }}" method="POST">
        @csrf

        <!-- Pilih Reservasi -->
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

        <!-- Prosedur Dinamis -->
        <div class="mb-3">
            <label class="form-label">Procedures</label>
            <div id="procedures-container">
                <!-- Prosedur Dinamis Akan Ditambahkan Di Sini -->
            </div>
            <button type="button" class="btn btn-secondary mt-2" id="add-procedure-btn">Add Procedure</button>
        </div>

        <div class="mb-3">
            <label for="treatment" class="form-label">Treatment</label>
            <input type="text" class="form-control" id="treatment" name="treatment" required>
        </div>

        <!-- Kondisi Gigi -->
        <div class="mb-3">
            <label for="teeth_condition" class="form-label">Teeth Condition</label>
            <input type="text" class="form-control" id="teeth_condition" name="teeth_condition" required>
        </div>

        <!-- Catatan -->
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes"></textarea>
        </div>

        <!-- Odontogram -->
        <div class="odontogram mt-4">
            <h4>Odontogram</h4>
            <div class="odontogram-diagram mb-4">
                @for ($i = 1; $i <= 32; $i++)
                    <button
                    type="button"
                    class="tooth btn btn-outline-primary mb-2"
                    data-tooth="{{ $i }}"
                    onclick="selectTooth({{ $i }})">
                    {{ $i }}
                    </button>
                    @endfor
            </div>

            <!-- Hidden Fields for Odontogram -->
            <div id="odontogram-fields"></div>
        </div>

        <button type="submit" class="btn btn-primary">Save Medical Record</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prosedur Dinamis
        const addProcedureBtn = document.getElementById('add-procedure-btn');
        const proceduresContainer = document.getElementById('procedures-container');

        addProcedureBtn.addEventListener('click', function() {
            const procedureCount = proceduresContainer.children.length + 1;
            const procedureElement = `
                <div class="mb-3 border p-3">
                    <label for="procedure_${procedureCount}" class="form-label">Procedure ${procedureCount}</label>
                    <select name="procedure_id[]" id="procedure_${procedureCount}" class="form-select" required>
                        <option value="">Select Procedure</option>
                        @foreach($procedures as $procedure)
                        <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-danger mt-2 remove-procedure-btn">Remove</button>
                </div>`;
            proceduresContainer.insertAdjacentHTML('beforeend', procedureElement);
        });

        proceduresContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-procedure-btn')) {
                e.target.closest('.mb-3').remove();
            }
        });

        // Odontogram Dinamis
        const odontogramFields = document.getElementById('odontogram-fields');

        window.selectTooth = function(toothNumber) {
            const existingField = document.querySelector(`[data-tooth-number="${toothNumber}"]`);

            if (!existingField) {
                odontogramFields.insertAdjacentHTML('beforeend', `
            <div data-tooth-number="${toothNumber}" class="mb-3 border p-3">
                <h5>Tooth ${toothNumber}</h5>
                <input type="hidden" name="tooth_number[]" value="${toothNumber}">
                <label for="odontogram_condition_${toothNumber}" class="form-label">Condition</label>
                <select name="odontogram_condition[]" id="odontogram_condition_${toothNumber}" class="form-select">
                    <option value="Healthy">Healthy</option>
                    <option value="Cavity">Cavity</option>
                    <option value="Filled">Filled</option>
                    <option value="Extracted">Extracted</option>
                </select>
                <label for="odontogram_notes_${toothNumber}" class="form-label">Notes</label>
                <textarea name="odontogram_notes[]" id="odontogram_notes_${toothNumber}" class="form-control"></textarea>
                <button type="button" class="btn btn-danger mt-2" onclick="removeTooth(${toothNumber})">Remove</button>
            </div>
        `);
            } else {
                alert(`Tooth ${toothNumber} is already selected.`);
            }
        };

        window.removeTooth = function(toothNumber) {
            const field = document.querySelector(`[data-tooth-number="${toothNumber}"]`);
            if (field) {
                field.remove();
            }
        };

    });
</script>
@endsection