@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchases Invoices', 'url' => route('dashboard.purchases.index')],
['text' => 'Create Purchase Invoice']
]
])
@endsection
@section('container')
<div class="container">
    <h1>Purchase Order #{{ $purchaseOrder->order_number }}</h1>
    <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->nama ?? '-' }}</p>
    <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date }}</p>
    <p><strong>Status:</strong> {{ ucfirst($purchaseOrder->status) }}</p>
    <p><strong>Notes:</strong> {{ $purchaseOrder->notes }}</p>

    <h4>Details</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Material</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->details as $detail)
            <tr>
                <td>{{ $detail->material->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->unit }}</td>
                <td>{{ number_format($detail->price, 2) }}</td>
                <td>{{ $detail->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">

    @if($purchaseOrder->purchaseInvoices->isEmpty())
    <a href="{{ route('dashboard.purchases.createFromOrder', $purchaseOrder) }}" class="btn btn-primary">Buat Invoice</a>
@endif


    <a href="{{ route('dashboard.purchase_orders.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection