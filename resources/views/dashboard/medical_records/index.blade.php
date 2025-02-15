@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Medical Records for Patient: {{ $patientName }}</h3>
    <a href="{{ route('dashboard.medical_records.create', ['patientId' => $patientId]) }}" class="btn btn-primary mb-3">Add Medical Record</a>

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
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicalRecords as $record)
            <tr>
                <td>{{ $record->reservation->tanggal_reservasi }}</td>
                <td>{{ $record->teeth_condition }}</td>
                <!-- <td>{{ $record->treatment }}</td> -->
                <td>
    @if($record->procedureOdontograms->isNotEmpty())  
        @foreach($proceduress as $procedure) 
            @php
                $procedureOdontograms = $record->procedureOdontograms->where('procedure_id', $procedure->id);
            @endphp

            @if($procedureOdontograms->isNotEmpty())
                <div class="mb-2">
                    <strong>{{ $procedure->name }}</strong>

                    <!-- @if($procedure->requires_tooth === 1)
                        <br>
                        <strong>Teeth:</strong>
                        {{ $procedureOdontograms->pluck('tooth_number')->implode(', ') }}
                    @endif

                    @php
                        $notes = $procedureOdontograms->pluck('notes')->filter()->implode(', ');
                    @endphp

                    @if($notes)
                        <br>
                        <small class="text-muted">Notes: {{ $notes }}</small>
                    @endif -->

                    @if($procedure->requires_tooth === 1)
                        <ul>
                            @foreach($procedureOdontograms as $procedureOdontogram)
                                <li>
                                    Tooth: {{ $procedureOdontogram->tooth_number }}
                                    @if($procedureOdontogram->notes)
                                        - <small class="text-muted">{{ $procedureOdontogram->notes }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        @endforeach
    @else
        <span class="text-muted">No procedures</span>
    @endif
</td>




                <!-- <td>{{ $record->notes }}</td> -->
                <td>{{ $record->reservation->doctor->name }}</td>
                <td>
                    @if(!$record->transaction)
                    <a href="{{ route('dashboard.transactions.create', ['medicalRecordId' => $record->id]) }}"
                        class="btn btn-sm btn-success">Create Transaction</a>
                    @else
                    <span class="badge bg-secondary">Transaction Created</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('dashboard.medical_records.edit', ['patientId' => $patientId, 'recordId' => $record->id]) }}"
                        class="btn btn-sm btn-warning">Edit</a>

                    <a href="{{ route('dashboard.medical_records.selectMaterials', ['medicalRecordId' => $record->id]) }}"
                        class="btn btn-sm btn-info">Select Materials</a>


                    <form action="{{ route('dashboard.medical_records.destroy', ['patientId' => $patientId, 'recordId' => $record->id]) }}"
                        method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                    </form>
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