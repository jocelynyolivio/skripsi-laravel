@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3>Request Detail: {{ $purchaseRequest->request_number }}</h3>

    <div class="card p-3 mb-3">
        <p><strong>Date:</strong> {{ $purchaseRequest->request_date }}</p>
        <p><strong>Requested By:</strong> {{ $purchaseRequest->requester->name ?? '-' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($purchaseRequest->status) }}</p>
        <p><strong>Notes:</strong> {{ $purchaseRequest->notes ?? '-' }}</p>
    </div>

    <h5>Materials Requested</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Material</th>
                <th>Quantity</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseRequest->details as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $detail->material->name }} ({{ $detail->material->unit_type }})</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($purchaseRequest->status === 'approved')
    <a href="{{ route('dashboard.purchases.createFromRequest', $purchaseRequest->id) }}" class="btn btn-primary mt-3">
        Create Purchase Invoice
    </a>
    <a href="{{ route('dashboard.purchase_requests.duplicate', $purchaseRequest->id) }}" class="btn btn-warning mt-3">
        Duplicate Request
    </a>
    @endif

    <a href="{{ route('dashboard.purchase_requests.index') }}" class="btn btn-secondary mt-3">← Back to List</a>
</div>
@endsection