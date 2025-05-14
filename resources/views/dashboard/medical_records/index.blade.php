@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Master Patients', 'url' => route('dashboard.masters.patients')],
['text' => 'Medical Record for '. $patientName]
]
])
@endsection
@section('container')
<div class="container mt-5">
    <h3 class="text-center">Medical Records for Patient: {{ $patientName }}</h3>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <table id="medicalRecordsTable" class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Date</th>
                <th>Teeth Condition</th>
                <th>Procedures & Teeth</th>
                <th>Doctor</th>
                <th>Adjustment Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicalRecords as $record)
            <tr>
                <td>{{ $record->tanggal_reservasi }}</td>
                <td>{{ $record->teeth_condition }}</td>
                <td>
                    @if($record->procedures->isNotEmpty())
                    @foreach($proceduress as $procedure)
                    @php
                    $procedureRecords = $record->procedures->where('id', $procedure->id);
                    @endphp

                    @if($procedureRecords->isNotEmpty())
                    <div class="mb-2">
                        <strong>{{ $procedure->name }}</strong>

                        @if($procedure->requires_tooth === 1)
                        <ul>
                            @foreach($procedureRecords as $procedureRecord)
                            <li>
                                Tooth: {{ $procedureRecord->pivot->tooth_number }}
                                @if($procedureRecord->pivot->notes)
                                - <small class="text-muted">{{ $procedureRecord->pivot->notes }}</small>
                                @endif
                                @if($procedureRecord->pivot->surface)
                                - <small class="text-muted">{{ $procedureRecord->pivot->surface }}</small>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @else
                        @php
                        $notes = $procedureRecords->pluck('pivot.notes')->filter()->implode(', ');
                        @endphp
                        @if($notes)
                        <br>
                        <small class="text-muted">Notes: {{ $notes }}</small>
                        @endif
                        @endif
                    </div>
                    @endif
                    @endforeach
                    @else
                    <span class="text-muted">No procedures</span>
                    @endif
                    <span>
                        <p>Last edited by: {{ $record->editor->name ?? 'Unknown' }}</p>
                        <p>Last edited at: {{ $record->updated_at->format('d M Y H:i') }}</p>
                    </span>
                </td>
                <td>{{ $record->doctor->name }}</td>
                <td>
                    @if($record->adjustments->isNotEmpty())
                    <ul class="mb-0 ps-3">
                        @foreach($record->adjustments as $adjustment)
                        <li>
                            <strong>{{ $adjustment->adjusted_at }}</strong><br>
                            {{ $adjustment->notes }}
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <em>No adjustment</em>
                    @endif
                </td>
                <td>
                    <div class="d-flex flex-column gap-2">
                    @if(auth()->user()?->role?->role_name === 'admin' || auth()->user()?->role?->role_name === 'manager')    
                    @if(!$record->transaction)
                        
                        <a href="{{ route('dashboard.transactions.create', ['medicalRecordId' => $record->id]) }}" class="btn btn-sm btn-success d-flex align-items-center justify-content-center" style="width: 160px; height: 40px;"> <i class="fas fa-plus-circle me-1"></i> Create Transaction
                        </a>
                        @else
                        <span class="badge bg-secondary">Transaction Created</span>
                        @endif
                        @endif

                        @if(auth()->user()?->role?->role_name === 'dokter tetap' || auth()->user()?->role?->role_name === 'dokter luar')
                        @if($record->procedures->isEmpty())
                        <!-- Tampilkan tombol Edit hanya jika belum ada prosedur -->
                        <a href="{{ route('dashboard.medical_records.edit', ['patientId' => $patientId, 'recordId' => $record->id]) }}" class="btn btn-sm btn-warning d-flex align-items-center justify-content-center" style="width: 160px; height: 40px;"> <i class="fas fa-plus-circle me-1"></i> Add Record
                        </a>
                        @endif

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-sm btn-primary d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#adjustmentModal-{{ $record->id }}" style="width: 160px; height: 40px;">
                            <i class="fas fa-plus-circle me-1"></i> Add Adjustment
                        </button>

                        <!-- <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Add Adjustment Note</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.medical_records.adjustments.store', $record->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Adjustment Notes</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" required>{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i> Add Adjustment
                                </button>
                            </form>
                        </div>
                    </div> -->
                        <!-- Adjustment Modal -->
                        <div class="modal fade" id="adjustmentModal-{{ $record->id }}" tabindex="-1" aria-labelledby="adjustmentModalLabel-{{ $record->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('dashboard.medical_records.adjustments.store', $record->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="adjustmentModalLabel-{{ $record->id }}">Add Adjustment Note for {{ $record->tanggal_reservasi }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="notes-{{ $record->id }}" class="form-label">Adjustment Notes</label>
                                                <textarea name="notes" id="notes-{{ $record->id }}" class="form-control" rows="4" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Adjustment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        @endif

                        <a href="{{ route('dashboard.medical_records.selectMaterials', ['medicalRecordId' => $record->id]) }}"
                            class="btn btn-sm btn-info d-flex align-items-center justify-content-center"
                            style="width: 160px; height: 40px;">
                            <i class="fas fa-plus-circle me-1"></i> Select Material
                        </a>


                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#medicalRecordsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });

    // Event delegation for SweetAlert confirmation
    $('#medicalRecordsTable').on('click', '.delete-button', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection