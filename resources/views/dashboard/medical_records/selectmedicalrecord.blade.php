@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Unfilled Medical Record</h3>
    </div>

    @if ($records->isEmpty())
        <p class="text-success">All medical records are complete.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" id="unfilledTable">
                <thead>
                    <tr>
                        <th>Reservation Date</th>
                        <th>Patient Name</th>
                        <th>Doctor</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $record->tanggal_reservasi ?? '-' }}</td>
                            <td>
                                @php
                                    $p = $record->patient;
                                    $namaPasien = $p ? trim("{$p->fname} {$p->mname} {$p->lname}") : '-';
                                @endphp
                                {{ $namaPasien ?: '-' }}
                            </td>
                            <td>{{ $record->doctor->name ?? '-' }}</td>
                            <td>
                                <a href="/dashboard/patients/{{ $record->patient_id }}/medical_records/{{ $record->id }}/edit" class="btn btn-sm btn-warning">
                                   Fill Medical Record
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<script>
    $(document).ready(function() {
        $('#unfilledTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection
