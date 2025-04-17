@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Stock Card List</h3>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('dashboard.stock_cards.index') }}" class="mb-3 row">
    <div class="col-md-3">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>
    <div class="col-md-3">
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('dashboard.stock_cards.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>


    <table id="stockCardTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Material</th>
                <th>Reference No.</th>
                <th>Qty In</th>
                <th>Qty Out</th>
                <th>Remaining Stock</th>
                <th>Avg Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockCards as $card)
            <tr>
                <td>{{ \Carbon\Carbon::parse($card->date)->format('d M Y') }}</td>
                <td>{{ $card->dentalMaterial->name ?? '-' }}</td>
                <td>{{ $card->reference_number }}</td>
                <td>{{ $card->quantity_in }}</td>
                <td>{{ $card->quantity_out }}</td>
                <td>{{ $card->remaining_stock }}</td>
                <td>{{ number_format($card->average_price, 2) }}</td>
                <td>
                               </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#stockCardTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true
        });
    });

    $('#stockCardTable').on('click', '.delete-button', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
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
