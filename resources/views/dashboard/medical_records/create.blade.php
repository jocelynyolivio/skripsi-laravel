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
                        data-requires-tooth="{{ $procedure->requires_tooth ? '1' : '0' }}"
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
                            onclick="selectToothForProcedure('{{ $i }}')">
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

<!-- Hidden input untuk menyimpan data -->
<!-- Hidden input untuk menyimpan data -->
<input type="hidden" id="procedureData" name="procedureData">

<!-- Modal for Tooth Notes -->
<div class="modal fade" id="toothNotesModal" tabindex="-1" aria-labelledby="toothNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toothNotesModalLabel">Enter Notes for Tooth</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="toothNotesForm">
                    <input type="hidden" id="selectedToothNumber">
                    <div class="mb-3">
                        <label for="toothNotes" class="form-label">Notes</label>
                        <textarea id="toothNotes" class="form-control" placeholder="Enter notes for the selected tooth"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="saveToothNotes()">Save Notes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectedProcedures = new Map();

        window.selectProcedure = function() {
            // ambil dari select
            const procedureSelect = document.getElementById('currentProcedure');
            const procedureId = procedureSelect.value;
            const requiresTooth = procedureSelect.options[procedureSelect.selectedIndex].dataset.requiresTooth === "1";
            const procedureName = procedureSelect.options[procedureSelect.selectedIndex].text;

            if (!procedureId) {
                alert('Please select a procedure first');
                return;
                l
            }

            if (!selectedProcedures.has(procedureId)) {
                selectedProcedures.set(procedureId, {
                    name: procedureName,
                    requiresTooth: requiresTooth,
                    teeth: [] // Hanya diisi jika requiresTooth = true
                });

                updateSelectedProceduresDisplay();
            }
        };

        window.selectToothForProcedure = function(toothNumber) {
            const procedureSelect = document.getElementById('currentProcedure');
            const procedureId = procedureSelect.value;

            if (!procedureId || !selectedProcedures.has(procedureId)) {
                alert('Please select a procedure first');
                return;
            }

            const procedureData = selectedProcedures.get(procedureId);
            if (!procedureData.requiresTooth) {
                alert('This procedure does not require a specific tooth.');
                return;
            }

            // Show modal for notes
            document.getElementById('selectedToothNumber').value = toothNumber;
            const modal = new bootstrap.Modal(document.getElementById('toothNotesModal'));
            modal.show();
        };

        window.saveToothNotes = function() {
            const toothNumber = document.getElementById('selectedToothNumber').value;
            const notes = document.getElementById('toothNotes').value;

            const procedureSelect = document.getElementById('currentProcedure');
            const procedureId = procedureSelect.value;
            const procedureData = selectedProcedures.get(procedureId);

            if (!procedureData.teeth.includes(toothNumber)) {
                procedureData.teeth.push(toothNumber);
            }

            // Save notes
            procedureData.notes = procedureData.notes || {};
            procedureData.notes[toothNumber] = notes;

            updateSelectedProceduresDisplay();
            highlightSelectedTooth(toothNumber);

            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('toothNotesModal'));
            modal.hide();
        };

        function updateSelectedProceduresDisplay() {
            const container = document.getElementById('selectedProceduresContainer');
            container.innerHTML = '';

            let procedureDataObj = {}; // Data yang akan dikirim ke backend

            selectedProcedures.forEach((data, procedureId) => {
                procedureDataObj[procedureId] = data.teeth.length > 0 ? data.teeth : null; // Null jika tidak butuh gigi

                const div = document.createElement('div');
                div.className = 'mb-3 border p-3';
                div.innerHTML = `
                <h5>${data.name}</h5>
                <input type="hidden" name="procedure_id[]" value="${procedureId}">
                ${data.requiresTooth ? data.teeth.map(tooth => `
                    <div class="mb-2">
                        <p>Tooth ${tooth}</p>
                        <input type="hidden" name="tooth_numbers[${procedureId}][]" value="${tooth}">
                        <textarea name="procedure_notes[${procedureId}][${tooth}]" class="form-control" 
                                placeholder="Notes for tooth ${tooth}">${data.notes[tooth] || ''}</textarea>
                    </div>
                `).join('') : `
                    <p class="text-muted">This procedure does not require a specific tooth.</p>
                    <input type="hidden" name="tooth_numbers[${procedureId}][]" value="">
                `}
                <button type="button" class="btn btn-danger btn-sm mt-2" 
                        onclick="removeProcedure('${procedureId}')">
                    Remove Procedure
                </button>
            `;
                container.appendChild(div);
            });

            // Simpan data JSON ke hidden input
            document.getElementById('procedureData').value = JSON.stringify(procedureDataObj);
        }

        window.removeProcedure = function(procedureId) {
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

        document.getElementById('currentProcedure').addEventListener('change', selectProcedure);
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