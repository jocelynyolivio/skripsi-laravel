{{-- resources/views/dashboard/dental_materials/create.blade.php --}}
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Add New Dental Material</h3>

    <form action="{{ route('dashboard.dental-materials.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Material Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>

        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
        </div>

        <div class="mb-3">
            <label for="unit_price" class="form-label">Unit Price</label>
            <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
