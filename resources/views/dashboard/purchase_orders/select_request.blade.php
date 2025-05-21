<!-- dd('bro'); -->
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3>Select Approved Purchase Request</h3>
    <table class="table table-bordered" id="requestTable">
        <thead>
            <tr>
                <th>Request Number</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
            <tr>
                <td>{{ $request->request_number }}</td>
                <td>{{ $request->request_date }}</td>
                <td>
                    <a href="{{ route('dashboard.purchase_orders.create', ['request_id' => $request->id]) }}" class="btn btn-primary btn-sm">Create Order</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#requestTable').DataTable();
    });
</script>
@endsection
