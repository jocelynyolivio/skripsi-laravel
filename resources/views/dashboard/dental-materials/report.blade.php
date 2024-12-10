@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Dental Materials Report</h3>
    <table id="dentalMaterialReportTable" class="display">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Stock Quantity</th>
                <th>Total Purchased</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($materials as $material)
                <tr>
                    <td>{{ $material->name }}</td>
                    <td>{{ $material->description }}</td>
                    <td>{{ $material->stock_quantity }}</td>
                    <td>{{ $material->expenses->sum('quantity') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#dentalMaterialReportTable').DataTable();
    });
</script>
@endsection
