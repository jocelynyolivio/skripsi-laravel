@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Pricelists'],
]
])
@endsection
@section('container')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Pricelists</h3>
        <a href="{{ route('dashboard.pricelists.create') }}" class="btn btn-primary">Create Pricelist</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered" id="pricelistTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Procedure</th>
                <th>Price</th>
                <th>Promo</th>
                <th>Effective Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pricelists as $pl)
            <tr>
                <td>{{ $pl->id }}</td>
                <td>{{ $pl->procedure->name ?? 'N/A' }}</td>
                <td>{{ number_format($pl->price, 2) }}</td>
                <td>{{ $pl->is_promo ? 'Yes' : 'No' }}</td>
                <td>{{ $pl->effective_date }}</td>
                <td>
                    <a href="{{ route('dashboard.pricelists.edit', $pl) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.pricelists.destroy', $pl) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm delete-button" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#pricelistTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });

    $('#pricelistTable').on('click', '.delete-button', function(e) {
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