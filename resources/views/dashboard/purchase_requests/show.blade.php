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
    <div class="d-flex justify-content-between mb-3">

        <h3 class="mb-4">Request Detail: {{ $purchaseRequest->request_number }}</h3>
        <div class="mb-4 d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard.purchase_requests.duplicate', $purchaseRequest->id) }}" class="btn btn-warning">
                Duplicate Request
            </a>

            @if(auth()->user()?->role?->role_name === 'manager')
            @if($purchaseRequest->status === 'approved' && !$purchaseRequest->isFullyOrdered())
            <a href="{{ route('dashboard.purchase_orders.create', ['request_id' => $purchaseRequest->id]) }}" class="btn btn-primary">
                Create Purchase Order
            </a>
            @elseif($purchaseRequest->status === 'pending')
            <form action="{{ route('dashboard.purchase_requests.approve', $purchaseRequest) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" id="approveButton">Approve</button>
            </form>

            <!-- Tombol Reject diganti trigger swal -->
            <button type="button" class="btn btn-danger" id="rejectSwalButton">
                Reject
            </button>

            @endif
            @endif
        </div>
    </div>

    <div class="card p-4 mb-4">
        <div class="row mb-2">
            <div class="col-md-6"><strong>Date:</strong> {{ $purchaseRequest->request_date }}</div>
            <div class="col-md-6"><strong>Requested By:</strong> {{ $purchaseRequest->requester->name ?? '-' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Approved By:</strong> {{ $purchaseRequest->approver->name ?? '-' }}</div>
            <div class="col-md-6">
                @php
                $statusColors = [
                'pending' => 'bg-warning text-dark',
                'approved' => 'bg-success',
                'rejected' => 'bg-danger'
                ];
                @endphp

                <strong>Status:</strong>
                <span class="badge {{ $statusColors[$purchaseRequest->status] ?? 'bg-secondary' }}">
                    {{ ucfirst($purchaseRequest->status) }}
                </span>

                @if($purchaseRequest->status === 'rejected' && $purchaseRequest->approval_notes)
                <div class="mt-2">
                    <strong>Rejection Notes:</strong>
                    <p class="mb-0">{{ $purchaseRequest->approval_notes }}</p>
                </div>
                @endif
            </div>

        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Notes:</strong> {{ $purchaseRequest->notes ?? '-' }}</div>
            <div class="col-md-6">
                <strong>Last edited by:</strong> {{ $purchaseRequest->editor->name ?? 'Unknown' }}<br>
                <strong>at:</strong> {{ $purchaseRequest->updated_at->format('d M Y H:i') }}
            </div>
        </div>
    </div>


    <h5 class="mb-3">Materials Requested</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered">
            <thead class="table-light">
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
                    <td>{{ $detail->material->lastStock() ?? '0' }}</td>
                    <td>{{ $detail->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <form id="rejectForm" method="POST" action="{{ route('dashboard.purchase_requests.reject', $purchaseRequest) }}" style="display: none;">
            @csrf
            <input type="hidden" name="approval_notes" id="rejectNotesInput">
        </form>

    </div>

    <!-- <a href="{{ route('dashboard.purchase_requests.index') }}" class="btn btn-secondary">← Back to List</a> -->
</div>

<script>
    document.getElementById('approveButton')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to approve this request?',
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

    document.getElementById('rejectSwalButton')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Reject Request',
            input: 'textarea',
            inputLabel: 'Rejection Notes (optional)',
            inputPlaceholder: 'Enter your reason here...',
            inputAttributes: {
                'aria-label': 'Rejection Notes'
            },
            showCancelButton: true,
            confirmButtonText: 'Reject',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            preConfirm: (notes) => {
                document.getElementById('rejectNotesInput').value = notes;
                document.getElementById('rejectForm').submit();
            }
        });
    });
</script>
@endsection