@extends('dashboard.layouts.main')
@section('container')
<div class="container">
    <h4>Materials Used in Medical Record #{{ $medicalRecord->id }}</h4>

    @if ($materialsUsed->isEmpty())
        <div class="alert alert-warning">
            No materials have been selected for this record.
        </div>
    @else
        <table class="table table-bordered" id="showMaterialsTable">
            <thead>
                <tr>
                    <th>Material Name</th>
                    <th>Quantity Used</th>
                    <th>HPP Price</th>
                    <th>Total Cost</th>
                    <th>Usage Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materialsUsed as $usage)
                    <tr>
                        <td>{{ $usage->material->name ?? '-' }}</td>
                        <td>{{ $usage->quantity_out }}</td>
                        <td>Rp {{ number_format($usage->price_out, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($usage->quantity_out * $usage->price_out, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($usage->date)->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

<a href="{{ route('dashboard.medical_records.index', ['patientId' => $patient->id]) }}" class="btn btn-secondary mt-3">← Back</a>
</div>

<script>
    $(document).ready(function() {
        $('#showMaterialsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection
