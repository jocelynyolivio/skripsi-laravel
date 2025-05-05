@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchase Requests', 'url' => route('dashboard.purchase_requests.index')],
['text' => 'Detail Purchase Request ' . $purchaseRequest->request_number]
]
])
@endsection

@section('container')
<div class="container mt-5">
    <h3>Request Detail: {{ $purchaseRequest->request_number }}</h3>

    <div class="card p-3 mb-3">
        <p><strong>Date:</strong> {{ $purchaseRequest->request_date }}</p>
        <p><strong>Requested By:</strong> {{ $purchaseRequest->requester->name ?? '-' }}</p>
        <p><strong>Status:</strong>
            @php
            $statusColors = [
            'pending' => 'bg-warning text-dark',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger'
            ];
            @endphp
            <span class="badge {{ $statusColors[$purchaseRequest->status] ?? 'bg-secondary' }}">
                {{ ucfirst($purchaseRequest->status) }}
            </span>
        </p>
        <p><strong>Notes:</strong> {{ $purchaseRequest->notes ?? '-' }}</p>
    </div>

    <h5>Materials Requested</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Material</th>
                <th>Quantity</th>
                <th>Average Usage</th>
                <th>Last Stock</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseRequest->details as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $detail->material->name }} ({{ $detail->material->unit_type }})</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ number_format($detail->material->averageUsage(), 2) }}</td>
                <td>{{ $detail->material->lastStock() }}</td>
                <td>{{ $detail->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('dashboard.purchase_requests.duplicate', $purchaseRequest->id) }}" class="btn btn-warning mt-3">
        Duplicate Request
    </a>

    @if(auth()->user()?->role?->role_name === 'manager')

    @if($purchaseRequest->status === 'approved')

    <a href="{{ route('dashboard.purchase_orders.create', ['request_id' => $purchaseRequest->id]) }}" class="btn btn-primary mt-3">
        Create Purchase Order
    </a>
    @elseif($purchaseRequest->status === 'pending')
    <form action="{{ route('dashboard.purchase_requests.approve', $purchaseRequest) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success" id="approveButton">Approve</button>
    </form>

    <!-- Trigger modal reject -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $purchaseRequest->id }}">
        Reject
    </button>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal-{{ $purchaseRequest->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $purchaseRequest->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('dashboard.purchase_requests.reject', $purchaseRequest) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel-{{ $purchaseRequest->id }}">Reject Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label>Rejection Notes (optional):</label>
                        <textarea name="approval_notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" id="rejectButton">Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif

    <p>Last edited by: {{ $purchaseRequest->editor->name ?? 'Unknown' }}</p>
    <p>Last edited at: {{ $purchaseRequest->updated_at->format('d M Y H:i') }}</p>
    <p>Approved by: {{ $purchaseRequest->approver->name ?? 'Unknown' }}</p>

    <a href="{{ route('dashboard.purchase_requests.index') }}" class="btn btn-secondary mt-3">← Back to List</a>
</div>

<script>
    // Confirmation on approve button click
    document.getElementById('approveButton')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure approve this request?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.closest('form').submit();
            }
        });
    });

    // Confirmation on approve button click
    document.getElementById('rejectButton')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure reject this request?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reject!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.closest('form').submit();
            }
        });
    });
</script>
@endsection