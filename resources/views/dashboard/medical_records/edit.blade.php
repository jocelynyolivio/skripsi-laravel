@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Master Patients', 'url' => route('dashboard.masters.patients')],
            [
                'text' => 'Medical Record for ' . $medicalRecord->patient->fname,
                'url' => route('dashboard.medical_records.index', ['patientId' => $medicalRecord->patient_id]),
            ],
            ['text' => $medicalRecord->tanggal_reservasi],
        ],
    ])
@endsection

@section('container')
    <div class="container mt-4">
        <div class="text-center mb-4">
            <h3>Medical Record for Patient: {{ $medicalRecord->patient->fname }}
                {{ $medicalRecord->patient->mname }} {{ $medicalRecord->patient->lname }}</h3>
            <h6>{{ \Carbon\Carbon::parse($medicalRecord->tanggal_reservasi)->format('d F Y') }}</h6>
        </div>

        <form
            action="{{ route('dashboard.medical_records.update', ['patientId' => $patientId, 'recordId' => $medicalRecord->id]) }}"
            method="POST">
            @csrf
            @method('PUT')

            <div class="row gx-4">
                <div class="col-lg-5">
                    {{-- Bagian Form SOAP Kiri --}}
                    <div class="mb-3">
                        <label for="teeth_condition" class="form-label">Teeth Condition <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="teeth_condition" name="teeth_condition"
                            value="{{ old('teeth_condition', $medicalRecord->teeth_condition) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="subjective" class="form-label">Subjective <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="subjective" name="subjective" rows="3" required>{{ old('subjective', $medicalRecord->subjective) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="objective" class="form-label">Objective <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="objective" name="objective" rows="3" required>{{ old('objective', $medicalRecord->objective) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="assessment" class="form-label">Assessment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="assessment" name="assessment" rows="3" required>{{ old('assessment', $medicalRecord->assessment) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="plan" class="form-label">Plan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="plan" name="plan" rows="3" required>{{ old('plan', $medicalRecord->plan) }}</textarea>
                    </div>
                </div>

                <div class="col-lg-7">
                    {{-- Bagian Prosedur Kanan --}}
                    <div class="mb-4 border p-3 rounded shadow-sm">
                        <h4>Select Procedure and Teeth</h4>

                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Select Procedure</label>
                            <select id="currentProcedure" class="form-select form-select-lg">
                                <option value="">-- Choose Procedure --</option>
                                @foreach ($procedures as $procedure)
                                    <option value="{{ $procedure->id }}"
                                        data-requires-tooth="{{ $procedure->requires_tooth ? '1' : '0' }}"
                                        data-default-condition="{{ $procedure->default_condition }}">
                                        {{ $procedure->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ODONTOGRAM SECTION START --}}
                        <div id="odontogramSection" class="mb-3" style="display: none;">
                            <p class="form-label fw-bold">2. Select Tooth (if applicable)</p>
                            <p class="text-muted text-center small">Click on a tooth to select for the chosen procedure.</p>
                            <div class="odontogram-diagram mb-3 text-center">
                                @php
                                    $permUpperRight = range(18, 11); $permUpperLeft = range(21, 28);
                                    $deciUpperRight = range(55, 51); $deciUpperLeft = range(61, 65);
                                    $deciLowerRight = range(85, 81); $deciLowerLeft = range(71, 75);
                                    $permLowerRight = range(48, 41); $permLowerLeft = range(31, 38);
                                @endphp

                                {{-- Baris 1: Gigi Tetap Atas --}}
                                <div class="odontogram-row mb-1">
                                    <div class="odontogram-quadrant d-flex justify-content-end">
                                        @foreach ($permUpperRight as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="odontogram-midline px-1">|</div>
                                    <div class="odontogram-quadrant d-flex justify-content-start">
                                        @foreach ($permUpperLeft as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Baris 2: Gigi Sulung Atas --}}
                                <div class="odontogram-row mb-1">
                                    <div class="odontogram-quadrant d-flex justify-content-end">
                                        @foreach ($deciUpperRight as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="odontogram-midline px-1">|</div>
                                    <div class="odontogram-quadrant d-flex justify-content-start">
                                        @foreach ($deciUpperLeft as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Baris 3: Gigi Sulung Bawah --}}
                                <div class="odontogram-row mb-2">
                                    <div class="odontogram-quadrant d-flex justify-content-end">
                                        @foreach ($deciLowerRight as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="odontogram-midline px-1">|</div>
                                    <div class="odontogram-quadrant d-flex justify-content-start">
                                        @foreach ($deciLowerLeft as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Baris 4: Gigi Tetap Bawah --}}
                                <div class="odontogram-row">
                                    <div class="odontogram-quadrant d-flex justify-content-end">
                                        @foreach ($permLowerRight as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="odontogram-midline px-1">|</div>
                                    <div class="odontogram-quadrant d-flex justify-content-start">
                                        @foreach ($permLowerLeft as $i)
                                            <div class="p-1">
                                                <button type="button" class="tooth btn btn-outline-primary" data-tooth="{{ $i }}" onclick="prepareToothForProcedure('{{ $i }}')">{{ $i }}</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ODONTOGRAM SECTION END --}}


                        <div id="toothSelectionActionArea" class="mb-3">
                            <div id="selectedToothForProcedureMessage" class="alert alert-info" style="display:none;">
                            </div>
                            <div id="inlineToothFormContainer" style="display: none;" class="mt-2 p-3 border rounded bg-light">
                                <h5 id="inlineFormHeader" class="mb-3"></h5>
                                <input type="hidden" id="inlineFormProcedureId">
                                <input type="hidden" id="inlineFormToothNumber">
                                <div class="mb-3">
                                    <label for="inlineToothNotes" class="form-label">Notes</label>
                                    <textarea id="inlineToothNotes" class="form-control form-control-sm" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="inlineToothSurface" class="form-label">Tooth Surface(s)</label>
                                    <select id="inlineToothSurface" class="form-select form-select-sm" multiple>
                                        <option value="M">Mesial (M)</option>
                                        <option value="O">Occlusal (O)</option>
                                        <option value="L">Lingual (L)</option>
                                        <option value="D">Distal (D)</option>
                                        <option value="B">Buccal (B)</option>
                                        <option value="F">Facial (F)</option>
                                        <option value="I">Incisal (I)</option>
                                        <option value="C">Cervical (C)</option>
                                        <option value="R">Root (R)</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-success btn-sm me-2" onclick="saveInlineToothNotes()">
                                    <i class="fas fa-save me-1"></i> Save Tooth Details
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelInlineToothForm()">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="selectedProceduresContainer" class="mt-4">
                        <h4 class="mb-3">Added Procedures</h4>
                        {{-- Selected procedures will be added here dynamically --}}
                    </div>
                </div>
            </div>

            <div id="hiddenProcedureFormInputs" style="display:none;"></div>

            <div class="mt-4 text-center border-top pt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Save All Changes
                </button>
                <a href="{{ route('dashboard.medical_records.index', ['patientId' => $patientId]) }}"
                    class="btn btn-secondary btn-lg">
                    <i class="fas fa-ban me-2"></i>Cancel Update
                </a>
            </div>
        </form>
    </div>

    <input type="hidden" id="procedureData" name="procedureData">

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" /> --}}


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectedProcedures = new Map();
            let currentProcedureIdGlobal = null;
            let currentProcedureNameGlobal = "";
            let requiresToothGlobal = false;

            const procedureSelect = document.getElementById('currentProcedure');
            const odontogramSection = document.getElementById('odontogramSection');
            const selectedToothMessageDiv = document.getElementById('selectedToothForProcedureMessage');
            const selectedProceduresContainer = document.getElementById('selectedProceduresContainer');
            const mainProcedureDataInput = document.getElementById('procedureData'); 
            const hiddenActualFormInputsContainer = document.getElementById('hiddenProcedureFormInputs'); 

            const inlineToothFormContainer = document.getElementById('inlineToothFormContainer');
            const inlineFormHeader = document.getElementById('inlineFormHeader');
            const inlineFormProcedureIdInput = document.getElementById('inlineFormProcedureId');
            const inlineFormToothNumberInput = document.getElementById('inlineFormToothNumber');
            const inlineToothNotesTextarea = document.getElementById('inlineToothNotes');
            const inlineToothSurfaceSelect = document.getElementById('inlineToothSurface');

            procedureSelect.addEventListener('change', function() {
                currentProcedureIdGlobal = this.value;
                const selectedOption = this.options[this.selectedIndex];
                currentProcedureNameGlobal = selectedOption.text;
                requiresToothGlobal = selectedOption.dataset.requiresTooth === "1";

                hideInlineFormAndMessage();
                unhighlightAllTeeth();

                if (currentProcedureIdGlobal && requiresToothGlobal) {
                    odontogramSection.style.display = 'block';
                } else {
                    odontogramSection.style.display = 'none';
                    if (currentProcedureIdGlobal && !requiresToothGlobal) {
                        addProcedureWithoutTooth(currentProcedureIdGlobal, currentProcedureNameGlobal);
                        resetProcedureSelectionUI();
                    }
                }
            });

            function addProcedureWithoutTooth(procId, procName) {
                if (selectedProcedures.has(procId)) {
                    alert(`Procedure "${procName}" is already added.`);
                    return;
                }
                selectedProcedures.set(procId, {
                    name: procName,
                    requiresTooth: false,
                    teeth: {}
                });
                updateSelectedProceduresDisplayAndFormInputs();
            }

            window.prepareToothForProcedure = function(toothNumber) {
                if (!currentProcedureIdGlobal || !requiresToothGlobal) {
                    alert('Please select a procedure that requires a tooth first.');
                    return;
                }
                unhighlightAllTeeth();
                highlightSpecificTooth(toothNumber);

                inlineFormProcedureIdInput.value = currentProcedureIdGlobal;
                inlineFormToothNumberInput.value = toothNumber;
                inlineFormHeader.textContent = `Details for Tooth ${toothNumber} (${currentProcedureNameGlobal})`;

                const existingProcedure = selectedProcedures.get(currentProcedureIdGlobal);
                let notes = '';
                let surfaces = [];

                if (existingProcedure && existingProcedure.teeth && existingProcedure.teeth[toothNumber]) {
                    notes = existingProcedure.teeth[toothNumber].notes;
                    surfaces = existingProcedure.teeth[toothNumber].surfaces;
                    selectedToothMessageDiv.innerHTML = `Editing details for tooth <b>${toothNumber}</b> for procedure: <b>${currentProcedureNameGlobal}</b>.`;
                } else {
                    selectedToothMessageDiv.innerHTML = `Adding details for tooth <b>${toothNumber}</b> for procedure: <b>${currentProcedureNameGlobal}</b>.`;
                }
                selectedToothMessageDiv.style.display = 'block';

                inlineToothNotesTextarea.value = notes;
                Array.from(inlineToothSurfaceSelect.options).forEach(option => {
                    option.selected = surfaces.includes(option.value);
                });

                inlineToothFormContainer.style.display = 'block';
                inlineToothNotesTextarea.focus();
            };

            window.saveInlineToothNotes = function() {
                const procedureId = inlineFormProcedureIdInput.value;
                const toothNumber = inlineFormToothNumberInput.value;
                const notes = inlineToothNotesTextarea.value;
                const selectedSurfaces = Array.from(inlineToothSurfaceSelect.selectedOptions).map(opt => opt.value);

                if (!procedureId || !toothNumber) {
                    alert('Error: Procedure or tooth information is missing.');
                    return;
                }
                
                const procNameForMap = currentProcedureIdGlobal === procedureId ? currentProcedureNameGlobal : (selectedProcedures.get(procedureId)?.name || "Unknown Procedure");

                if (!selectedProcedures.has(procedureId)) {
                    selectedProcedures.set(procedureId, {
                        name: procNameForMap,
                        requiresTooth: true, 
                        teeth: {}
                    });
                }

                const procedureData = selectedProcedures.get(procedureId);
                 if (!procedureData.name) { 
                    procedureData.name = procNameForMap;
                }
                procedureData.teeth[toothNumber] = {
                    notes: notes,
                    surfaces: selectedSurfaces
                };

                updateSelectedProceduresDisplayAndFormInputs();
                hideInlineFormAndMessage();
                resetProcedureSelectionUI();
            };

            window.cancelInlineToothForm = function() {
                hideInlineFormAndMessage();
                unhighlightAllTeeth();
            };

            function hideInlineFormAndMessage() {
                inlineToothFormContainer.style.display = 'none';
                selectedToothMessageDiv.style.display = 'none';
                selectedToothMessageDiv.innerHTML = '';
                inlineToothNotesTextarea.value = '';
                Array.from(inlineToothSurfaceSelect.options).forEach(option => option.selected = false);
            }

            function resetProcedureSelectionUI() {
                procedureSelect.value = "";
                odontogramSection.style.display = 'none';
                unhighlightAllTeeth();
                currentProcedureIdGlobal = null;
                currentProcedureNameGlobal = "";
                requiresToothGlobal = false;
            }

            function updateSelectedProceduresDisplayAndFormInputs() {
                selectedProceduresContainer.innerHTML = '<h4 class="mb-3">Added Procedures</h4>';
                if (selectedProcedures.size === 0) {
                    selectedProceduresContainer.innerHTML += '<p class="text-muted">No procedures added yet.</p>';
                }

                if (hiddenActualFormInputsContainer) {
                    hiddenActualFormInputsContainer.innerHTML = '';
                } else {
                    console.error("Critical: hiddenActualFormInputsContainer not found!");
                    return; 
                }
                
                const simplifiedJsonForMainData = {};

                selectedProcedures.forEach((data, procedureId) => {
                    const displayDiv = document.createElement('div');
                    displayDiv.className = 'mb-3 border p-3 rounded bg-white shadow-sm';
                    let teethDisplayHtml = '';
                    if (data.requiresTooth && Object.keys(data.teeth).length > 0) {
                        Object.entries(data.teeth).forEach(([toothNum, toothDetails]) => {
                            teethDisplayHtml += `
                                <div class="mb-2 ms-3 ps-2 border-start border-2">
                                    <p class="mb-1"><strong>Tooth ${toothNum}</strong>
                                        <button type="button" class="btn btn-sm btn-outline-info py-0 px-1 ms-2" onclick="editToothDetailsFromList('${procedureId}', '${toothNum}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </p>
                                    <p class="mb-1 small text-muted">Notes: ${toothDetails.notes || '<em>No notes</em>'}</p>
                                    <p class="mb-0 small text-muted">Surfaces: ${toothDetails.surfaces.join(', ') || '<em>No surfaces</em>'}</p>
                                </div>`;
                        });
                    } else if (data.requiresTooth) {
                        teethDisplayHtml = '<p class="text-muted ms-3 small"><em>No teeth selected for this procedure yet.</em></p>';
                    } else {
                        teethDisplayHtml = '<p class="text-muted ms-3 small"><em>This procedure does not require specific teeth.</em></p>';
                    }
                    displayDiv.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary">${data.name}</h5>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeProcedure('${procedureId}')">
                                <i class="fas fa-trash-alt me-1"></i> Remove
                            </button>
                        </div>
                        ${teethDisplayHtml}`;
                    selectedProceduresContainer.appendChild(displayDiv);

                    const procIdInput = document.createElement('input');
                    procIdInput.type = 'hidden';
                    procIdInput.name = 'procedure_id[]';
                    procIdInput.value = procedureId;
                    hiddenActualFormInputsContainer.appendChild(procIdInput);

                    let currentProcToothNumbersArray = []; 

                    if (data.requiresTooth) {
                        if (Object.keys(data.teeth).length > 0) {
                            Object.entries(data.teeth).forEach(([toothNum, toothDetails]) => {
                                currentProcToothNumbersArray.push(toothNum);

                                const toothNumInput = document.createElement('input');
                                toothNumInput.type = 'hidden';
                                toothNumInput.name = `tooth_numbers[${procedureId}][]`;
                                toothNumInput.value = toothNum;
                                hiddenActualFormInputsContainer.appendChild(toothNumInput);

                                const notesTextarea = document.createElement('textarea'); 
                                notesTextarea.name = `procedure_notes[${procedureId}][${toothNum}]`;
                                notesTextarea.textContent = toothDetails.notes; 
                                hiddenActualFormInputsContainer.appendChild(notesTextarea);

                                toothDetails.surfaces.forEach(surface => {
                                    const surfaceInput = document.createElement('input');
                                    surfaceInput.type = 'hidden';
                                    surfaceInput.name = `procedure_surface[${procedureId}][${toothNum}][]`;
                                    surfaceInput.value = surface;
                                    hiddenActualFormInputsContainer.appendChild(surfaceInput);
                                });
                            });
                        } else {
                            const emptyToothInput = document.createElement('input');
                            emptyToothInput.type = 'hidden';
                            emptyToothInput.name = `tooth_numbers[${procedureId}][]`;
                            emptyToothInput.value = ""; 
                            hiddenActualFormInputsContainer.appendChild(emptyToothInput);
                        }
                        simplifiedJsonForMainData[procedureId] = currentProcToothNumbersArray.length > 0 ? currentProcToothNumbersArray : null;
                    } else {
                        simplifiedJsonForMainData[procedureId] = null;
                    }
                });
                mainProcedureDataInput.value = JSON.stringify(simplifiedJsonForMainData);
            }
            updateSelectedProceduresDisplayAndFormInputs();

            window.editToothDetailsFromList = function(procedureId, toothNumber) {
                const procedureData = selectedProcedures.get(procedureId);
                if (!procedureData || (procedureData.requiresTooth && (!procedureData.teeth || !procedureData.teeth[toothNumber]))) {
                     console.warn("Data for edit not found:", procedureId, toothNumber);
                     return;
                }

                procedureSelect.value = procedureId; 
                currentProcedureIdGlobal = procedureId;
                currentProcedureNameGlobal = procedureData.name;
                requiresToothGlobal = procedureData.requiresTooth;

                hideInlineFormAndMessage(); 
                unhighlightAllTeeth();

                if (requiresToothGlobal) {
                    odontogramSection.style.display = 'block';
                    highlightSpecificTooth(toothNumber); 
                    prepareToothForProcedure(toothNumber); 
                } else {
                    odontogramSection.style.display = 'none';
                    alert("This procedure does not have specific tooth details to edit in this manner.");
                }
            };

            window.removeProcedure = function(procedureId) {
                selectedProcedures.delete(procedureId);
                updateSelectedProceduresDisplayAndFormInputs();
                if (currentProcedureIdGlobal === procedureId) {
                    hideInlineFormAndMessage();
                    resetProcedureSelectionUI();
                }
            };

            function highlightSpecificTooth(toothNumber) {
                const toothButton = document.querySelector(`.tooth[data-tooth="${toothNumber}"]`);
                if (toothButton) {
                    toothButton.classList.remove('btn-outline-primary');
                    toothButton.classList.add('btn-primary', 'active');
                }
            }

            function unhighlightAllTeeth() {
                document.querySelectorAll('.tooth').forEach(button => {
                    button.classList.remove('btn-primary', 'active');
                    button.classList.add('btn-outline-primary');
                });
            }
        });
    </script>

    <style>
        .tooth {
            width: 36px;
            height: 36px;
            padding: 0.2rem;
            margin: 1px;
            font-size: 0.70rem; 
            line-height: 1.3; 
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.2rem;
            flex-shrink: 0; 
        }
        .odontogram-diagram {
            max-width: 100%;
            margin: 0 auto;
            padding: 0px 5px; 
            box-sizing: border-box;
            overflow-x: auto; 
            overflow-y: hidden;
        }
        .odontogram-row {
            display: grid;
            grid-template-columns: 1fr auto 1fr; 
            align-items: center; 
            /* column-gap: 0.25rem; */ /* Optional: if you prefer gap over midline padding */
        }
        .odontogram-quadrant {
            /* Bootstrap d-flex & justify-content-end/start are in HTML for button alignment inside quadrant */
            flex-wrap: nowrap; /* Default: try to keep in one line */
            min-width: 0; /* Important for flex items within grid cells */
        }
        .odontogram-midline {
            color: #adb5bd;
            font-weight: bold;
            display: flex;       
            align-items: center; 
            justify-content: center; 
            /* px-1 for spacing is applied in HTML */
        }
        
        /* Media Query to enable wrapping on smaller screens */
        @media (max-width: 767.98px) { 
            .odontogram-quadrant {
                flex-wrap: wrap; 
                /* justify-content: center !important; */ /* Keeping original justify for now */
            }
            .tooth {
                font-size: 0.65rem; 
                width: 32px;       
                height: 32px;
            }
        }

        #inlineToothSurface {
            min-height: 120px;
        }
        #selectedProceduresContainer .border-start {
            border-left-width: 3px !important;
        }
    </style>
@endsection