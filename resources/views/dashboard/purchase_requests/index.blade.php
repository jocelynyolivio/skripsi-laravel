@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3>Purchase Requests</h3>
        <a href="{{ route('dashboard.purchase_requests.create') }}" class="btn btn-success mb-3">Create New Request</a>
    </div>
    <table id="purchaseRequestTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Request Number</th>
                <th>Date</th>
                <th>Requested By</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $request->request_number }}</td>
                <td>{{ $request->request_date }}</td>
                <td>{{ $request->requester->name ?? '-' }}</td>
                <td>{{ ucfirst($request->status) }}</td>
                <td><a href="{{ route('dashboard.purchase_requests.show', $request->id) }}" class="btn btn-info btn-sm">Detail</a>
                    @if($request->status === 'pending')
                    <form action="{{ route('dashboard.purchase_requests.approve', $request) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve</button>
                    </form>

                    <!-- Trigger modal reject -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $request->id }}">
                        Reject
                    </button>

                    <!-- Modal Reject -->
                    <div class="modal fade" id="rejectModal-{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $request->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('dashboard.purchase_requests.reject', $request) }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel-{{ $request->id }}">Reject Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label>Rejection Notes (optional):</label>
                                        <textarea name="approval_notes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

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