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
                <th>Status</th>
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
                    <span><p>Last edited by: {{ $record->editor->name ?? 'Unknown' }}</p>
                    <p>Last edited at: {{ $record->updated_at->format('d M Y H:i') }}</p></span>
                </td>
                <td>{{ $record->doctor->name }}</td>
                <td>
                    @if(!$record->transaction)
                        <a href="{{ route('dashboard.transactions.create', ['medicalRecordId' => $record->id]) }}" class="btn btn-sm btn-success">Create Transaction</a>
                    @else
                        <span class="badge bg-secondary">Transaction Created</span>
                    @endif
                </td>
                <td>
                @if(auth()->user()?->role?->role_name === 'dokter tetap' || auth()->user()?->role?->role_name === 'dokter luar')
                    @if($record->procedures->isEmpty())
                        <!-- Tampilkan tombol Edit hanya jika belum ada prosedur -->
                        <a href="{{ route('dashboard.medical_records.edit', ['patientId' => $patientId, 'recordId' => $record->id]) }}" class="btn btn-sm btn-warning">Tambahkan Medical Record</a>
                    @endif
                @endif

                    <a href="{{ route('dashboard.medical_records.selectMaterials', ['medicalRecordId' => $record->id]) }}" class="btn btn-sm btn-info">Select Materials</a>

                    @if(auth()->user()?->role?->role_name === 'manager')
                    <form action="{{ route('dashboard.medical_records.destroy', ['patientId' => $patientId, 'recordId' => $record->id]) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                    </form>
                    @endif
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
