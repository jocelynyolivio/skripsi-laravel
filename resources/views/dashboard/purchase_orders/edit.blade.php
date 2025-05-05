@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Purchases Order', 'url' => route('dashboard.purchase_orders.index')],
            ['text' => 'Edit Purchase Order']
        ]
    ])
@endsection
@section('container')
<div class="container">
    <h1>Edit Purchase Order</h1>
    <form action="{{ route('dashboard.purchase_orders.update', $purchaseOrder) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="order_number" class="form-label">Order Number</label>
            <input type="text" class="form-control" id="order_number" name="order_number" value="{{ old('order_number', $purchaseOrder->order_number) }}" required>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control">
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="purchase_request_id" class="form-label">Purchase Request</label>
            <select name="purchase_request_id" id="purchase_request_id" class="form-control">
                <option value="">-- Select Purchase Request --</option>
                @foreach($requests as $request)
                    <option value="{{ $request->id }}" {{ old('purchase_request_id', $purchaseOrder->purchase_request_id) == $request->id ? 'selected' : '' }}>
                        {{ $request->request_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="order_date" class="form-label">Order Date</label>
            <input type="date" class="form-control" id="order_date" name="order_date" value="{{ old('order_date', $purchaseOrder->order_date) }}" required>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes', $purchaseOrder->notes) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                @foreach(['draft', 'sent', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ old('status', $purchaseOrder->status) == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
