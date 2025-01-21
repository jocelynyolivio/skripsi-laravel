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
                <th>Treatment</th>
                <th>Procedures & Teeth</th>
                <th>Notes</th>
                <th>Doctor</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicalRecords as $record)
            <tr>
                <td>{{ $record->date }}</td>
                <td>{{ $record->teeth_condition }}</td>
                <td>{{ $record->treatment }}</td>
                <td>
                    @if($record->procedures->count() > 0)
                    @foreach($record->procedures as $procedure)
                    <div class="mb-2">
                        <strong>{{ $procedure->name }}:</strong>
                        <br>
                        Teeth:
                        {{ $record->odontograms->where('procedure_id', $procedure->id)->pluck('tooth_number')->implode(', ') }}
                        @php
                        $notes = $record->odontograms->where('procedure_id', $procedure->id)->pluck('notes')->filter()->implode(', ');
                        @endphp
                        @if($notes)
                        <br>
                        <small class="text-muted">
                            Notes: {{ $notes }}
                        </small>
                        @endif
                        @if($record->procedureOdontograms->count() > 0)
                        <ul>
                            @foreach($record->procedureOdontograms->where('procedure_id', $procedure->id) as $procedureOdontogram)
                            <li>
                                Tooth: {{ $procedureOdontogram->tooth_number }} -
                                <small class="text-muted">{{ $procedureOdontogram->notes }}</small>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @endforeach
                    @else
                    <span class="text-muted">No procedures</span>
                    @endif
                </td>

                <td>{{ $record->notes }}</td>
                <td>{{ $record->doctor->name }}</td>
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

                    <form action="{{ route('dashboard.medical_records.destroy', ['patientId' => $patientId, 'recordId' => $record->id]) }}"
                        method="POST" style="display:inline;"
                        onsubmit="return confirm('Are you sure you want to delete this record?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
</script>
@endsection