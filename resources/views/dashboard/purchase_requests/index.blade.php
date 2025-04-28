@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchase Requests']
]
])
@endsection
@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3>Purchase Requests</h3>
        <a href="{{ route('dashboard.purchase_requests.create') }}" class="btn btn-success mb-3">Create New Request</a>
    </div>
    <table id="purchaseRequestTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Request Number</th>
                <th>Materials</th>
                <th>Date</th>
                <th>Requested By</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>
                    <a href="{{ route('dashboard.purchase_requests.show', $request->id) }}" class="text-primary fw-bold text-decoration-none">
                        <i class="bi bi-eye"></i> {{ $request->request_number }}
                    </a>
                </td>
                <td>@foreach($request->details as $detail)
                    <li>{{ $detail->material->name }}@endforeach</li>
                </td>
                <td>{{ $request->request_date }}</td>
                <td>{{ $request->requester->name ?? '-' }}</td>
                <td>
                    @if ($request->status == 'pending')
                    <span class="badge bg-warning text-dark">{{ ucfirst($request->status) }}</span>
                    @elseif ($request->status == 'approved')
                    <span class="badge bg-success">{{ ucfirst($request->status) }}</span>
                    @elseif ($request->status == 'rejected')
                    <span class="badge bg-danger">{{ ucfirst($request->status) }}</span>
                    @else
                    <span class="badge bg-secondary">{{ ucfirst($request->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#purchaseRequestTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection