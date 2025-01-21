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

        <!-- Kondisi Gigi -->
        <div class="mb-3">
            <label for="teeth_condition" class="form-label">Teeth Condition</label>
            <input type="text" class="form-control" id="teeth_condition" name="teeth_condition" required>
        </div>

        <div class="mb-3">
            <label for="treatment" class="form-label">Treatment</label>
            <input type="text" class="form-control" id="treatment" name="treatment" required>
        </div>

        <!-- Catatan -->
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes"></textarea>
        </div>

        <!-- Prosedur dan Odontogram Integration -->
        <div class="mb-4">
            <h4>Select Procedure and Teeth</h4>
            
            <!-- Procedure Selection -->
            <div class="mb-3">
                <label class="form-label">Select Procedure</label>
                <select id="currentProcedure" class="form-select">
                    <option value="">Select Procedure</option>
                    @foreach($procedures as $procedure)
                    <option value="{{ $procedure->id }}" 
                            data-default-condition="{{ $procedure->default_condition }}">
                        {{ $procedure->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Odontogram Diagram -->
            <div class="odontogram-diagram mb-3">
                <div class="row">
                    @for ($i = 1; $i <= 32; $i++)
                        <div class="col-md-1 mb-2">
                            <button type="button"
                                    class="tooth btn btn-outline-primary w-100"
                                    data-tooth="{{ $i }}"
                                    onclick="selectToothForProcedure({{ $i }})">
                                {{ $i }}
                            </button>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Selected Procedures and Teeth -->
            <div id="selectedProceduresContainer">
                <!-- Selected procedures will be added here dynamically -->
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Medical Record</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedProcedures = new Map(); // To track selected procedures and teeth

    window.selectToothForProcedure = function(toothNumber) {
        const procedureSelect = document.getElementById('currentProcedure');
        const procedureId = procedureSelect.value;
        
        if (!procedureId) {
            alert('Please select a procedure first');
            return;
        }

        const procedureName = procedureSelect.options[procedureSelect.selectedIndex].text;
        const defaultCondition = procedureSelect.options[procedureSelect.selectedIndex].dataset.defaultCondition;

        // Check if tooth is already used
        const isToothUsed = Array.from(selectedProcedures.values()).some(proc => 
            proc.teeth.includes(toothNumber)
        );

        if (isToothUsed) {
            alert(`Tooth ${toothNumber} is already assigned to a procedure`);
            return;
        }

        // Add or update procedure-tooth mapping
        if (!selectedProcedures.has(procedureId)) {
            selectedProcedures.set(procedureId, {
                name: procedureName,
                teeth: [toothNumber],
                defaultCondition: defaultCondition
            });
        } else {
            selectedProcedures.get(procedureId).teeth.push(toothNumber);
        }

        updateSelectedProceduresDisplay();
        highlightSelectedTooth(toothNumber);
    };

    function updateSelectedProceduresDisplay() {
    const container = document.getElementById('selectedProceduresContainer');
    container.innerHTML = '';

    selectedProcedures.forEach((data, procedureId) => {
        const div = document.createElement('div');
        div.className = 'mb-3 border p-3';
        div.innerHTML = `
            <h5>${data.name}</h5>
            ${data.teeth.map(tooth => `
                <div class="mb-2">
                    <p>Tooth ${tooth}</p>
                    <input type="hidden" name="procedure_id[]" value="${procedureId}">
                    <input type="hidden" name="tooth_numbers[]" value="${tooth}">
                    <textarea name="procedure_notes[]" class="form-control" 
                            placeholder="Notes for tooth ${tooth}"></textarea>
                </div>
            `).join('')}
            <button type="button" class="btn btn-danger btn-sm mt-2" 
                    onclick="removeProcedure('${procedureId}')">
                Remove Procedure
            </button>
        `;
        container.appendChild(div);
    });
}

    window.removeProcedure = function(procedureId) {
        const teeth = selectedProcedures.get(procedureId).teeth;
        teeth.forEach(tooth => unhighlightTooth(tooth));
        selectedProcedures.delete(procedureId);
        updateSelectedProceduresDisplay();
    };

    function highlightSelectedTooth(toothNumber) {
        const toothButton = document.querySelector(`button[data-tooth="${toothNumber}"]`);
        if (toothButton) {
            toothButton.classList.remove('btn-outline-primary');
            toothButton.classList.add('btn-primary');
        }
    }

    function unhighlightTooth(toothNumber) {
        const toothButton = document.querySelector(`button[data-tooth="${toothNumber}"]`);
        if (toothButton) {
            toothButton.classList.remove('btn-primary');
            toothButton.classList.add('btn-outline-primary');
        }
    }
});
</script>

<style>
.tooth {
    width: 40px;
    height: 40px;
    padding: 0;
    margin: 2px;
    font-size: 12px;
}

.odontogram-diagram {
    max-width: 1000px;
    margin: 0 auto;
}
</style>
@endsection
