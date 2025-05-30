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

    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
    <table id="purchaseRequestTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Request Number</th>
                <th>Materials</th>
                <th>Date</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>
                    {{ $request->request_number }} {{-- Hanya menampilkan nomor request --}}
                </td>
                <td>@foreach($request->details as $detail)
                    <li>{{ $detail->material->name }} ( {{ $detail->material->unit_type }} )@endforeach</li>
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
                <td><a href="{{ route('dashboard.purchase_requests.show', $request->id) }}" class="btn btn-primary btn-sm">
                        Detail
                    </a></td>
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